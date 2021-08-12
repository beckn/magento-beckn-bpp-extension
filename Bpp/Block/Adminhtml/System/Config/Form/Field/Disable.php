<?php

namespace Beckn\Bpp\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Disable
 * @author Indoglobal
 * @package Beckn\Bpp\Block\System\Config\Form\Field
 */
class Disable extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setDisabled('disabled');
        return $element->getElementHtml();
    }
}