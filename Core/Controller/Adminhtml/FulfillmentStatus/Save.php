<?php

namespace Beckn\Core\Controller\Adminhtml\FulfillmentStatus;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\OrderRepository;
use Beckn\Core\Helper\Data as HelperData;

/**
 * Class Save
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Fulfillment
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Save constructor.
     * @param Context $context
     * @param OrderRepository $orderRepository
     * @param JsonFactory $resultJsonFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        JsonFactory $resultJsonFactory,
        HelperData $helperData
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helperData  = $helperData;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $response = [
                'success' => false,
                'message' => '',
            ];
            $data = $this->getRequest()->getParams();
            $order = $this->_orderRepository->get($data['order_id']);
            $order->setFulfillmentStatus($data['fulfillment_status']);
            $order->setAgentName($data['agent_name']);
            $order->setAgentPhone($data['agent_phone']);
            $order->setAgentTemperature($data['agent_temperature']);
            $order->setTrackingLink($data['tracking_link']);
            if($order->save()){
                $this->_helperData->dispatchFulfillmentSave($order);
                $response['success'] = true;
                $response['message'] = __("Fulfillment status saved successfully.");
            }

        }catch (\Exception $ex){
            $response['message'] = $ex->getMessage();
        }
        $resultJson = $this->_resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}