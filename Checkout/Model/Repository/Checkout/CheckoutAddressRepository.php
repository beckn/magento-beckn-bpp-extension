<?php

namespace Beckn\Checkout\Model\Repository\Checkout;

use Beckn\Core\Helper\Data as Helper;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Model\Group;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CheckoutAddressRepository
 * @author Indglobal
 * @package Beckn\Checkout\Model\Repository\Checkout
 */
class CheckoutAddressRepository implements \Beckn\Checkout\Api\CheckoutAddressRepositoryInterface
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
     * CheckoutRepository constructor.
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory
     * @param \Beckn\Checkout\Model\ManageCheckout $manageCheckout
     * @param \Beckn\Core\Model\ManageCart $manageCart
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory,
        \Beckn\Checkout\Model\ManageCheckout $manageCheckout,
        \Beckn\Core\Model\ManageCart $manageCart
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_quoteRepository = $quoteRepository;
        $this->_cartItemInterfaceFactory = $cartItemInterfaceFactory;
        $this->_manageCheckout = $manageCheckout;
        $this->_manageCart = $manageCart;
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     */
    public function manageAddress($context, $message)
    {
        $authStatus = $this->_helper->validateAuth($context, $message);
        if(!$authStatus){
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
            echo json_encode($acknowledge);
            session_write_close();
            fastcgi_finish_request();
        }
        ignore_user_abort(true);
        ob_start();

        //Add code here
        if (empty($validateMessage['message'])) {
            $this->processAddress($context, $message);
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
    private function processAddress($context, $message)
    {
        $this->_logger->info("Address data here");
        $this->_logger->info(json_encode($message));
        $onInitResponse = [];
        $onInitResponse["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_INIT, $context);
        try {
            $quoteId = $this->_manageCart->getQuoteId($context["transaction_id"]);
            /**
             * @var \Magento\Quote\Api\Data\CartInterface $quote
             */
            $quote = $this->_quoteRepository->getActive($quoteId);
            if ($quote->getItemsQty() == 0) {
                $onInitResponse["error"] = $this->_helper->acknowledgeError("", __("Items not found in Quote. Can't save address"));
                return $this->_helper->sendResponse($apiUrl, $onInitResponse);
            }
            $billingAddress = $this->_manageCheckout->getBillingAddress($message);
            $shippingAddress = $this->_manageCheckout->getShippingAddress($message);
            if (empty($billingAddress) || empty($shippingAddress)) {
                $onInitResponse["error"] = $this->_helper->acknowledgeError("", __("Shipping or Billing address is missing."));
                return $this->_helper->sendResponse($apiUrl, $onInitResponse);
            }
            $billingEmail = $this->_manageCheckout->getBillingEmail($message);
            if ($billingEmail == "") {
                $billingEmail = "test@nomail.com";
//                $onInitResponse["error"] = $this->_helper->acknowledgeError("", __("Shipping or Billing address is missing."));
//                return $this->_helper->sendResponse($apiUrl, $onInitResponse);
            }
            $quote->getBillingAddress()->addData($billingAddress);
            $quote->getShippingAddress()->addData($shippingAddress);
            $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
            $quote->getBillingAddress()->setEmail($billingEmail);
            $quote->setCustomerId(null);
            $quote->setCustomerEmail($billingEmail);
            $quote->setCustomerIsGuest(true);
            $quote->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);

            $estimateShipping = $this->_manageCheckout->estimateShippingByAddress($quote->getId(), $quote->getShippingAddress());
            $shippingMethod = "";
            foreach ($estimateShipping as $_estimate) {
                if (empty($shippingMethod)) {
                    $shippingMethod = $_estimate->getCarrierCode() . "_" . $_estimate->getMethodCode();
                }
            }
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod($shippingMethod);

            $this->_quoteRepository->save($quote);
            $quote->collectTotals();
            $onInitResponse["message"]["order"] = $this->_manageCheckout->prepareOnInitResponse($quote, $message);
            $this->_manageCheckout->saveResponseBody($quote, $onInitResponse);
        } catch (NoSuchEntityException $ex) {
            $onInitResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        } catch (\Exception $ex) {
            $onInitResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        }
        return $this->_helper->sendResponse($apiUrl, $onInitResponse);
    }
}