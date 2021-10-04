<?php

namespace Beckn\Checkout\Model;

use Beckn\Core\Helper\Data as Helper;
use Magento\Customer\Model\Group;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class ManageCheckout
 * @author Indglobal
 * @package Beckn\Checkout\Model
 */
class ManageCheckout
{
    /**
     * @var \Beckn\Core\Model\BecknQuoteMask
     */
    public $_becknQuoteMask;

    /**
     * @var Helper
     */
    public $_helper;

    /**
     * @var \Magento\Quote\Api\GuestCartManagementInterface
     */
    protected $_guestCart;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $_quoteIdMaskFactory;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $_cartTotalRepository;

    /**
     * @var Quote
     */
    protected $_quote;

    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var ShipmentEstimationInterface
     */
    private $_shipmentEstimationManagement;

    /**
     * @var \Beckn\Core\Model\ManageCart
     */
    protected $_manageCart;

    /**
     * @var ShippingMethodManagementInterface
     */
    protected $_shippingMethodManagement;

    /**
     * @var Razorpay
     */
    protected $_razorpay;

    /**
     * ManageCheckout constructor.
     * @param \Beckn\Core\Model\BecknQuoteMask $becknQuoteMask
     * @param Helper $helper
     * @param \Magento\Quote\Api\GuestCartManagementInterface $guestCart
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param Quote $quote
     * @param CartRepositoryInterface $quoteRepository
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param \Beckn\Core\Model\ManageCart $manageCart
     * @param Razorpay $razorpay
     */
    public function __construct(
        \Beckn\Core\Model\BecknQuoteMask $becknQuoteMask,
        Helper $helper,
        \Magento\Quote\Api\GuestCartManagementInterface $guestCart,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartTotalRepositoryInterface $cartTotalRepository,
        Quote $quote,
        CartRepositoryInterface $quoteRepository,
        ShippingMethodManagementInterface $shippingMethodManagement,
        \Beckn\Core\Model\ManageCart $manageCart,
        Razorpay $razorpay
    )
    {
        $this->_becknQuoteMask = $becknQuoteMask;
        $this->_helper = $helper;
        $this->_guestCart = $guestCart;
        $this->_quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_cartTotalRepository = $cartTotalRepository;
        $this->_quote = $quote;
        $this->_quoteRepository = $quoteRepository;
        $this->_shippingMethodManagement = $shippingMethodManagement;
        $this->_manageCart = $manageCart;
        $this->_razorpay = $razorpay;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getBillingAddress(array $data)
    {
        $billingAddressData = $data["order"]["billing"] ?? [];
        $billingAddress = [];
        if (!empty($billingAddressData)) {
            $address = $billingAddressData["address"] ?? "";
            $telephone = $billingAddressData["phone"] ?? "";
            $name = array_map('trim', explode(' ', $billingAddressData["name"]));
            $firstName = $name[0] ?? "";
            $lastName = $name[1] ?? $firstName;
            $state = $address["state"] ?? "";
            $regionData = $this->_helper->getRegionData($state);
            $regionId = $regionData["region_id"] ?? "";
            $billingAddress = [
                'firstname' => $firstName,
                'lastname' => $lastName,
                'street' => $address["door"] ?? "" . " " . $address["name"] ?? "" . " " . $address["locality"] ?? "",
                'city' => ($address["city"] == "") ? "bangalore" : $address["city"],
                'country_id' => $this->_helper->loadCountryByIso($address["country"]),
                'region' => $address["state"] ?? "",
                'region_id' => $regionId,
                'postcode' => $address["area_code"] ?? "",
                'telephone' => $telephone,
                'email' => $billingAddressData["email"] ?? "test@nomail.com"
            ];
        }
        return $billingAddress;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getShippingAddress(array $data)
    {
        $shippingAddressData = $data["order"]["fulfillment"]["end"] ?? [];
        $billingAddressData = $data["order"]["billing"] ?? [];
        $shippingAddress = [];
        if (!empty($shippingAddressData)) {
            $address = $shippingAddressData["location"]["address"];
            $telephone = $shippingAddressData["contact"]["phone"];
            $email = $shippingAddressData["contact"]["email"] ?? "";
            if ($email == "") {
                $email = "test@nomail.com";
            }
            $name = array_map('trim', explode(' ', $billingAddressData["name"]));
            $firstName = $name[0] ?? "";
            $lastName = $name[1] ?? $firstName;
            $state = $address["state"] ?? "";
            $regionData = $this->_helper->getRegionData($state);
            $regionId = $regionData["region_id"] ?? "";
            $shippingAddress = [
                'firstname' => $firstName,
                'lastname' => $lastName,
                'street' => $address["door"] ?? "" . " " . $address["name"] ?? "" . " " . $address["locality"] ?? "",
                'city' => ($address["city"] == "") ? "bangalore" : $address["city"],
                'country_id' => $this->_helper->loadCountryByIso($address["country"]),
                'region' => $address["state"] ?? "",
                'region_id' => $regionId,
                'postcode' => $address["area_code"] ?? "",
                'telephone' => $telephone,
                'email' => $email
            ];
        }
        return $shippingAddress;
    }

    /**
     * @param array $data
     * @return mixed|string
     */
    public function getBillingEmail(array $data)
    {
        return $data["order"]["billing"]["email"] ?? "";
    }


    /**
     * Get shipment estimation management service
     * @return ShipmentEstimationInterface
     * @deprecated 100.0.7
     */
    private function getShipmentEstimationManagement()
    {
        if ($this->_shipmentEstimationManagement === null) {
            $this->_shipmentEstimationManagement = ObjectManager::getInstance()
                ->get(ShipmentEstimationInterface::class);
        }
        return $this->_shipmentEstimationManagement;
    }

    /**
     * @param $quoteId
     * @param $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    public function estimateShippingByAddress($quoteId, AddressInterface $address)
    {
        return $this->getShipmentEstimationManagement()
            ->estimateByExtendedAddress((int)$quoteId, $address);
    }

    /**
     * @param CartInterface $quote
     * @param array $message
     * @return array
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function prepareOnInitResponse(CartInterface $quote, array $message)
    {
        try {
            $finalItems = $this->_manageCart->getFinalItem($quote);
            $availableStoreId = $this->_manageCart->getQuoteProductStoreId($quote);
            $totalSegment = $this->_manageCart->getTotalSegment($quote);
            $providerDetails = $this->_helper->getProvidersDetails([], $availableStoreId);
            $providerLocation = $this->_helper->getProvidersLocation($providerDetails);
            $paymentMethod = $this->_helper->getConfigData(Helper::XML_PATH_SELECTED_PAYMENT_METHOD);
            $paymentParams = [];
            $status = Helper::STATUS_NOT_PAID;
            if ($paymentMethod == Helper::RAZORPAY) {
                $paymentLink = $this->_razorpay->generateRazorpayPaymentLink($quote);
                if (!$paymentLink) {
                    throw new \Exception(__("Can't able to generate Razorpay payment link"));
                }
                $transactionStatus = $this->_razorpay->getRazorpayTransactionStatus($quote->getId());
                $paymentParams['uri'] = $paymentLink;
                $paymentParams['method'] = "http/get";
                $paymentParams["transaction_status"] = $transactionStatus;
            }

            return [
                "provider" => $providerDetails,
                "provider_location" => [
                    "id" => $providerLocation["id"]
                ],
                "items" => $finalItems,
                "billing" => $message["order"]["billing"],
                "fulfillment" => $this->getFulfillmentAddress($message["order"]["fulfillment"], $providerDetails),
                "quote" => $totalSegment,
                "payment" => $this->getPaymentData($status, $quote->getGrandTotal(), $quote->getQuoteCurrencyCode(), $paymentParams),
            ];
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(__($ex->getMessage()));
        }
    }

    /**
     * @param OrderInterface $order
     * @param array $message
     * @param string $status
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareOnConfirmResponse(OrderInterface $order, array $message, string $status)
    {
        try {
            $finalItems = $message["order"]["items"];
            $totalSegment = $this->_manageCart->getOrderTotalSegment($order);
            $availableStoreId = $this->_manageCart->getOrderProductStoreId($order);
            $providerDetails = $this->_helper->getProvidersDetails([], $availableStoreId);
            $providerLocation = $this->_helper->getProvidersLocation($providerDetails);
            $method = $order->getPayment()->getMethod();
            $params = [];
            if($method==Helper::RAZORPAY){
                $transactionStatus = $this->_razorpay->getRazorpayTransactionStatus($order->getQuoteId());
                $params["transaction_status"] = $transactionStatus;
            }
            return [
                "id" => $order->getIncrementId(),
                "state" => $order->getStatusLabel(),
                "provider" => $providerDetails,
                "provider_location" => [
                    "id" => $providerLocation["id"]
                ],
                "items" => $finalItems,
                "billing" => $message["order"]["billing"],
                "fulfillment" => $this->getFulfillmentAddress($message["order"]["fulfillment"], $providerDetails),
                "quote" => $totalSegment,
                "payment" => $this->getPaymentData($status, $order->getGrandTotal(), $order->getOrderCurrencyCode(), $params),
                "created_at" => date('Y-m-d\TH:i:s\Z', strtotime($order->getCreatedAt())),
                "updated_at" => date('Y-m-d\TH:i:s\Z', strtotime($order->getUpdatedAt()))
            ];
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(__($ex->getMessage()));
        }
    }

    /**
     * @param string $status
     * @param $grandTotal
     * @param $currency
     * @param array $data
     * @return array
     */
    public function getPaymentData(string $status, $grandTotal, $currency, $data = [])
    {
        $paymentType = $this->_helper->getConfigData(Helper::XML_PATH_SELECTED_PAYMENT_TYPE);
        $paymentData = [
            "params" => [
                "amount" => $grandTotal,
                "currency" => $currency,
            ],
            "type" => $paymentType,
            "status" => $status
        ];
        if(isset($data["transaction_status"])){
            $paymentData["params"]["transaction_status"] = $data["transaction_status"];
        }
        if (isset($data["uri"])) {
            $paymentData["uri"] = $data["uri"];
        }
        if (isset($data["method"])) {
            $paymentData["tl_method"] = $data["method"];
        }
        return $paymentData;
    }

    /**
     * @param string $transactionId
     * @return bool
     * @throws LocalizedException
     */
    public function updateTransactionStatus(string $transactionId)
    {
        $quoteMaskData = $this->_becknQuoteMask->loadByTransactionId($transactionId);
        if (!empty($quoteMaskData)) {
            $quoteMask = $this->_becknQuoteMask->load($quoteMaskData["entity_id"]);
            $quoteMask->setStatus(0)->save();
        }
        return false;
    }

    /**
     * @param int $quoteId
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    public function getQuote(int $quoteId)
    {
        return $this->_quoteRepository->getActive($quoteId);
    }

    /**
     * @param CartInterface $quote
     * @return CartInterface
     */
    public function prepareGuestQuote(CartInterface $quote)
    {
        $quote->setCustomerId(null);
        $quote->setCustomerEmail($quote->getBillingAddress()->getEmail());
        $quote->setCustomerIsGuest(true);
        $quote->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);
        return $quote;
    }

    /**
     * @param CartInterface $quote
     * @param array $responseBody
     * @return \Beckn\Core\Model\BecknQuoteMask
     * @throws \Exception
     */
    public function saveResponseBody(CartInterface $quote, array $responseBody)
    {
        /**
         * @var \Beckn\Core\Model\BecknQuoteMask $quoteMask
         */
        $quoteMask = $this->_becknQuoteMask
            ->getCollection()
            ->addFieldToFilter("quote_id", $quote->getId())
            ->getFirstItem();
        if (!empty($quoteMask->getData())) {
            $quoteMask->setRequestBody(json_encode($responseBody));
            $quoteMask->save();
        }
        return $quoteMask;
    }

    /**
     * @param array $fulfillmentAddress
     * @param array $providerDetails
     * @return array
     */
    public function getFulfillmentAddress(array $fulfillmentAddress, $providerDetails = [])
    {
        $deliveryType = $this->_helper->getConfigData(Helper::XML_PATH_FULFILMENT_TYPE);
        $selectedType = array_map('trim', explode(',', $deliveryType));
        $location = $providerDetails["locations"][0] ?? [];
        return [
            "type" => $selectedType[0] ?? "",
            "tracking" => false,
            "start" => [
                "location" => [
                    "id" => $location["id"] ?? "",
                    "descriptor" => [
                        "name" => $this->_helper->getConfigData(Helper::XML_PATH_PROVIDER_NAME),
                        "short_desc" => $this->_helper->getConfigData(Helper::XML_PATH_PROVIDER_SHORT_DESC),
                        "images" => [$this->_helper->getProviderImage()],
                    ],
                    "gps" => $location["gps"] ?? ""
                ],
                "contact" => [
                    "phone" => $this->_helper->getConfigData(Helper::XML_PATH_PROVIDER_EMAIL),
                    "email" => $this->_helper->getConfigData(Helper::XML_PATH_PROVIDER_PHONE),
                ],
            ],
            "end" => $fulfillmentAddress["end"] ?? []
        ];
    }

    /**
     * @param OrderInterface $order
     * @param array $becknBillingAddress
     * @param array $becknShippingAddress
     * @throws \Exception
     */
    public function updateOrderAddress(OrderInterface $order, array $becknBillingAddress = [], array $becknShippingAddress = [])
    {
        /**
         * @var \Magento\Sales\Model\Order\Address|null $shippingAddress
         */
        $shippingAddress = $order->getShippingAddress();
        if (!empty($shippingAddress)) {
            $shippingAddress->setData("beckn_customer_address", json_encode($becknShippingAddress));
            $shippingAddress->save();
        }
        /**
         * @var \Magento\Sales\Model\Order\Address|null $billingAddress
         */
        $billingAddress = $order->getBillingAddress();
        if (!empty($billingAddress)) {
            $billingAddress->setData("beckn_customer_address", json_encode($becknBillingAddress));
            $billingAddress->save();
        }
    }
}