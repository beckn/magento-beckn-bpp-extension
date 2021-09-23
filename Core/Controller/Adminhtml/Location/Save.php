<?php

namespace Beckn\Core\Controller\Adminhtml\Location;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Beckn\Core\Api\Data\PolicyRequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Beckn\Core\Model\Config\Source\PolicyType;


/**
 * Class Save
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Location
 */
class Save extends \Beckn\Core\Controller\Adminhtml\PolicySaveController {

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $policyType = PolicyType::POLICY_TYPE['location_policy'];
        $this->savePolicy($policyType);
        return $this->_redirect('beckn/location/index');
    }
}