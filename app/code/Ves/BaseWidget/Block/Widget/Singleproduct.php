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
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Widget;
use Ves\BaseWidget\Block\AbstractProductWidget;

class Singleproduct extends AbstractProductWidget{

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
       if($this->hasData("template")) {
            $my_template = $this->getData("template");
        } elseif(isset($data['template']) && $data['template']) {
            $my_template = $data['template'];
        } else{
            $my_template = "widget/single_product.phtml";
        }

        $this->setTemplate($my_template);

        return parent::_beforeToHtml();
    }

    public function getColorSwatchesHtml($_product = null) {
        $html = "";
        $swatches = $this->getLayout()->createBlock(
                'Magento\Swatches\Block\Product\Renderer\Listing\Configurable',
                '',
                []
                );
        $swatches->setProduct($_product);
        $swatches->setTemplate("Magento_Swatches::product/listing/renderer.phtml");

        $html = $swatches->toHtml();
        return $html;
    }

}