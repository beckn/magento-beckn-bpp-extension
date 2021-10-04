<?php

namespace Beckn\Checkout\Model;

use Beckn\Core\Helper\Data as Helper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Razorpay
 * @author Indglobal
 * @package Beckn\Checkout\Model
 */
class Razorpay
{
    const RAZORPAY_PAYMENT_LINK_URI = "https://api.razorpay.com/v1/payment_links/";
    const RAZORPAY_FETCH_PAYMENT_LINK_URI = "https://api.razorpay.com/v1/payments/";
    const RAZORPAY_ORDER_PAYMENT_LINK_URI = "https://api.razorpay.com/v1/orders/";
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var RazorpayPaymentLink
     */
    protected $_razorpayPaymentLink;

    /**
     * @var ResourceModel\RazorpayPaymentLink\Collection
     */
    protected $_razorpayPaymentLinkCollection;

    /**
     * Razorpay constructor.
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param Helper $helper
     * @param RazorpayPaymentLink $razorpayPaymentLink
     * @param ResourceModel\RazorpayPaymentLink\Collection $razorpayPaymentLinkCollection
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\HTTP\Client\Curl $curl,
        Helper $helper,
        RazorpayPaymentLink $razorpayPaymentLink,
        ResourceModel\RazorpayPaymentLink\Collection $razorpayPaymentLinkCollection
    )
    {
        $this->_moduleManager = $moduleManager;
        $this->_objectManager = $objectManager;
        $this->_curl = $curl;
        $this->_helper = $helper;
        $this->_razorpayPaymentLink = $razorpayPaymentLink;
        $this->_razorpayPaymentLinkCollection = $razorpayPaymentLinkCollection;
    }

    /**
     * @return bool
     */
    public function isRazorpayExists()
    {
        if ($this->_moduleManager->isOutputEnabled('Razorpay_Magento')) {
            return true;
        }
        return false;
    }

