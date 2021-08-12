<?php

namespace Beckn\Bpp\Controller\Adminhtml\Index;

use Beckn\Bpp\Helper\Data;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Subscribe
 * @author Indoglobal
 * @package Beckn\Bpp\Controller\Adminhtml\Index
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
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Helper $helper
     */
    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Data $helperData
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        $this->_productCollection = $productCollectionFactory;
        $this->_helperData = $helperData;
        parent::__construct($context);
    }

    public function execute(){
        $url = $this->_helperData->getConfigData('bpp_config/subscriber/url');
        $validFrom = $this->_helperData->getConfigData('bpp_config/subscriber/valid_from');
        $validTo = $this->_helperData->getConfigData('bpp_config/subscriber/valid_until');
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