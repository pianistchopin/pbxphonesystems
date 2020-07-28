<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Observer\Admin;

use Magento\Framework\Data\Form;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Event\ObserverInterface;
use Amasty\ShopbyBrand\Observer\Admin\OptionFormBuildAfter;
use Amasty\ShopbyBase\Helper\FilterSetting;

/**
 * Class OptionFormFeatured
 *
 * @package Amasty\ShopbyBrand\Observer\Admin
 */
class OptionFormFeatured implements ObserverInterface
{
    /**
     * @var Yesno
     */
    private $yesNoSource;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $helper;

    /**
     * OptionFormFeatured constructor.
     * @param Yesno $yesNosource
     * @param \Amasty\ShopbyBrand\Helper\Data $brandHelper
     */
    public function __construct(
        Yesno $yesNosource,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper
    ) {
        $this->yesNoSource = $yesNosource;
        $this->helper = $brandHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Form $form */
        $fieldSet = $observer->getEvent()->getFieldset();
        $setting = $observer->getEvent()->getSetting();
        $brandFilterCode = FilterSetting::ATTR_PREFIX . $this->helper->getBrandAttributeCode();
        if ($setting->getFilterCode() == $brandFilterCode) {
            $fieldSet->setData('legend', 'Slider Options');
            $elements = $fieldSet->getElements();
            foreach ($elements as $element) {
                if ($element->getId() == 'is_featured') {
                    $element->setLabel(__('Show in Brand Slider'))
                        ->setTitle(__('Show in Brand Slider'));
                    break;
                }
            }

            $fieldSet->addField(
                'slider_position',
                'text',
                [
                    'name' => 'slider_position',
                    'label' => __('Position in Slider'),
                    'title' => __('Position in Slider'),
                    'note' => __('Please make sure Sort By "Position" is selected in the following setting group:
                                  STORES -> Configuration -> Improved Layered Navigation: Brands -> Brand Slider')
                ]
            );
        }
    }
}
