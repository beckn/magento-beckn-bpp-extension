<?php

namespace Beckn\Core\Ui\Component\Listing\Column;

/**
 * Class EventData
 * @package Beckn\Core\Ui\Component\Listing\Column
 */
class EventData extends \Magento\Ui\Component\Listing\Columns\Column {

    /**
     * ResponseData constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ){
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['event_data'] = substr($item['event_data'],0,50);
            }
        }
        return $dataSource;
    }
}