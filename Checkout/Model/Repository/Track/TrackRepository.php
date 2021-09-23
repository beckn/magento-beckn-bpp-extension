<?php

namespace Beckn\Checkout\Model\Repository\Track;

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
 * Class TrackRepository
 * @author Indglobal
 * @package Beckn\Checkout\Model\Repository\Order
 */
class TrackRepository implements \Beckn\Checkout\Api\TrackRepositoryInterface
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    public function __construct(
        Helper $helper,
        LoggerInterface $logger
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    public function trackOrder($context, $message)
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
            $this->processTrackOrder($context, $message);
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

    private function processTrackOrder($context, $message){
        $onTrackResponse = [];
        $onTrackResponse["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_TRACK, $context);
        try {
            $onTrackResponse["message"] = [
                "tracking" => [
                    "url" => "https://tinyurl.com/y2tguhnr",
                    "status" => "active"
                ]
            ];
        } catch (NoSuchEntityException $ex) {
            $onTrackResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        } catch (\Exception $ex) {
            $onTrackResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        }
        return $this->_helper->sendResponse($apiUrl, $onTrackResponse);
    }
}