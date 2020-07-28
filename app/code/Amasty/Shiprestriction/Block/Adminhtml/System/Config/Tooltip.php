<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Block\Adminhtml\System\Config;

/**
 * Class Tooltip
 */
class Tooltip extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $tooltipConfig = [
                'tooltip' => [
                    'trigger' => '[data-tooltip-trigger=trigger]',
                    'action' => 'click',
                    'delay' => 0,
                    'track' => false,
                    'position' => 'top'
                ]
            ];
            $tooltipConfig = str_replace('"', "'", \Zend_Json::encode($tooltipConfig));

            $html .= '<div data-bind="' . $tooltipConfig . '" class="hidden">' . $element->getTooltip() . '</div>';
            $html .= '<div class="tooltip" data-tooltip-trigger="trigger"><span class="help"><span></span></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->_getElementHtml($element);
        }
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }
}
