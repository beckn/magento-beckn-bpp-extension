<?php

namespace Beckn\Core\Model\Config\Source;

/**
 * Class ShippingOption
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class ShippingOption implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    protected $_shippingAllmethods;

    /**
     * ShippingOption constructor.
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods
     */
    public function __construct(
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods
    )
    {
        $this->_shippingAllmethods = $shippingAllmethods;
    }

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return $this->_shippingAllmethods->toOptionArray();;
    }
}