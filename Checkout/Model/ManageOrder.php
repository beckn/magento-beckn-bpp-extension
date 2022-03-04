<?php

namespace Beckn\Checkout\Model;

use Beckn\Core\Helper\Data as Helper;
use Beckn\Core\Model\ManageCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Beckn\Checkout\Model\Config\FilterOption\PaymentStatus;

/**
 * Class ManageOrder
 * @author Indglobal
 * @package Beckn\Checkout\Model
 */
class ManageOrder
{

    const ORDER_STATIC_STATUS = [
        "0" => "Order being packed",
        "10" => "Order packed and ready to ship",
        "20" => "Order picked up",
        "30" => "Order delivered",
    ];

    /**
     * @var Helper
     */
    public $_helper;

    /**
     * @var ManageCart
     */
    protected $_manageCart;

    /**
     * @var OrderInterface
     */
    protected $_order;

    /**
     * @var ManageCheckout
     */
    protected $_manageCheckout;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $_orderManagement;

    /**
     * @var Razorpay
     */
    protected $_razorpay;

    /**
     * ManageOrder constructor.
     * @param Helper $helper
     * @param ManageCart $manageCart
     * @param OrderInterface $order
     * @param ManageCheckout $manageCheckout
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param Razorpay $razorpay
     */
    public function __construct(
        Helper $helper,
        \Beckn\Core\Model\ManageCart $manageCart,
        OrderInterface $order,
        ManageCheckout $manageCheckout,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        Razorpay $razorpay
    )
    {
        $this->_helper = $helper;
        $this->_manageCart = $manageCart;
        $this->_order = $order;
        $this->_manageCheckout = $manageCheckout;
        $this->_orderManagement = $orderManagement;
        $this->_razorpay = $razorpay;
    }

    /**
     * @param $incrementId
     * @return Order
     */
    public function loadByIncrementId($incrementId)
    {
        return $this->_order->loadByIncrementId($incrementId);
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function orderCancelByIncrementId($orderId)
    {
        return $this->_orderManagement->cancel($orderId);
    }

    /**
     * @param Order $order
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareOrderResponse(Order $order)
    {
        $finalItems = $this->getAllItems($order);
        $availableStoreId = $this->_manageCart->getOrderProductStoreId($order);
        $providerDetails = $this->_helper->getProvidersDetails([], $availableStoreId);
        $providerLocation = $this->_helper->getProvidersLocation($providerDetails);
        $billingAddressData = $order->getBillingAddress();
        $shippingAddressData = $order->getShippingAddress();
        $shippingAddress = $billingAddress = [];
        if (!empty($billingAddressData)) {
            $billingAddress = json_decode($billingAddressData->getData("beckn_customer_address"));
        }
        if (!empty($shippingAddressData)) {
            $shippingAddress = json_decode($shippingAddressData->getData("beckn_customer_address"));
        }
        $fulfillmentAddress = [];
        $fulfillmentAddress["end"] = $shippingAddress;
        $status = $order->getPaymentStatus();
        if ($status == PaymentStatus::PAYMENT_PAID) {
            $status = PaymentStatus::PAYMENT_PAID_LABEL;
        } else {
            $status = PaymentStatus::PAYMENT_NOT_PAID_LABEL;
        }
        $method = $order->getPayment()->getMethod();
        $params = [];
        if($method==Helper::RAZORPAY){
            $transactionStatus = $this->_razorpay->getRazorpayTransactionStatus($order->getQuoteId());
            $params["transaction_status"] = $transactionStatus;
        }
        $fulfillment = $this->_manageCheckout->getFulfillmentAddress($fulfillmentAddress, $providerDetails);

        $fulfillment["state"] = [
            "descriptor" => [
                "name" => $this->getFulfillmentStatus($order->getData("fulfillment_status")),
                "code" => ($order->getData("fulfillment_status")=="") ? Helper::PACKING_ORDER : $order->getData("fulfillment_status"),
            ]
        ];
        $fulfillment["agent"] = [
            "name" => $order->getData("agent_name"),
            "phone" => $order->getData("agent_phone"),
        ];
        return [
            "id" => $order->getIncrementId(),
            //"state" => $order->getStatusLabel(),
            "state" => $this->_helper->getOrderStatusByCode($order->getState()),
            "provider" => $providerDetails,
            "provider_location" => [
                "id" => $providerLocation["id"]
            ],
            "items" => $finalItems,
            "billing" => $billingAddress,
            "fulfillment" => $fulfillment,
            "quote" => $this->_manageCart->getOrderTotalSegment($order),
            "payment" => $this->_manageCheckout->getPaymentData($status, $order->getGrandTotal(), $order->getOrderCurrencyCode(), $params)
        ];
    }

    /**
     * @param $fulfillmentStatus
     * @return mixed|string
     */
    public function getFulfillmentStatus($fulfillmentStatus){
        return $this->_helper->getFulfillmentStatusByCode($fulfillmentStatus);
    }

    /**
     * @param $orderDate
     * @return string
     */
    public function getOrderStaticStatus($orderDate){
        $currentTime = strtotime(date("y-m-d H:i:s"));
        $orderDateTime = strtotime($orderDate);
        $differenceInSeconds = $currentTime-$orderDateTime;
        $this->_helper->logData("Time difference => ".$differenceInSeconds);
        if($differenceInSeconds>=0 && $differenceInSeconds<=20){
            return self::ORDER_STATIC_STATUS[0];
        }
        elseif ($differenceInSeconds>=20 && $differenceInSeconds<=40){
            return self::ORDER_STATIC_STATUS[10];
        }
        elseif ($differenceInSeconds>=40 && $differenceInSeconds<=60){
            return self::ORDER_STATIC_STATUS[20];
        }
        elseif ($differenceInSeconds>=60 && $differenceInSeconds<=80){
            return self::ORDER_STATIC_STATUS[30];
        }
        else{
            return self::ORDER_STATIC_STATUS[30];
        }
    }

    /**
     * @param Order $order
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAllItems(Order $order)
    {
        try {
            $allVisibleItems = $order->getAllVisibleItems();
            $finalItems = [];
            /**
             * @var \Magento\Sales\Model\Order\Item $eachItem
             */
            foreach ($allVisibleItems as $eachItem) {
                $finalItems[] = [
                    "id" => $eachItem->getId(),
                    "price" => [
                        "currency" => $order->getOrderCurrencyCode(),
                        "value" => $this->_helper->formatPrice($eachItem->getPrice())
                    ],
                    "quantity" => [
                        "selected" => [
                            "count" => $eachItem->getQtyOrdered()
                        ]
                    ],
                    "descriptor" => [
                        "code" => $eachItem->getSku(),
                        "name" => $eachItem->getName()
                    ]
                ];
            }
            return $finalItems;
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(__($ex->getMessage()));
        }
    }
}