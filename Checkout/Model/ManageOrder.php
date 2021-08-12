<?php

namespace Beckn\Checkout\Model;

use Beckn\Bpp\Helper\Data as Helper;
use Beckn\Bpp\Model\ManageCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Beckn\Checkout\Model\Config\FilterOption\PaymentStatus;
use Beckn\Checkout\Block\Sales\Order\Totals;

/**
 * Class ManageOrder
 * @author Indglobal
 * @package Beckn\Checkout\Model
 */
class ManageOrder
{
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
     * @var Totals
     */
    protected $_orderTotal;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $_orderManagement;

    /**
     * ManageOrder constructor.
     * @param Helper $helper
     * @param ManageCart $manageCart
     * @param OrderInterface $order
     * @param ManageCheckout $manageCheckout
     * @param Totals $orderTotal
     */
    public function __construct(
        Helper $helper,
        \Beckn\Bpp\Model\ManageCart $manageCart,
        OrderInterface $order,
        ManageCheckout $manageCheckout,
        Totals $orderTotal,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement
    )
    {
        $this->_helper = $helper;
        $this->_manageCart = $manageCart;
        $this->_order = $order;
        $this->_manageCheckout = $manageCheckout;
        $this->_orderTotal = $orderTotal;
        $this->_orderManagement = $orderManagement;
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
        $providerDetails = $this->_helper->getProvidersDetails();
        $providerLocation = $this->_helper->getProvidersLocation();
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
        return [
            "id" => $order->getIncrementId(),
            "state" => $order->getStatusLabel(),
            "provider" => $providerDetails,
            "provider_location" => [
                "id" => $providerLocation["id"]
            ],
            "items" => $finalItems,
            "billing" => $billingAddress,
            "fulfillment" => $this->_manageCheckout->getFulfillmentAddress($fulfillmentAddress),
            "quote" => $this->getTotalSegment($order),
            "payment" => $this->_manageCheckout->getPaymentData($status)
        ];
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
                    "id" => $eachItem->getSku(),
                    "price" => [
                        "currency" => $order->getOrderCurrencyCode(),
                        "value" => $eachItem->getPrice()
                    ],
                    "quantity" => [
                        "selected" => [
                            "count" => $eachItem->getQtyOrdered()
                        ]
                    ]
                ];
            }
            return $finalItems;
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(__($ex->getMessage()));
        }
    }

    /**
     * @param Order $order
     * @return array
     */
    public function getTotalSegment(Order $order)
    {
        $totalsBreakup = [];
        $priceBreakUp = [];
        $allVisibleItems = $order->getAllVisibleItems();
        /**
         * @var \Magento\Sales\Model\Order\Item $eachItem
         */
        foreach ($allVisibleItems as $eachItem) {
            $totalsBreakup[] = [
                "type" => "item",
                "title" => $eachItem->getName(),
                "ref_id" => $eachItem->getSku(),
                "price" => [
                    "currency" => $order->getOrderCurrencyCode(),
                    "value" => $eachItem->getRowTotal(),
                ]
            ];
        }
        $totalsBlock = $this->_orderTotal->setOrder($order);
        $totalsBlock->_initTotals();
        foreach ($totalsBlock->getTotals() as $total) {
            if (!in_array($total->getCode(), Helper::EXCLUDE_TOTALS)) {
                $title = $total->getLabel();
                if ($total->getCode() == "shipping") {
                    $title = __(Helper::SHIPPING_LABEL);
                }
                $totalsBreakup[] = [
                    "type" => $total->getCode(),
                    "title" => $title,
                    "ref_id" => "",
                    "price" => [
                        "currency" => $order->getOrderCurrencyCode(),
                        "value" => $total->getValue(),
                    ]
                ];
            }
        }
        $priceBreakUp["price"] = [
            "currency" => $order->getOrderCurrencyCode(),
            "value" => $order->getGrandTotal()
        ];
        $priceBreakUp["breakup"] = $totalsBreakup;
        return $priceBreakUp;
    }
}