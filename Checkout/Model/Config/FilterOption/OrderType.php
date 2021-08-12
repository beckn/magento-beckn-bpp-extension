<?php

namespace Beckn\Checkout\Model\Config\FilterOption;

/**
 * Class OrderType
 * @author Indglobal
 * @package Beckn\Checkout\Model\Config\FilterOption
 */
class OrderType implements \Magento\Framework\Option\ArrayInterface
{
    const DEFAULT_STORE = 'store';
    const BECKN_STORE = 'beckn';
    const DEFAULT_STORE_LABEL = 'Store';
    const BECKN_STORE_LABEL = 'Beckn';

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
            self::DEFAULT_STORE => __(self::DEFAULT_STORE_LABEL),
            self::BECKN_STORE => __(self::BECKN_STORE_LABEL)
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