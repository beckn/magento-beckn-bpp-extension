<?php

namespace Beckn\Core\Controller\Adminhtml\Itemfulfillment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Options
 * @package Beckn\Core\Controller\Adminhtml\Fulfillment
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
        $resultPage->getConfig()->getTitle()->prepend((__('Item Fulfillment Options')));
        return $resultPage;
    }
}