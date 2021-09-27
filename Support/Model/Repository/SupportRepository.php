<?php

namespace Beckn\Support\Model\Repository;

use Beckn\Core\Helper\Data as Helper;
use Psr\Log\LoggerInterface;

/**
 * Class OrderRepository
 * @author Indglobal
 * @package Beckn\Checkout\Model\Repository\Order
 */
class SupportRepository implements \Beckn\Support\Api\SupportRepositoryInterface
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
     * SupportRepository constructor.
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    public function manageSupport($context, $message)
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
            $this->processSupport($context, $message);
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
    public function processSupport($context, $message){
        $onSupportResponse = [];
        $onSupportResponse["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_SUPPORT, $context);
        $onSupportResponse["message"] = [
            "phone" => $this->_helper->getConfigData(Helper::XML_PATH_SUPPORT_PHONE),
            "email" => $this->_helper->getConfigData(Helper::XML_PATH_SUPPORT_EMAIL),
            "uri" => $this->_helper->getConfigData(Helper::XML_PATH_SUPPORT_URL),
        ];
        return $this->_helper->sendResponse($apiUrl, $onSupportResponse);
    }
}