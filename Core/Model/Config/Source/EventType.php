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
            Helper::KEY_SEARCH => __("search"),
            Helper::ON_SEARCH => __("on_search"),
            Helper::KEY_SELECT => __("select"),
            Helper::ON_SELECT => __("on_select"),
            Helper::KEY_INIT => __("init"),
            Helper::ON_INIT => __("on_init"),
            Helper::KEY_CONFIRM => __("confirm"),
            Helper::ON_CONFIRM => __("on_confirm"),
            Helper::KEY_STATUS => __("status"),
            Helper::ON_STATUS => __("on_status"),
            Helper::KEY_SUPPORT => __("support"),
            Helper::ON_SUPPORT => __("on_support"),
            Helper::KEY_UPDATE => __("update"),
            Helper::ON_UPDATE => __("on_update"),
            Helper::KEY_TRACK => __("track"),
            Helper::ON_TRACK => __("on_track"),
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