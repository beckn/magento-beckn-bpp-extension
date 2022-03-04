<?php

namespace Beckn\Core\Model\Config\Source;

use Beckn\Core\Helper\Data as Helper;

/**
 * Class FulfillmentStatusType
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class FulfillmentStatusType implements \Magento\Framework\Data\OptionSourceInterface
{
    const MANUAL_UPDATE = "manual_update";
    const FETCHED_NETWORK = "fetched_network";

    /**
     * Retrieve options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::MANUAL_UPDATE => __("Manual Update"),
            self::FETCHED_NETWORK => __("Fetched from downstream network"),
        ];
    }



    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}