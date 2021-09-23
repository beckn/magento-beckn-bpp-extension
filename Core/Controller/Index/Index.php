<?php

namespace Beckn\Core\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Beckn\Core\Model\DigitalSignature as DigitalSignature;
use Beckn\Core\Helper\Data as HelperData;

/**
 * Class Index
 * @author Indglobal
 * @package Beckn\Core\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var DigitalSignature
     */
    protected $_digitalSignature;

    public function __construct
    (
        Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Psr\Log\LoggerInterface $logger,
        HelperData $helperData,
        DigitalSignature $digitalSignature
    )
    {
        $this->_productCollectionFactory = $collectionFactory;
        $this->_pageFactory = $pageFactory;
        $this->_logger = $logger;
        $this->_helperData = $helperData;
        $this->_digitalSignature = $digitalSignature;
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPost();
        $postJson = json_encode($post);
        $this->_logger->info($postJson);
    }

    /**
     * Validate Crf to skip parent validation
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Validate Crf to skip parent validation
     *
     * @param RequestInterface $request
     * @return boolean
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}