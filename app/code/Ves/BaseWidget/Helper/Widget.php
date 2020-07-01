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
 * @package    Ves_BlockBuilder
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Helper;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\App\Filesystem\DirectoryList;

class Widget extends \Magento\Framework\App\Helper\AbstractHelper{

	var $_widgetinfo = "widgetinfo.xml";
	var $_data = array();
    /**
     * Group Collection
     */
    protected $_groupCollection;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * BaseWidget config node per website
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    /**
     * Template filter factory
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    protected $_registry;

    protected $_moduleReader;

    protected $_cache;

    protected $_readFactory;
     /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        /*\Magento\Framework\App\RequestInterface $request,*/
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem $filesystem,
        ReadFactory $readFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
        ) {
        parent::__construct($context);
        /*$this->_request = $request;*/
        $this->_storeManager = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_registry = $registry;
        $this->_moduleReader = $moduleReader;
        $this->_readFactory = $readFactory;
        $this->_filesystem = $filesystem;
    }

    public function getRootDirPath() {
        return $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath();
    }
    public function getETCDirPath( $module_name = "Ves_BaseWidget") {
       return $this->_moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR, $module_name);
    }

    public function getViewDirPath( $module_name = "Ves_BaseWidget") {
       return $this->_moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_VIEW_DIR, $module_name);
    }

	public function getListWidgetTypes($type = "array", $available_widgets = array()) {
		$widgets = array();
		$controller_name = $this->_request->getControllerName();
		$module_name = $this->_request->getModuleName();
		$module_controller = $module_name."/".$controller_name;

		/*Get Widget Information*/
		if($this->_registry->registry("widgets_data")) {
			$widgets = $this->_registry->registry("widgets_data");
		} else {
			$widgetinfo_xml = $this->getETCDirPath().DIRECTORY_SEPARATOR.$this->_widgetinfo;
			$widgets = $this->getWidgetsInfoArray();
			$type_widgets = $this->_getData("type_widgets");
			
			/*Get other available widgets*/
			$tmp_available_widgets = array();

			if(is_array($available_widgets) && $available_widgets) {
				foreach($available_widgets as $widget) {

					$tmp_available_widgets[$widget['type']] = $widget['type'];

					if(is_array($type_widgets) && in_array($widget['type'], $type_widgets)) 
						continue;
                    
                    $show_in_extensions = array();
                    $checked = true;

                    if(isset($widget['show']) && $widget['show']) {
                        $show_in_extensions = explode(",", (string)$widget['show']);
                    }
                    if($show_in_extensions) {
                        if(!in_array($module_name."/".$controller_name, $show_in_extensions)) {
                            $checked = false;
                        }
                    }
                    if(!$checked)
                        continue;

					$tmp = array();
					$tmp['type'] = $widget['type'];
	                $tmp['title'] = (string)$widget['name'];
                    $tmp['title'] = str_replace(array("'",'"','\"'), "", $tmp['title']);
                    $tmp['code'] = (string)$widget['code'];
                    $tmp['description'] = isset($widget['description'])?(string)$widget['description']:"";
                    $tmp['description'] = str_replace(array("'",'"','\"'), "", $tmp['description']);
	                $tmp['icon'] = isset($widget['icon'])?(string)$widget['icon']:"";
                    $tmp['font_icon'] = isset($widget['font_icon'])?(string)$widget['font_icon']:"";
	                $tmp['group'] = "others";
	                $widgets[] = $tmp;
				}
			}
			/*Remove not available widget*/
			if($widgets && $tmp_available_widgets) {
				$tmp_widgets = array();
				foreach($widgets as $widget) {
					if(in_array($widget['type'], $tmp_available_widgets)) {
                        $widget['title'] = str_replace(array("'",'"','\"'), "", $widget['title']);
                        $widget['description'] = str_replace(array("'",'"','\"'), "", $widget['description']);
                        if(!isset($widget['icon']) || (isset($widget['icon']) && !$widget['icon'])){
                            $widget['font_icon'] = "fa fa-square-o";
                        }
						$tmp_widgets[] = $widget;
					}
				}
				$widgets = $tmp_widgets;
			}
		}
		if($type == "json") {
			return \Zend_Json::encode($widgets);
		}

		return $widgets;
	}

	public function getWidgetInfo($widget_type = "") {

	}


    /**
     * Load Widgets XML config from widget.xml files and cache it
     *
     * @return Varien_Simplexml_Config
     */
    public function getWidgetInfoConfig()
    {
        $widgetinfo_xml = $this->getETCDirPath().DIRECTORY_SEPARATOR.$this->_widgetinfo;

        $fileReader = $this->_readFactory->create($widgetinfo_xml, DriverPool::FILE);
        $cachedXml = $fileReader->readAll($this->_widgetinfo);
        $xmlConfig = $this->_loadXmlString($cachedXml);
        
        return $xmlConfig;
    }

    /**
     * Return filtered list of widgets as SimpleXml object
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return Varien_Simplexml_Element
     */
    public function getWidgetsInfoXml($filters = array())
    {
        $widgets = $this->getWidgetInfoConfig()->children();
        $result = clone $widgets;

        // filter widgets by params
        if (is_array($filters) && count($filters) > 0 && $widgets) {
            foreach ($widgets as $code => $widget) {
                try {
                    $reflection = new ReflectionObject($widget);
                    foreach ($filters as $field => $value) {
                        if (!$reflection->hasProperty($field) || (string)$widget->{$field} != $value) {
                            throw new Exception();
                        }
                    }
                } catch (Exception $e) {
                    unset($result->{$code});
                    continue;
                }
            }
        }
        return $result;
    }

    /**
     * Return list of widgets as array
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return array
     */
    public function getWidgetsInfoArray($filters = array())
    {
        if (!$this->_getData('widgetsinfo_array')) {
            $result = array();
            $types = array();
            foreach ($this->getWidgetsInfoXml($filters) as $widget) {
                $widget_type  = $widget->getAttribute('type') ? $widget->getAttribute('type') : '';
                $types[] = $widget_type;
                $tmp = array(
                	'type'          => $widget_type,
                    'title'         => __((string)$widget->title),
                    'code'          => (string)$widget->code,
                    'description'   => __((string)$widget->description),
                    'icon'   		=> (string)$widget->icon,
                    'font_icon'          => (string)$widget->font_icon,
                    'group'   		=> (string)$widget->group
                );
                $tmp['description'] = str_replace("'", "", $tmp['description']);
                $result[] = $tmp;
            }
            usort($result, array($this, "_sortWidgets"));

            $this->setData('widgetsinfo_array', $result);
            $this->setData('type_widgets', $types);
        }

        return $this->_getData('widgetsinfo_array');
    }

    public function setData($key, $value =null ) {
    	if($value !== null) {
    		$this->_data[$key] = $value;
    	}
    }
    /**
     * User-defined widgets sorting by Name
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortWidgets($a, $b)
    {
        return strcmp($a["title"], $b["title"]);
    }
    protected function _getData($key) {
    	return isset($this->_data[$key])?$this->_data[$key]:false;
    }

    /**
     * Return object representation of XML string
     *
     * @param string $xmlString
     * @return \SimpleXMLElement
     */
    protected function _loadXmlString($xmlString)
    {
        return simplexml_load_string($xmlString, 'Magento\Framework\View\Layout\Element');
    }

    /**
     * Retrieve data from the cache, if the layout caching is allowed, or FALSE otherwise
     *
     * @param string $cacheId
     * @return string|bool
     */
    protected function _loadCache($cacheId)
    {
        return $this->_cache->load($cacheId);
    }
    /**
     * Save data to the cache, if the layout caching is allowed
     *
     * @param string $data
     * @param string $cacheId
     * @param array $cacheTags
     * @return void
     */
    protected function _saveCache($data, $cacheId, array $cacheTags = [])
    {
        $this->_cache->save($data, $cacheId, $cacheTags, null);
    }
}