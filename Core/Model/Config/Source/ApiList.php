<?php

namespace Beckn\Core\Model\Config\Source;

/**
 * Class ApiList
 * @author Indglobal
 * @package Beckn\Core\Model\Config\Source
 */
class ApiList implements \Magento\Framework\Data\OptionSourceInterface
{
    const API_LIST = [
        "search" => "Search",
        "select" => "Select",
        "init" => "Init",
        "confirm" => "Confirm",
        "update" => "Update",
        "status" => "Status",
        "track" => "Track",
        "cancel" => "Cancel",
        "rating" => "Rating",
        "support" => "Support",
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
        $fulfilmentType = self::API_LIST;
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