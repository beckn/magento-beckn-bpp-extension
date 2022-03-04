<?php

namespace Beckn\Core\Controller\Adminhtml\Event;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Event
 */
class Index extends \Magento\Backend\App\Action
{

    protected $_resultPageFactory = false;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Event Log')));
        return $resultPage;
    }
}