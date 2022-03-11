<?php

namespace Beckn\Search\Model\Repository\Search;

use Beckn\Core\Helper\Data as Helper;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Search\Search as MagentoCoreSearch;
use Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory as SearchCriteriaInterfaceFactory;
use Beckn\Core\Model\ProductFlagReferenceFactory;

/**
 * Class SearchRepository
 * @author Indglobal
 * @package Beckn\Search\Model\Repository\Search
 */
class SearchRepository implements \Beckn\Search\Api\SearchRepositoryInterface
{

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var MagentoCoreSearch
     */
    protected $_magentoCoreSearch;
    /**
     * @var SearchCriteriaInterfaceFactory
     */
    protected $_searchCriteriaInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * @var ProductFlagReferenceFactory
     */
    protected $_productFlagReferenceFactory;

    /**
     * SearchRepository constructor.
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param CollectionFactory $productCollectionFactory
     * @param CategoryFactory $categoryFactory
     * @param MagentoCoreSearch $magentoCoreSearch
     * @param SearchCriteriaInterfaceFactory $searchCriteriaInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ProductFlagReferenceFactory $productFlagReferenceFactory
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        CollectionFactory $productCollectionFactory,
        CategoryFactory $categoryFactory,
        MagentoCoreSearch $magentoCoreSearch,
        SearchCriteriaInterfaceFactory $searchCriteriaInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        ProductFlagReferenceFactory $productFlagReferenceFactory
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_magentoCoreSearch = $magentoCoreSearch;
        $this->_searchCriteriaInterfaceFactory = $searchCriteriaInterfaceFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_productFlagReferenceFactory = $productFlagReferenceFactory;
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \SodiumException
     */
    public function getSearch($context, $message)
    {
        $authStatus = $this->_helper->validateAuth($context, $message);
        if (!$authStatus) {
            echo $this->_helper->unauthorizedResponse();
            exit();
        }
        $validateMessage = $this->_helper->validateApiRequest($context, $message);
        if (is_callable('fastcgi_finish_request')) {
            $acknowledge = $this->_helper->getAcknowledge($context);
            if (!empty($validateMessage['message'])) {
                $errorAcknowledge = $this->_helper->acknowledgeError($validateMessage['code'], $validateMessage['message']);
                $acknowledge["message"]["ack"]["status"] = Helper::NACK;
                $acknowledge["error"] = $errorAcknowledge;
            }
            $this->_helper->apiResponseEvent($context, $acknowledge);
            echo json_encode($acknowledge);
            session_write_close();
            fastcgi_finish_request();
        }
        ignore_user_abort(true);
        ob_start();

        //Add code here
        if (empty($validateMessage['message'])) {
            $this->processSearch($context, $message);
        }
        echo $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        header($serverProtocol . ' 200 OK');
        // Disable compression (in case content length is compressed).
        header('Content-Encoding: none');
        header('Content-Length: ' . ob_get_length());
        // Close the connection.
        header('Connection: close');
        ob_end_flush();
        ob_flush();
        flush();
    }

    /**
     * @param $context
     * @param $message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function processSearch($context, $message)
    {
        $apiUrl = $this->_helper->getBapUri(Helper::ON_SEARCH, $context);
        $response = [];
        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
         */
        $filterStoreId = $this->_helper->getAllStoreIds($message);
        $this->_logger->info("all store ids => ".json_encode($filterStoreId));
        if (!empty($filterStoreId)) {
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('product_store_bpp', ["in", $filterStoreId]);
            $collection->addAttributeToFilter('type_id', "simple");
            $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
            if (isset($message["intent"]["item"]["descriptor"]["name"]) && !empty($message["intent"]["item"]["descriptor"]["name"])) {
                $searchQuery = $message["intent"]["item"]["descriptor"]["name"];
                //$productIds = $this->getDefaultMagentoSearch($searchQuery);
                //$collection->addAttributeToFilter('entity_id', ["IN", $productIds]);
                $collection->addAttributeToFilter('name', ['like' => '%'.$searchQuery.'%']);
            }
            $collection = $this->_helper->addCondition($message, $collection);
        }

