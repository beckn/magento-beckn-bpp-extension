<?php

namespace Beckn\Checkout\Model\Repository\Checkout;

use Beckn\Core\Helper\Data as Helper;
use Beckn\Checkout\Model\Config\FilterOption\OrderType;
use Beckn\Checkout\Model\Config\FilterOption\PaymentStatus;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\Quote;

/**
 * Class CheckoutRepository
 * @author Indglobal
 * @package Beckn\Checkout\Model\Repository\Checkout
 */
class CheckoutRepository implements \Beckn\Checkout\Api\CheckoutRepositoryInterface
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
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $_cartItemInterfaceFactory;

    /**
     * @var \Beckn\Checkout\Model\ManageCheckout
     */
    protected $_manageCheckout;

    /**
     * @var \Beckn\Core\Model\ManageCart
     */
    protected $_manageCart;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    private $_quoteManagement;

    /**
     * CheckoutRepository constructor.
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory
     * @param \Beckn\Checkout\Model\ManageCheckout $manageCheckout
     * @param \Beckn\Core\Model\ManageCart $manageCart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory,
        \Beckn\Checkout\Model\ManageCheckout $manageCheckout,
        \Beckn\Core\Model\ManageCart $manageCart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteManagement $quoteManagement
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_quoteRepository = $quoteRepository;
        $this->_cartItemInterfaceFactory = $cartItemInterfaceFactory;
        $this->_manageCheckout = $manageCheckout;
        $this->_manageCart = $manageCart;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteManagement = $quoteManagement;
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @throws \SodiumException
     */
    public function manageCheckout($context, $message)
    {
        $this->_logger->info("Order request log");
        $this->_logger->info(json_encode($message));
        $authStatus = $this->_helper->validateAuth($context, $message);
        if(!$authStatus){
            echo $this->_helper->unauthorizedResponse();
            exit();
        }
        $validateMessage = [];
        if (is_callable('fastcgi_finish_request')) {
            $acknowledge = $this->_helper->getAcknowledge($context);
            $validateMessage = $this->_helper->validateConfirmRequest($context, $message);
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
            $this->processCheckout($context, $message);
        }
        $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);

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
     * @return mixed
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    private function processCheckout($context, $message)
    {
        $onConfirmResponse = [];
        $onConfirmResponse["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_CONFIRM, $context);
        try {
            $quoteId = $this->_manageCart->getQuoteId($context["transaction_id"]);
            /**
             * @var \Magento\Quote\Api\Data\CartInterface $quote
             */
            $quote = $this->_quoteRepository->getActive($quoteId);
            //$quote = $this->_quoteRepository->get($quoteId);
            if ($quote->getId() == '') {
                $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", __("Your cart is empty."));
                return $this->_helper->sendResponse($apiUrl, $onConfirmResponse);
            }

            if ($quote->getItemsQty() == 0) {
                $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", __("Items not found in Quote. Can't save address"));
                return $this->_helper->sendResponse($apiUrl, $onConfirmResponse);
            }
            $quote->getBillingAddress()->setEmail($quote->getBillingAddress()->getEmail());
            $this->_manageCheckout->prepareGuestQuote($quote);
            $quote->reserveOrderId();
            //$quote->setPaymentMethod(Helper::PAYMENT_COD["code"]);
            $quote->getPayment()->importData(['method' => Helper::PAYMENT_COD["code"]]);
            $quote->collectTotals();
            $this->_quoteRepository->save($quote);
            $this->_checkoutSession->setLastSuccessQuoteId($quote->getId());
            $this->_checkoutSession->setLastQuoteId($quote->getId());
            $this->_checkoutSession->clearHelperData();
            $order = $this->_quoteManagement->submit($quote);
            if (!$order->getId()) {
                $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", __("Something went wrong!"));
                return $this->_helper->sendResponse($apiUrl, $onConfirmResponse);
            }
            $this->_checkoutSession->setLastOrderId($order->getId());
            $this->_checkoutSession->setLastRealOrderId($order->getIncrementId());
            $this->_checkoutSession->setLastOrderStatus($order->getStatus());
            $order->setOrderType(OrderType::BECKN_STORE);
            $order->setPaymentStatus(PaymentStatus::PAYMENT_NOT_PAID);
            $order->save();
            $this->_manageCheckout->updateTransactionStatus($context["transaction_id"]);
            $onConfirmResponse["message"]["order"] = $this->_manageCheckout->prepareOnConfirmResponse($order, $message, Helper::STATUS_NOT_PAID);
            $this->_manageCheckout->updateOrderAddress($order, $onConfirmResponse["message"]["order"]["billing"], $onConfirmResponse["message"]["order"]["fulfillment"]["end"]);
        } catch (NoSuchEntityException $ex) {
            $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        } catch (\Exception $ex) {
            $onConfirmResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        }
        return $this->_helper->sendResponse($apiUrl, $onConfirmResponse);
    }
}