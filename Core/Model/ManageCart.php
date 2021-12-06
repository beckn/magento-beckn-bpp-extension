<?php

namespace Beckn\Core\Model;

use Beckn\Checkout\Block\Sales\Order\Totals;
use Beckn\Core\Helper\Data as Helper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Model\Cart;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

/**
 * Class ManageCart
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class ManageCart
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
     * @var Cart
     */
    protected $_cart;

    /**
     * @var Totals
     */
    protected $_orderTotal;

    /**
     * ManageCart constructor.
     * @param BecknQuoteMask $becknQuoteMask
     * @param Helper $helper
     * @param \Magento\Quote\Api\GuestCartManagementInterface $guestCart
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param Quote $quote
     * @param CartRepositoryInterface $quoteRepository
     * @param Cart $cart
     * @param Totals $orderTotal
     */
    public function __construct(
        \Beckn\Core\Model\BecknQuoteMask $becknQuoteMask,
        Helper $helper,
        \Magento\Quote\Api\GuestCartManagementInterface $guestCart,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartTotalRepositoryInterface $cartTotalRepository,
        Quote $quote,
        CartRepositoryInterface $quoteRepository,
        \Magento\Checkout\Model\Cart $cart,
        Totals $orderTotal
    )
    {
        $this->_becknQuoteMask = $becknQuoteMask;
        $this->_helper = $helper;
        $this->_guestCart = $guestCart;
        $this->_quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_cartTotalRepository = $cartTotalRepository;
        $this->_quote = $quote;
        $this->_quoteRepository = $quoteRepository;
        $this->_cart = $cart;
        $this->_orderTotal = $orderTotal;
    }

    /**
     * @param $transactionId
     * @return string
     * @throws LocalizedException
     */
    public function loadByTransactionId($transactionId)
    {
        return $this->_becknQuoteMask->loadByTransactionId($transactionId);
    }

    /**
     * @param $transactionId
     * @return BecknQuoteMask|null
     * @throws CouldNotSaveException
     * @throws \Exception
     */
    public function createQuote($transactionId)
    {
        try {
            $maskId = $this->_guestCart->createEmptyCart();
            if ($maskId) {
                $quoteIdMask = $this->_quoteIdMaskFactory->create()->load($maskId, 'masked_id');
                $becknQuoteMask = $this->_becknQuoteMask->setData([
                    "quote_id" => $quoteIdMask->getQuoteId(),
                    "masked_id" => $maskId,
                    "transaction_id" => $transactionId,
                ])->save();
                return $becknQuoteMask;
            } else {
                return null;
            }
        } catch (CouldNotSaveException $ex) {
            throw new CouldNotSaveException(
                __('Could not Beckn quote mask: %1', $ex->getMessage()),
                $ex
            );
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @param $transactionId
     * @return array|int|mixed|string|null
     * @throws CouldNotSaveException
     * @throws \Exception
     */
    public function getQuoteId($transactionId)
    {
        try {
            $quoteMaskId = $this->loadByTransactionId($transactionId);
            if (empty($quoteMaskId)) {
                $becknQuoteMask = $this->createQuote($transactionId);
                if ($becknQuoteMask) {
                    $quoteId = $becknQuoteMask->getQuoteId();
                } else {
                    $quoteId = null;
                }
            } else {
                if ($quoteMaskId["status"]  === 0) {
                    throw new \Exception("Error transaction id is already processed.");
                }
                $quoteId = $quoteMaskId['quote_id'];
            }
            return $quoteId;


















        } catch (CouldNotSaveException $ex) {
            throw new CouldNotSaveException(
                __('Could not Beckn quote mask: %1', $ex->getMessage()),
                $ex
            );
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @param CartInterface $quote
     * @return array
     */
    public function getAllAvailableItem(CartInterface $quote)
    {
        $alreadyAdded = [];
        $quoteItems = $quote->getAllVisibleItems();
        foreach ($quoteItems as $eachItem) {
            $alreadyAdded[$eachItem->getSku()] = $eachItem->getItemId();
        }
        return $alreadyAdded;
    }

    /**
     * @param CartInterface $quote
     * @param string $type
     * @return array
     * @throws NoSuchEntityException
     */
    public function getFinalItem(CartInterface $quote, $type="init")
    {
        try {
            $allVisibleItems = $quote->getAllVisibleItems();
            /**
             * @var \Magento\Quote\Model\Quote\Item $eachItem
             */
            $finalItems = [];
            foreach ($allVisibleItems as $eachItem) {
                $eachFinalItem = [
                    "id" => $eachItem->getSku(),
//                    "descriptor" => [
//                        "name" => $eachItem->getName(),
//                        "code" => $eachItem->getSku(),
//                        "symbol" => "",
//                        "short_desc" => $eachItem->getProduct()->getShortDescription(),
//                        "long_desc" => $eachItem->getProduct()->getDescription(),
//                        "images" => $this->_helper->getProductMediaGallery($eachItem->getSku()),
//                        //"audio" => "",
//                        //"3d_render" => "",
//                    ],
                    "price" => [
                        "currency" => $quote->getQuoteCurrencyCode(),
                        "value" => $eachItem->getPrice()
                    ]
                ];
                if($type=="select"){
                    $eachFinalItem["quantity"] = [
                        "selected" => [
                            "count" => $eachItem->getQty()
                        ]
                    ];
                }
                else{
                    $eachFinalItem["quantity"] = [
                        "count" => $eachItem->getQty()
                    ];
                }
                $finalItems[] = $eachFinalItem;
            }
            return $finalItems;
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(__($ex->getMessage()));
        }
    }

    /**
     * @param CartInterface $quote
     * @return array
     */
    public function getQuoteProductStoreId(CartInterface $quote){
        $allVisibleItems = $quote->getAllVisibleItems();
        /**
         * @var \Magento\Quote\Model\Quote\Item $eachItem
         */
        $availableStoreId = [];
        foreach ($allVisibleItems as $eachItem) {
            $product = $eachItem->getProduct()->load($eachItem->getProduct()->getId());
            $availableStoreId[] = $product->getData("product_store_bpp");
        }
        return $availableStoreId;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function getOrderProductStoreId(OrderInterface $order){
        $allVisibleItems = $order->getAllVisibleItems();
        /**
         * @var \Magento\Quote\Model\Quote\Item $eachItem
         */
        $availableStoreId = [];
        foreach ($allVisibleItems as $eachItem) {
            $product = $eachItem->getProduct()->load($eachItem->getProduct()->getId());
            $availableStoreId[] = $product->getData("product_store_bpp");
        }
        return $availableStoreId;
    }

    /**
     * @param CartInterface $quote
     * @return array
     * @throws NoSuchEntityException
     */
    public function getTotalSegment(CartInterface $quote)
    {
        try {
            $totals = $this->_cartTotalRepository->get($quote->getId());
            $totalsBreakup = [];
            $priceBreakUp = [];
            $allVisibleItems = $quote->getAllVisibleItems();
            /**
             * @var \Magento\Quote\Model\Quote\Item $eachItem
             */
            foreach ($allVisibleItems as $eachItem) {
                $totalsBreakup[] = [
                    //"type" => "item",
                    "title" => $eachItem->getName(),
                    //"ref_id" => $eachItem->getSku(),
                    "price" => [
                        "currency" => $quote->getQuoteCurrencyCode(),
                        "value" => $eachItem->getRowTotal(),
                    ]
                ];
            }
            foreach ($totals->getTotalSegments() as $totalSegment) {
                if ($totalSegment->getValue() != 0 && $totalSegment->getValue() != "") {
                    if (!in_array($totalSegment->getCode(), Helper::EXCLUDE_TOTALS)) {
                        $title = $totalSegment->getTitle();
                        $code = $totalSegment->getCode();
                        if ($totalSegment->getCode() == "shipping") {
                            //$title = $totalSegment->getCode();
                            $title = __(Helper::SHIPPING_LABEL);
                        }
                        $totalsBreakup[] = [
                            //"type" => $code,
                            "title" => $title,
                            //"ref_id" => "",
                            "price" => [
                                "currency" => $quote->getQuoteCurrencyCode(),
                                "value" => $totalSegment->getValue(),
                            ]
                        ];
                    }
                }
            }
            $priceBreakUp["price"] = [
                "currency" => $quote->getQuoteCurrencyCode(),
                "value" => $quote->getGrandTotal()
            ];
            $priceBreakUp["breakup"] = $totalsBreakup;
            return $priceBreakUp;
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(__($ex->getMessage()));
        }
    }

    /**
     * @param CartInterface $quote
     * @param array $context
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareOnSelectResponse(CartInterface $quote, array $context)
    {
        try {
            $availableStoreId = $this->getQuoteProductStoreId($quote);
            $finalItems = $this->getFinalItem($quote, "select");
            $totalSegment = $this->getTotalSegment($quote);
            $providerDetails = $this->_helper->getProvidersDetails([], $availableStoreId);
            $providerLocation = $this->_helper->getProvidersLocation($providerDetails);
            return [
                "selected_data" => [
                    "provider" => $providerDetails,
                    "items" => $finalItems,
                    "quote" => $totalSegment
                ],
                "provider_location" => $providerLocation,
            ];
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(__($ex->getMessage()));
        }
    }

    /**
     * @param Order $order
     * @return array
     */
    public function getOrderTotalSegment(Order $order)
    {
        $totalsBreakup = [];
        $priceBreakUp = [];
        $allVisibleItems = $order->getAllVisibleItems();
        /**
         * @var \Magento\Sales\Model\Order\Item $eachItem
         */
        foreach ($allVisibleItems as $eachItem) {
            $totalsBreakup[] = [
                //"type" => "item",
                "title" => $eachItem->getName(),
                //"ref_id" => $eachItem->getSku(),
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
                $code = $total->getCode();
                if ($total->getCode() == "shipping") {
                    //$title = $total->getCode();
                    $title = __(Helper::SHIPPING_LABEL);
                }
                $totalsBreakup[] = [
                    //"type" => $code,
                    "title" => $title,
                    //"ref_id" => "",
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