<?php

namespace Beckn\Core\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ButtonSubscribe
 * @author Indglobal
 * @package Beckn\Core\Block\Adminhtml\System\Config
 */
class ButtonSubscribe extends Field
{
    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('system/config/subscribe.phtml');
        return $this;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl(
            'beckn/index/subscribe'
        );
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return "beckn_subscribe";
    }
}