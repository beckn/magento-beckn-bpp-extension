<?php

namespace Beckn\Core\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Beckn\Core\Model\ProductFlagReferenceFactory;
use Magento\Framework\HTTP\Client\Curl;
use Beckn\Core\Helper\Data as Helper;
use Magento\Catalog\Model\Product\Action as ProductAction;

/**
 * Class Product
 * @package Beckn\Core\Observer\Adminhtml
 */
class Product implements ObserverInterface
{
    const API_URL = 'https://dev.studio.dhiway.com/api/v1/cord/register-product';

    /**
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
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var ProductAction
     */
    protected $_productAction;

    /**
     * Product constructor.
     * @param ProductFlagReferenceFactory $productFlagReferenceFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Helper $helper
     * @param Curl $curl
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param \Magento\Framework\App\Request\Http $request
     * @param ProductAction $action
     */
    public function __construct(
        ProductFlagReferenceFactory $productFlagReferenceFactory,
        \Psr\Log\LoggerInterface $logger,
        Helper $helper,
        Curl $curl,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\Framework\App\Request\Http $request,
        ProductAction $action
    )
    {
        $this->_productFlagReferenceFactory = $productFlagReferenceFactory;
        $this->_curl = $curl;
        $this->_logger = $logger;
        $this->_helper = $helper;
        $this->_stockState = $stockState;
        $this->_request = $request;
        $this->_productAction = $action;
    }

    /**
     * @param Observer $observer
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
            //$postBody['identifier'] = $this->_helper->getConfigData(Helper::XML_PATH_SUBSCRIBER_ID);
            $postBody['identifier'] = "supplychainbap.becknprotocol.io";
            $postBody['product'] = $productData;
            $postBody['selling_price'] = $product->getPrice();
            $postBody['quantity'] = $allParams['product']["quantity_and_stock_status"]["qty"] ?? $allParams["product"]["stock_data"]["qty"];
            $postBody['seller_name'] = $this->_helper->getConfigData(Helper::XML_PATH_BUSINESS_NAME);
            $_postBody = json_encode($postBody);
            $this->_logger->info('post body => ' . $_postBody);
            $this->_curl->post(Helper::CORD_BASE_URL . "item_add", $_postBody);
            $response = $this->_curl->getBody();
            $this->_logger->info('response => ' . $response);
            if ($this->_curl->getStatus() == 200) {
                $responseData = json_decode($response, true);
                $product->setProductListIdBpp($responseData['id']);
                $product->setBlockHashBpp($responseData['blockHash']);
                $data = [
                    'product_id' => $product->getId(),
                    'product_sku' => $product->getSku(),
                    'flag' => 1,
                    'product_list_id' => $responseData['id'],
                    'blockhash' => $responseData['blockHash']
                ];
                $productFlagReferenceFactory->setData($data)->save();
            }
            else{
                throw new \Magento\Framework\Exception\LocalizedException(__("Unauthorized catalog update. Kindly contact manufacturer for further details."));
            }
        }
        return $product;
    }

    /**
     * @param $product
     * @return array
     */
    public function getProductData($product)
    {
        return [
            "sku" => $product->getSku(),
            "name" => $product->getName(),
        ];
    }
}
