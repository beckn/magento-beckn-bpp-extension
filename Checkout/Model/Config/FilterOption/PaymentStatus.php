<?php

namespace Beckn\Checkout\Model\Config\FilterOption;

/**
 * Class PaymentStatus
 * @author Indglobal
 * @package Beckn\Checkout\Model\Config\FilterOption
 */
class PaymentStatus implements \Magento\Framework\Option\ArrayInterface
{
    const PAYMENT_PAID = 'paid';
    const PAYMENT_NOT_PAID = 'not-paid';
    const PAYMENT_PAID_LABEL = 'PAID';
    const PAYMENT_NOT_PAID_LABEL = 'NOT-PAID';

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
            self::PAYMENT_NOT_PAID => __(self::PAYMENT_NOT_PAID_LABEL),
            self::PAYMENT_PAID => __(self::PAYMENT_PAID_LABEL)
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