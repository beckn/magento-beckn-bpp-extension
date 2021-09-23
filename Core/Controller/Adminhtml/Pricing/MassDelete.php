<?php

namespace Beckn\Core\Controller\Adminhtml\Pricing;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Beckn\Core\Model\ResourceModel\PricePolicy\CollectionFactory;

/**
 * Class MassDelete
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Pricing
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Beckn\Core\Model\PolicyRequestFactory
     */
    protected $_policyRequestBodyFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Beckn\Core\Model\PolicyRequestFactory $policyRequestBodyFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Beckn\Core\Model\PolicyRequestFactory $policyRequestBodyFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_policyRequestBodyFactory = $policyRequestBodyFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordDeleted = 0;

        foreach ($collection->getItems() as $record) {
            $record->setId($record->getEntityId());
            $policyRequestBodyData = $this->_policyRequestBodyFactory->create()->getCollection()->addFieldToFilter('policy_id', $record->getEntityId())->getData();
            foreach ($policyRequestBodyData as $data ){
                $id = $data['entity_id'];
                $policyRequestBodyModel = $this->_policyRequestBodyFactory->create();
                $policyRequestBodyModel->load($id)->delete();
            }
            $record->delete();
            $recordDeleted++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * Check Category Map recode delete Permission.
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Beckn_Core::pricing');
    }
}
