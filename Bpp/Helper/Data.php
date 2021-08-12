<?php

namespace Beckn\Bpp\Helper;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Directory\Model\Country;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Math\Random;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Beckn\Bpp\Model\BecknLookupFactory;
use Beckn\Bpp\Model\DigitalSignature as DigitalSignature;

/**
 * Class Data
 * @author Indglobal
 * @package Beckn\Bpp\Helper
 */
class Data extends AbstractHelper
{
    const BPP_PROVIDER_IMAGE_DIR = "bpp_provider_image/";
    const BUSINESS_LOGO_DIR = "beckn/business/logo/";
    const PROVIDER_LOGO_DIR = "beckn/provider/logo/";
    const DROP_LOCATION_KEY = "drop_location";
    const LOCATION_ID = "provider_address";
    const CATALOG_MEDIA_DIR = 'catalog/product';
    const ON_SEARCH = "on_search";
    const ON_SELECT = "on_select";
    const ON_INIT = "on_init";
    const ON_CONFIRM = "on_confirm";
    const ON_STATUS = "on_status";
    const ON_SUPPORT = "on_support";
    const ON_CANCEL = "on_cancel";
    const ACK = "ACK";
    const NACK = "NACK";
    const ERROR_CODE = [
        "bad_request" => 400,
    ];
    const EXCLUDE_TOTALS = [
        "subtotal",
        "grand_total",
    ];
    const SHIPPING_LABEL = "Fulfilment";
    const CORE_VERSION = "0.9.3";
    const PAYMENT_COD = [
        "type" => "ON-FULFILLMENT",
        "status" => "NOT-PAID",
        "code" => "cashondelivery"
    ];
    const RAZORPAY = "razorpay";
    const RAZORPAY_HTTP_METHOD = "get";
    const RAZORPAY_CALLBACK_URL = "beckncheckout/index/response";
    const STATUS_NOT_PAID = "NOT-PAID";
    const STATUS_PAID = "PAID";

    const REGISTRY_SUBSCRIBE = "/subscribe";
    const REGISTRY_LOOKUP = "/lookup";
    const SUBSCRIBER_STATUS_PATH = 'bpp_config/subscriber/status';
    const SIGN_PUBLIC_KEY_PATH = 'bpp_config/subscriber/signing_public_key';
    const SIGN_PRIVATE_KEY_PATH = 'bpp_config/subscriber/signing_private_key';
    const ENCRYPTION_PUBLIC_KEY_PATH = 'bpp_config/subscriber/encryption_public_key';
    const ENCRYPTION_PRIVATE_KEY_PATH = 'bpp_config/subscriber/encryption_private_key';

    const AUTHORIZATION_KEY = 'Authorization';
    const PROXY_AUTHORIZATION_KEY = 'Proxy-Authorization';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @var ProductAttributeMediaGalleryManagementInterface
     */
    protected $_productAttributeMediaGallery;

    /**
     * @var Country
     */
    protected $_country;

    /**
     * @var TimezoneInterface
     */
    protected $_date;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var RegionCollectionFactory
     */
    protected $_regionCollectionFactory;
    /**
     * @var Random
     */
    protected $_random;

    /**
     * @var WriterInterface
     */
    protected $_configWriter;

    /**
     * @var TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var BecknLookupFactory
     */
    protected $_becknLookupFactory;

    /**
     * @var \Magento\Framework\Webapi\Request
     */
    protected $_request;

