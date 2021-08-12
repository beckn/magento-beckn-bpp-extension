<?php

namespace Beckn\Bpp\Block\Adminhtml\System\Config;

/**
 * Class Date
 * @author Indoglobal
 * @package Beckn\Bpp\Block\Adminhtml\System\Config
 */
class Date extends \Magento\Config\Block\System\Config\Form\Field
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setDateFormat(\Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
        $element->setTimeFormat('HH:mm:ss');
        $element->setShowsTime(true);
        return parent::render($element);
    }
}