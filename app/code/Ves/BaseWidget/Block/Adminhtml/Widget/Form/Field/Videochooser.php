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
 * @package    Ves_BaseWidget
 * @copyright  Copyright (c) 2018 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Adminhtml\Widget\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Escaper;

class Videochooser extends Template implements RendererInterface
{

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory
     */
    protected $_factoryCollection;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_factoryElement;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    protected $_storeManager;
    protected $_dataHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                $context           
     * @param \Magento\Framework\Data\Form\Element\Factory           $factoryElement    
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection 
     * @param Escaper                                                $escaper           
     * @param \Ves\BaseWidget\Helper\Data                            $dataHelper     
     * @param \Magento\Framework\View\LayoutInterface                $layout            
     * @param \Magento\Backend\Helper\Data                           $backendData       
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        /*Escaper $escaper,*/
        \Ves\BaseWidget\Helper\Data $dataHelper,
        /*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
        /*\Magento\Framework\View\LayoutInterface $layout,*/
        \Magento\Backend\Helper\Data $backendData
        ){
        $this->_factoryElement = $factoryElement;
        $this->_factoryCollection = $factoryCollection;
        /*$this->_escaper = $escaper;
        $this->_layout = $layout;*/
        $this->_backendData = $backendData;
        $this->_dataHelper = $dataHelper;
        /*$this->_storeManager = $storeManager;*/
        parent::__construct($context);
    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getConnectorUrl($params = []) {
        return $this->getUrl(
            'vesbasewidget/baseconnector/index',
            $params
        );
    }

    public function render(AbstractElement $element){
        $html = '';
        $url = $element->getValue();
        $id = $element->getHtmlId();    
        if ($url) {
            $linkStyle = "display:inline;";
            if(!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = $this->getBaseMediaUrl() . $url;
            }
        }else{
            $linkStyle = "display:none;";
            $url = "#";
        }
        $class = '';
        if($element->getRequired()){
            $class = 'required-entry _required';
        }

        $hiddenField = '<input type="text" name="'.$element->getName().'" id="hidden_file_'.$id.'" class="widget-option input-text admin__control-text hidden-file-path '.$class.'" value="'.$element->getValue().'"/>';
        $imagePreview = '';

        //Chooser button
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'cms/page_widget/chooser',
            ['uniq_id' => $uniqId, 'use_massaction' => false, 'target_element_id' => $id]
        );

        $label = ($element->getValue()) ? __('Change Video') : __('Select Video');

        $chooseButton = $this->_layout->createBlock(
                'Magento\Backend\Block\Widget\Button',
                '',
                [
                    'data' => [
                        'label' =>  $label,
                        'type' => 'button',
                        'uniq_id' => $uniqId,
                        'source_url' => $sourceUrl,
                        'class' => 'add action-add',
                        'onclick' => 'openEfinder(this, \'hidden_file_'.$id.'\', \'#'.$id.'\', changeElFieldImage)',
                        'style' => 'display:inline;margin-top:7px'
                    ]
                ]
            );

        $selectButtonId = 'add-image-' . mt_rand();


        // Remove Image Button
        $onclickJs = '
            document.getElementById(\'hidden_file_'. $id .'\').value=\'\';
            if(document.getElementById(\''. $id .'_image\')){
                document.getElementById(\''. $id .'_image\').parentNode.style.display = \'none\';
            }
            document.getElementById(\''. $selectButtonId .'\').innerHTML=\'<span><span><span>' . addslashes(__('Select Video')) . '</span></span></span>\';
            document.getElementById(\''. $id . '_link\').hide();
        ';

        $removeButton = $this->_layout->createBlock(
                'Magento\Backend\Block\Widget\Button',
                '',
                [
                    'data' => [
                        'label' => __('Remove Video'),
                        'type' => 'button',
                        'class' => 'delete action-delete',
                        'onclick' => $onclickJs,
                        'style' => 'margin-top:7px'
                    ]
                ]
            );
        $wrapperStart = '<div id="buttons_' . $id . '" class="buttons-set">';
        $wrapperEnd = '</div>';

        // Add our custom HTML after the form element
        $element->setAfterElementHtml($wrapperStart . $hiddenField. $imagePreview . $chooseButton->toHtml() . $removeButton->toHtml() . $wrapperEnd);


        $html .= '<div class="admin__field field field-options_'.$element->getId().'  with-note">';
        $html .= $element->getLabelHtml();

        $html .= '<div class="admin__field-control control">';
        $html .= $wrapperStart. $hiddenField. $imagePreview. $chooseButton->toHtml() . $removeButton->toHtml(). $wrapperEnd;

        if($element->getNote()) {
            $html .= '<div class="note" id="'.$element->getId().'-note">'.$element->getNote().'</div>';
        }

        $html .= <<<HTML
            <script>
            
            require([
                'jquery',
                'Ves_BaseWidget/js/jquery/ui/jquery-ui.min',
                'Ves_BaseWidget/js/elfinder/js/elfinder.min',
            ], function(jQuery){
                //code js at here

            });
            </script>
HTML;
        $html .= '</div>';
        $html .= '</div>';
        return $html;
        //$element->setData('after_element_html', $html);
        //return $element->getElementHtml();
    }

}