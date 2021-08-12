<?php

namespace Beckn\Bpp\Model\Repository\Search;

use Beckn\Bpp\Helper\Data as Helper;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class SearchRepository
 * @author Indglobal
 * @package Beckn\Bpp\Model\Repository\Search
 */
class SearchRepository implements \Beckn\Bpp\Api\SearchRepositoryInterface
{

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * SearchRepository constructor.
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param CollectionFactory $productCollectionFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        CollectionFactory $productCollectionFactory,
        CategoryFactory $categoryFactory
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSearch($context, $message)
    {
        $validateMessage = $this->_helper->validateApiRequest($context, $message);
        if (is_callable('fastcgi_finish_request')) {
            $acknowledge = $this->_helper->getAcknowledge($context);
            if (!empty($validateMessage['message'])) {
                $errorAcknowledge = $this->_helper->acknowledgeError($validateMessage['code'], $validateMessage['message']);
                $acknowledge["message"]["ack"]["status"] = Helper::NACK;
                $acknowledge["error"] = $errorAcknowledge;
            }
            echo json_encode($acknowledge);
            session_write_close();
            fastcgi_finish_request();
        }
        ignore_user_abort(true);
        ob_start();

        //Add code here
        if (empty($validateMessage['message'])) {
            $this->processSearch($context, $message);
        }
        echo $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        die('end here');
        header($serverProtocol . ' 200 OK');
        // Disable compression (in case content length is compressed).
        header('Content-Encoding: none');
        header('Content-Length: ' . ob_get_length());
        // Close the connection.
        header('Connection: close');
        ob_end_flush();
        ob_flush();
        flush();
    }

    /**
     * @param $context
     * @param $message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function processSearch($context, $message)
    {
        $apiUrl = $this->_helper->getBapUri(Helper::ON_SEARCH, $context);
        $response = [];
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('type_id', "simple");
        $collection = $this->_helper->addCondition($message, $collection);
        $allItems = [];
        if (!empty($collection)) {
            foreach ($collection as $_collection) {
                $allItems[] = $this->_helper->prepareProduct($_collection);
            }
        }
        if (!empty($allItems)) {
            $provider = $this->_helper->getProvidersDetails($allItems);
            $response["context"] = $this->_helper->getContext($context);
            $response["message"]["catalog"]["bpp/descriptor"] = $this->_helper->getDescriptorDetails();
            $response["message"]["catalog"]["bpp/providers"][0] = $provider;
            $this->_helper->sendResponse($apiUrl, $response);
        } else {
            $this->_logger->info("No match found hence not firing on_search.");
        }
    }
}