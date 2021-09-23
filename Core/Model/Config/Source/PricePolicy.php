<?php

namespace Beckn\Core\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Beckn\Core\Model\ResourceModel\PricePolicy\CollectionFactory;

/**
 * Class PolicyType
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class PricePolicy extends AbstractSource
{

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->_collectionFactory = $collectionFactory;
    }

    public function getAllOptions()
    {
        /**
         * @var \Beckn\Core\Model\ResourceModel\PricePolicy\Collection $collection
         */
        $collection = $this->_collectionFactory->create();
        $options = [];
        $options[] = [
            'value' => "",
            'label' => __("Disable")
        ];
        /**
         * @var \Beckn\Core\Model\PricePolicy $item
         */
        foreach ($collection as $item) {
            $options[] =
                [
                    'value' => $item->getEntityId(),
                    'label' => $item->getName()
                ];
        }
        return $options;
    }
}