    /**
     * @param CartInterface $quote
     * @return bool|mixed
     * @throws \Exception
     */
    public function generateRazorpayPaymentLink(CartInterface $quote)
    {
        try {
            $keyId = $this->_helper->getConfigData("payment/razorpay/key_id");
            $keySecret = $this->_helper->getConfigData("payment/razorpay/key_secret");
            /**
             * @var RazorpayPaymentLink $razorpayPayment
             */
            $razorpayPayment = $this->_razorpayPaymentLinkCollection
                ->addFieldToFilter("quote_id", $quote->getId())->getFirstItem();
            if ($razorpayPayment->getId()) {
                if ($keyId != "" && $keySecret != "") {
                    $paymentId = $razorpayPayment->getPaymentId();
                    $this->cancelPaymentLink($paymentId, $keyId, $keySecret);
                }
            }
            if ($keyId != "" && $keySecret != "") {
                $this->_curl->setCredentials($keyId, $keySecret);
                $this->_curl->addHeader("Content-Type", "application/json");
                $uri = self::RAZORPAY_PAYMENT_LINK_URI;
                $params = $this->getBody($quote);
                $this->_curl->post($uri, json_encode($params));
                $response = $this->_curl->getBody();
                $status = $this->_curl->getStatus();
                if ($status == 200) {
                    $responseData = json_decode($response, true);
                    $data = [
                        "quote_id" => $quote->getId(),
                        "payment_link" => $responseData["short_url"],
                        "payment_id" => $responseData["id"],
                        "full_response" => $response,
                        "transaction_status" => $responseData["status"],
                    ];
                    if (!empty($razorpayPayment->getData())) {
                        $razorpayPayment->addData($data)->setEntityId($razorpayPayment->getEntityId())->save();
                    } else {
                        $this->_razorpayPaymentLink->setData($data)->save();
                    }
                    return $responseData["short_url"];
                } else {
                    $data = [
                        "quote_id" => $quote->getId(),
                        "full_response" => $response,
                    ];
                    $this->_razorpayPaymentLink->setData($data)->save();
                }
            }
            return false;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @param $paymentLink
     * @param $keyId
     * @param $keySecret
     * @return bool
     */
    public function cancelPaymentLink($paymentLink, $keyId, $keySecret)
    {
        $this->_curl->setCredentials($keyId, $keySecret);
        $this->_curl->addHeader("Content-Type", "application/json");
        $uri = self::RAZORPAY_PAYMENT_LINK_URI . $paymentLink . "/cancel";
        $this->_curl->post($uri, []);
        $response = $this->_curl->getBody();
        $status = $this->_curl->getStatus();
        if ($status == 200) {
            return true;
        }
        return false;
    }

    /**
     * @param CartInterface $quote
     * @return array
     * @throws \Exception
     */
    private function getBody(CartInterface $quote)
    {
        return [
            "amount" => (string)$quote->getGrandTotal() * 100,
            "currency" => $quote->getQuoteCurrencyCode(),
            "accept_partial" => false,
            "expire_by" => $this->getExpiryTimestamp(),
            "reference_id" => $quote->getId(),
            "description" => "Payment for magento store order",
            "customer" => [
                "name" => $quote->getBillingAddress()->getFirstname() . " " . $quote->getBillingAddress()->getLastname(),
                "contact" => $quote->getBillingAddress()->getTelephone(),
                "email" => $quote->getBillingAddress()->getEmail(),
            ],
            "notify" => [
                "sms" => false,
                "email" => false,
            ],
            "reminder_enable" => false,
            "notes" => [
                "merchant_quote_id" => $quote->getId()
            ],
            "callback_url" => $this->_helper->getRazorpayCallbackUrl(),
            "callback_method" => Helper::RAZORPAY_HTTP_METHOD
        ];
    }

    /**
     * @return false|int
     * @throws \Exception
     */
    public function getExpiryTimestamp()
    {
        $minutes_to_add = 30;
        $time = new \DateTime(date('Y-m-d H:i:s'));
        $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
        $stamp = $time->format('Y-m-d H:i');
        return strtotime($stamp);
    }

    /**
     * @param string $uri
     * @return array
     */
    public function razorpayGetApi(string $uri)
    {
        $keyId = $this->_helper->getConfigData("payment/razorpay/key_id");
        $keySecret = $this->_helper->getConfigData("payment/razorpay/key_secret");
        $razorpayResponse = [];
        $success = false;
        if ($keyId != "" && $keySecret != "") {
            $this->_curl->setCredentials($keyId, $keySecret);
            $this->_curl->addHeader("Content-Type", "application/json");
            $this->_curl->get($uri);
            $response = $this->_curl->getBody();
            $status = $this->_curl->getStatus();
            if ($status == 200) {
                $razorpayResponse = json_decode($response, true);
                $success = true;
            } else {
                $razorpayResponse = json_decode($response, true);
            }
        }
        return [
            "response" => $razorpayResponse,
            "success" => $success,
        ];
    }

    /**
     * @param OrderInterface $order
     * @param array $razorpayData
     * @return object
     * @throws \Exception
     */
    public function updateRazorpaySalesOrder(OrderInterface $order, array $razorpayData)
    {
        $saveData = [
            "quote_id" => $order->getQuoteId(),
            "order_id" => $order->getEntityId(),
            "increment_order_id" => $order->getIncrementId(),
            "rzp_order_id" => $razorpayData["order_id"],
            "rzp_payment_id" => $razorpayData["payment_id"],
            "rzp_signature" => $razorpayData["signature"],
            "by_frontend" => 1,
            "order_placed" => 1,

        ];
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * @var \Razorpay\Magento\Model\OrderLink $orderLink
         */
        $orderLink = $_objectManager->get('Razorpay\Magento\Model\ResourceModel\OrderLink\Collection')
            ->addFieldToFilter('quote_id', $order->getQuoteId())
            ->getFirstItem();
        if (empty($orderLink->getData())) {
            $orderLink->addData($saveData)->setId($orderLink->getId())->save();
        } else {
            $orderLink->setData($saveData)->save();
        }
        return $orderLink;
    }

    /**
     * @param OrderInterface $order
     * @param string $transactionStatus
     * @return RazorpayPaymentLink
     * @throws \Exception
     */
    public function updatePaymentLinkStatus(OrderInterface $order, $transactionStatus)
    {
        /**
         * @var RazorpayPaymentLink $razorpayPayment
         */
        $razorpayPayment = $this->_razorpayPaymentLinkCollection
            ->addFieldToFilter("quote_id", $order->getQuoteId())->getFirstItem();
        if (!empty($razorpayPayment->getData())) {
            $razorpayPayment->setStatus(1);
            $razorpayPayment->setTransactionStatus($transactionStatus);
            $razorpayPayment->save();
        }
        return $razorpayPayment;
    }

    /**
     * @param int $quoteId
     * @return array|mixed|string|null
     */
    public function getRazorpayTransactionStatus($quoteId){
        /**
         * @var RazorpayPaymentLink $razorpayPayment
         */
        $razorpayPayment = $this->_razorpayPaymentLinkCollection
            ->addFieldToFilter("quote_id", $quoteId)->getFirstItem();
        if (!empty($razorpayPayment->getData())) {
            return $razorpayPayment->getTransactionStatus();
        }
        return "";
    }
}