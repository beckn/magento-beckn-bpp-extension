<?php

namespace Beckn\Core\Block\Adminhtml\Persons;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Form
 * @package Beckn\Core\Block\Adminhtml\Persons
 */
class Form extends \Magento\Backend\Block\Template {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;


    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return \Beckn\Core\Model\PersonDetails
     */
    public function getPersonDetails(){
        return $this->_coreRegistry->registry("row_data");
    }

    /**
     * @return string
     */
    public function getFormAction(){
        return $this->_urlBuilder->getUrl("beckn/persons/save");
    }

    /**
     * @return array|array[]
     */
    public function getValueType(){
        return $this->_valueType->toOptionArray();
    }
}