<?php

namespace Beckn\Razorpay\Model;

use Razorpay\Api\Api;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Payment\Model\InfoInterface;
use Razorpay\Magento\Model\Config;
use Magento\Catalog\Model\Session;


/**
 * Class PaymentMethod
 * @author Indglobal
 * @package Beckn\Razorpay\Model
 */
class PaymentMethod extends \Razorpay\Magento\Model\PaymentMethod
{
    const CHANNEL_NAME = 'Magento';
    const METHOD_CODE = 'razorpay';
    const CONFIG_MASKED_FIELDS = 'masked_fields';
    const CURRENCY = 'INR';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = false;        //Disable module for Magento Admin Order

    /**
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var array|null
     */
    protected $requestMaskedFields = null;

    /**
     * @var \Razorpay\Magento\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var TransactionCollectionFactory
     */
    protected $salesTransactionCollectionFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetaData;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * PaymentMethod constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param Config $config
     * @param \Magento\Framework\App\RequestInterface $request
     * @param TransactionCollectionFactory $salesTransactionCollectionFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetaData
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Razorpay\Magento\Controller\Payment\Order $order
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Razorpay\Magento\Model\Config $config,
        \Magento\Framework\App\RequestInterface $request,
        TransactionCollectionFactory $salesTransactionCollectionFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Razorpay\Magento\Controller\Payment\Order $order,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $config, $request, $salesTransactionCollectionFactory, $productMetaData, $regionFactory, $orderRepository, $order, $resource, $resourceCollection, $data);
    }

    /**
     * Authorizes specified amount
     *
     * @param InfoInterface $payment
     * @param string $amount
     * @return \Razorpay\Magento\Model\PaymentMethod
     * @throws LocalizedException
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        try {
            /** @var \Magento\Sales\Model\Order\Payment $payment */
            $order = $payment->getOrder();
            $orderId = $order->getIncrementId();

            $request = $this->getPostData();

            $isWebhookCall = false;

            //validate RzpOrderamount with quote/order amount before signature
            $orderAmount = (int)(number_format($order->getGrandTotal() * 100, 0, ".", ""));

            if ((empty($request) === true) and (isset($_POST['razorpay_signature']) === true)) {
                //set request data based on redirect flow
                $request['paymentMethod']['additional_data'] = [
                    'rzp_payment_id' => $_POST['razorpay_payment_id'],
                    'rzp_order_id' => $_POST['razorpay_order_id'],
                    'rzp_signature' => $_POST['razorpay_signature']
                ];
            }

            if (empty($request['payload']['payment']['entity']['id']) === false) {
                $payment_id = $request['payload']['payment']['entity']['id'];
                $rzp_order_id = $request['payload']['order']['entity']['id'];

                $isWebhookCall = true;
                //validate that request is from webhook only
                $this->validateWebhookSignature($request);
            } else {
                //check for GraphQL
                if (empty($request['query']) === false) {

                    //update orderLink
                    $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                    $orderLinkCollection = $_objectManager->get('Razorpay\Magento\Model\OrderLink')
                        ->getCollection()
                        ->addFilter('quote_id', $order->getQuoteId())
                        ->getFirstItem();

                    $orderLink = $orderLinkCollection->getData();

                    if (empty($orderLink['entity_id']) === false) {
                        $payment_id = $orderLink['rzp_payment_id'];

                        $rzp_order_id = $orderLink['rzp_order_id'];

                        $rzp_signature = $orderLink['rzp_signature'];

                        $rzp_order_amount_actual = (int)$orderLink['rzp_order_amount'];

                        if ((empty($payment_id) === true) and
                            (emprty($rzp_order_id) === true) and
                            (emprty($rzp_signature) === true)) {
                            throw new LocalizedException(__("Razorpay Payment details missing."));
                        }

                        if ($orderAmount !== $rzp_order_amount_actual) {
                            $rzpOrderAmount = $order->getOrderCurrency()->formatTxt(number_format($rzp_order_amount_actual / 100, 2, ".", ""));

                            throw new LocalizedException(__("Cart order amount = %1 doesn't match with amount paid = %2", $order->getOrderCurrency()->formatTxt($order->getGrandTotal()), $rzpOrderAmount));
                        }

                        //validate payment signature first
                        $this->validateSignature([
                            'razorpay_payment_id' => $payment_id,
                            'razorpay_order_id' => $rzp_order_id,
                            'razorpay_signature' => $rzp_signature
                        ]);

                        try {
                            //fetch the payment from API and validate the amount
                            $payment_data = $this->rzp->payment->fetch($payment_id);
                        } catch (\Razorpay\Api\Errors\Error $e) {
                            $this->_logger->critical($e);
                            throw new LocalizedException(__('Razorpay Error: %1.', $e->getMessage()));
                        }

                        if ($payment_data->order_id === $rzp_order_id) {
                            try {
                                //fetch order from API
                                $rzp_order_data = $this->rzp->order->fetch($rzp_order_id);
                            } catch (\Razorpay\Api\Errors\Error $e) {
                                $this->_logger->critical($e);
                                throw new LocalizedException(__('Razorpay Error: %1.', $e->getMessage()));
                            }

                            //verify order receipt
                            if ($rzp_order_data->receipt !== $order->getQuoteId()) {
                                throw new LocalizedException(__("Not a valid Razorpay Payment"));
                            }

                            //verify currency
                            if ($payment_data->currency !== $order->getOrderCurrencyCode()) {
                                throw new LocalizedException(__("Order Currency:(%1) not matched with payment currency:(%2)", $order->getOrderCurrencyCode(), $payment_data->currency));
                            }
                        } else {
                            throw new LocalizedException(__("Not a valid Razorpay Payments."));
                        }

                    } else {
                        throw new LocalizedException(__("Razorpay Payment details missing."));
                    }
                } else {
                    // Order processing through front-end

                    $payment_id = $request['paymentMethod']['additional_data']['rzp_payment_id'];

                    $rzp_order_id = $this->order->getOrderId();

                    if (empty($_GET)) {
                        if ($orderAmount !== $this->order->getRazorpayOrderAmount()) {
                            $rzpOrderAmount = $order->getOrderCurrency()->formatTxt(number_format($this->order->getRazorpayOrderAmount() / 100, 2, ".", ""));

                            throw new LocalizedException(__("Cart order amount = %1 doesn't match with amount paid = %2", $order->getOrderCurrency()->formatTxt($order->getGrandTotal()), $rzpOrderAmount));
                        }
                        $this->validateSignature([
                            'razorpay_payment_id' => $payment_id,
                            'razorpay_order_id' => $rzp_order_id,
                            'razorpay_signature' => $request['paymentMethod']['additional_data']['rzp_signature']
                        ]);
                    }
                }
            }

            $payment->setStatus(self::STATUS_APPROVED)
                ->setAmountPaid($amount)
                ->setLastTransId($payment_id)
                ->setTransactionId($payment_id)
                ->setIsTransactionClosed(true)
                ->setShouldCloseParentTransaction(true);

            //update the Razorpay payment with corresponding created order ID of this quote ID
            $this->updatePaymentNote($payment_id, $order, $rzp_order_id, $isWebhookCall);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            throw new LocalizedException(__('Razorpay Error: %1.', $e->getMessage()));
        }
        return $this;
    }

    /**
     * @return array|false|mixed|string
     */
    protected function getPostData()
    {
        $request = file_get_contents('php://input');
        if (empty($request)) {
            $request = [
                "paymentMethod" => [
                    "additional_data" => [
                        "order_id" => $_GET["razorpay_payment_link_reference_id"],
                        "rzp_payment_id" => $_GET["razorpay_payment_id"],
                        "rzp_signature" => $_GET["razorpay_signature"],
                    ],
                ],
            ];
        } else {
            $request = json_decode($request, true);
        }
        return $request;
    }
}