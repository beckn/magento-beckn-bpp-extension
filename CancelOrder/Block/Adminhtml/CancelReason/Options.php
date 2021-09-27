<?php

namespace Beckn\CancelOrder\Block\Adminhtml\CancelReason;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Options
 * @author Indglobal
 * @package Beckn\Checkout\Block\Adminhtml\CancelReason
 */
class Options extends AbstractFieldArray
{

    protected $_scopeConfig;

    /**
     * Options constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfigInterface;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('label', ['label' => __('Add Reason'), 'size' => '50px', 'class' => 'cancelreason_field_text']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add More');
    }
}
