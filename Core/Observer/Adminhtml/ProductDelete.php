<?php

namespace Beckn\Core\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Beckn\Core\Model\ProductFlagReferenceFactory;
use Beckn\Core\Helper\Data as Helper;

/**
 * Class ProductDelete
 * @package Beckn\Core\Observer\Adminhtml
 */
class ProductDelete implements ObserverInterface
{
    /**
     * @var ProductFlagReferenceFactory
     */
    protected $_productFlagReferenceFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * ProductDelete constructor.
     * @param ProductFlagReferenceFactory $productFlagReferenceFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Helper $helper
     */
    public function __construct(
        ProductFlagReferenceFactory $productFlagReferenceFactory,
        \Psr\Log\LoggerInterface $logger,
        Helper $helper
    )
    {
        $this->_productFlagReferenceFactory = $productFlagReferenceFactory;
        $this->_logger = $logger;
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $sku = $product->getSku();
        /**
         * @var \Beckn\Core\Model\ProductFlagReference $productFlag
         */
        $productFlag = $this->_productFlagReferenceFactory->create();
        $data = $productFlag->productLoadBySku($sku, true);
        if(!empty($data["entity_id"])){
            $this->_productFlagReferenceFactory->create()->load($data["entity_id"])
            ->delete();
        }
        return $this;
    }
}