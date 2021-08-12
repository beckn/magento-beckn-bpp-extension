<?php

namespace Beckn\Checkout\Model\Config\Source;

/**
 * Class PaymentMethod
 * @author Indglobal
 * @package Beckn\Checkout\Model\Config\Source
 */
class PaymentMethod implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $_scopeConfig;

    public function __construct(
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    )
    {
        $this->_paymentConfig = $paymentConfig;
        $this->_scopeConfig = $scopeConfigInterface;
    }

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
        $payments = $this->_paymentConfig->getActiveMethods();
        $option = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->_scopeConfig->getValue('payment/' . $paymentCode . '/title');
            if ($paymentCode != 'paypal_billing_agreement') {
                $option[] = array(
                    'label' => $paymentTitle,
                    'value' => $paymentCode
                );
            }
        }
        return $option;
    }
}