<?php

namespace Beckn\Core\Block\Adminhtml\Itemfulfillment;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class FulfillmentStatus
 * @package Beckn\Core\Block\Adminhtml\Itemfulfillment
 */
class FulfillmentStatus extends Select {

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
        Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, array $data = []
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
            $storeOptions = [
                [
                    'label' => 'Yes',
                    'value' => 1,
                ],
                [
                    'label' => 'No',
                    'value' => 0,
                ]
            ];
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
