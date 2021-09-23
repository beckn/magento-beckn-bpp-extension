<?php

namespace Beckn\Core\Model\Config\Source;

/**
 * Class ValueType
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class ValueType implements \Magento\Framework\Data\OptionSourceInterface
{
    const TYPE_LITERAL = 'literal';
    const TYPE_BODY_PATH = 'body_path';

    const TYPE_LITERAL_TITLE = 'Literal';
    const TYPE_BODY_PATH_TITLE = 'Request Body path';

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
        $policyType = [
            self::TYPE_BODY_PATH => self::TYPE_BODY_PATH_TITLE,
            self::TYPE_LITERAL => self::TYPE_LITERAL_TITLE,
        ];
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