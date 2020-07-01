<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ves\BaseWidget\Model\Config;

use Magento\Framework\Module\Dir;
use Magento\Widget\Model\Config;

class Reader extends \Magento\Widget\Model\Config\Reader
{
    protected $_ves_widgets_folder = "widgets";
    protected $_ves_widgets_path = null;
    protected $_ves_widgets = null;
    /**
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param Converter $converter
     * @param \Magento\Framework\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     * @param string $folderWidget
     */
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Widget\Model\Config\Converter $converter,
        \Magento\Framework\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'widget.xml',
        $idAttributes = [],
        $domDocumentClass = \Magento\Framework\Config\Dom::class,
        $defaultScope = 'global',
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        $folderWidget = 'widgets'

    ) {
        $etcDir = $moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR, 'Ves_BaseWidget');
        $this->_ves_widgets_folder = $folderWidget;
        $this->_ves_widgets_path = $etcDir."/".$folderWidget."/";
        $this->_ves_widgets = $this->getFilesInFolder( $this->_ves_widgets_path );
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
    public function getWidgetsPath() {
        return $this->_ves_widgets_path;
    }
    public function getWidgetsFolder() {
        return $this->_ves_widgets_folder;
    }
    public function getFilesInFolder( $folder = "") {
        if(!$folder && $this->_ves_widgets_path) {
            $folder = $this->_ves_widgets_path;
        }
        if($folder && is_dir($folder)) {
            $result = [];
            $file_ext = ".xml";

            $dirs = glob( $folder.'*'.$file_ext );
            if($dirs) { //load 
                foreach($dirs as $dir) {
                    $file_name = basename( $dir );
                    $result[] = $file_name;
                }
            }
            return $result;
        }
        return false;
    }
    /**
     * Load configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null)
    {
        $output = parent::read($scope);
        if($this->_ves_widgets) {
            $output2 = [];
            $scope = $scope ?: $this->_defaultScope;
            foreach($this->_ves_widgets as $val ) {
                $widget_file_name = $this->_ves_widgets_folder."/".$val;
                $fileList = $this->_fileResolver->get($widget_file_name, $scope);
                if (count($fileList)) {
                    $output2 = $this->_readFiles($fileList);
                    $output = array_merge($output, $output2);
                }
            }
        }
        return $output;
        
    }
}
