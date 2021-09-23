<?php

namespace Beckn\Select\Observer;

use \Magento\Framework\Event\ObserverInterface;
use Beckn\Core\Helper\Data as Helper;

/**
 * Class AddPricePolicy
 * @author Indglobal
 * @package Beckn\Select\Observer
 */
class AddPricePolicy implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $_helper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * AddPricePolicy constructor.
     * @param Helper $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getEvent()->getData('quote_item');
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        $product = $this->_helper->getProductBySku($item->getSku());
        if($product->getPricePolicyBpp()!=""){
            $price = $this->_helper->getPriceFromPolicy($product->getPricePolicyBpp());
            $this->_logger->info("Price herer");
            $this->_logger->info($price);
            $item->setCustomPrice((float)$price);
            $item->setOriginalCustomPrice((float)$price);
            $item->getProduct()->setIsSuperMode(true);
        }
    }
}