<?php

namespace Beckn\Checkout\Controller\Index;

use Beckn\Core\Helper\Data as Helper;
use Beckn\Core\Model\BecknQuoteMask;
use Beckn\Checkout\Model\ResourceModel\RazorpayPaymentLink\Collection;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Beckn\Checkout\Model\Razorpay;
use Beckn\Checkout\Model\ManageCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Beckn\Checkout\Model\Config\FilterOption\OrderType;
use Beckn\Checkout\Model\Config\FilterOption\PaymentStatus;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Response
 * @author Indglobal
 * @package Beckn\Checkout\Controller\Index
 */
class Response extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var Razorpay
     */
    protected $_razorpay;

    /**
     * @var ManageCheckout
     */
    protected $_manageCheckout;

    /**
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var Session
     */
    private $_checkoutSession;

    /**
     * @var QuoteManagement
     */
    private $_quoteManagement;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var BuilderInterface
     */
    protected $_transactionBuilder;

    /**
     * @var BecknQuoteMask
     */
    public $_becknQuoteMask;

    /**
     * @var Collection
     */
    protected $_razorpayPaymentLinkCollection;

    /**
     * Index constructor.
     * @param Context $context
     * @param Helper $helper
     * @param Curl $curl
     * @param Razorpay $razorpay
     * @param ManageCheckout $manageCheckout
     * @param CartRepositoryInterface $quoteRepository
     * @param Session $checkoutSession
     * @param QuoteManagement $quoteManagement
     * @param LoggerInterface $logger
     * @param BuilderInterface $transportBuilder
     * @param BecknQuoteMask $becknQuoteMask
     * @param Collection $razorpayPaymentLinkCollection
     */
    public function __construct(
        Context $context,
        Helper $helper,
        Curl $curl,
        Razorpay $razorpay,
        ManageCheckout $manageCheckout,
        CartRepositoryInterface $quoteRepository,
        Session $checkoutSession,
        QuoteManagement $quoteManagement,
        LoggerInterface $logger,
        BuilderInterface $transportBuilder,
        BecknQuoteMask $becknQuoteMask,
        Collection $razorpayPaymentLinkCollection
    )
    {
        $this->_curl = $curl;
        $this->_helper = $helper;
        $this->_razorpay = $razorpay;
        $this->_manageCheckout = $manageCheckout;
        $this->_quoteRepository = $quoteRepository;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteManagement = $quoteManagement;
        $this->_logger = $logger;
        $this->_transactionBuilder = $transportBuilder;
        $this->_becknQuoteMask = $becknQuoteMask;
        $this->_razorpayPaymentLinkCollection = $razorpayPaymentLinkCollection;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $validation = $this->validateParams($params);
        if (!empty($validation)) {
            $this->_logger->info("Razorpay params is missing (" . implode(",", $validation) . ")");
            echo "<h1 align='center'>".__("Unauthorized Request")."</h1>"; exit();
        } else {
            $quoteId = $params["razorpay_payment_link_reference_id"];
            if(!$this->checkPaymentCapture($quoteId)){
                echo "<h1 align='center'>".__("Order is already processed")."</h1>"; exit();
            }
            $contextData = $this->getContextData((int)$quoteId);
            $onConfirmResponse = [];
            $onConfirmResponse["context"] = $contextData["context"] ?? [];
            $apiUrl = $this->_helper->getBapUri(Helper::ON_CONFIRM, $onConfirmResponse["context"]);
            $onConfirmMessage = $contextData["message"] ?? [];
            $uri = Razorpay::RAZORPAY_FETCH_PAYMENT_LINK_URI . $params["razorpay_payment_id"];
            $razorpayResponse = $this->_razorpay->razorpayGetApi($uri);
            if ($razorpayResponse["success"] == true) {
                $razorpayOrderId = $razorpayResponse["response"]["order_id"];
                $uri = Razorpay::RAZORPAY_ORDER_PAYMENT_LINK_URI . $razorpayOrderId;
                $razorpayOrderResponse = $this->_razorpay->razorpayGetApi($uri);
                if ($razorpayResponse["success"] == true && $razorpayOrderResponse["response"]["receipt"] == $params["razorpay_payment_link_reference_id"]) {
                    $quoteId = $razorpayOrderResponse["response"]["receipt"];
                    try {
                        $quote = $this->_manageCheckout->getQuote((int)$quoteId);
                    } catch (NoSuchEntityException $e) {
                        $this->_logger->info($e->getMessage());
                    }
                    if ($quote->getId() == '') {
                        $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", "Quote id not found");
                        $this->_helper->sendResponse($apiUrl, $onConfirmResponse); exit();
                    }
                    if ($quote->getItemsQty() == 0) {
                        $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", "Quote item not found");
                        $this->_helper->sendResponse($apiUrl, $onConfirmResponse); exit();
                    }
                    $quote->getBillingAddress()->setEmail($quote->getBillingAddress()->getEmail());
                    $this->_manageCheckout->prepareGuestQuote($quote);
                    $quote->reserveOrderId();
                    $quote->getPayment()->importData(['method' => Helper::RAZORPAY]);
                    $quote->collectTotals();
                    $this->_quoteRepository->save($quote);
                    $this->_checkoutSession->setLastSuccessQuoteId($quote->getId());
                    $this->_checkoutSession->setLastQuoteId($quote->getId());
                    $this->_checkoutSession->clearHelperData();
                    $this->_checkoutSession->setRazorpayOrderID($razorpayOrderId);
                    $amount = (int)(number_format($quote->getGrandTotal() * 100, 0, ".", ""));
                    $this->_checkoutSession->setRazorpayOrderAmount($amount);
                    try {
                        $order = $this->_quoteManagement->submit($quote);
                        $order->setOrderType(OrderType::BECKN_STORE);
                        $order->setPaymentStatus(PaymentStatus::PAYMENT_PAID);
                        $order->save();
                        $this->_checkoutSession->setLastOrderId($order->getId());
                        $this->_checkoutSession->setLastRealOrderId($order->getIncrementId());
                        $this->_checkoutSession->setLastOrderStatus($order->getStatus());
                        $razorpayData = [
                            "order_id" => $razorpayOrderId,
                            "payment_id" => $params["razorpay_payment_id"],
                            "signature" => $params["razorpay_signature"],
                        ];
                        $this->_razorpay->updatePaymentLinkStatus($order, $razorpayOrderResponse["response"]["status"]);
                        $this->_razorpay->updateRazorpaySalesOrder($order, $razorpayData);
                        $this->_manageCheckout->updateTransactionStatus($onConfirmResponse["context"]["transaction_id"] ?? "");
                        $this->_logger->info("Order Placed successfully " . $order->getIncrementId());
                        $onConfirmResponse["message"]["order"] = $this->_manageCheckout->prepareOnConfirmResponse($order, $onConfirmMessage, Helper::STATUS_PAID);
                        $this->_manageCheckout->updateOrderAddress($order, $onConfirmResponse["message"]["order"]["billing"], $onConfirmResponse["message"]["order"]["fulfillment"]["end"]);
                        echo "<h1 align='center'>".__("Your order has been placed successfully.")."</h1>";
                    } catch (LocalizedException $e) {
                        $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", "Order Localized Exception Here => " . $e->getMessage());
                    } catch (\Exception $e) {
                        $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", "Order Exception Here => " . $e->getMessage());
                    }
                } else {
                    $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", "Can't able to get Razorpay info with order id ".$razorpayOrderId);
                }
            } else {
                $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", "Can't able to get Razorpay info with payment id ".$params["razorpay_payment_id"]);
            }
            $this->_helper->sendResponse($apiUrl, $onConfirmResponse);
            exit();
        }
    }

    /**
     * @param array $params
     * @return array
     */
    private function validateParams(array $params)
    {
        $validation = [];
        $requiredFields = [
            "razorpay_payment_id",
            "razorpay_payment_link_id",
            "razorpay_payment_link_reference_id",
            "razorpay_payment_link_status",
            "razorpay_signature",

        ];
        foreach ($requiredFields as $required => $key) {
            if (!array_key_exists($key, $params)) {
                $validation['required'][] = $key;
            }
        }
        return $validation;
    }

    /**
     * Validate Crf to skip parent validation
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Validate Crf to skip parent validation
     *
     * @param RequestInterface $request
     * @return boolean
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param int $quoteId
     * @return array
     */
    private function getContextData(int $quoteId)
    {
        $contextData = $this->_becknQuoteMask->getCollection()
            ->addFieldToFilter("quote_id", $quoteId)
            ->getFirstItem()->getData();
        if(!empty($contextData)){
            return json_decode($contextData["request_body"], true);
        }
        return [];
    }

    /**
     * @param $quoteId
     * @return bool
     */
    private function checkPaymentCapture($quoteId){
        $paymentLink = $this->_razorpayPaymentLinkCollection->addFieldToFilter("quote_id", $quoteId)
            ->getFirstItem();
        if(!empty($paymentLink->getData()) && $paymentLink->getStatus()==0){
            return true;
        }
        return false;
    }
}