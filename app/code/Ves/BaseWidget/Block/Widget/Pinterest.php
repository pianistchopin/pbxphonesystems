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
use Ves\BaseWidget\Block\AbstractWidget;

class Pinterest extends AbstractWidget{

	protected $_blockModel;
	protected $_dataFilterHelper;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->setTemplate("widget/pinterest.phtml");
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$description = $this->getConfig('description');
		$description = strip_tags($description);
		$description = str_replace(" ","%20", $description);
		
		$button_type = $this->getConfig("button_type", "small");
		$color = $this->getConfig("color", "gray");

		if($button_type == "large") {
			switch ($color) {
				case 'red':
					$color_image = "pinit_fg_en_rect_red_28.png";
					$color = ' data-pin-color="red"';
					break;
				case 'white':
					$color_image = "pinit_fg_en_rect_white_28.png";
					$color = ' data-pin-color="white"';
					break;
				default:
					$color_image = "pinit_fg_en_rect_gray_28.png";
					$color = "";
					break;
			}
			$button_type = ' data-pin-tall="true"';
		} elseif($button_type == "small") {
			switch ($color) {
				case 'red':
					$color_image = "pinit_fg_en_rect_red_20.png";
					$color = ' data-pin-color="red"';
					break;
				case 'white':
					$color_image = "pinit_fg_en_rect_white_20.png";
					$color = ' data-pin-color="white"';
					break;
				default:
					$color_image = "pinit_fg_en_rect_gray_20.png";
					break;
			}
		} else {
			$color_image = "pinit_fg_en_round_red_32.png";
			$button_type = ' data-pin-round="true" data-pin-tall="true"';
		}

		$this->assign('button_type', $button_type);
		$this->assign('color_image', $color_image);
		$this->assign('color', $color);
		$this->assign('description', $this->getConfig('description'));
        $this->assign('select_type', $this->getConfig('select_type', "beside"));
		$this->assign('widget_heading', $this->getConfig('title'));
		$this->assign('url', $this->getConfig('url'));
		$this->assign('media', $this->getConfig('media'));
		$this->assign('description', $description);
		return parent::_toHtml();
	}
	
}