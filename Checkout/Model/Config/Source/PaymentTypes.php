<?php

namespace Beckn\Checkout\Model\Config\Source;

/**
 * Class PaymentTypes
 * @author Indglobal
 * @package Beckn\Checkout\Model\Config\Source
 */
class PaymentTypes implements \Magento\Framework\Data\OptionSourceInterface
{
    const PAYMENT_TYPE = [
        "ON-ORDER" => "ON-ORDER",
        "PRE-FULFILLMENT" => "PRE-FULFILLMENT",
        "ON-FULFILLMENT" => "ON-FULFILLMENT",
        "POST-FULFILLMENT" => "POST-FULFILLMENT",
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
        $fulfilmentType = self::PAYMENT_TYPE;
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