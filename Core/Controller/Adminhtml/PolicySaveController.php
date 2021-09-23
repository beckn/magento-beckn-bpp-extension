<?php

namespace Beckn\Core\Controller\Adminhtml;

use Beckn\Core\Model\LocationPolicy;
use Beckn\Core\Model\PricePolicy;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Beckn\Core\Api\Data\PolicyRequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Beckn\Core\Model\Config\Source\PolicyType;
use Beckn\Core\Model\Config\Source\RequestType;


/**
 * Class Save
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Pricing
 */
abstract class PolicySaveController extends \Magento\Backend\App\Action
{
    /**
     * @var \Beckn\Core\Model\LocationPolicyFactory
     */
    protected $_locationPolicy;

    /**
     * @var \Beckn\Core\Model\PricePolicyFactory
     */
    protected $_pricePolicy;

    /**
     * @var \Beckn\Core\Model\PolicyRequestFactory
     */
    protected $_policyRequestBody;

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
     * @param \Beckn\Core\Model\PricePolicyFactory $pricePolicyFactory
     * @param \Beckn\Core\Model\PolicyRequestFactory $policyRequestBody
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Beckn\Core\Model\LocationPolicyFactory $locationPolicyFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Beckn\Core\Model\PricePolicyFactory $pricePolicyFactory,
        \Beckn\Core\Model\PolicyRequestFactory $policyRequestBody,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Beckn\Core\Model\LocationPolicyFactory $locationPolicyFactory
    )
    {
        $this->_locationPolicy = $locationPolicyFactory;
        $this->_pricePolicy = $pricePolicyFactory;
        $this->_policyRequestBody = $policyRequestBody;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * @param $policyType
     * @return bool
     */
    public function savePolicy($policyType)
    {
        $post = $this->getRequest()->getPostValue();
        if ($post['name'] == "") {
            $this->_messageManager->addErrorMessage(__("We can't submit your request, Please try again."));
        } else {
            try {
                $requestBody = $post["request_body"] ?? [];
                $requestHeaders = $post["request_headers"] ?? [];
                unset($post["request_body"]);
                $policyObj = $this->getModelObject($policyType);
                $policyObj->setData($post);
                if (isset($post["entity_id"])) {
                    $policyObj->setEntityId($post["entity_id"]);
                }
                $policyObj->save();
                if ($policyObj->getEntityId()) {
                    $this->saveRequest($policyObj, $requestBody, $policyType, RequestType::BODY);
                    $this->saveRequest($policyObj, $requestHeaders, $policyType, RequestType::HEADER);
                }
                $this->_messageManager->addSuccessMessage(__("Data Saved Successfully."));
            } catch (\Exception $ex) {
                $this->_messageManager->addErrorMessage(__("We can't submit your request, Please try again."));
            }
        }
        return true;
    }

    /**
     * @param $policy
     * @param $request
     * @param $policyType
     * @param $requestType
     * @throws \Exception
     */
    public function saveRequest($policy, $request, $policyType, $requestType){
        $this->removeExistingRequest($policy->getEntityId(), $policyType, $requestType);
        foreach ($request["key"] as $key => $value) {
            /**
             * @var \Beckn\Core\Model\PolicyRequest $policyRequest
             */
            $policyRequest = $this->_policyRequestBody->create();
            $policyRequest->setData([
                PolicyRequestInterface::POLICY_ID => $policy->getEntityId(),
                PolicyRequestInterface::POLICY_TYPE => $policyType,
                PolicyRequestInterface::REQUEST_TYPE => $requestType,
                PolicyRequestInterface::KEY => $value,
                PolicyRequestInterface::VALUE_TYPE => $request["value_type"][$key] ?? null,
                PolicyRequestInterface::VALUE => $request["value"][$key],
            ]);
            $policyRequest->save();
        }
    }

    /**
     * @param $type
     * @return PricePolicy|LocationPolicy|null
     */
    private function getModelObject($type){
        $modelObj = null;
        if($type=="price"){
            /**
             * @var PricePolicy $pricePolicy
             */
            $modelObj = $this->_pricePolicy->create();
        }
        elseif ($type=="location"){
            /**
             * @var LocationPolicy $pricePolicy
             */
            $modelObj = $this->_locationPolicy->create();
        }
        return $modelObj;
    }

    /**
     * @param $id
     * @param $policyType
     * @param $requestType
     * @return bool
     */
    private function removeExistingRequest($id, $policyType, $requestType)
    {
        $collection = $this->_policyRequestBody->create()->getCollection()
            ->addFieldToFilter(PolicyRequestInterface::POLICY_ID, $id)
            ->addFieldToFilter(PolicyRequestInterface::REQUEST_TYPE, $requestType)
            ->addFieldToFilter(PolicyRequestInterface::POLICY_TYPE, $policyType);
        if ($collection->getSize()) {
            foreach ($collection as $_collection) {
                $_collection->delete();
            }
        }
        return true;
    }
}