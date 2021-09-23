<?php

namespace Beckn\Core\Controller\Adminhtml\Index;

use Beckn\Core\Helper\Data as Helper;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Subscribe
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Index
 */
class Subscribe extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollection;

    /**
     * @var Helper
     */
    protected $_helperData;

    /**
     * Subscribe constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Helper $helperData
     */
    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Helper $helperData
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        $this->_productCollection = $productCollectionFactory;
        $this->_helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute(){
        $url = $this->_helperData->getConfigData(Helper::XML_PATH_SECURITY_REGISTRY_URL);
        $validFrom = $this->_helperData->getConfigData(Helper::XML_PATH_SECURITY_VALID_FROM);
        $validTo = $this->_helperData->getConfigData(Helper::XML_PATH_SECURITY_VALID_UNTIL);
        if(!empty($url && $validFrom && $validTo)){
            $this->_helperData->checkSubscriber($url);
            $this->_messageManager->addSuccessMessage('Subscriber Status Saved Successfully.');
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }else{
            $this->_messageManager->addErrorMessage('Please fill the Url first.');
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
    }

}