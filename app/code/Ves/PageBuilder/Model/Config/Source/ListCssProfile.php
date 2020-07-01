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
 * @package    Ves_PageBuilder
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Model\Config\Source;
use Magento\Framework\App\Filesystem\DirectoryList;

class ListCssProfile implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                   $context          
     * @param \Ves\Themesettings\Model\System\Config\Source\Css\Font\GoogleFonts $_googleFontModel 
     * @param \Ves\Themesettings\Helper\Theme                                    $ves              
     * @param array                                                              $data             
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Ves\PageBuilder\Helper\Data $dataHelper
        ){
        $this->_filesystem = $filesystem;
        $this->_dataHelper = $dataHelper;
        
    }


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $output = [
                        ["value"=>"", "label"=> __("-- Select A Custom Css File --")]
                      ];

        $path_css_profile = $this->_dataHelper->getConfig("general/path_css_profile", null, "media/pagebuilder/livecss", "veslivecss");

        $path_css_profile = str_replace("/", DIRECTORY_SEPARATOR, $path_css_profile);// pub/pagebuilder/livecss/customize/
        $path = $this->getPubDirPath() . $path_css_profile . DIRECTORY_SEPARATOR;

        $default_path_css_profile = str_replace("/", DIRECTORY_SEPARATOR, "pagebuilder/livecss/customize");
        $default_path = $this->getPubDirPath() . $default_path_css_profile . DIRECTORY_SEPARATOR;

        if( $path && is_dir($path) ) {
            $files = glob( $path.'*.css' );
            foreach( $files as $dir ){
                if( preg_match("#.css#", $dir)){
                    $file_name = str_replace("","",basename( $dir ) );
                    $output[] = array('label' => ucfirst($file_name),
                                      'value' => $file_name);
                }
            }
        } elseif($default_path && is_dir($default_path)) {
            $files = glob( $default_path.'*.css' );
            foreach( $files as $dir ){
                if( preg_match("#.css#", $dir)){
                    $file_name = str_replace("","",basename( $dir ) );
                    $output[] = array('label' => ucfirst($file_name),
                                      'value' => $file_name);
                }
            }
        }

        return $output;
    }

    public function getPubDirPath( $path_type = "") {
        $path_type = $path_type?$path_type:DirectoryList::PUB;
        return $this->_filesystem->getDirectoryRead($path_type)->getAbsolutePath();
    }
}