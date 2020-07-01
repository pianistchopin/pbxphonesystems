<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ves\BaseWidget\Model;
use Magento\Framework\Module\Dir;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\Component\ComponentRegistrar;
/**
 * Widget model for different purposes
 */
class Widget extends \Magento\Widget\Model\Widget
{
    /**
     * Return filtered list of widgets
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return array
     * @api
     */
    public function getWidgets($filters = [])
    {
        $core_widgets = parent::getWidgets($filters);
        $result = $core_widgets;
        $widgets = array();

        // filter widgets by params
        if (is_array($filters) && count($filters) > 0 && $widgets) {
            foreach ($widgets as $code => $widget) {
                try {
                    foreach ($filters as $field => $value) {
                        if (!isset($widget[$field]) || (string)$widget[$field] != $value) {
                            throw new \Exception();
                        }
                    }
                } catch (\Exception $e) {
                    unset($result[$code]);
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Return widget presentation code in WYSIWYG editor
     *
     * @param string $type Widget Type
     * @param array $params Pre-configured Widget Params
     * @param bool $asIs Return result as widget directive(true) or as placeholder image(false)
     * @return string Widget directive ready to parse
     * @api
     */
    public function getWidgetDeclaration($type, $params = [], $asIs = true)
    {
        $field_pattern = ["pretext","pretext_html","shortcode","html","raw_html","content","tabs","latestmod_desc","custom_css","block_params"];
        $widget_types = ["Ves\BaseWidget\Block\Widget\Accordionbg"];

        foreach ($params as $k => $value) {
            if(0 < strpos($k, 'class') || 0 < strpos($k, 'Class')) {
                continue;
            }
            // Retrieve default option value if pre-configured
            if(is_array($params[$k]) || !base64_decode($params[$k], true)) {
                if(in_array($k, $field_pattern) || preg_match("/^tabs(.*)/", $k) || preg_match("/^html_(.*)/", $k) || preg_match("/^content_(.*)/", $k) || (preg_match("/^header_(.*)/", $k) && in_array($type, $widget_types))) {
                    if(is_array($params[$k])){
                        $params[$k] = base64_encode(serialize($params[$k]));
                    }elseif(!$this->isBase64Encoded($params[$k])){
                        $params[$k] = base64_encode($params[$k]);
                    }
                }
            }
            
        }
        return parent::getWidgetDeclaration($type, $params, $asIs);
    }

    public function isBase64Encoded($data) {
        if(base64_encode($data) === $data) return false;
        if (!preg_match('~[^0-9a-zA-Z+/=]~', $data)) {
            $check = str_split(base64_decode($data));
            $x = 0;
            foreach ($check as $char) if (ord($char) > 126) $x++;
            if ($x/count($check)*100 < 30) return true;
        }
        $decoded = base64_decode($data);
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data)) return false;
        // if string returned contains not printable chars
        if (0 < preg_match('/((?![[:graph:]])(?!\s)(?!\p{L}))./', $decoded, $matched)) return false;
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) return false;

        if(base64_encode(base64_decode($data)) === $data){
            return true;
        }
        return false;
    }

}
