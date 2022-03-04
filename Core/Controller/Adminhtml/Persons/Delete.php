<?php

namespace Beckn\Core\Controller\Adminhtml\Persons;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Delete
 * @package Beckn\Core\Controller\Adminhtml\Persons
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Beckn\Core\Model\PersonDetailsFactory
     */
    protected $_personDetailsFactory;


    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param \Beckn\Core\Model\PersonDetailsFactory $personDetailsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Beckn\Core\Model\PersonDetailsFactory $personDetailsFactory
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_personDetailsFactory = $personDetailsFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try{
            $rowId = (int) $this->getRequest()->getParam('id');
            $fulfillmentModel = $this->_personDetailsFactory->create();
            $fulfillmentModel->load($rowId);
            $fulfillmentModel->delete();
            $this->messageManager->addSuccessMessage(__("Record deleted successfully."));
        }
        catch (\Exception $ex){
            $this->messageManager->addErrorMessage(__("We can\'t submit your request, Please try again."));

        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $this->_redirect('beckn/persons/index');
    }
}