        $allItems = [];
        $priceData = [];
        $availableStore = [];
        if ($collection->getSize()) {
            $this->_logger->info("I am herer");
            foreach ($collection as $_collection) {
                $prepareProduct = $this->prepareProduct($_collection, $context, $message);
                $allItems[] = $prepareProduct["item"];
                $priceData[$prepareProduct["id"]] = $prepareProduct["price"];
                $availableStore[] = $prepareProduct["store_id"];
            }
        }
        $this->_logger->info("all items 1 => ".json_encode($allItems));
        if (isset($message["intent"]["item"]["price"]["minimum_value"])) {
            $minValue = $message["intent"]["item"]["price"]["minimum_value"];
            $maxValue = $message["intent"]["item"]["price"]["maximum_value"];
            $allItems = $this->_helper->addPriceFilter($allItems, $priceData, $minValue, $maxValue);
        }
        $this->_logger->info("all items 2 => ".json_encode($allItems));
        if (!empty($allItems)) {
            $provider = $this->_helper->getProvidersDetails($allItems, array_unique($availableStore));
            $response["context"] = $this->_helper->getContext($context);
            $response["message"]["catalog"]["bpp/descriptor"] = $this->_helper->getDescriptorDetails();
            $response["message"]["catalog"]["bpp/providers"][0] = $provider;
            $this->_helper->sendResponse($apiUrl, $response);
        } else {
            $this->_logger->info("No match found hence not firing on_search.");
        }
    }

    /**
     * @param $searchQuery
     * @return array
     */
    protected function getDefaultMagentoSearch($searchQuery)
    {
        $searchRequest = [
            "requestName" => "quick_search_container",
            "filter_groups" => [
                [
                    "filters" => [
                        [
                            "field" => "search_term",
                            "value" => $searchQuery
                        ]
                    ]
                ]
            ]
        ];
        $object = $this->_searchCriteriaInterfaceFactory->create();
        $interface = \Magento\Framework\Api\Search\SearchCriteriaInterface::class;
        $this->_dataObjectHelper->populateWithArray(
            $object, $searchRequest, $interface
        );
        $searchCollection = $this->_magentoCoreSearch->search($object);
        $productIds = [];
        if ($searchCollection->getTotalCount()) {
            foreach ($searchCollection->getItems() as $_search) {
                $productIds[] = $_search->getId();
            }
        }
        return $productIds;
    }

    /**
     * @param Product $product
     * @param array $context
     * @param array $message
     * @return array
     * @throws NoSuchEntityException
     */
    private function prepareProduct(Product $product, array $context, array $message)
    {
        $this->_logger->info("Product id => ".$product->getId());
        $productData = [
            "id" => $product->getSku(),
            "descriptor" => [
                "name" => $product->getName(),
                "images" => $this->_helper->getProductMediaGallery($product->getSku()),
            ],
            "price" => [
                "currency" => $this->_helper->getCurrentCurrencyCode(),
                "value" => $this->_helper->formatPrice($product->getFinalPrice())
            ],
            "matched" => true,
        ];
        if ($product->getShortDescription() != "") {
            $productData["descriptor"]["short_desc"] = $product->getShortDescription();
        }
        if ($product->getDescription() != "") {
            $productData["descriptor"]["long_desc"] = $product->getDescription();
        }
        if ($product->getItemCodeBpp() != "") {
            $productData["descriptor"]["code"] = $product->getItemCodeBpp();
        }
        if ($product->getData("time_range_start_date_bpp") != "") {
            $productData["time"] = [
                "range" => [
                    "start" => $this->_helper->formatDate($product->getData("time_range_start_date_bpp"), false),
                    "end" => $this->_helper->formatDate($product->getData("time_range_end_date_bpp"), false),
                ]
            ];
        }

        $productReferance = $this->getAdditionalTags($product->getId());
        if(!empty($productReferance) && !empty($productReferance["product_list_id"]) && !empty($productReferance["product_list_id"])){
            $productData["tags"]["product_list_id"] = $productReferance["product_list_id"];
            $productData["tags"]["blockhash"] = $productReferance["blockhash"];
        }

        if ($product->getPricePolicyBpp() != "") {
            $price = $this->_helper->getPriceFromPolicy($product->getPricePolicyBpp());
            if ($price != "") {
                $productData["price"]["value"] = $this->_helper->formatPrice($price);
            }
        }
        $productStoreId = $product->getProductStoreBpp();
        $productData["location_id"] = $productStoreId;
        if ($productStoreId != "") {
            $locationId = $this->_helper->getProductLocationId($productStoreId);
            if ($locationId != "") {
                $productData["location_id"] = $locationId;
            }
        }

        return [
            "item" => $productData,
            "id" => $productData["id"],
            "price" => $productData["price"]["value"],
            "store_id" => $productStoreId
        ];
    }

    /**
     * @param $productId
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAdditionalTags($productId){
        /**
         * @var \Beckn\Core\Model\ProductFlagReference $productFlag
         */
        $productFlag = $this->_productFlagReferenceFactory->create();
        $productReferance = $productFlag->productLoadById($productId, true);
        if(!empty($productReferance)){
            return [
                "product_list_id" => $productReferance["product_list_id"],
                "blockhash" => $productReferance["blockhash"]
            ];
        }
        return false;
    }
}