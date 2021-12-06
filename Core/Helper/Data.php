<?php

namespace Beckn\Core\Helper;

use Beckn\Core\Model\LocationPolicyFactory;
use Beckn\Core\Model\FulfillmentPolicyFactory;
use Beckn\Core\Model\PolicyRequest;
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
use Beckn\Core\Model\BecknLookupFactory;
use Beckn\Core\Model\DigitalSignature as DigitalSignature;
use Beckn\Core\Model\PricePolicyFactory;
use Beckn\Core\Model\ResourceModel\PolicyRequest\Collection as RequestBodyCollection;
use Beckn\Core\Model\Config\Source\Method;
use Magento\Store\Api\StoreRepositoryInterface;
use Beckn\Core\Model\Config\Source\ValueType;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Data
 * @author Indglobal
 * @package Beckn\Core\Helper
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
    //const ON_CANCEL = "on_cancel";
    const ON_CANCEL = "cancellation_reasons";
    const ON_UPDATE = "on_update";
    const ON_TRACK = "on_track";
    const ON_RATING = "on_rating";
    const ACK = "ACK";
    const NACK = "NACK";
    const ERROR_CODE = [
        "bad_request" => 400,
    ];
    const EXCLUDE_TOTALS = [
        "subtotal",
        "grand_total",
    ];
    const SHIPPING_LABEL = "FULFILLMENT";
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
    const SUBSCRIBER_STATUS_PATH = 'security_config/subscriber/status';

    const AUTHORIZATION_KEY = 'Authorization';
    const PROXY_AUTHORIZATION_KEY = 'Proxy-Authorization';

    /*
     * ALL Config XML PATH
     */
    const XML_PATH_SUBSCRIBER_ID = "subscriber_config/subscriber/subscriber_id";
    const XML_PATH_SUBSCRIBER_INDUSTRY_DOMAIN = "subscriber_config/subscriber/industry_domain";
    const XML_PATH_SUBSCRIBER_COUNTRY = "subscriber_config/subscriber/country";
    const XML_PATH_SUBSCRIBER_CITY = "subscriber_config/subscriber/city";
    const XML_PATH_SUBSCRIBER_URI = "subscriber_config/subscriber/uri";
    const XML_PATH_BUSINESS_NAME = "business_config/business/name";
    const XML_PATH_BUSINESS_SHORT_DESC = "business_config/business/short_desc";
    const XML_PATH_BUSINESS_LOGO = "business_config/business/logo";
    const XML_PATH_FULFILMENT_TYPE = "fulfillment_config/fulfilment/type";
    const XML_PATH_PROVIDER_ID = "provider_config/provider_details/provider_id";
    const XML_PATH_PROVIDER_NAME = "provider_config/provider_details/provider_name";
    const XML_PATH_PROVIDER_SHORT_DESC = "provider_config/provider_details/short_desc";
    const XML_PATH_PROVIDER_LOGO = "provider_config/provider_details/logo";
    const XML_PATH_PROVIDER_PHONE = "provider_config/provider_details/phone";
    const XML_PATH_PROVIDER_EMAIL = "provider_config/provider_details/email";
    const XML_PATH_CANCEL_REASON = "cancel_config/order_cancel/reason";
    const XML_PATH_SECURITY_VALID_FROM = "security_config/security/valid_from";
    const XML_PATH_SECURITY_VALID_UNTIL = "security_config/security/valid_until";
    const XML_PATH_SECURITY_REGISTRY_URL = "security_config/security/url";
    const XML_PATH_SIGNATURE_AUTH_ENABLE = "security_config/security/enable";
    const XML_PATH_API_OPTION = "api_config/api/option";
    const XML_PATH_SELECTED_PAYMENT_METHOD = "payment_config/payment/method";
    const XML_PATH_SELECTED_PAYMENT_TYPE = "payment_config/payment/types";
    const XML_PATH_SUPPORT_PHONE = "provider_config/support_info/phone";
    const XML_PATH_SUPPORT_EMAIL = "provider_config/support_info/email";
    const XML_PATH_SUPPORT_URL = "provider_config/support_info/url";

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
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $_restRequest;

    /**
     * @var DigitalSignature
     */
    protected $_digitalSignature;

    /**
     * @var PricePolicyFactory
     */
    protected $_pricePolicyFactory;

    /**
     * @var LocationPolicyFactory
     */
    protected $_locationPolicyFactory;

    /**
     * @var FulfillmentPolicyFactory
     */
    protected $_fulfillmentPolicyFactory;

    /**
     * @var StoreRepositoryInterface
     */
    protected $_storeRepository;
    /**
     * @var ProductRepository
     */
    protected $_productRepository;

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
     * @param PricePolicyFactory $pricePolicyFactory
     * @param LocationPolicyFactory $locationPolicyFactory
     * @param FulfillmentPolicyFactory $fulfillmentPolicyFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param ProductRepository $productRepository
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
        \Magento\Framework\Webapi\Rest\Request $restRequest,
        DigitalSignature $digitalSignature,
        PricePolicyFactory $pricePolicyFactory,
        LocationPolicyFactory $locationPolicyFactory,
        FulfillmentPolicyFactory $fulfillmentPolicyFactory,
        StoreRepositoryInterface $storeRepository,
        ProductRepository $productRepository
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
        $this->_restRequest = $restRequest;
        $this->_digitalSignature = $digitalSignature;
        $this->_pricePolicyFactory = $pricePolicyFactory;
        $this->_locationPolicyFactory = $locationPolicyFactory;
        $this->_fulfillmentPolicyFactory = $fulfillmentPolicyFactory;
        $this->_storeRepository = $storeRepository;
        $this->_productRepository = $productRepository;
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
        return $this->scopeConfig->getValue($path, $scope);
    }

    /**
     * @param $type
     * @param $context
     * @return string
     */
    public function getBapUri($type, $context)
    {
        $proxy_authorization = $this->_request->getHeader(self::PROXY_AUTHORIZATION_KEY);
        if (!empty($proxy_authorization)) {
            $keyId = $this->_digitalSignature->getDataFromAuth($proxy_authorization, "keyId");
            $subscriberId = $this->_digitalSignature->getSubscriberIdFromAuth($keyId);
            $model = $this->_becknLookupFactory->create();
            $collection = $model->getCollection()
                ->addFieldToFilter('subscriber_id', $subscriberId)->getData();
            if (!empty($collection)) {
                return $collection["subscriber_url"]. "/" . $type;
            }
        }
        return $context['bap_uri'] . "/" . $type;
    }

    /**
     * @param $apiUrl
     * @param $postData
     * @return mixed
     */
    public function sendResponse($apiUrl, $postData)
    {
        //array_walk_recursive($postData,function(&$item){$item=strval($item);});
        $postBody = json_encode($postData, JSON_UNESCAPED_SLASHES);
        $authorization = $this->_digitalSignature->createAuthorization($postBody);
        $this->_logger->info("Authorization Header.");
        if ($authorization["success"] == true) {
            $authHeader = $authorization["auth"];
            $this->_logger->info($authHeader);
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
            $imagePath = $this->getConfigData(self::XML_PATH_PROVIDER_LOGO);
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
            $imagePath = $this->getConfigData(self::XML_PATH_BUSINESS_LOGO);
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
            $configProviderId = $this->getConfigData(self::XML_PATH_PROVIDER_ID);
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
            $configSeller = $this->getConfigData(self::XML_PATH_BUSINESS_NAME);
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
            $fulfillmentType = $this->getConfigData(self::XML_PATH_FULFILMENT_TYPE);
            $fulfillmentType = array_map('trim', explode(',', $fulfillmentType));
            if (in_array($type, $fulfillmentType)) {
                $match = true;
            }
        }
        if (!$match) {
            return null;
        }
        if (isset($message["intent"]["item"]["descriptor"]["code"]) && !empty($message["intent"]["item"]["descriptor"]["code"])) {
            $itemCode = $message["intent"]["item"]["descriptor"]["code"];
            return $collection->addAttributeToFilter('item_code_bpp', $itemCode);
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
                ]);
                $collection->addAttributeToFilter([
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
     * @param array $allItems
     * @param array $priceData
     * @param $minValue
     * @param $maxValue
     * @return array
     */
    public function addPriceFilter(array $allItems, array $priceData, $minValue, $maxValue)
    {
        $filteredData = array_filter(array_flip($priceData), function ($v) use ($minValue, $maxValue) {
            return $v >= $minValue && $v <= $maxValue;
        });
        $filterItems = [];
        foreach ($allItems as $item) {
            if (array_key_exists($item["id"], $filteredData)) {
                $filterItems[] = $item;
            }
        }
        return $filterItems;
    }

    /**
     * @param string $date
     * @param bool $currentDate
     * @return false|string
     */
    public function formatDate($date = "", $currentDate = true)
    {
        $formattedDate = "";
        if ($date != "") {
            $formattedDate = date('Y-m-d\TH:i:s\Z', strtotime($date));
        }
        if ($date == "" && $currentDate == true) {
            $formattedDate = date('Y-m-d\TH:i:s\Z', strtotime(date("Y-m-d H:i:s")));
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
     * @return int
     * @throws NoSuchEntityException
     */
    public function currentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
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
     * @param array $availableStore
     * @return array
     */
    public function getProvidersDetails(array $allItems = [], array $availableStore = [])
    {
        $providerDetails = [
            "id" => $this->getConfigData(self::XML_PATH_PROVIDER_ID),
            "descriptor" => [
                "name" => $this->getConfigData(self::XML_PATH_PROVIDER_NAME),
                "short_desc" => $this->getConfigData(self::XML_PATH_PROVIDER_SHORT_DESC),
                "images" => [$this->getProviderImage()],
            ],
            "locations" => $this->getLocations($availableStore),
        ];
        if (!empty($allItems)) {
            $providerDetails['items'] = $allItems;
        }
        return $providerDetails;
    }

    /**
     * @param array $availableStore
     * @return array
     */
    public function getProvidersLocation($availableStore = [])
    {
        $storeCode  = $availableStore["locations"][0]["id"] ?? "";
        //$location = $this->getLocations();
        return [
            "id" => $storeCode,
//            "descriptor" => [
//                "name" => $this->getConfigData("provider_config/provider_details/name"),
//                "short_desc" => $this->getConfigData("provider_config/provider_details/short_desc"),
//                "images" => [$this->getProviderImage()],
//            ],
//            "gps" => $location["gps"],
//            "address" => $location["address"],
//            "station_code" => (string)$location["station_code"],
//            "city" => $location["city"],
//            "country" => $location["country"],
        ];
    }

    /**
     * @param array $availableStore
     * @return array
     */
    public function getLocations($availableStore = [])
    {
        $storeList = $this->_storeRepository->getList();
        $allLocation = [];
        foreach ($storeList as $item) {
            if ($item->getId() == 0 || (!empty($availableStore) && !in_array($item->getId(), $availableStore))) {
                continue;
            }
            $gpsLocation = $item->getGpsLocation();
            if ($item->getLocationId() != "") {
                $gpsLocation = $this->getGpsLocationByApi($item->getLocationId());
            }
            $allLocation[] = [
                "id" => $item->getCode(),
                "gps" => $gpsLocation,
                "address" => [
                    "door" => (string)$item->getAddressDoor(),
                    "name" => (string)$item->getAddressName(),
                    "building" => (string)$item->getAddressBuilding(),
                    "street" => (string)$item->getAddressStreet(),
                    "locality" => (string)$item->getAddressLocality(),
                    "state" => (string)$item->getAddressState(),
                    "country" => (string)$this->getCountryName($item->getAddressCountry()),
                    "area_code" => (string)$item->getAddressAreaCode(),
                ],
                "station_code" => (string)$item->getAddressStationCode(),
                "city" => [
                    "name" => (string)$item->getAddressCityName(),
                    "code" => (string)$item->getAddressCityCode(),
                ],
                "country" => [
                    "name" => (string)$this->getCountryName($item->getAddressCountry()),
                    "code" => (string)$item->getAddressCountry(),
                ],
            ];
        }
        return $allLocation;
    }

    /**
     * @param array $message
     * @return array
     */
    public function getAllStoreIds(array $message)
    {
        $storeList = $this->_storeRepository->getList();
        $allStoreIds = [];
        foreach ($storeList as $item) {
            if ($item->getId() == 0)
                continue;
            if (isset($message["intent"]["fulfillment"]["end"]["location"]["gps"]) && !empty($message["intent"]["fulfillment"]["end"]["location"]["gps"]) && $item->getFulfillmentId() != "") {
                $gps_cordinate = $message["intent"]["fulfillment"]["end"]["location"]["gps"];
                $gpsLatLong = explode(',', $gps_cordinate);
                $latTo = $gpsLatLong[0] ?? "";
                $longTo = $gpsLatLong[1] ?? "";
                /**
                 * @var \Beckn\Core\Model\FulfillmentPolicy $fulfillmentPolicy
                 */
                $fulfillmentPolicy = $this->_fulfillmentPolicyFactory->create()->load($item->getFulfillmentId());
                if ($fulfillmentPolicy->getId()) {
                    $gpsCord = explode(',', $fulfillmentPolicy->getCenter());
                    $latFrom = $gpsCord[0] ?? "";
                    $longFrom = $gpsCord[1] ?? "";
                    $totalDistance = $this->getDistanceBetweenPoints($latFrom, $longFrom, $latTo, $longTo);
                    if ($totalDistance <= $fulfillmentPolicy->getRadius()) {
                        $allStoreIds[] = $item->getId();
                    }
                }
            } else {
                $allStoreIds[] = $item->getId();
            }
        }
        return $allStoreIds;
    }

    /**
     * @param $countryCode
     * @return string
     */
    public function getCountryName($countryCode)
    {
        $countryName = '';
        if ($countryCode != '') {
            $country = $this->_country->loadByCode($countryCode);
            if ($country) {
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
     * @param $id
     * @return mixed
     */
    public function loadCountryByIso($id)
    {
        $countryCollection = $this->_country->getCollection()->addFieldToFilter('iso3_code', $id)
            ->getFirstItem()
            ->getData();
        return $countryCollection['country_id'] ?? "";
    }

    /**
     * @return array
     */
    public function getDescriptorDetails()
    {
        return [
            "name" => $this->getConfigData(self::XML_PATH_BUSINESS_NAME),
            "short_desc" => $this->getConfigData(self::XML_PATH_BUSINESS_SHORT_DESC),
            "images" => [$this->getDescriptorImage()],
        ];
    }

    /**
     * @param array $context
     * @return array
     */
    public function getContext(array $context)
    {
        $context["domain"] = $this->getConfigData(self::XML_PATH_SUBSCRIBER_INDUSTRY_DOMAIN);
        $context["country"] = $this->getCountryName($this->getConfigData(self::XML_PATH_SUBSCRIBER_COUNTRY));
        $context["action"] = $this->getReturnAction($context["action"]);
        $context["city"] = $this->getConfigData(self::XML_PATH_SUBSCRIBER_CITY);
        $context["core_version"] = self::CORE_VERSION;
        $context["bpp_id"] = $this->getConfigData(self::XML_PATH_SUBSCRIBER_ID);
        $context["bpp_uri"] = $this->getConfigData(self::XML_PATH_SUBSCRIBER_URI);
        $context["timestamp"] = $this->formatDate();
        //$context["ttl"] = (int)$this->getConfigData("beckn/config/ttl");
        return $context;
    }

    /**
     * @param $action
     * @return string
     */
    public function getReturnAction($action)
    {
        switch (strtolower($action)) {
            case "search":
                $updateAction = self::ON_SEARCH;
                break;
            case "select":
                $updateAction = self::ON_SELECT;
                break;
            case "init":
                $updateAction = self::ON_INIT;
                break;
            case "confirm":
                $updateAction = self::ON_CONFIRM;
                break;
            case "update":
                $updateAction = self::ON_UPDATE;
                break;
            case "status":
                $updateAction = self::ON_STATUS;
                break;
            case "track":
                $updateAction = self::ON_TRACK;
                break;
            case "cancel":
                $updateAction = self::ON_CANCEL;
                break;
            case "rating":
                $updateAction = self::ON_RATING;
                break;
            case "support":
                $updateAction = self::ON_SUPPORT;
                break;
            default:
                $updateAction = $action;
                break;
        }
        return $updateAction;
    }

    /**
     * @param $context
     * @return array
     */
    public function getAcknowledge($context)
    {
        $updatedContext = $this->getContext($context);
        $response = [];
        //$response["context"] = $updatedContext;
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
        $apiStatus = $this->checkApiStatus($context["action"]);
        if($apiStatus==false){
            $errorMessage = __("API is not Enabled.");
            $code = self::ERROR_CODE['bad_request'];
        } elseif ($bapUri == "") {
            $errorMessage = __("BAP URI is not set in request");
            $code = self::ERROR_CODE['bad_request'];
        } elseif (filter_var($bapUri, FILTER_VALIDATE_URL) == "") {
            $errorMessage = __("BAP URI is not valid");
            $code = self::ERROR_CODE['bad_request'];
        } elseif ($transactionId == "") {
            $errorMessage = __("Transaction Id is required");
            $code = self::ERROR_CODE['bad_request'];
        }
//        elseif (empty($message)) {
//            $errorMessage = __("Message should contain at least one intent object");
//            $code = self::ERROR_CODE['bad_request'];
//        } elseif (!isset($message['intent'])) {
//            $errorMessage = __("Message should contain at least one intent object");
//            $code = self::ERROR_CODE['bad_request'];
//        }
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
        $apiStatus = $this->checkApiStatus($context["action"]);
        if($apiStatus==false){
            $errorMessage = __("API is not Enabled.");
            $code = self::ERROR_CODE['bad_request'];
        }
        elseif ($bapUri == "") {
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
        }
//        elseif (empty($message["ref_id"])) {
//            $errorMessage = __("Order id is required");
//            $code = self::ERROR_CODE['bad_request'];
//        }
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
        $cancelReason = $this->getConfigData(self::XML_PATH_CANCEL_REASON);
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
    public function getCancelReasonById($cancelReasonId)
    {
        $cancelReason = $this->getConfigData(self::XML_PATH_CANCEL_REASON);
        $optionLabel = "";
        if (!empty($cancelReason)) {
            $allOption = json_decode($cancelReason, true);
            foreach ($allOption as $key => $_option) {
                if ($key == $cancelReasonId) {
                    $optionLabel = $_option["label"];
                }
            }
        }
        return $optionLabel;
    }

    /**
     * @param $url
     * @return bool
     */
    public function checkSubscriber($url)
    {
        $apiUrl = $url . self::REGISTRY_SUBSCRIBE;
        $this->_curl->addHeader('content-type', 'application/json');
        $postData = $this->getSubscriberBody();
        $this->_curl->post($apiUrl, json_encode($postData));
        $response = $this->_curl->getBody();
        $response = json_decode($response, true);
        $status = $response['status'] ?? "";
        $this->setConfigData(self::SUBSCRIBER_STATUS_PATH, $status);
        $this->flushCache();
        return true;
    }

    /**
     * @return bool
     */
    public function autoSubscribe(){
        $url = $this->getConfigData(self::XML_PATH_SECURITY_REGISTRY_URL);
        $validFrom = $this->getConfigData(self::XML_PATH_SECURITY_VALID_FROM);
        $validTo = $this->getConfigData(self::XML_PATH_SECURITY_VALID_UNTIL);
        $signingPublic = DigitalSignature::SIGN_PUBLIC_KEY_PATH;
        $encryptionPublic = DigitalSignature::ENCRYPTION_PUBLIC_KEY_PATH;
        if($url!="" && $validFrom!="" && $validTo!="" && $signingPublic!="" && $encryptionPublic!=""){
            $this->checkSubscriber($url);
        }
        return true;
    }

    /**
     * @return array
     */
    public function getSubscriberBody()
    {
        try {
            return [
                "subscriber_id" => $this->getConfigData(self::XML_PATH_SUBSCRIBER_ID),
                "country" => $this->getCountryName($this->getConfigData(self::XML_PATH_SUBSCRIBER_COUNTRY)),
                "city" => $this->getConfigData(self::XML_PATH_SUBSCRIBER_CITY),
                "domain" => $this->getConfigData(self::XML_PATH_SUBSCRIBER_URI),
                "signing_public_key" => $this->getConfigData(DigitalSignature::SIGN_PUBLIC_KEY_PATH),
                "encr_public_key" => $this->getConfigData(DigitalSignature::ENCRYPTION_PUBLIC_KEY_PATH),
                "valid_from" => $this->formatDate($this->getConfigData(self::XML_PATH_SECURITY_VALID_FROM)),
                "valid_until" => $this->formatDate($this->getConfigData(self::XML_PATH_SECURITY_VALID_UNTIL)),
                "nonce" => $this->_random->getRandomString(12),
            ];
        } catch (LocalizedException $e) {
        }
    }

    /**
     * @param $path
     * @param $value
     */
    public function setConfigData($path, $value)
    {
        return $this->_configWriter->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
    }

    /**
     * Flush Cache
     */
    public function flushCache()
    {
        $types = array('config', 'layout', 'block_html', 'collections', 'reflection', 'db_ddl', 'eav', 'config_integration', 'config_integration_api', 'full_page', 'translate', 'config_webservice');
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
    public function saveLookup()
    {
        $url = $this->getConfigData(self::XML_PATH_SECURITY_REGISTRY_URL);
        $apiUrl = $url . self::REGISTRY_LOOKUP;
        $this->_curl->addHeader('content-type', 'application/json');
        $postData = $this->getSubscriberLookupBody();
        $this->_curl->post($apiUrl, json_encode($postData));
        $response = $this->_curl->getBody();
        if (!empty($response)) {
            $data = json_decode($response, true);
            foreach ($data as $_data) {
                $model = $this->_becknLookupFactory->create();
                $model->setData($_data)->save();
            }
        }
    }

    /**
     * @return array
     */
    public function getSubscriberLookupBody()
    {
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

    /**
     * @param array $context
     * @param array $message
     * @return bool
     * @throws \SodiumException
     */
    public function validateAuth($context = [], $message = [])
    {
        $authStatus = true;
        if ($this->getConfigData(self::XML_PATH_SIGNATURE_AUTH_ENABLE)) {
            $apiBody = [
                "context" => $context,
                "message" => $message
            ];
            $body = json_encode($apiBody, JSON_UNESCAPED_SLASHES);
            $auth = $this->_request->getHeader(self::AUTHORIZATION_KEY);
            $proxyAuth = $this->_request->getHeader(self::PROXY_AUTHORIZATION_KEY);
            if (!empty($auth)) {
                $authStatus = $this->_digitalSignature->validateAuth($auth, $body);
            }
            if (!empty($proxyAuth)) {
                $authStatus = $this->_digitalSignature->validateAuth($proxyAuth, $body);
            }
        }
        return $authStatus;
    }

    /**
     * @return false|string
     */
    public function unauthorizedResponse()
    {
        $subscriberId = $this->_digitalSignature->getSubscriberId();
        header('WWW-Authenticate: Signature realm="' . $subscriberId . '",headers="(created) (expires) digest');
        header('HTTP/1.0 401 Unauthorized');
        $response = [
            "message" => [
                "ack" => [
                    "status" => self::NACK
                ]
            ]
        ];
        return json_encode($response, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $policyId
     * @return array|bool|string
     */
    public function getPriceFromPolicy($policyId)
    {
        $apiBody = $this->_restRequest->getBodyParams();
        /**
         * @var \Beckn\Core\Model\PricePolicy $policy
         */
        $policy = $this->_pricePolicyFactory->create()->load($policyId);
        if (!$policy->getId()) {
            return false;
        }
        $this->_curl->addHeader("Content-Type", "application/json");
        $headers = $policy->getRequestHeader();
        if ($headers->getSize()) {
            /**
             * @var PolicyRequest $_header
             */
            foreach ($headers as $_header) {
                $this->_curl->addHeader($_header->getKey(), $_header->getValue());
            }
        }
        $body = $this->getPolicyRequestBody($policy->getRequestBody(), $apiBody);
        if ($policy->getMethod() == Method::GET) {
            $apiUrl = $policy->getApiUrl() . "?" . http_build_query($body);
            $this->_curl->get($apiUrl);
        } elseif ($policy->getMethod() == Method::POST) {
            $this->_curl->post($policy->getApiUrl(), json_encode($body));
        }
        $apiResponse = json_decode($this->_curl->getBody(), true);
        return $this->updateValue($policy->getResponseBodyPath(), $apiResponse);
    }

    /**
     * @param $locationId
     * @param $context
     * @param $message
     * @return array|bool|string
     */
    public function getGpsLocationByApi($locationId)
    {
        $apiBody = $this->_restRequest->getBodyParams();
        /**
         * @var \Beckn\Core\Model\PricePolicy $policy
         */
        $location = $this->_locationPolicyFactory->create()->load($locationId);
        if (!$location->getId()) {
            return false;
        }
        $this->_curl->addHeader("Content-Type", "application/json");
        $headers = $location->getRequestHeader();
        if ($headers->getSize()) {
            /**
             * @var PolicyRequest $_header
             */
            foreach ($headers as $_header) {
                $this->_curl->addHeader($_header->getKey(), $_header->getValue());
            }
        }
        $body = $this->getPolicyRequestBody($location->getRequestBody(), $apiBody);
        if ($location->getMethod() == Method::GET) {
            $apiUrl = $location->getApiUrl() . "?" . http_build_query($body);
            $this->_curl->get($apiUrl);
        } elseif ($location->getMethod() == Method::POST) {
            $this->_curl->post($location->getApiUrl(), json_encode($body));
        }
        $apiResponse = json_decode($this->_curl->getBody(), true);
        return $this->updateValue($location->getResponseBodyPath(), $apiResponse);
    }

    /**
     * @param RequestBodyCollection $policyRequest
     * @param array $apiBody
     * @return array
     */
    public function getPolicyRequestBody(RequestBodyCollection $policyRequest, array $apiBody)
    {
        $body = [];
        if ($policyRequest->getSize()) {
            /**
             * @var \Beckn\Core\Model\PolicyRequest $item
             */
            foreach ($policyRequest as $item) {
                if($item->getValueType()==ValueType::TYPE_BODY_PATH){
                    $body[$item->getKey()] = $this->updateValue($item->getValue(), $apiBody);
                }
                else{
                    $body[$item->getKey()] = $item->getValue();
                }
            }
        }
        return $body;
    }

    /**
     * @param $key
     * @param $data
     * @return string|array
     */
    public function updateValue($key, $data)
    {
        $parseKey = explode('.', $key);
        foreach ($parseKey as $item) {
            $data = $data[$item] ?? "";
        }
        return $data;
    }

    /**
     * @param $productStoreId
     * @return string
     */
    public function getProductLocationId($productStoreId)
    {
        try {
            $storeData = $this->_storeRepository->getById($productStoreId);
            return $storeData->getCode();
        } catch (NoSuchEntityException $e) {
            return "";
        }
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed|null
     * @throws NoSuchEntityException
     */
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    /**
     * @param $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|Product
     * @throws NoSuchEntityException
     */
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function checkApiStatus(string $type){
        $allApi = $this->getConfigData(self::XML_PATH_API_OPTION);
        $allApiOption = explode(",", $allApi);
        if(in_array(strtolower($type), $allApiOption)){
            return true;
        }
        return false;
    }

    /**
     * @param null $data
     * @return string
     * @throws \Exception
     */
    public function generateMessageId($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
