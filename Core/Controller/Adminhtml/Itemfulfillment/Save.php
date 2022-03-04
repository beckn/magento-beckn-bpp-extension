<?php

namespace Beckn\Core\Controller\Adminhtml\Itemfulfillment;

use Magento\Backend\App\Action;
use Beckn\Core\Model\ItemFulfillmentOptionsFactory;
use Beckn\Core\Model\ItemFulfillmentTimesFactory;
use Beckn\Core\Model\ResourceModel\ItemFulfillmentTimes\CollectionFactory;

/**
 * Class Save
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Pricing
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var ItemFulfillmentOptionsFactory
     */
    protected $_itemFulfillmentOptionsFactory;

    /**
     * @var ItemFulfillmentTimesFactory
     */
    protected $_itemFulfillmentTimesFactoy;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    protected $_itemFulfillmentTimesCollectionFactory;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param ItemFulfillmentOptionsFactory $itemFulfillmentOptionsFactory
     * @param ItemFulfillmentTimesFactory $itemFulfillmentTimesFactory
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        Action\Context $context,
        ItemFulfillmentOptionsFactory $itemFulfillmentOptionsFactory,
        ItemFulfillmentTimesFactory $itemFulfillmentTimesFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        CollectionFactory $collectionFactory

    ) {
        $this->_itemFulfillmentOptionsFactory = $itemFulfillmentOptionsFactory;
        $this->_itemFulfillmentTimesFactoy = $itemFulfillmentTimesFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_itemFulfillmentTimesCollectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try{
            $data = $this->getRequest()->getParams();
            $modelItemFulfillmentOption = $this->_itemFulfillmentOptionsFactory->create();
            $modelItemFulfillmentOption->setData($data);
            if($modelItemFulfillmentOption->save()){
                $this->saveFulfillmentTimes($modelItemFulfillmentOption->getId());
                $this->messageManager->addSuccessMessage(__('You saved the data.'));
            }else{
                $this->messageManager->addErrorMessage(__('Data was not saved.'));
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('beckn/itemfulfillment/index');
            return $resultRedirect;
        } catch (\Exception $e) {
        }

    }

    /**
     * @param $fulfillmentId
     */
    private function saveFulfillmentTimes($fulfillmentId){
        $entityId = $this->getRequest()->getParam('entity_id');
        if(!empty($entityId)){
            $this->DeletePreviousFulfillmentTimes($entityId);
        }
        $fulfillmentTimes = $this->getRequest()->getParam('fulfillmentTimes', []);
        foreach ($fulfillmentTimes['start_time'] as $key => $value){
            $eachTimeData = [];
            $eachTimeData['fulfillment_option_id'] = $fulfillmentId;
            $eachTimeData['start_time'] = $value;
            $eachTimeData['end_time'] = $fulfillmentTimes['end_time'][$key];
            $modelItemFulfillmentTimes = $this->_itemFulfillmentTimesFactoy->create();
            $modelItemFulfillmentTimes->setData($eachTimeData);
            $modelItemFulfillmentTimes->save();
        }
    }

    /**
     * @param $entityId
     */
    private function DeletePreviousFulfillmentTimes($entityId){
        $collections = $this->_itemFulfillmentTimesCollectionFactory->create()->addFieldToFilter('fulfillment_option_id', $entityId);
        foreach ($collections as $collection){
            $collection->delete();
        }
    }
}