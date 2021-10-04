<?php

namespace Beckn\Select\Model\Repository\Cart;

use Beckn\Core\Helper\Data as Helper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Form\FormKey;

/**
 * Class SearchRepository
 * @author Indglobal
 * @package Beckn\Select\Model\Repository\Search
 */
class CartRepository implements \Beckn\Select\Api\CartRepositoryInterface
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
     * @var \Beckn\Core\Model\ManageCart
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
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var FormKey
     */
    protected $_formKey;

    /**
     * CartRepository constructor.
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param \Beckn\Core\Model\ManageCart $manageCart
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger,
        \Beckn\Core\Model\ManageCart $manageCart,
        CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory,
        \Magento\Checkout\Model\Cart $cart,
        FormKey $formKey
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_manageCart = $manageCart;
        $this->_quoteRepository = $quoteRepository;
        $this->_cartItemInterfaceFactory = $cartItemInterfaceFactory;
        $this->_cart = $cart;
        $this->_formKey = $formKey;
    }

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string|void
     * @throws LocalizedException
     * @throws \SodiumException
     */
    public function manageCart($context, $message)
    {
        $authStatus = $this->_helper->validateAuth($context, $message);
        if (!$authStatus) {
            echo $this->_helper->unauthorizedResponse();
            exit();
        }
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
            $this->processCart($context, $message);
        }
        echo $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
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
            $this->_cart->setQuote($quote);
            $this->_cart->truncate();
            foreach ($message["order"]['items'] as $_item) {
                if ($_item['quantity']["count"] > 0) {
                    $itemSku = $_item["id"];
                    $product = $this->_helper->getProductBySku($itemSku);
                    $params = array(
                        'form_key' => $this->_formKey->getFormKey(),
                        'product' => $product->getId(),
                        'qty' => $_item['quantity']["count"]
                    );
                    $this->_cart->addProduct($product, $params);
                }
            }
            $this->_cart->save();
            $quote->collectTotals();
            $prepareOnSelect = $this->_manageCart->prepareOnSelectResponse($quote, $context);
            $onSelectResponse["message"]["order"] = $prepareOnSelect["selected_data"];
            $onSelectResponse["message"]["order"]["provider_location"] = $prepareOnSelect["provider_location"];
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