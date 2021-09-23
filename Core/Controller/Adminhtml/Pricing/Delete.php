<?php

namespace Beckn\Core\Controller\Adminhtml\Pricing;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Delete
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Pricing
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Beckn\Core\Model\PricePolicyFactory
     */
    protected $_pricePolicyFactory;

    /**
     * @var \Beckn\Core\Model\PolicyRequestFactory
     */
    protected $_policyRequestBodyFactory;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param \Beckn\Core\Model\PricePolicyFactory $pricePolicyFactory
     * @param \Beckn\Core\Model\PolicyRequestFactory $policyRequestBodyFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Beckn\Core\Model\PricePolicyFactory $pricePolicyFactory,
        \Beckn\Core\Model\PolicyRequestFactory $policyRequestBodyFactory
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_pricePolicyFactory = $pricePolicyFactory;
        $this->_policyRequestBodyFactory = $policyRequestBodyFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try{
            $rowId = (int) $this->getRequest()->getParam('id');
            $policyModel = $this->_pricePolicyFactory->create();
            $policyModel->load($rowId);
            $policyRequestBodyData = $this->_policyRequestBodyFactory->create()->getCollection()->addFieldToFilter('policy_id', $rowId)->getData();
            foreach ($policyRequestBodyData as $data ){
                $id = $data['entity_id'];
                $policyRequestBodyModel = $this->_policyRequestBodyFactory->create();
                $policyRequestBodyModel->load($id)->delete();
            }
            $policyModel->delete();
            $this->messageManager->addSuccessMessage(__("Record deleted successfully."));
        }
        catch (\Exception $ex){
            $this->messageManager->addErrorMessage(__("We can\'t submit your request, Please try again."));

        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $this->_redirect('beckn/pricing/index');;
    }
}