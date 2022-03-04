<?php

namespace Beckn\Core\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class CopyData
 * @package UsFx\DeepLinking\Ui\Component\Listing\Columns
 */
class CopyData extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Unblock constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_html'] = "<button class='action- scalable primary'><span>Copy request body</span></button>";
                $item[$fieldName . '_submitlabel'] = __('Unblock');
                $item[$fieldName . '_cancellabel'] = __('Cancel');
                $item[$fieldName . '_customerid'] = $item['entity_id'];
                $item[$fieldName . '_event_data'] = $item['event_data'];
            }
        }
        return $dataSource;
    }
}