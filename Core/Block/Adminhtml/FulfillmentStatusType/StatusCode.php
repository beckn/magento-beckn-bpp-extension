<?php

namespace Beckn\Core\Block\Adminhtml\FulfillmentStatusType;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Beckn\Core\Helper\Data as Helper;

/**
 * Class StatusCode
 * @package Beckn\Core\Block\Adminhtml\FulfillmentStatusType
 */
class StatusCode extends Select {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml() {
        if (!$this->getOptions()) {
            $fulfilmentStatusCode = Helper::FULFILLMENT_STATUS_CODES;
            $storeOptions = [];
            foreach ($fulfilmentStatusCode as $code){
                $storeOptions[] = [
                    'label' => $code,
                    'value' => $code,
                ];
            }
            $this->setOptions($storeOptions);
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value) {
        return $this->setName($value);
    }

}
