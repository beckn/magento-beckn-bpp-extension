<?php

namespace Beckn\Core\Block\Adminhtml\Itemfulfillment;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Options
 * @package Beckn\CancelOrder\Block\Adminhtml\Itemfulfillment
 */
class Options extends AbstractFieldArray
{

    protected $fulfilmentStatus = null;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
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
        $this->addColumn('label', ['label' => __('Add Fulfillment Type'), 'size' => '50px', 'class' => 'fulfillment_type_field_text']);
        $this->addColumn('fulfillment_status', ['label' => __('Status'), 'size' => '40px', 'class' => 'required-entry', 'renderer' => $this->getFulfillmentStatusRenderer()]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add More');
    }

    protected function getFulfillmentStatusRenderer() {
        if (!$this->fulfilmentStatus) {
            $this->fulfilmentStatus = $this->getLayout()->createBlock(
                FulfillmentStatus::class, '', ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->fulfilmentStatus;
    }


    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        $fulfillmentStatus = $row->getFulfillmentStatus();
        $options = [];
        $options['option_' . $this->getFulfillmentStatusRenderer()->calcOptionHash($fulfillmentStatus)] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }
}
