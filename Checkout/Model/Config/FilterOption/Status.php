<?php

namespace Beckn\Checkout\Model\Config\FilterOption;

/**
 * Class PaymentStatus
 * @author Indglobal
 * @package Beckn\Checkout\Model\Config\FilterOption
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    const AWAITING_PAYMENT = 0;
    const PAID_PAYMENT = 1;
    const PAID_PAYMENT_LABEL = 'Paid';
    const AWAITING_PAYMENT_LABEL = 'Awaiting Payment';

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
            self::AWAITING_PAYMENT => __(self::AWAITING_PAYMENT_LABEL),
            self::PAID_PAYMENT => __(self::PAID_PAYMENT_LABEL)
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