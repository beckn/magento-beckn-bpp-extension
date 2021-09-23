<?php

namespace Beckn\Core\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Store
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class Store extends AbstractSource
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    public function __construct(
        \Magento\Store\Model\System\Store $systemStore
    )
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $options = [];
        $options[] = [
            "value" => "",
            "label" => __("Disabled")
        ];
        $storeOptions = $this->_systemStore->getStoreValuesForForm(false, false);
        $allOptions = array_merge($options, $storeOptions);
        return $allOptions;
    }
}