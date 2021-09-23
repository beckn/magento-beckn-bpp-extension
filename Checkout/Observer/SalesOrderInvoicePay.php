<?php

namespace Beckn\Checkout\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Beckn\Checkout\Model\Config\FilterOption\PaymentStatus;

/**
 * Class SalesOrderInvoicePay
 * @author Indglobal
 * @package Beckn\Checkout\Observer
 */
class SalesOrderInvoicePay implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $order->setPaymentStatus(PaymentStatus::PAYMENT_PAID);
        $order->save();
    }
}