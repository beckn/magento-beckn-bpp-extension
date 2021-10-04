<?php

namespace Beckn\CancelOrder\Model\Repository;

use Beckn\Core\Helper\Data as Helper;
use Beckn\Checkout\Model\ManageOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class CancelRepository
 * @author Indglobal
 * @package Beckn\CancelOrder\Model\Repository
 */
class CancelRepository implements \Beckn\CancelOrder\Api\CancelRepositoryInterface
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

    /**
     * CancelRepository constructor.
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param \Beckn\Checkout\Model\ManageCheckout $manageCheckout
     * @param \Beckn\Core\Model\ManageCart $manageCart
     * @param ManageOrder $manageOrder
     */
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
     * @return string|void
     */
    public function cancelReason($context)
    {
//        $authStatus = $this->_helper->validateAuth($context, $message);
//        if(!$authStatus){
//            echo $this->_helper->unauthorizedResponse();
//            exit();
//        }
//        $validateMessage = $this->_helper->validateApiRequest($context, $message);
        if (is_callable('fastcgi_finish_request')) {
            $acknowledge = $this->_helper->getAcknowledge($context);
//            $validateMessage = $this->_helper->validateOrderStatusRequest($context, $message);
//            if (!empty($validateMessage['message'])) {
//                $errorAcknowledge = $this->_helper->acknowledgeError($validateMessage['code'], $validateMessage['message']);
//                $acknowledge["message"]["ack"]["status"] = Helper::NACK;
//                $acknowledge["error"] = $errorAcknowledge;
//            }
            echo json_encode($acknowledge);
            session_write_close();
            fastcgi_finish_request();
        }
        ignore_user_abort(true);
        ob_start();

        //Add code here
        if (empty($validateMessage['message'])) {
            $this->manageCancelReason($context);
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
        //$cancelReason = $this->_helper->getCancelReasonOption();
        //return json_decode(json_encode($cancelReason), true);
    }

    /**
     * @param $context
     * @return mixed
     */
    public function manageCancelReason($context){
        $onCancelReason = [];
        $onCancelReason["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_CANCEL, $context);
        $cancelReason = $this->_helper->getCancelReasonOption();
        $onCancelReason["message"]["cancellation_reasons"] = $cancelReason;
        return $this->_helper->sendResponse($apiUrl, $onCancelReason);
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     * @throws \SodiumException
     */
    public function cancelOrder($context, $message)
    {
        $authStatus = $this->_helper->validateAuth($context, $message);
        if (!$authStatus) {
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
            $this->processCancelOrder($context, $message);
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
    public function processCancelOrder($context, $message)
    {
        $onCancelResponse = [];
        $onCancelResponse["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_CANCEL, $context);
        try {
            $order = $this->_manageOrder->loadByIncrementId($message["order_id"]);
            if (!$order->getId()) {
                $onCancelResponse["error"] = $this->_helper->acknowledgeError("", 'Order not found');
                return $this->_helper->sendResponse($apiUrl, $onCancelResponse);
            }
            $orderStatus = $order->getStatus();
            if ($orderStatus != "pending") {
                $onCancelResponse["error"] = $this->_helper->acknowledgeError("", 'Either the order is already canceled or the order has been processed and cannot be canceled.');
                return $this->_helper->sendResponse($apiUrl, $onCancelResponse);
            } else {
                $cancelReason = $this->_helper->getCancelReasonById($message["cancellation_reason_id"]);
                if ($cancelReason != "") {
                    $order->addStatusToHistory('canceled', $cancelReason);
                    $order->save();
                }
                $this->_manageOrder->orderCancelByIncrementId($order->getId());
                $order = $this->_manageOrder->loadByIncrementId($message["order_id"]);
                $onCancelResponse["message"]["order"] = $this->_manageOrder->prepareOrderResponse($order);
            }
            $onCancelResponse["message"]["order"] = $this->_manageOrder->prepareOrderResponse($order);
        } catch (NoSuchEntityException $ex) {
            $onCancelResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        } catch (\Exception $ex) {
            $onCancelResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        }
        return $this->_helper->sendResponse($apiUrl, $onCancelResponse);
    }
}