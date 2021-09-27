<?php

namespace Beckn\Checkout\Model\Repository\Order;

use Beckn\Core\Helper\Data as Helper;
use Beckn\Checkout\Model\Config\FilterOption\OrderType;
use Beckn\Checkout\Model\Config\FilterOption\PaymentStatus;
use Beckn\Checkout\Model\ManageOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OrderRepository
 * @author Indglobal
 * @package Beckn\Checkout\Model\Repository\Order
 */
class OrderRepository implements \Beckn\Checkout\Api\OrderRepositoryInterface
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
     * @var \Beckn\Checkout\Model\ManageCheckout
     */
    protected $_manageCheckout;

    /**
     * @var \Beckn\Core\Model\ManageCart
     */
    protected $_manageCart;

    /**
     * @var ManageOrder $manageOrder
     */
    protected $_manageOrder;

    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        \Beckn\Checkout\Model\ManageCheckout $manageCheckout,
        \Beckn\Core\Model\ManageCart $manageCart,
        ManageOrder $manageOrder
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_manageCheckout = $manageCheckout;
        $this->_manageCart = $manageCart;
        $this->_manageOrder = $manageOrder;
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     * @throws \SodiumException
     */
    public function manageOrder($context, $message)
    {
        $authStatus = $this->_helper->validateAuth($context, $message);
        if(!$authStatus){
            echo $this->_helper->unauthorizedResponse();
            exit();
        }
        $validateMessage = $this->_helper->validateApiRequest($context, $message);
        if (is_callable('fastcgi_finish_request')) {
            $acknowledge = $this->_helper->getAcknowledge($context);
            $validateMessage = $this->_helper->validateOrderStatusRequest($context, $message);
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
            $this->processOrder($context, $message);
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
     */
    private function processOrder($context, $message)
    {
        $onStatusResponse = [];
        $onStatusResponse["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_STATUS, $context);
        try {
            $order = $this->_manageOrder->loadByIncrementId($message["order_id"]);
            if(!$order->getId()){
                throw new \Exception("Order not found.");
            }
            $onStatusResponse["message"]["order"] = $this->_manageOrder->prepareOrderResponse($order);
        } catch (NoSuchEntityException $ex) {
            $onStatusResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        } catch (\Exception $ex) {
            $onStatusResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        }
        return $this->_helper->sendResponse($apiUrl, $onStatusResponse);
    }

}