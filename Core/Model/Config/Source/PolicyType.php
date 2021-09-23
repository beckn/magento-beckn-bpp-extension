<?php

namespace Beckn\Core\Model\Config\Source;

/**
 * Class PolicyType
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class PolicyType implements \Magento\Framework\Data\OptionSourceInterface
{
    const POLICY_TYPE = [
        "price_policy" => "price",
        "location_policy" => "location",
        "fulfillment_policy" => "fulfillment"
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
        $policyType = self::POLICY_TYPE;
        $option = [];
        foreach ($policyType as $key => $_type) {
            $option[] = [
                "value" => $key,
                "label" => $_type,
            ];
        }
        return $option;
    }
}