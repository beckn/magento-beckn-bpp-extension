<?php

namespace Beckn\Core\Observer\Adminhtml;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Beckn\Core\Model\ProductFlagReferenceFactory;
use Magento\Framework\HTTP\Client\Curl;
use Beckn\Core\Helper\Data as Helper;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class ProductBefore
 * @package Beckn\Core\Observer\Adminhtml
 */
class ProductBefore implements ObserverInterface
{
    const API_URL = 'https://dev.studio.dhiway.com/api/v1/cord/check-item-delegation';
    /***
     * @var ProductFlagReferenceFactory
     */
    protected $_productFlagReferenceFactory;

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * ProductBefore constructor.
     * @param ProductFlagReferenceFactory $productFlagReferenceFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Helper $helper
     * @param Curl $curl
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        ProductFlagReferenceFactory $productFlagReferenceFactory,
        \Psr\Log\LoggerInterface $logger,
        Helper $helper,
        Curl $curl,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_productFlagReferenceFactory = $productFlagReferenceFactory;
        $this->_curl = $curl;
        $this->_logger = $logger;
        $this->_helper = $helper;
        $this->_messageManager = $messageManager;
        $this->_resultRedirectFactory = $resultRedirectFactory;
        $this->_request = $request;
    }

    /**
     * @param Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $allParams = $this->_request->getParams();
        $product = $observer->getEvent()->getProduct();
        /**
         * @var \Beckn\Core\Model\ProductFlagReference $productFlagReferenceFactory
         */
        $productFlagReferenceFactory = $this->_productFlagReferenceFactory->create();
        if (!$productFlagReferenceFactory->productLoadBySku($product->getSku())) {
            $postBody = [];
            $this->_curl->addHeader('content-type', 'application/json');
            $productData = $this->getProductData($product);
            $postBody['identifier'] = $this->_helper->getConfigData(Helper::XML_PATH_SUBSCRIBER_ID);
            $postBody['product'] = $productData;
            $postBody['selling_price'] = $product->getPrice();
            $postBody['quantity'] = $allParams['product']["quantity_and_stock_status"]["qty"] ?? $allParams["product"]["stock_data"]["qty"];
            $postBody['seller_name'] = $this->_helper->getConfigData(Helper::XML_PATH_BUSINESS_NAME);
            $_postBody = json_encode($postBody);
            $this->_logger->info('API url => ' . Helper::CORD_BASE_URL."check-item-delegation");
            $this->_logger->info('post body => ' . $_postBody);
            $this->_curl->post(Helper::CORD_BASE_URL."check-item-delegation", $_postBody);
            $response = $this->_curl->getBody();
            $this->_logger->info('response => ' . $response);
            if ($this->_curl->getStatus() != 200) {
                throw new \Magento\Framework\Exception\LocalizedException(__("Unauthorized catalog update. Kindly contact manufacturer for further details."));
            }
        }
        return $this;
    }

    /**
     * @param $product
     * @return array
     */
    public function getProductData($product)
    {
        return [
            "name" => $product->getName(),
            "sku" => $product->getSku(),
        ];
    }
}
