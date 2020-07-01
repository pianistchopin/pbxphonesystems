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

class ListLiveProfile implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                   $context          
     * @param \Ves\Themesettings\Model\System\Config\Source\Css\Font\GoogleFonts $_googleFontModel 
     * @param \Ves\Themesettings\Helper\Theme                                    $ves              
     * @param array                                                              $data             
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem
        ){
        $this->_filesystem = $filesystem;
        
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $output = [
                        ["value"=>"", "label"=> __("-- Select A Profile --")]
                      ];

        $path = $this->getPubDirPath() .'pagebuilder'.DIRECTORY_SEPARATOR.'livecss'.DIRECTORY_SEPARATOR.'profiles'.DIRECTORY_SEPARATOR;

        if( $path && is_dir($path) ) {
            $files = glob( $path.'*' );
            foreach( $files as $dir ){
                if( preg_match("#.xml#", $dir)){
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