    /**
     * @var DigitalSignature
     */
    protected $_digitalSignature;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Curl $curl
     * @param ProductAttributeMediaGalleryManagementInterface $productAttributeMediaGallery
     * @param Country $country
     * @param TimezoneInterface $date
     * @param \Psr\Log\LoggerInterface $logger
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param Random $random
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param BecknLookupFactory $becknLookupFactory
     * @param \Magento\Framework\Webapi\Request $request
     * @param DigitalSignature $digitalSignature
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Curl $curl,
        ProductAttributeMediaGalleryManagementInterface $productAttributeMediaGallery,
        Country $country,
        TimezoneInterface $date,
        \Psr\Log\LoggerInterface $logger,
        RegionCollectionFactory $regionCollectionFactory,
        Random $random,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        BecknLookupFactory $becknLookupFactory,
        \Magento\Framework\Webapi\Request $request,
        DigitalSignature $digitalSignature
    )
    {
        $this->_storeManager = $storeManager;
        $this->_curl = $curl;
        $this->_productAttributeMediaGallery = $productAttributeMediaGallery;
        $this->_country = $country;
        $this->_date = $date;
        $this->_logger = $logger;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        $this->_random = $random;
        $this->_configWriter = $configWriter;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_becknLookupFactory = $becknLookupFactory;
        $this->_request = $request;
        $this->_digitalSignature = $digitalSignature;
        parent::__construct($context);
    }

    /**
     * @param string $path
     * @param string $scope
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($path, $scope = ScopeInterface::SCOPE_STORE, $storeId = 0)
    {
        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * @param $type
     * @param $context
     * @return string
     */
    public function getBapUri($type, $context)
    {
//        $authorization = $this->_request->getHeader(self::AUTHORIZATION_KEY);
//        $proxy_authorization = $this->_request->getHeader(self::PROXY_AUTHORIZATION_KEY);
//        if(!empty($authorization)){
//            if ($context["bap_uri"] == "https://bpp1.beckn.org/beckn/index/index") {
//                return $context["bap_uri"];
//            } else {
//                return $context['bap_uri'] . "/" . $type;
//            }
//        }elseif(!empty($proxy_authorization)){
//            $model = $this->_becknLookupFactory->create();
//            $collection = $model->getCollection()->addFieldToFilter('subscriber_id', $context['bap_id'])->getData();
//            if(!empty($collection)){
//                $this->saveLookup();
//            }
//        }
        if ($context["bap_uri"] == "https://bpp1.beckn.org/beckn/index/index") {
            return $context["bap_uri"];
        } else {
            return $context['bap_uri'] . "/" . $type;
        }
    }

    /**
     * @param $apiUrl
     * @param $postData
     * @return mixed
     */
    public function sendResponse($apiUrl, $postData)
    {
        $postBody = json_encode($postData, JSON_UNESCAPED_SLASHES);
        $authorization = $this->_digitalSignature->createAuthorization($postBody);
        $this->_logger->info("Authorization Header.");
        $this->_logger->info(json_encode($authorization, JSON_UNESCAPED_SLASHES));
        if($authorization["success"]==true){
            $authHeader = $authorization["success"];
            $this->_curl->addHeader('Authorization', $authHeader);
        }
        $this->_curl->addHeader('content-type', 'application/json');
        $this->_curl->post($apiUrl, $postBody);
        $response = $this->_curl->getBody();
        $this->_logger->info("Response Data.");
        $this->_logger->info(json_encode($postData));
        $this->_logger->info("Endpoint => " . $apiUrl);
        $this->_logger->info("Response of Sandbox");
        $this->_logger->info($response);
        return json_decode($response, true);
    }

    /**
     * @return string
     */
    public function getProviderImage()
    {
        try {
            $imagePath = $this->getConfigData('provider_config/provider_details/logo');
            return $this->getMediaUrl() . self::PROVIDER_LOGO_DIR . $imagePath;
        } catch (NoSuchEntityException $e) {

        }
    }

    /**
     * @return string
     */
    public function getDescriptorImage()
    {
        try {
            $imagePath = $this->getConfigData('bpp_config/business/logo');
            return $this->getMediaUrl() . self::BUSINESS_LOGO_DIR . $imagePath;
        } catch (NoSuchEntityException $e) {

        }
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param array $message
     * @param Collection $collection
     * @return Collection|null
     */
    public function addCondition(array $message, Collection $collection)
    {
        $match = true;
        if (isset($message["intent"]["provider"]["id"]) && !empty($message["intent"]["provider"]["id"])) {
            $match = false;
            $providerId = $message["intent"]["provider"]["id"];
            $configProviderId = $this->getConfigData('bpp_config/subscriber/subscriber_id');
            if ($providerId == $configProviderId) {
                $match = true;
            }
        }
        if (!$match) {
            return null;
        }
        if (isset($message["intent"]["item"]["id"]) && !empty($message["intent"]["item"]["id"])) {
            $sku = $message["intent"]["item"]["id"];
            $collection->addAttributeToFilter('sku', $sku);
            return $collection;
        }
        if (isset($message["intent"]["provider"]["descriptor"]["name"]) && !empty($message["intent"]["provider"]["descriptor"]["name"])) {
            $match = false;
            $sellerName = $message["intent"]["provider"]["descriptor"]["name"];
            $configSeller = $this->getConfigData('bpp_config/business/name');
            if ($sellerName == $configSeller) {
                $match = true;
            }
        }
        if (!$match) {
            return null;
        }
        if (isset($message["intent"]["fulfillment"]["type"]) && !empty($message["intent"]["fulfillment"]["type"])) {
            $match = false;
            $type = $message["intent"]["fulfillment"]["type"];
            $fulfillmentType = $this->getConfigData('bpp_config/fulfilment/type');
            $fulfillmentType = array_map('trim', explode(',', $fulfillmentType));
            if (in_array($type, $fulfillmentType)) {
                $match = true;
            }
        }
        if (!$match) {
            return null;
        }
        if (isset($message["intent"]["fulfillment"]["end"]["location"]["gps"]) && !empty($message["intent"]["fulfillment"]["end"]["location"]["gps"])) {
            $match = false;
            $gps_cordinate = $message["intent"]["fulfillment"]["end"]["location"]["gps"];
            if (!empty($gps_cordinate)) {
                $gpsLatLong = explode(',', $gps_cordinate);
                $latTo = $gpsLatLong[0];
                $longTo = $gpsLatLong[1];
                $totalDistance = $this->checkDistance($latTo, $longTo);
                $areaRadius = $this->getConfigData('provider_config/provider_address/radius');
                if ($totalDistance <= $areaRadius) {
                    $match = true;
                }
            }
        }
        if (!$match) {
            return null;
        }
        if (isset($message["intent"]["item"]["descriptor"]["code"]) && !empty($message["intent"]["item"]["descriptor"]["code"])) {
            $itemCode = $message["intent"]["item"]["descriptor"]["code"];
            return $collection->addAttributeToFilter('item_code_bpp', $itemCode);
        }

        if (isset($message["intent"]["item"]["descriptor"]["name"]) && !empty($message["intent"]["item"]["descriptor"]["name"])) {
            $name = $message["intent"]["item"]["descriptor"]["name"];
            $collection->addAttributeToFilter('name', ['like' => "%" . $name . "%"]);
        }
        if (isset($message["intent"]["query_string"]) && !empty($message["intent"]["query_string"])) {
            $name = $message["intent"]["query_string"];
            $collection->addAttributeToFilter('name', ['like' => "%" . $name . "%"]);
        }

        if (isset($message["intent"]["item"]["price"]["minimum_value"])) {
            $minValue = $message["intent"]["item"]["price"]["minimum_value"];
            $maxValue = $message["intent"]["item"]["price"]["maximum_value"];
            $collection->addAttributeToFilter(
                'price',
                [
                    'from' => $minValue,
                    'to' => $maxValue
                ]
            );
        }
        if (isset($message["intent"]["item"]["time"]["range"]) && !empty($message["intent"]["item"]["time"]["range"])) {
            $range = $message["intent"]["item"]["time"]["range"];
            $starRangeDate = $range['start'] ?? "";
            $endRangeDate = $range['end'] ?? "";
            if ($starRangeDate != "" && $endRangeDate != "") {
                $startDate = date('Y-m-d H:i:s', strtotime($starRangeDate));
                $endDate = date('Y-m-d H:i:s', strtotime($endRangeDate));
                $collection->addAttributeToFilter([
                    array(
                        'attribute' => 'time_range_start_date_bpp',
                        'gteq' => $startDate
                    ),
                    array(
                        'attribute' => 'time_range_end_date_bpp',
                        'lteq' => $endDate
                    ),
                ]);
            }
        }

        if ($match) {
            return $collection;
        } else {
            return null;
        }

    }

    /**
     * @param Product $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareProduct(Product $product)
    {
        return [
            "id" => $product->getSku(),
            "descriptor" => [
                "name" => $product->getName(),
                "short_desc" => $product->getShortDescription(),
                "long_desc" => $product->getDescription(),
                "images" => $this->getProductMediaGallery($product->getSku()),
            ],
            "price" => [
                "currency" => $this->getCurrentCurrencyCode(),
                "value" => $product->getFinalPrice()
            ],
            "time" => [
                "range" => [
                    "start" => $this->formatDate($product->getData("time_range_start_date_bpp")),
                    "end" => $this->formatDate($product->getData("time_range_end_date_bpp")),
                ]
            ],
            "matched" => true,
        ];
    }

    /**
     * @param string $date
     * @return false|string
     */
    public function formatDate($date=""){
        if($date==""){
            $formattedDate = date('Y-m-d\TH:i:s\Z', strtotime(date("Y-m-d H:i:s")));
        }
        else{
            $formattedDate = date('Y-m-d\TH:i:s\Z', strtotime($date));
        }
        return $formattedDate;
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrencyCode()
    {
        try {
            return $this->_storeManager->getStore()->getCurrentCurrencyCode();
        } catch (NoSuchEntityException $e) {
        }
    }

    /**
     * @param $sku
     * @return array
     * @throws NoSuchEntityException
     */
    public function getProductMediaGallery($sku)
    {
        $gallery = $this->_productAttributeMediaGallery->getList($sku);
        /**
         * @var ProductAttributeMediaGalleryEntryInterface $_gallery
         */
        $productImage = [];
        foreach ($gallery as $_gallery) {
            $productImage[] = $this->getCatalogMediaUrl() . $_gallery->getFile();
        }
        return $productImage;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCatalogMediaUrl()
    {
        return $this->getMediaUrl() . self::CATALOG_MEDIA_DIR;
    }

    /**
     * @param array $allItems
     * @return array
     */
    public function getProvidersDetails(array $allItems = [])
    {
        $providerDetails = [
            "id" => $this->getConfigData("provider_config/provider_address/provider_id"),
            "descriptor" => [
                "name" => $this->getConfigData("provider_config/provider_address/provider_name"),
                "short_desc" => $this->getConfigData("bpp_config/business/short_desc"),
                "images" => [$this->getProviderImage()],
            ],
            "locations" => [
                $this->getLocations()
            ],
        ];
        if (!empty($allItems)) {
            $providerDetails['items'] = $allItems;
        }
        return $providerDetails;
    }

    /**
     * @return array
     */
    public function getProvidersLocation()
    {
        $location = $this->getLocations();
        return [
            "id" => $this->getConfigData("provider_config/provider_address/provider_id"),
            "descriptor" => [
                "name" => $this->getConfigData("provider_config/provider_details/name"),
                "short_desc" => $this->getConfigData("provider_config/provider_details/short_desc"),
                "images" => [$this->getProviderImage()],
            ],
            "gps" => $location["gps"],
            "address" => $location["address"],
            "station_code" => $location["station_code"],
            "city" => $location["city"],
            "country" => $location["country"],
        ];
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return [
            "id" => self::LOCATION_ID,
            "gps" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/gps_location"),
            "address" => [
                "door" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/door"),
                "name" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/name"),
                "building" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/building"),
                "street" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/street"),
                "locality" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/locality"),
                "state" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/state"),
                "country" => $this->getCountryName($this->getConfigData("provider_config/" . self::LOCATION_ID . "/country")),
                "area_code" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/area_code"),
            ],
            "station_code" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/station_code"),
            "city" => [
                "name" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/city"),
                "code" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/city"),
            ],
            "country" => [
                "name" => $this->getCountryName($this->getConfigData("provider_config/" . self::LOCATION_ID . "/country")),
                "code" => $this->getConfigData("provider_config/" . self::LOCATION_ID . "/country"),
            ],
        ];
    }

    /**
     * @param $countryCode
     * @return string
     */
    public function getCountryName($countryCode)
    {
        $countryName = '';
        if($countryCode!=''){
            $country = $this->_country->loadByCode($countryCode);
            if ($country) {
                //$countryName = $country->getName();
                $countryName = $country->getData('iso3_code');
            }
        }
        return $countryName;
    }

    /**
     * @param $name
     * @return string
     */
    public function getCountryId($name)
    {
        return array_search($name, \Zend_Locale::getTranslationList('territory'));
    }

    /**
     * @return array
     */
    public function getDescriptorDetails()
    {
        return [
            "name" => $this->getConfigData("bpp_config/business/name"),
            "short_desc" => $this->getConfigData("bpp_config/business/short_desc"),
            "images" => [$this->getDescriptorImage()],
        ];
    }

    /**
     * @param array $context
     * @return array
     */
    public function getContext(array $context)
    {
        $context["domain"] = $this->getConfigData("bpp_config/subscriber/industry_domain");
        $context["country"] = $this->getCountryName($this->getConfigData("bpp_config/subscriber/country"));
        $context["city"] = $this->getConfigData("bpp_config/subscriber/city");
        $context["core_version"] = self::CORE_VERSION;
        $context["bpp_id"] = $this->getConfigData("bpp_config/subscriber/subscriber_id");
        $context["bpp_uri"] = $this->getConfigData("bpp_config/subscriber/uri");
        //$context["timestamp"] = $this->_date->date()->format('Y-m-d\TH:i:s\Z');
        $context["timestamp"] = $this->formatDate();
        //$context["ttl"] = (int)$this->getConfigData("beckn/config/ttl");
        return $context;
    }

    /**
     * @param $context
     * @return array
     */
    public function getAcknowledge($context)
    {
        $updatedContext = $this->getContext($context);
        $response = [];
        $response["context"] = $updatedContext;
        $response["message"] = $this->acknowledgeMessage();
        //$response["error"] = $this->acknowledgeError();
        return $response;
    }

    /**
     * @return \string[][]
     */
    public function acknowledgeMessage()
    {
        return [
            "ack" => [
                "status" => self::ACK
            ],
        ];
    }

    /**
     * @param string $code
     * @param string $message
     * @param string $type
     * @param string $path
     * @return string[]
     */
    public function acknowledgeError(string $code = "", string $message = "", string $type = "CONTEXT-ERROR", string $path = "")
    {
        return [
            "type" => $type,
            "code" => $code,
            "path" => $path,
            "message" => $message,
        ];
    }

    /**
     * @param $context
     * @param $message
     * @return array
     */
    public function validateApiRequest($context, $message)
    {
        $errorMessage = $code = "";
        $bapUri = $context['bap_uri'] ?? "";
        $transactionId = $context['transaction_id'] ?? "";
        if ($bapUri == "") {
            $errorMessage = __("BAP URI is not set in request");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (filter_var($bapUri, FILTER_VALIDATE_URL) == "") {
            $errorMessage = __("BAP URI is not valid");
            $code = self::ERROR_CODE['bad_request'];
        } elseif ($transactionId == "") {
            $errorMessage = __("Transaction Id is required");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (empty($message)) {
            $errorMessage = __("Message should contain at least one intent object");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (!isset($message['intent'])) {
            $errorMessage = __("Message should contain at least one intent object");
            $code = self::ERROR_CODE['bad_request'];
        }
        return [
            "message" => $errorMessage,
            "code" => $code
        ];
    }

    /**
     * @param $context
     * @param $message
     * @return array
     */
    public function validateConfirmRequest($context, $message)
    {
        $errorMessage = $code = "";
        $bapUri = $context['bap_uri'] ?? "";
        $transactionId = $context['transaction_id'] ?? "";
        if ($bapUri == "") {
            $errorMessage = __("BAP URI is not set in request");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (filter_var($bapUri, FILTER_VALIDATE_URL) == "") {
            $errorMessage = __("BAP URI is not valid");
            $code = self::ERROR_CODE['bad_request'];
        } elseif ($transactionId == "") {
            $errorMessage = __("Transaction Id is required");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (empty($message)) {
            $errorMessage = __("Message should contain at least one order object");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (!isset($message['order'])) {
            $errorMessage = __("Message should contain at least one order object");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (!isset($message['order']["items"])) {
            $errorMessage = __("Message should contain items");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (!isset($message['order']["items"])) {
            $errorMessage = __("Message should contain items");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (!isset($message['order']["payment"])) {
            $errorMessage = __("Message should contain payment");
            $code = self::ERROR_CODE['bad_request'];
        }
        return [
            "message" => $errorMessage,
            "code" => $code
        ];
    }

    /**
     * @param $context
     * @param $message
     * @return array
     */
    public function validateOrderStatusRequest($context, $message)
    {
        $errorMessage = $code = "";
        $bapUri = $context['bap_uri'] ?? "";
        $transactionId = $context['transaction_id'] ?? "";
        if ($bapUri == "") {
            $errorMessage = __("BAP URI is not set in request");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (filter_var($bapUri, FILTER_VALIDATE_URL) == "") {
            $errorMessage = __("BAP URI is not valid");
            $code = self::ERROR_CODE['bad_request'];
        } elseif ($transactionId == "") {
            $errorMessage = __("Transaction Id is required");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (empty($message)) {
            $errorMessage = __("Message should contain order id key");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (empty($message["order_id"])) {
            $errorMessage = __("Order id is required");
            $code = self::ERROR_CODE['bad_request'];
        }
        return [
            "message" => $errorMessage,
            "code" => $code
        ];
    }

    /**
     * @param $latTo
     * @param $longTo
     * @return false|float
     */
    public function checkDistance($latTo, $longTo)
    {
        $fromLocation = $this->getConfigData('beckn/config/drop_location/gps_location');
        if (!empty($fromLocation)) {
            $gpsCord = explode(',', $fromLocation);
            $latFrom = $gpsCord[0];
            $longFrom = $gpsCord[1];
            return $this->getDistanceBetweenPoints($latFrom, $longFrom, $latTo, $longTo);
        }
    }

    /**
     * @param $latitude1
     * @param $longitude1
     * @param $latitude2
     * @param $longitude2
     * @param string $unit
     * @return false|float
     */
    public function getDistanceBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'kilometers')
    {
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        switch ($unit) {
            case 'miles':
                break;
            case 'kilometers' :
                $distance = $distance * 1.609344;
        }
        return (round($distance, 2));
    }

    /**
     * @param string $name
     * @return array
     */
    public function getRegionData(string $name): array
    {
        $regionData = $this->_regionCollectionFactory->create()
            ->addRegionNameFilter($name)
            ->getFirstItem()
            ->toArray();
        return $regionData;
    }

    /**
     * @return string
     */
    public function getRazorpayCallbackUrl()
    {
        return $this->_urlBuilder->getUrl(self::RAZORPAY_CALLBACK_URL);
    }

    /**
     * @return array
     */
    public function getCancelReasonOption()
    {
        $cancelReason = $this->getConfigData('bpp_config/order_cancel/reason');
        $cancelReasonOption = [];
        if (!empty($cancelReason)) {
            $allOption = json_decode($cancelReason, true);
            foreach ($allOption as $key => $_option) {
                $cancelReasonOption[] = [
                    "id" => $key,
                    "descriptor" => [
                        "name" => $_option['label']
                    ],
                ];
            }
        }
        return $cancelReasonOption;
    }

    /**
     * @param $cancelReasonId
     * @return mixed|string
     */
    public function getCancelReasonById($cancelReasonId){
        $cancelReason = $this->getConfigData('bpp_config/order_cancel/reason');
        $optionLabel = "";
        if (!empty($cancelReason)) {
            $allOption = json_decode($cancelReason, true);
            foreach ($allOption as $key => $_option) {
                if($key==$cancelReasonId){
                    $optionLabel = $_option["label"];
                }
            }
        }
        return $optionLabel;
    }

    /**
     * @param $url
     */
    public function checkSubscriber($url){
        $apiUrl = $url.self::REGISTRY_SUBSCRIBE;

        $this->_curl->addHeader('content-type', 'application/json');
        $postData = $this->getSubscriberBody();

        $this->_curl->post($apiUrl, json_encode($postData));
        $response = $this->_curl->getBody();
        $response = json_decode($response, true);
        $status = $response['status'] ?? "";
        $this->setConfigData(self::SUBSCRIBER_STATUS_PATH, $status);
        $this->flushCache();

    }

    /**
     * @return array
     */
    public function getSubscriberBody(){
        try {
            return [
                "subscriber_id" => $this->getConfigData("bpp_config/subscriber/subscriber_id"),
                "country" => $this->getCountryName($this->getConfigData("bpp_config/subscriber/country")),
                "city" => $this->getConfigData("bpp_config/subscriber/city"),
                "domain" => $this->getConfigData("bpp_config/subscriber/uri"),
                "signing_public_key" => $this->getConfigData("bpp_config/subscriber/signing_public_key"),
                "encr_public_key" => $this->getConfigData("bpp_config/subscriber/encryption_public_key"),
                "valid_from" => $this->formatDate($this->getConfigData("bpp_config/subscriber/valid_from")),
                "valid_until" => $this->formatDate($this->getConfigData("bpp_config/subscriber/valid_until")),
                "nonce" => $this->_random->getRandomString(12),
            ];
        } catch (LocalizedException $e) {
        }
    }

    /**
     * @param $path
     * @param $value
     */
    public function setConfigData($path, $value){
        return $this->_configWriter->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
    }

    /**
     * Flush Cache
     */
    public function flushCache()
    {
        $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');

        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }


    /**
     *
     */
    public function saveLookup(){
        $url = $this->getConfigData("bpp_config/subscriber/url");
        $apiUrl = $url.self::REGISTRY_LOOKUP;
        $this->_curl->addHeader('content-type', 'application/json');
        $postData = $this->getSubscriberLookupBody();
        $this->_curl->post($apiUrl, json_encode($postData));
        $response = $this->_curl->getBody();
        if(!empty($response)){
            $data = json_decode($response, true);
            foreach ($data as $_data){
                $model = $this->_becknLookupFactory->create();
                $model->setData($_data)->save();
            }
        }
    }

    /**
     * @return array
     */
    public function getSubscriberLookupBody(){
        try {
            return [
                "subscriber_id" => 'beckn.org',
                "type" => 'BAP',
                "domain" => 'MOBILITY',
                "country" => 'IND',
                "city" => 'Pune',
            ];
        } catch (LocalizedException $e) {
        }
    }
}
