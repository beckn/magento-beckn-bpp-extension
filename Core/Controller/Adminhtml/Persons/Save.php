<?php

namespace Beckn\Core\Controller\Adminhtml\Persons;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Beckn\Core\Model\PersonDetailsFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;

/**
 * Class Save
 * @package Beckn\Core\Controller\Adminhtml\Persons
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var PersonDetailsFactory
     */
    protected $_personDetailsFactory;

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Save constructor.
     * @param UploaderFactory $fileUploaderFactory
     * @param Action\Context $context
     * @param PersonDetailsFactory $personDetailsFactory
     * @param AdapterFactory $adapterFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Action\Context $context,
        PersonDetailsFactory $personDetailsFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem

    ) {

        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_personDetailsFactory = $personDetailsFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            try{
                $uploaderFactory = $this->_fileUploaderFactory->create(['fileId' => 'image']);
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploaderFactory->setAllowRenameFiles(false);
                $uploaderFactory->setFilesDispersion(false);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('person/images/');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                $imagePath = 'person/images/'.$result['file'];
                $data['image'] = $imagePath;
            } catch (\Exception $e) {
            }
        }
        $model = $this->_personDetailsFactory->create();
        $model->setData($data);
        if($model->save()){
            $this->messageManager->addSuccessMessage(__('You saved the data.'));
        }else{
            $this->messageManager->addErrorMessage(__('Data was not saved.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('beckn/persons/index');
        return $resultRedirect;
    }
}