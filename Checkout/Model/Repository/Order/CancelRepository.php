<?php

namespace Beckn\Checkout\Model\Repository\Order;

use Beckn\Bpp\Helper\Data as Helper;
use Beckn\Checkout\Model\ManageOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CancelRepository
 * @author Indoglobal
 * @package Beckn\Checkout\Model\Repository\Order
 */
class CancelRepository implements \Beckn\Checkout\Api\CancelRepositoryInterface
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
     * @var \Beckn\Bpp\Model\ManageCart
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
     * @param \Beckn\Bpp\Model\ManageCart $manageCart
     * @param ManageOrder $manageOrder
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        \Beckn\Checkout\Model\ManageCheckout $manageCheckout,
        \Beckn\Bpp\Model\ManageCart $manageCart,
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
        $cancelReason = $this->_helper->getCancelReasonOption();
        return json_decode(json_encode($cancelReason), true);
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     */
    public function cancelOrder($context, $message)
    {
        $validateMessage = [];
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