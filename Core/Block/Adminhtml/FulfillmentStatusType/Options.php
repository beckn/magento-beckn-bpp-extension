<?php

namespace Beckn\Core\Block\Adminhtml\FulfillmentStatusType;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Options
 * @package Beckn\CancelOrder\Block\Adminhtml\FulfillmentStatusType
 */
class Options extends AbstractFieldArray
{

    protected $statusCode = null;
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
        $this->addColumn('status_code', ['label' => __('Status Code'), 'size' => '150px', 'class' => 'fulfillment_type_field_text', 'renderer' => $this->getStatusCodeRenderer()]);
        $this->addColumn('status_message', ['label' => __('Status Message'), 'size' => '150px', 'class' => 'required-entry']);
        $this->addColumn('parent_status', ['label' => __('Parent Status'), 'size' => '150px']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add More');
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStatusCodeRenderer() {
        if (!$this->statusCode) {
            $this->statusCode = $this->getLayout()->createBlock(
                StatusCode::class, '', ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->statusCode;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        $statusCode = $row->getStatusCode();
        $options = [];
        $options['option_' . $this->getStatusCodeRenderer()->calcOptionHash($statusCode)] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }
}
