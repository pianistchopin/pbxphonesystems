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
namespace Ves\PageBuilder\Model\ResourceModel;

class Block extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store model
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\Datetime
     */
    protected $dateTime;

    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(){
        $this->_init('ves_blockbuilder_block','block_id');
    }

    
    /**
     * Process brand data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['block_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('ves_blockbuilder_cms'), $condition);
        $this->getConnection()->delete($this->getTable('ves_blockbuilder_page'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process brand data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        if ($object->isObjectNew() && !$object->hasCreated()) {
            $object->setCreated($this->_date->gmtDate());
        }

        $object->setModified($this->_date->gmtDate());

        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */

        if($object->getBlockType() == "page") {
            
            foreach (['custom_theme_from', 'custom_theme_to'] as $field) {
                $value = !$object->getData($field) ? null : $object->getData($field);
                $object->setData($field, $this->dateTime->formatDate($value));
            }

            if (!$this->getIsUniquePageToStores($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('A CMS page URL key for specified store already exists.')
                );
            }
            if (!$this->isValidPageIdentifier($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The page URL key contains capital letters or disallowed symbols.')
                );
            }

            if ($this->isNumericPageIdentifier($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The page URL key cannot be made of only numbers.')
                );
            }

        }

        return parent::_beforeSave($object);
    }

    /**
     * Assign brand to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if($object->getBlockType() == "page") {
            $oldStores = $this->lookupStoreIds($object->getId());
            $newStores = (array)$object->getStores();
            if (empty($newStores)) {
                $newStores = (array)$object->getStoreId();
            }

            $table  = $this->getTable('ves_blockbuilder_cms');
            $insert = array_diff($newStores, $oldStores);
            $delete = array_diff($oldStores, $newStores);

            if ($delete) {
                $where = [
                    'block_id = ?'     => (int) $object->getId(),
                    'store_id IN (?)' => $delete
                ];

                $this->getConnection()->delete($table, $where);
            }

            if ($insert) {
                $data = [];
                foreach ($insert as $storeId) {
                    $data[] = [
                        'block_id'  => (int) $object->getId(),
                        'store_id' => (int) $storeId
                    ];
                }

                $this->getConnection()->insertMultiple($table, $data);
            }

        }

        //Store widget short code into table ves_blockbuilder_widget
        /*
        if($widgets = $object->getWpowidget()){
            $data = [];
            $table  = $this->getTable('ves_blockbuilder_widget');
            foreach($widgets as $wkey=>$val){
                $widget_shortcode = isset($val['config'])?$val['config']:"";
                if($widget_shortcode) {
                    if ($wkey) {
                        $where = [
                            'block_id = ?'     => (int) $object->getId()
                        ];

                        $this->getConnection()->delete($table, $where);

                        $data[] = [
                            'block_id'   => (int) $object->getId(),
                            'widget_key' => $wkey,
                            'widget_shortcode'  => $widget_shortcode,
                            'created'    => date( 'Y-m-d H:i:s' )
                        ];
                    }
                    
                }
            }
            if ($data) {
                $this->getConnection()->insertMultiple($table, $data);
            }
            
        }*/

        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'alias' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $stores = array();
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
        }
        // get cms page data
        if(($alias = $object->getAlias()) && $object->getBlockType() == "page") {
          $block_model = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
          $cms_page = $block_model->loadCMSPage($alias, "identifier", $stores);

          if($cms_page->getPageId()) {
            if(!$stores) {
                $stores = $cms_page->getStoreId();
            }
            //$stores = $cms_page->getStoreId();

            $object->setData("cmspage_id", $cms_page->getPageId());
            $object->setData("page_layout", $cms_page->getPageLayout());
            $object->setData("root_template", $cms_page->getPageLayout());
            $object->setData("layout_update_xml", $cms_page->getLayoutUpdateXml());
            $object->setData("custom_theme_from", $cms_page->getCustomThemeFrom());
            $object->setData("custom_theme_to", $cms_page->getCustomThemeTo());
            $object->setData("custom_theme", $cms_page->getCustomTheme());
            $object->setData("custom_root_template", $cms_page->getCustomRootTemplate());
            $object->setData("custom_layout_update_xml", $cms_page->getCustomLayoutUpdateXml());
            $object->setData("meta_keywords", $cms_page->getMetaKeywords());
            $object->setData("meta_title", $cms_page->getMetaTitle());
            $object->setData("meta_description", $cms_page->getMetaDescription());
          }
        }
        if($settings = $object->getSettings()) {
              $settings = unserialize($settings);
              if($settings) {
                foreach($settings as $key => $val) {
                  $object->setData($key, $val);
                }
              }
        }
        $stores = $stores?$stores:array(0);
        $object->setData("store_id", $stores);

        //Load params and widgets
        /*
        if($params = $object->getParams()) {
            $widgets = $this->lookupWidgets($object->getId());
            $data_widgets = [];
            if($widgets) {
                foreach($widgets as $key => $widget){
                    $data_widgets[$widget['widget_key']] = $widget['widget_shortcode'];
                }
            }
            $object->setData("widgets", $data_widgets);
        }*/
	    $object->setData("widgets", []);
        return parent::_afterLoad($object);
    }

    public function walkAfterLoad(\Magento\Framework\Model\AbstractModel $object) {
        return $this->_afterLoad($object);
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->join(
            ['cps' => $this->getTable('ves_blockbuilder_cms')],
            'cp.block_id = cps.block_id',
            []
        )->where(
            'cp.alias = ?',
            $identifier
        )->where(
            'cp.block_type = ?',
            "page"
        )->where(
            'cps.store_id IN (?)',
            $store
        );

        if (!is_null($isActive)) {
            $select->where('cp.status = ?', $isActive);
        }

        return $select;
    }

    /**
     * Check for unique of alias of page to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniquePageToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $stores = $object->getStores();
        if(!$stores) {
            $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }
        if ($this->_store) {
            $stores[] = (int)$this->getStore()->getId();
        }

        $select = $this->_getLoadByIdentifierSelect($object->getAlias(), $stores);

        if ($object->getId()) {
            $select->where('cp.block_id <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     *  Check whether page identifier is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('alias'));
    }

    /**
     *  Check whether page identifier is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('alias'));
    }

    /**
     *  Check whether page identifier is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($pageId)
    {
        $adapter = $this->getConnection();

        $select  = $adapter->select()
                            ->from($this->getTable('ves_blockbuilder_cms'), 'store_id')
                            ->where('block_id = ?',(int)$pageId);

        return $adapter->fetchCol($select);
    }

    public function lookupWidgets($pageId = 0) {
        $adapter = $this->getConnection();

        $select  = $adapter->select()
                            ->from($this->getTable('ves_blockbuilder_widget'), '*');
        if($pageId) {
            $select->where('block_id = ?',(int)$pageId);
        }
        $select->order("created", "DESC");
        return $adapter->fetchAll($select);
    }

    /**
     * Set store model
     *
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }
}