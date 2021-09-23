<?php

namespace Beckn\Core\Model\Config\Source;

/**
 * Class FulfilmentType
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class FulfilmentType implements \Magento\Framework\Data\OptionSourceInterface
{
    const FULFILMENT_TYPE = [
        "store-pickup" => "Store Pickup",
        "home-delivery" => "Home Delivery"
    ];

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $fulfilmentType = self::FULFILMENT_TYPE;
        $option = [];
        foreach ($fulfilmentType as $key => $_type) {
            $option[] = [
                "value" => $key,
                "label" => $_type,
            ];
        }
        return $option;
    }
}