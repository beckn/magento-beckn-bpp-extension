<?php

namespace Beckn\Bpp\Model\Repository\Cart;

use Beckn\Bpp\Helper\Data as Helper;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SearchRepository
 * @author Indglobal
 * @package Beckn\Bpp\Model\Repository\Search
 */
class CartRepository implements \Beckn\Bpp\Api\CartRepositoryInterface
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
     * @var \Beckn\Bpp\Model\ManageCart
     */
    protected $_manageCart;

    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $_cartItemInterfaceFactory;

    /**
     * CartRepository constructor.
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param \Beckn\Bpp\Model\ManageCart $manageCart
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        \Beckn\Bpp\Model\ManageCart $manageCart,
        CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_manageCart = $manageCart;
        $this->_quoteRepository = $quoteRepository;
        $this->_cartItemInterfaceFactory = $cartItemInterfaceFactory;
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     * @throws LocalizedException
     */
    public function manageCart($context, $message)
    {
        //$this->processCart($context, $message); die;
//        $this->_logger->info(json_encode($context));
//        $this->_logger->info("Received Search message");
//        $this->_logger->info(json_encode($message));
//        $this->_logger->info("Received context");
        //$validateMessage = $this->_helper->validateApiRequest($context, $message);
        $validateMessage = [];
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
            $this->processCart($context, $message);
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
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function processCart($context, $message)
    {
        $onSelectResponse = [];
        $onSelectResponse["context"] = $this->_helper->getContext($context);
        $apiUrl = $this->_helper->getBapUri(Helper::ON_SELECT, $context);
        try {
            $quoteId = $this->_manageCart->getQuoteId($context["transaction_id"]);
            $quote = $this->_quoteRepository->getActive($quoteId);
            $quote = $this->_manageCart->removeQuoteItem($quote, $message["selected"]['items']);
            /**
             * @var CartItemInterface[] $quoteItems
             */
            $quoteItems = $quote->getItems();
            $alreadyAdded = $this->_manageCart->getAllAvailableItem($quote);
            foreach ($message["selected"]['items'] as $_item) {
                if ($_item['quantity']["count"] > 0) {
                    $cartItem = $this->prepareCartItem($_item, $alreadyAdded, $quoteId);
                    $quoteItems[] = $cartItem;
                    $quote->setItems($quoteItems);
                }
            }
            $this->_quoteRepository->save($quote);
            $quote->collectTotals();
            $onSelectResponse["message"]["selected"] = $this->_manageCart->prepareOnSelectResponse($quote, $context);
        } catch (NoSuchEntityException $ex) {
            $onSelectResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        } catch (\Exception $ex) {
            $onSelectResponse["error"] = $this->_helper->acknowledgeError("", $ex->getMessage());
        }
        return $this->_helper->sendResponse($apiUrl, $onSelectResponse);
    }

    /**
     * @param array $item
     * @param array $alreadyAdded
     * @param int $quoteId
     * @return CartItemInterface
     */
    private function prepareCartItem(array $item, array $alreadyAdded, int $quoteId)
    {
        /**
         * @var CartItemInterface $cartItem
         */
        $cartItem = $this->_cartItemInterfaceFactory->create();
        if (array_key_exists($item["id"], $alreadyAdded)) {
            $cartItem->setItemId($alreadyAdded[$item['id']]);
        } else {
            $cartItem->setSku($item['id']);
        }
        $cartItem->setQty($item['quantity']["count"]);
        $cartItem->setQuoteId($quoteId);
        return $cartItem;
    }
}