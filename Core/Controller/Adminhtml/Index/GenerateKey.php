<?php

namespace Beckn\Core\Controller\Adminhtml\Index;

use Beckn\Core\Helper\Data;
use Beckn\Core\Model\DigitalSignature;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenerateKey
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Index
 */
class GenerateKey extends \Magento\Backend\App\Action
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
     * @var Data
     */
    protected $_helperData;
    /**
     * @var DigitalSignature
     */
    protected $_digitalSignature;

    /**
     * Generateenckey constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Data $helperData
     * @param DigitalSignature $digitalSignature
     */
    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Data $helperData,
        DigitalSignature $digitalSignature
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        $this->_helperData = $helperData;
        $this->_digitalSignature = $digitalSignature;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $type = $this->getRequest()->getParam("type");
            $this->_digitalSignature->generateKeyPair($type);
            $this->_helperData->autoSubscribe();
            $this->_messageManager->addSuccessMessage(__('Keys Generated Successfully.'));
        } catch (\SodiumException $e) {
            $this->_messageManager->addSuccessMessage(__($e->getMessage()));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}