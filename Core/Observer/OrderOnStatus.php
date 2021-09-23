<?php

namespace Beckn\Core\Observer;

use Beckn\Checkout\Model\ManageOrder;
use Beckn\Core\Helper\Data as Helper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Beckn\Core\Model\ResourceModel\BecknQuoteMask\CollectionFactory;

/**
 * Class OrderOnStatus
 * @author Indglobal
 * @package Beckn\Core\Observer
 */
class OrderOnStatus implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var ManageOrder $manageOrder
     */
    protected $_manageOrder;

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $_collectionFactory;

    public function __construct(
        ManageOrder $manageOrder,
        Helper $helper,
        CollectionFactory $collectionFactory
    )
    {
        $this->_manageOrder = $manageOrder;
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $quoteId = $order->getQuoteId();
            /**
             * @var \Beckn\Core\Model\ResourceModel\BecknQuoteMask\Collection $collection
             */
            $collection = $this->_collectionFactory->create();
            $collection->addFieldToFilter("quote_id", $quoteId);
            $maskData = $collection->getFirstItem()->getData();
            if(!empty($maskData)){
                $requestBody = json_decode($maskData["request_body"], true);
                if(is_array($requestBody)){
                    $context = $requestBody["context"];
                    $context["action"] = "status";
                    $onStatusResponse["context"] = $this->_helper->getContext($context);
                    $onStatusResponse["message_id"] = $this->_helper->generateMessageId();
                    $apiUrl = $this->_helper->getBapUri(Helper::ON_STATUS, $context);
                    $onStatusResponse["message"]["order"] = $this->_manageOrder->prepareOrderResponse($order);
                    return $this->_helper->sendResponse($apiUrl, $onStatusResponse);
                }
            }
        } catch (NoSuchEntityException $e) {

        } catch (LocalizedException $e) {

        }
    }
}