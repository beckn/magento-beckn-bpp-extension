<?php

namespace Beckn\Core\Model\Config\Source;

use Beckn\Core\Helper\Data as Helper;

/**
 * Class EventType
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class EventType implements \Magento\Framework\Data\OptionSourceInterface
{

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
            Helper::KEY_SEARCH => __("Search"),
            Helper::ON_SEARCH => __("On Search"),
            Helper::KEY_SELECT => __("Select"),
            Helper::ON_SELECT => __("On Select"),
            Helper::KEY_INIT => __("Init"),
            Helper::ON_INIT => __("On Init"),
            Helper::KEY_CONFIRM => __("Confirm"),
            Helper::ON_CONFIRM => __("On Confirm"),
            Helper::KEY_STATUS => __("Status"),
            Helper::ON_STATUS => __("On Status"),
            Helper::KEY_SUPPORT => __("Support"),
            Helper::ON_SUPPORT => __("On Support"),
            Helper::KEY_UPDATE => __("Update"),
            Helper::ON_UPDATE => __("On Update"),
            Helper::KEY_TRACK => __("Track"),
            Helper::ON_TRACK => __("On Track"),
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