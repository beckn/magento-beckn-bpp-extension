<?php

namespace Beckn\Core\Controller\Adminhtml\Fulfillment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Beckn\Core\Api\Data\FulfillmentStatusInterface;
use Magento\Framework\View\Result\PageFactory;


/**
 * Class Save
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Fulfillment
 */
class Save extends \Magento\Backend\App\Action {

    /**
     * @var \Beckn\Core\Model\FulfillmentPolicyFactory
     */
    protected $_fulfillmentPolicy;

    /**
     * @var \Beckn\Core\Model\FulfillmentStatusFactory
     */
    protected $_fulfillmentStatus;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Save constructor.
     * @param Context $context
     * @param \Beckn\Core\Model\FulfillmentPolicyFactory $fulfillmentPolicyFactory
     * @param \Beckn\Core\Model\FulfillmentStatusFactory $fulfillmentStatusFactory
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Beckn\Core\Model\FulfillmentPolicyFactory $fulfillmentPolicyFactory,
        \Beckn\Core\Model\FulfillmentStatusFactory $fulfillmentStatusFactory,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_fulfillmentPolicy = $fulfillmentPolicyFactory;
        $this->_fulfillmentStatus = $fulfillmentStatusFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if($post['name'] == ""){
            $this->_messageManager->addErrorMessage(__("We can't submit your request, Please try again."));
            return $this->_redirect('beckn/fulfillment/index');
        }
        try{
            $status = $post["status"] ?? [];
            unset($post["status"]);
            /**
             * @var \Beckn\Core\Model\FulfillmentPolicy $fulfillmentPolicy
             */
            $fulfillmentPolicy = $this->_fulfillmentPolicy->create();
            $fulfillmentPolicy->setData($post);
            if(isset($post["entity_id"])){
                $fulfillmentPolicy->setEntityId($post["entity_id"]);
            }
            $fulfillmentPolicy->save();
            if($fulfillmentPolicy->getEntityId()){
                $this->removeExistingStatus($fulfillmentPolicy->getEntityId());
                foreach ($status as $key => $value){
                    /**
                     * @var \Beckn\Core\Model\FulfillmentStatus $fulfillmentStatus
                     */
                    $fulfillmentStatus = $this->_fulfillmentStatus->create();
                    $fulfillmentStatus->setData([
                        FulfillmentStatusInterface::LOCATION_ID => $fulfillmentPolicy->getEntityId(),
                        FulfillmentStatusInterface::STATUS => $value,
                    ]);
                    $fulfillmentStatus->save();
                }
            }
            $this->_messageManager->addSuccessMessage(__("Data Saved Successfully."));
        }
        catch(\Exception $ex){
            $this->_messageManager->addErrorMessage(__("We can't submit your request, Please try again."));
        }
        return $this->_redirect('beckn/fulfillment/index');
    }

    /**
     * @param $id
     * @return bool
     */
    private function removeExistingStatus($id){
        $collection = $this->_fulfillmentStatus->create()->getCollection()
            ->addFieldToFilter(FulfillmentStatusInterface::LOCATION_ID, $id);
        if($collection->getSize()){
            foreach ($collection as $_collection){
                $_collection->delete();
            }
        }
        return true;
    }
}