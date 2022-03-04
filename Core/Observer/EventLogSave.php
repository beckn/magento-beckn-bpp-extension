<?php

namespace Beckn\Core\Observer;

use Beckn\Core\Helper\Data as Helper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Beckn\Core\Model\BecknEventLogFactory;
use Psr\Log\LoggerInterface;

/**
 * Class EventLogSave
 * @author Indglobal
 * @package Beckn\Core\Observer
 */
class EventLogSave implements ObserverInterface
{
    /**
     * @var BecknEventLogFactory
     */
    protected $_becknEventLogFactory;
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Webapi\Request
     */
    protected $_request;

    /**
     * EventLogSave constructor.
     * @param BecknEventLogFactory $becknEventLogFactory
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Webapi\Request $request
     */
    public function __construct(
        BecknEventLogFactory $becknEventLogFactory,
        Helper $helper,
        LoggerInterface $logger,
        \Magento\Framework\Webapi\Request $request
    )
    {
        $this->_becknEventLogFactory = $becknEventLogFactory;
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_request = $request;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $headers = apache_request_headers();
        $auth = $this->_request->getHeader(Helper::AUTHORIZATION_KEY);
        $proxyAuth = $headers[Helper::PROXY_AUTHORIZATION_KEY] ?? "";
        $this->_logger->info("Observer Authorization => ".$auth);
        $this->_logger->info("Observer Proxy Authorization => ".$proxyAuth);
        $observerData = $observer->getData();
        $observerData['header_authorization'] = $auth;
        $observerData['proxy_header_authorization'] = $proxyAuth;
        /**
         * @var \Beckn\Core\Model\BecknEventLog $becknEventLog
         */
        $becknEventLog = $this->_becknEventLogFactory->create();
        $becknEventLog->setData($observerData);
        $becknEventLog->save();
        return $this;
    }
}