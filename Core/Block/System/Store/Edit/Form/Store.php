<?php

declare(strict_types=1);

namespace Beckn\Core\Block\System\Store\Edit\Form;

use Magento\Framework\DataObject;
use Beckn\Core\Model\ResourceModel\FulfillmentPolicy\CollectionFactory as FulfillmentCollectionFactory;
use Beckn\Core\Model\ResourceModel\LocationPolicy\CollectionFactory as LocationPolicyCollectionFactory;

/**
 * Class AllowedTotalCalculator
 * @package UsFx\GiftCard\Model\GiftCardExtension\Quote
 */
class Store extends \Magento\Backend\Block\System\Store\Edit\Form\Store
{
    /**
     * @var FulfillmentCollectionFactory
     */
    protected $_fulfillmentCollectionFactory;

    /**
     * @var LocationPolicyCollectionFactory
     */
    protected $_locationCollectionFactory;
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_country;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\GroupFactory $groupFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        FulfillmentCollectionFactory $fulfillmentCollectionFactory,
        LocationPolicyCollectionFactory $locationCollectionFactory,
        \Magento\Directory\Model\Config\Source\Country $country,
        array $data = []
    )
    {
        $this->_fulfillmentCollectionFactory = $fulfillmentCollectionFactory;
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->_country = $country;
        parent::__construct($context, $registry, $formFactory, $groupFactory, $websiteFactory, $data);
    }

    /**
     * Prepare store specific fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @return void
     */
    protected function _prepareStoreFieldset(\Magento\Framework\Data\Form $form)
    {
        $storeModel = $this->_coreRegistry->registry('store_data');
        $postData = $this->_coreRegistry->registry('store_post_data');
        if ($postData) {
            $storeModel->setData($postData['store']);
        }
        $fieldset = $form->addFieldset('store_fieldset', ['legend' => __('Store View Information')]);
        $storeAction = $this->_coreRegistry->registry('store_action');
        if ($storeAction == 'edit' || $storeAction == 'add') {
            $fieldset->addField(
                'store_group_id',
                'select',
                [
                    'name' => 'store[group_id]',
                    'label' => __('Store'),
                    'value' => $storeModel->getGroupId(),
                    'values' => $this->_getStoreGroups(),
                    'required' => true,
                    'disabled' => $storeModel->isReadOnly()
                ]
            );
            $fieldset = $this->prepareGroupIdField($form, $storeModel, $fieldset);
        }
        $fieldset->addField(
            'store_name',
            'text',
            [
                'name' => 'store[name]',
                'label' => __('Name'),
                'value' => $storeModel->getName(),
                'required' => true,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_code',
            'text',
            [
                'name' => 'store[code]',
                'label' => __('Code'),
                'value' => $storeModel->getCode(),
                'required' => true,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $isDisabledStatusField = $storeModel->isReadOnly()
            || ($storeModel->getId() && $storeModel->isDefault() && $storeModel->isActive());
        $fieldset->addField(
            'store_is_active',
            'select',
            [
                'name' => 'store[is_active]',
                'label' => __('Status'),
                'value' => $storeModel->isActive(),
                'options' => [0 => __('Disabled'), 1 => __('Enabled')],
                'required' => true,
                'disabled' => $isDisabledStatusField
            ]
        );
        if ($isDisabledStatusField) {
            $fieldset->addField(
                'store_is_active_hidden',
                'hidden',
                [
                    'name' => 'store[is_active]',
                    'value' => $storeModel->isActive(),
                ]
            );
        }
        $fieldset->addField(
            'store_sort_order',
            'text',
            [
                'name' => 'store[sort_order]',
                'label' => __('Sort Order'),
                'value' => $storeModel->getSortOrder(),
                'required' => false,
                'class' => 'validate-number validate-zero-or-greater',
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_gps_location',
            'text',
            [
                'name' => 'store[gps_location]',
                'label' => __('Gps Location'),
                'value' => $storeModel->getGpsLocation(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_location_id',
            'select',
            [
                'name' => 'store[location_id]',
                'label' => __('Dynamic Location Policy'),
                'values' => $this->getLocationPolicy(),
                'value' => $storeModel->getLocationId(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_fulfillment_id',
            'select',
            [
                'id' => 'fulfillment_id',
                'name' => 'store[fulfillment_id]',
                'label' => __('Fulfillment Policy'),
                'values' => $this->getFulfillmentPolicy(),
                'value' => $storeModel->getFulfillmentId(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_door',
            'text',
            [
                'id' => 'address_door',
                'name' => 'store[address_door]',
                'label' => __('Address Door'),
                'value' => $storeModel->getAddressDoor(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_name',
            'text',
            [
                'id' => 'address_name',
                'name' => 'store[address_name]',
                'label' => __('Address Name'),
                'value' => $storeModel->getAddressName(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_building',
            'text',
            [
                'id' => 'address_building',
                'name' => 'store[address_building]',
                'label' => __('Address Building'),
                'value' => $storeModel->getAddressBuilding(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_street',
            'text',
            [
                'id' => 'address_street',
                'name' => 'store[address_street]',
                'label' => __('Address Street'),
                'value' => $storeModel->getAddressStreet(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_locality',
            'text',
            [
                'id' => 'address_locality',
                'name' => 'store[address_locality]',
                'label' => __('Address Locality'),
                'value' => $storeModel->getAddressLocality(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_state',
            'text',
            [
                'id' => 'address_state',
                'name' => 'store[address_state]',
                'label' => __('Address State'),
                'value' => $storeModel->getAddressState(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_country',
            'select',
            [
                'id' => 'address_country',
                'name' => 'store[address_country]',
                'label' => __('Address Country'),
                "values" => $this->_country->toOptionArray(),
                'value' => $storeModel->getAddressCountry(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_area_code',
            'text',
            [
                'id' => 'address_area_code',
                'name' => 'store[address_area_code]',
                'label' => __('Address Area Code'),
                'value' => $storeModel->getAddressAreaCode(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_station_code',
            'text',
            [
                'id' => 'address_station_code',
                'name' => 'store[address_station_code]',
                'label' => __('Address Station Code'),
                'value' => $storeModel->getAddressAreaCode(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_city_name',
            'text',
            [
                'id' => 'address_city_name',
                'name' => 'store[address_city_name]',
                'label' => __('Address City Name'),
                'value' => $storeModel->getAddressCityName(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_address_city_code',
            'text',
            [
                'id' => 'address_city_code',
                'name' => 'store[address_city_code]',
                'label' => __('Address City Code'),
                'value' => $storeModel->getAddressCityCode(),
                'required' => false,
                'disabled' => $storeModel->isReadOnly()
            ]
        );
        $fieldset->addField(
            'store_is_default',
            'hidden',
            ['name' => 'store[is_default]', 'no_span' => true, 'value' => $storeModel->getIsDefault()]
        );
        $fieldset->addField(
            'store_store_id',
            'hidden',
            [
                'name' => 'store[store_id]',
                'no_span' => true,
                'value' => $storeModel->getId(),
                'disabled' => $storeModel->isReadOnly()
            ]
        );
    }

    /**
     * Retrieve list of store groups
     *
     * @return array
     */
    protected function _getStoreGroups()
    {
        $websites = $this->_websiteFactory->create()->getCollection();
        $allgroups = $this->_groupFactory->create()->getCollection();
        $groups = [];
        foreach ($websites as $website) {
            $values = [];
            foreach ($allgroups as $group) {
                if ($group->getWebsiteId() == $website->getId()) {
                    $values[] = ['label' => $group->getName(), 'value' => $group->getId()];
                }
            }
            $groups[] = ['label' => $website->getName(), 'value' => $values];
        }
        return $groups;
    }

    /**
     * Prepare group id field in the fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param \Magento\Store\Model\Store $storeModel
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    private function prepareGroupIdField(
        \Magento\Framework\Data\Form $form,
        \Magento\Store\Model\Store $storeModel,
        \Magento\Framework\Data\Form\Element\Fieldset $fieldset
    )
    {
        if ($storeModel->getId() && $storeModel->getGroup()->getDefaultStoreId() == $storeModel->getId()) {
            if ($storeModel->getGroup() && $storeModel->getGroup()->getStoresCount() > 1) {
                $form->getElement('store_group_id')->setDisabled(true);

                $fieldset->addField(
                    'store_hidden_group_id',
                    'hidden',
                    ['name' => 'store[group_id]', 'no_span' => true, 'value' => $storeModel->getGroupId()]
                );
            } else {
                $fieldset->addField(
                    'store_original_group_id',
                    'hidden',
                    [
                        'name' => 'store[original_group_id]',
                        'no_span' => true,
                        'value' => $storeModel->getGroupId()
                    ]
                );
            }
        }
        return $fieldset;
    }

    /**
     * @return array
     */
    public function getLocationPolicy(){
        /**
         * @var \Beckn\Core\Model\ResourceModel\LocationPolicy\Collection $collection
         */
        $collection = $this->_locationCollectionFactory->create();
        $options = [];
        $options[] = [
            "value" => "",
            "label" => __("Disabled"),
        ];
        foreach ($collection as $item){
            $options[] = [
                "label" => $item->getName(),
                "value" => $item->getId(),
            ];
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getFulfillmentPolicy(){
        /**
         * @var \Beckn\Core\Model\ResourceModel\FulfillmentPolicy\Collection $collection
         */
        $collection = $this->_fulfillmentCollectionFactory->create();
        $options = [];
        $options[] = [
            "value" => "",
            "label" => __("Disabled"),
        ];
        foreach ($collection as $item){
            $options[] = [
                "label" => $item->getName(),
                "value" => $item->getId(),
            ];
        }
        return $options;
    }
}