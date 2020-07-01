<?php 
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Baseconnector
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Controller\Adminhtml\Basewidget;
use Magento\Framework\App\Filesystem\DirectoryList;

class Connector extends \Ves\BaseWidget\Controller\Adminhtml\Basewidget {

    protected function getDirPath( $dir_name = "") {
       return $this->_filesystem->getDirectoryWrite($dir_name)->getAbsolutePath();
    }
    /**
     * index action
     */ 
    public function execute() {
        //$elfinder_path  = $this->getDirPath(DirectoryList::APP).'code'.DIRECTORY_SEPARATOR.'Ves'.DIRECTORY_SEPARATOR.'BaseWidget'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'adminhtml'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'elfinder'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR;
        
        //include_once $elfinder_path.'elFinderConnector.class.php';
        //include_once $elfinder_path.'elFinder.class.php';
        //include_once $elfinder_path.'elFinderVolumeDriver.class.php';
        //include_once $elfinder_path.'elFinderVolumeLocalFileSystem.class.php';


        $dirMode = $this->_viewHelper->getConfig('general/dir_mode');
        $dirMode = $dirMode?$dirMode:0755;
        $fileMode = $this->_viewHelper->getConfig('general/file_mode');
        $fileMode = $fileMode?$fileMode:0644;
        $maxArcFilesSize = $this->_viewHelper->getConfig('general/max_arc_files_size');
        $maxArcFilesSize = $maxArcFilesSize?$maxArcFilesSize:0;
        $root_media_folder = $this->_viewHelper->getConfig('general/root_media');
        $path = $this->_mediaDirectory->getAbsolutePath();
        $url = $this->getBaseMediaUrl();

        if($root_media_folder) {
            $path2 = $this->_mediaDirectory->getAbsolutePath().'/'.$root_media_folder."/";
            $url2 = $this->getBaseMediaUrl().$root_media_folder."/";
            if(file_exists($path2)) {
               $path = $path2;
               $url = $url2;
            }
        }

        $opts = array(
            // 'debug' => true,
            'roots' => array(
                array(
                    'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
                    'path'          => $path,         // path to files (REQUIRED)
                    'URL'           => $url, // URL to files (REQUIRED)
                    'accessControl' => 'access'             // disable and hide dot starting files (OPTIONAL)
                )
            ),
            'dirMode' => $dirMode,
            'fileMode' => $fileMode,
            'maxArcFilesSize' => $maxArcFilesSize
        );

        // run elFinder
        $connector = new \Ves\BaseWidget\Classes\Elfinder\ElFinderConnector(new \Ves\BaseWidget\Classes\ElFinder($opts));
        $connector->run();

        exit();
    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_BaseWidget::manage_media');
    }
    
}
?>