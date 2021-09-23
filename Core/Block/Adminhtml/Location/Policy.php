<?php

namespace Beckn\Core\Block\Adminhtml\Location;

use Beckn\Core\Model\Config\Source\ValueType;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Policy
 * @author Indglobal
 * @package Beckn\Core\Block\Adminhtml\Location
 */
class Policy extends \Magento\Backend\Block\Template {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var ValueType
     */
    protected $_valueType;

    /**
     * Policy constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ValueType $valueType
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        ValueType $valueType,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_valueType = $valueType;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return \Beckn\Core\Model\LocationPolicy
     */
    public function getLocationPolicy(){
        return $this->_coreRegistry->registry("row_data");
    }

    /**
     * @return string
     */
    public function getFormAction(){
        return $this->_urlBuilder->getUrl("beckn/location/save");
    }

    /**
     * @return array|array[]
     */
    public function getValueType(){
        return $this->_valueType->toOptionArray();
    }
}