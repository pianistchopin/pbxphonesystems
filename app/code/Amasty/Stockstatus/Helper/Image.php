<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * FileTypes
     *
     * Image format to upload
     */
    const FILE_TYPES = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $fileUploaderFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * File check
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem\Io\FileFactory $ioFileFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->ioFile = $ioFileFactory->create();
        $this->storeManager = $storeManager;
    }

    public function getStatusIconUrl($optionId, $storeId = null)
    {
        $iconUrl = '';
        $name = $this->getFileName($optionId);

        if ($name) {
            $path = $this->storeManager->getStore($storeId)->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
            $iconUrl = $path . 'amasty/stockstatus/' . $name;
        }

        return $iconUrl;
    }

    public function delete($optionId)
    {
        $path = $this->getMediaPath();
        $name = $this->getFileName($optionId);

        if ($name) {
            $path .= $name;

            if ($this->ioFile->fileExists($path)) {
                $this->ioFile->rm($path);
            }
        }
    }

    /**
     * @param int $optionId
     * @param array $file
     * @return $this
     */
    public function uploadImage($optionId, $file)
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'amasty/stockstatus/'
        );
        $this->ioFile->checkAndCreateFolder($path);

        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->fileUploaderFactory->create(['fileId' => $file]);
            $explodeName = explode('.', $file['name']);
            $fileFormat = end($explodeName);
            $uploader->setAllowedExtensions(self::FILE_TYPES);
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path, $optionId . '.' . $fileFormat);
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\MediaStorage\Model\File\Uploader::TMP_NAME_EMPTY) {
                $this->_logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * @param int $optionId
     * @return string
     */
    private function getFileName($optionId)
    {
        $name = '';
        $path = $this->getMediaPath();
        $this->ioFile->checkAndCreateFolder($path);

        foreach (self::FILE_TYPES as $fileType) {
            if ($this->ioFile->fileExists($path . $optionId . "." . $fileType)) {
                $name = $optionId . "." . $fileType;
                break;
            }
        }

        return $name;
    }

    private function getMediaPath()
    {
        return $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'amasty/stockstatus/'
        );
    }
}
