<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as AttrOptionCollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute as ConfigurableAttributeModel;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProTypeModel;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap\Source\RootCat;
use Ebay;
use Webkul\MultiEbayStoreMageConnect\Api\EbayaccountsRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\ImportedtmpproductRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\EbaycategorymapRepositoryInterface;
use Magento\Catalog\Model\Product\AttributeSet\Options as AttributeSetList;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as AttrGroupCollection;
use Magento\Eav\Model\Entity as EavEntity;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Catalog\Model\Product\Option as ProductOptions;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ProductmapFactory;
use Webkul\MultiEbayStoreMageConnect\Api\OrdermapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Filter\FilterManager;
use Magento\Catalog\Helper\Product as ProductHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const _WSDL_VERSION_ = 967;

    public $config;

    public $client;

    private static $_operation = [
        '' => '--Select--',
        'Increase' => 'Increase',
        'Decrease' => 'Decrease',
    ];

    private static $_operationType = [
            '' => '--Select--',
            'Fixed' => 'Fixed',
            'Percent' => 'Percent',
    ];

    private static $_status = [
        '1' => 'Enable',
        '0' => 'Disable',
    ];

    public $sellerId;

    public $ruleId;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var Magento\CatalogInventory\Api\StockStateInterface
     */
    private $stockStateInterface;

    /**
     * @var Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    private $attributeFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formkey;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    private $attrOptionCollectionFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute
     */
    private $configurableAttributeModel;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $configurableProTypeModel;

    /**
     * @var \Magento\Quote\Api\Data\CurrencyInterface
     */
    private $currencyInterface;

    /**
     * @var \Magento\Quote\Api\Data\PaymentInterface
     */
    private $customerFactory;

    /**
     * @var \Magento\Sales\Model\Service\OrderService
     */
    private $orderService;

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $backendSession;

    /**
     * @var ImportedtmpproductRepositoryInterface
     */
    private $importedTmpProductRepository;

    /**
     * @var SaveProduct
     */
    private $saveProduct;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Logger\Logger
     */
    protected $logger;

    /**
     * @var RootCat's object
     */
    protected $rootCat;

    /**
     * @var EbayaccountsRepositoryInterface
     */
    protected $ebayAccountsRepository;

    /**
     * @var EbaycategorymapRepositoryInterface
     */
    protected $ebayCategoryMapRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreManagerInterface                 $storeManager
     * @param \Magento\Catalog\Model\Product        $product
     * @param StockStateInterface                   $stockStateInterface
     * @param StockRegistryInterface                $stockRegistry
     * @param AttributeFactory                      $attributeFactory
     * @param FormKey                               $formkey
     * @param \Magento\Framework\Filesystem         $filesystem
     * @param \Magento\Framework\Registry           $registry
     * @param AttrOptionCollectionFactory           $attrOptionCollectionFactory
     * @param ConfigurableAttributeModel            $configurableAttributeModel
     * @param ConfigurableProTypeModel              $configurableProTypeModel
     * @param \Magento\Quote\Api\Data\CurrencyInterface         $currencyInterface
     * @param \Magento\CustomDirectoryLister\Model\CustomerFactory           $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Model\Service\OrderService         $orderService
     * @param \Magento\Backend\Model\Session                    $backendSession
     * @param ModuleDataSetupInterface                          $setup
     * @param ImportedtmpproductRepositoryInterface             $importedTmpProductRepository
     * @param SaveProduct                                       $saveProduct
     * @param \Webkul\MultiEbayStoreMageConnect\Logger\Logger   $logger
     * @param \Magento\Quote\Api\CartRepositoryInterface        $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface        $cartManagementInterface
     * @param \Magento\Quote\Model\Quote\Address\Rate           $shippingRate
     * @param \Magento\Sales\Model\Order                        $order
     * @param EbayaccountsRepositoryInterface                   $ebayAccountsRepository
     * @param RootCat                                           $rootCat
     * @param EbaycategorymapRepositoryInterface                $ebayCategoryMapRepository
     */
    public function __construct(
        FilterManager $filterManager,
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $product,
        StockStateInterface $stockStateInterface,
        StockRegistryInterface $stockRegistry,
        AttributeFactory $attributeFactory,
        FormKey $formkey,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $registry,
        AttrOptionCollectionFactory $attrOptionCollectionFactory,
        ConfigurableAttributeModel $configurableAttributeModel,
        ConfigurableProTypeModel $configurableProTypeModel,
        \Magento\Quote\Api\Data\CurrencyInterface $currencyInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Backend\Model\Session $backendSession,
        ImportedtmpproductRepositoryInterface $importedTmpProductRepository,
        SaveProduct $saveProduct,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Quote\Model\Quote\Address\Rate $shippingRate,
        \Magento\Sales\Model\Order $order,
        EbayaccountsRepositoryInterface $ebayAccountsRepository,
        RootCat $rootCat,
        EbaycategorymapRepositoryInterface $ebayCategoryMapRepository,
        AttributeSetList $attributeSetList,
        AttrGroupCollection $attrGroupCollection,
        ProductAttributeRepositoryInterface $productAttribute,
        EavEntity $eavEntity,
        AttributeManagementInterface $attributeManagement,
        ProductOptions $productOptions,
        \Magento\Framework\Filesystem\DirectoryList $directory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        ProductmapFactory $productmapFactory,
        OrdermapRepositoryInterface $ordermapRepository,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Webkul\MultiEbayStoreMageConnect\Model\Ordermap $orderMapRecord,
        ListingTemplateFactory $listingTemplate,
        \Magento\Widget\Model\Template\Filter $templateProcessor,
        JsonHelper $jsonHelper,
        VariationsForeBay $variationsForeBay,
        \Webkul\MultiEbayStoreMageConnect\Model\Storage\DbStorage $dbStorage,
        ProductHelper $productHelper,
        \Webkul\MultiEbayStoreMageConnect\Model\PriceRuleFactory $priceRule,
        EncryptorInterface $encryptor,
        \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
    ) {
        $this->encryptor = $encryptor;
        $this->productHelper = $productHelper;
        $this->dbStorage = $dbStorage;
        $this->filterManager = $filterManager;
        $this->jsonHelper = $jsonHelper;
        $this->orderMapRecord = $orderMapRecord;
        $this->rootCat = $rootCat;
        $this->storeManager = $storeManager;
        $this->product = $product;
        $this->stockStateInterface = $stockStateInterface;
        $this->stockRegistry = $stockRegistry;
        $this->attributeFactory = $attributeFactory;
        $this->formkey = $formkey;
        $this->filesystem = $filesystem;
        $this->registry = $registry;
        $this->importedTmpProductRepository = $importedTmpProductRepository;
        $this->saveProduct = $saveProduct;
        $this->logger = $logger;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->configurableAttributeModel = $configurableAttributeModel;
        $this->configurableProTypeModel = $configurableProTypeModel;
        $this->currencyInterface = $currencyInterface;
        $this->customerFactory = $customerFactory;
        $this->_customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->backendSession = $backendSession;
        $this->_cartRepositoryInterface = $cartRepositoryInterface;
        $this->_cartManagementInterface = $cartManagementInterface;
        $this->_shippingRate = $shippingRate;
        $this->_order = $order;
        $this->ebayAccountsRepository = $ebayAccountsRepository;
        $this->ebayCategoryMapRepository = $ebayCategoryMapRepository;
        $this->attributeSetList = $attributeSetList->toOptionArray();
        $this->attrGroupCollection = $attrGroupCollection;
        $this->productAttribute = $productAttribute;
        $this->attributeManagement = $attributeManagement;
        $this->entityTypeId = $eavEntity->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
        $this->entityType = \Magento\Catalog\Model\Product::ENTITY;
        $this->productOptions = $productOptions;
        $this->directory = $directory;
        $this->productRepository = $productRepository;
        $this->productmapFactory = $productmapFactory;
        $this->ordermapRepository = $ordermapRepository;
        $this->_objectManager = $objectmanager;
        $this->listingTemplate = $listingTemplate;
        $this->templateProcessor = $templateProcessor;
        $this->variationsForeBay = $variationsForeBay;
        $this->priceRule    = $priceRule;
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }

    /**
     * get array of perform operation
     *
     * @return array
     */
    public function getOperations()
    {
        return self::$_operation;
    }

    /**
     * get opeation type
     *
     * @return array
     */
    public function getOperationsTypes()
    {
        return self::$_operationType;
    }

    /**
     * get rule status
     *
     * @return array
     */
    public function getStatus()
    {
        return self::$_status;
    }

    /**
     * get all stores of amazon
     *
     * @return array
     */
    public function getAllEbayStores()
    {
        $ebaystores = [];
        $accountCol = $this->_objectManager
                    ->create('Webkul\MultiEbayStoreMageConnect\Model\EbayaccountsFactory')
                    ->create()->getCollection();

        foreach ($accountCol as $account) {
            $ebaystores[$account->getId()] = $account->getStoreName();
        }
        return $ebaystores;
    }

    /**
     * get default trans email id
     *
     * @return void
     */
    public function getDefaultTransEmailId()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get default trans email id
     *
     * @return void
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * get list of required states
     *
     * @return array
     */
    public function getRequiredStateList()
    {
        return $this->scopeConfig->getValue(
            'general/region/state_required',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * perform action when eBay sold item event occur
     *
     * @return string
     */
    public function getSoldItemAction()
    {
        return $this->scopeConfig->getValue(
            'multiebaystoremageconnect/ebay_event/sold_item_action',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * includeEbayFiles
     */
    public function includeEbayFiles()
    {
        $path = $this->directory->getPath('lib_internal').'/Ebay/Autoloader.php';
        include_once($path);
    }

    /**
     * get Current Website Id
     * @return void
     */
    public function getCurrentWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * get status of product with description
     * @return int
     */
    public function isDesWithHtml()
    {
        return $this->config['item_with_html'];
    }

    /**
     * get status of product with specification
     * @return int
     */
    public function isProductWithSpeci()
    {
        return $this->config['item_speci'];
    }

    /**
     * get default website
     * @return int
     */
    public function getDefaultWebsite()
    {
        $store = $this->storeManager->getStore($this->config['default_store_view']);
        $websiteId = $store->getWebsiteId();
        return $websiteId;
    }

    /**
     * get revise status
     * @return int
     */
    public function getItemReviseStatus()
    {
        return $this->config['revise_item'];
    }

    /**
     * get ebay api key
     * @param  boolean $ebayAccountId
     * @return object
     */
    public function getEbayAPI($ebayAccountId = false)
    {
        if (!$this->client) {
            $this->includeEbayFiles();
            $eBayConfig = $this->geteBayConfiguration($ebayAccountId);
            $this->ruleId = $ebayAccountId;
            if ($eBayConfig) {
                $session = new Ebay\eBaySession(
                    $eBayConfig['dev'],
                    $eBayConfig['app'],
                    $eBayConfig['cert']
                );
                $session->token = $eBayConfig['token'];
                $session->site = $eBayConfig['globalsites'];
                $session->location = $eBayConfig['location'];
                $this->client = new Ebay\eBaySOAP($session);
            }
        }
        return $this->client;
    }

    public function geteBayStoreDetails($eBayStoreId)
    {
        return $this->ebayAccountsRepository->getConfigurationById($eBayStoreId);
    }
    /**
     * Get Configuration Detail of eBay.
     *
     * @return array of eBay Configuration Detail
     */
    public function geteBayConfiguration($ebayAccountId = false)
    {
        /*if ($this->config) {
            return $this->config;
        }*/
        if ($ebayAccountId) {
            $storeEbayConfiguration = $this->geteBayStoreDetails($ebayAccountId);
            $this->config = [
                'attribute_set' => trim($storeEbayConfiguration->getAttributeSetId()),
                'globalsites' => trim($storeEbayConfiguration->getGlobalSite()),
                'ebayuserid' => trim($storeEbayConfiguration->getEbayUserId()),
                'token' => trim($storeEbayConfiguration->getEbayAuthenticationToken()),
                'dev' => trim($storeEbayConfiguration->getEbayDeveloperId()),
                'app' => trim($storeEbayConfiguration->getEbayApplicationId()),
                'cert' => trim($storeEbayConfiguration->getEbayCertificationId()),
                'mode' => $this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/mode'),
                'location' => 'https://api.ebay.com/wsapi',
            ];
            $this->config = array_merge($this->config, $storeEbayConfiguration->toArray());
        } else {
            $this->config = [
                'globalsites' => $this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/glob_site'),
                'ebayuserid' => $this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/ebay_user_id'),
                'token' => $this->encryptor->decrypt($this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/ebay_user_token')),
                'dev' => $this->encryptor->decrypt($this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/ebay_dev_id')),
                'app' =>$this->encryptor->decrypt($this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/ebay_application_id')),
                'cert' => $this->encryptor->decrypt($this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/ebay_certification_id')),
                'mode' => $this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/mode'),
                'location' => 'https://api.ebay.com/wsapi',
                'app_ru_name' => $this->scopeConfig->getValue('multiebaystoremageconnect/general_settings/app_ru_name')
            ];
        }

        if ($this->config['mode'] == '1') {
            $this->config['location'] = 'https://api.sandbox.ebay.com/wsapi';
            $this->config['auth_url'] = 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll?SignIn';
        } else {
            $this->config['auth_url'] = 'https://signin.ebay.co.uk/ws/eBayISAPI.dll?SignIn';
        }
        foreach ($this->config as $value) {
            if (trim($value) == '') {
                return false;
            }
        }
        return $this->config;
    }

    /**
     * get default store option from configuration
     * @return int
     */
    public function getDefaultStoreForOrderSync()
    {
        return $this->config['default_store_view'];
    }

    /**
     * Get eBay Default settings on store.
     *
     * @return array of eBay Default settings
     */
    public function getEbayDefaultSettings($ebayAccountId = false)
    {
        $this->geteBayConfiguration($ebayAccountId);
        $eBayDefaultSetting = [
            'PayPalEmailAddress' => $this->config['paypal_id'],
            'ListingDuration' => $this->config['listing_duration'],
            'PostalCode' => $this->config['shop_postal_code'],
            'DispatchTimeMax' => $this->config['dispatch_time'],
            'Country' => $this->scopeConfig->getValue('general/country/default'),
            'Currency' => $this->storeManager->getStore()->getBaseCurrencyCode(),
            'DefaultOrderStatus' => $this->config['order_status'],
            'DefaultProQty' => $this->config['default_qty'],
            'useTemplate' => $this->config['template_id'],
            'ShippingDetails' => [
                'ShippingServiceOptions' => [[
                    'ShippingServicePriority' => $this->config['ship_priority'],
                    'ShippingService' => $this->config['shipping_service'],
                    'ShippingServiceCost' => $this->config['ship_cost'],
                    'ShippingServiceAdditionalCost' => $this->config['ship_aditional_cost'],
                    'ShippingTimeMin' => $this->config['ship_min_time'],
                    'ShippingTimeMax' => $this->config['ship_max_time'],
                    'FreeShipping' => $this->config['ship_free'],
                ]],
            ],
            'ReturnPolicy' => [
                        'ReturnsAcceptedOption' => $this->config['return_policy'],
                        'ReturnsWithinOption' => $this->config['returns_within'],
                        'Description' => $this->config['other_info'],
                        'ShippingCostPaidByOption' => $this->config['pay_by'],
                        ],
        ];
        return $eBayDefaultSetting;
    }

    /**
     * @param int Magento categoryId
     *
     * @return fixed
     */
    public function getStoreBayCatMapData($eBayCategory, $ruleId)
    {

        $ebayExistCatColl = $this->ebayCategoryMapRepository
            ->getCollectionByeBayCateIdnRuleId($eBayCategory, $ruleId);
        if ($ebayExistCatColl->getSize()) {
            foreach ($ebayExistCatColl as $ebayExistCat) {
                return $ebayExistCat;
            }
        }
        return false;
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    public function isMageCategoryMapped($leafMageCategory, $ruleId)
    {
        $ebayExistCatColl = $this->ebayCategoryMapRepository
            ->getCollectionByMageCateIdnRuleId($leafMageCategory, $ruleId);

        if ($ebayExistCatColl->getSize()) {
            foreach ($ebayExistCatColl as $ebayExistCat) {
                return $ebayExistCat->getEntityId();
            }
        }
        return false;
    }

    /**
     * @param object $conditionValues
     * @param string $ebayCategoryName
     * @return string
     */
    public function createProConditionAttr($conditionValues, $ebayCategoryName, $attributeSetId)
    {
        $values = $this->_convertConditionInArray($conditionValues);

        $attributeCode = str_replace(' ', '_', $ebayCategoryName);
        $attributeCode = preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
        $mageAttrCode = substr('ebay_cond_cat_'.strtolower($attributeCode), 0, 30);
        $attrName = 'Condition ( '.$ebayCategoryName.' )';
        $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
        $allStores = $this->storeManager->getStores();
        $attributeScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE;
        $attributeGroupId = $this->getAttributeGroupId('Ebay Product Conditions as Category', $attributeSetId);
        if ($attributeInfo === false) {
            $attribute = $this->attributeFactory->create();
            $attrData = [
                'entity_type_id' => $this->entityTypeId,
                'attribute_code' => $mageAttrCode,
                'frontend_label' => [0 => $attrName],
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
                'backend_type' => 'int',
                'frontend_input' => 'select',
                'backend' => '',
                'frontend' => '',
                'source' => '',
                'global' => $attributeScope,
                'visible' => true,
                'required' => false,
                'is_user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_html_allowed_on_front' => true,
                'visible_in_advanced_search' => false,
                'unique' => false,
            ];

            $labels = [];
            foreach ($allStores as $store) {
                $labels[$store->getId()] = $attrName;
            }
            $option = $this->_getAttributeOptions($mageAttrCode, $labels, $values);
            try {
                $attrData['option'] = $option;
                $attribute->addData($attrData)->save();
            } catch (\Exception $e) {
                $this->logger->info('new createProConditionAttr : '.$e->getMessage());
            }
        } else {
            try {
                $option = $this->_getAttributeOptionsForEdit($attributeInfo->getAttributeId(), $values);
                $this->attributeManagement->assign(
                    $this->entityType,
                    $attributeSetId,
                    $attributeGroupId,
                    $mageAttrCode,
                    $attributeInfo->getAttributeId()
                );
                if (isset($option['value'])) {
                    $attr = $this->productAttribute->get($attributeInfo->getAttributeCode());
                    $attr->setOption($option);
                    $this->productAttribute->save($attr);
                }
            } catch (\Exception $e) {
                $this->logger->info('update createProConditionAttr : '.$e->getMessage());
            }
        }
        return $mageAttrCode;
    }

    /**
     * @param array|object $conditionValues
     * @return array
     */
    private function _convertConditionInArray($conditionValues)
    {
        $values = [];
        if (isset($conditionValues->Condition)) {
            foreach ($conditionValues->Condition as $key => $option) {
                $values[$key] = $option->ID.' for '.$option->DisplayName;
            }
        } elseif (is_array($conditionValues)) {
            foreach ($conditionValues as $val) {
                $values[0] = $val;
            }
        }
        return $values;
    }

    /**
     * @param object $eBaySpecification
     * @param string $ebayCategoryName
     * @return string
     */
    public function createProductAttribute($eBaySpecification, $ebayCategoryName, $attributeSetId)
    {
        $dataValInputType = $this->_getValuesAndInputType($eBaySpecification);

        $values = $dataValInputType['values'];
        $type = $dataValInputType['type'];
        $input = $dataValInputType['input'];

        $attributeCode = str_replace(' ', '_', $eBaySpecification->Name);
        $attributeCode = preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
        $mageAttrCode = substr('ebay_'.strtolower($attributeCode), 0, 30);
        $attributeInfo = $this->_getAttributeInfo($mageAttrCode);

        $allStores = $this->storeManager->getStores();
        $attributeGroupId = $this->getAttributeGroupId('Ebay Specification', $attributeSetId);
        if ($attributeInfo === false) {
            $attributeScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE;
            $attribute = $this->attributeFactory->create();
            $attrLabel = $eBaySpecification->Name.' ( '.$ebayCategoryName.' )';
            $attrData = [
                'entity_type_id' => $this->entityTypeId,
                'attribute_code' => $mageAttrCode,
                'frontend_label' => [0 => $attrLabel],
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
                'backend_type' => $type,
                'frontend_input' => $input,
                'backend' => '',
                'frontend' => '',
                'source' => '',
                'global' => $attributeScope,
                'visible' => true,
                'required' => false,
                'is_user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_html_allowed_on_front' => true,
                'visible_in_advanced_search' => false,
                'unique' => false,
            ];

            if ($input != 'text') {
                $labels = [];
                foreach ($allStores as $store) {
                    $labels[$store->getId()] = $eBaySpecification->Name.' ( '.$ebayCategoryName.' )';
                }

                $option = $this->_getAttributeOptions($mageAttrCode, $labels, $values);
                $attrData['option'] = $option;
            }
            try {
                $attribute->setData($attrData);
                $attribute->save();
            } catch (\Exception $e) {
                $this->logger->info('Create createProductAttribute : '.$e->getMessage());
            }
        } else {
            if ($input != 'text') {
                try {
                    $option = $this->_getAttributeOptionsForEdit($attributeInfo->getAttributeId(), $values);
                    $this->attributeManagement->assign(
                        $this->entityType,
                        $attributeSetId,
                        $attributeGroupId,
                        $mageAttrCode,
                        $attributeInfo->getAttributeId()
                    );
                    if (isset($option['value'])) {
                        $attr = $this->productAttribute->get($attributeInfo->getAttributeCode());
                        $attr->setOption($option);
                        $this->productAttribute->save($attr);
                    }
                } catch (\Exception $e) {
                    $this->logger->info('update createProductAttribute : '.$e->getMessage());
                }
            }
        }
        return $mageAttrCode;
    }

    /**
     * @param array|object $eBaySpecification
     * @return array
     */
    private function _getValuesAndInputType($eBaySpecification)
    {
        $values = [];
        $type = 'int';
        $input = 'select';

        if (is_array($eBaySpecification->ValueRecommendation)) {
            foreach ($eBaySpecification->ValueRecommendation as $key => $options) {
                $values[$key] = $options->Value;
            }
        } else {
            $type = 'varchar';
            $input = 'text';
        }
        return ['type'=>$type, 'input'=>$input, 'values' => $values];
    }

    /**
     * getAttributeInfo
     * @param string $mageAttrCode
     * @return false | Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    private function _getAttributeInfo($mageAttrCode)
    {
        $attributeInfoColl = $this->attributeFactory->create()
                                    ->getCollection()
                                    ->addFieldToFilter(
                                        'attribute_code',
                                        ['eq' => $mageAttrCode]
                                    );
        $attributeInfo = false;
        foreach ($attributeInfoColl as $attrInfoData) {
            $attributeInfo = $attrInfoData;
        }
        return $attributeInfo;
    }

    /**
     * createSuperAttrMagento return supper attributes code with values
     * @param object $variations
     * @return array
     */
    public function createSuperAttrMagento($variations, $attributeSetId)
    {
        $variations = json_decode(json_encode($variations), true);
        $mapAttr = [];
        $option = [];
        try {
            $mageSupAttrs = $this->_getSupAttrWithValue($variations['Variation']);
            $allStores = $this->storeManager->getStores();
            foreach ($mageSupAttrs as $attrCode => $values) {
                $i = 0;
                if ($attrCode == '') {
                    continue;
                }
                $attributeCode = str_replace(' ', '_', $attrCode);
                $attributeCode = preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
                $mageAttrCode = substr('conf_'.strtolower($attributeCode), 0, 30);
                if ($mageAttrCode == 'conf_') {
                    continue;
                }
                $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
                $mapAttr[$attrCode] = $mageAttrCode;
                $attributeSetId = $attributeSetId;
                $attributeGroupId = $this->getAttributeGroupId('Ebay Product Variation', $attributeSetId);

                if ($attributeInfo === false) {
                    $attributeScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL;
                    $attrData = [
                        'entity_type_id' => $this->entityTypeId,
                        'attribute_code' => $mageAttrCode,
                        'frontend_label' => [0 => $attrCode],
                        'attribute_group_id' => $attributeGroupId,
                        'attribute_set_id' => $attributeSetId,
                        'backend_type' => 'int',
                        'frontend_input' => 'select',
                        'global' => $attributeScope,
                        'visible' => true,
                        'required' => false,
                        'is_user_defined' => true,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'visible_in_advanced_search' => false,
                        'unique' => false,
                    ];

                    $labels = [];
                    $labels[0] = $attrCode;
                    foreach ($allStores as $store) {
                        $labels[$store->getId()] = $attrCode;
                    }
                    $option = $this->_getAttributeOptions($mageAttrCode, $labels, $values);
                    try {
                        $attrData['option'] = $option;
                        $attribute = $this->attributeFactory->create();
                        $attributeIdNew = $attribute->setData($attrData)->save()->getId();

                        $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
                        $option = $this->_getAttributeOptionsForEdit($attributeIdNew, $values);

                        $this->attributeManagement->assign(
                            $this->entityType,
                            $attributeSetId,
                            $attributeGroupId,
                            $mageAttrCode,
                            $attributeIdNew
                        );
                        if (isset($option['value'])) {
                            $attr = $this->productAttribute->get($attributeInfo->getAttributeCode());
                            $attr->setOption($option);
                            $this->productAttribute->save($attr);
                        }
                    } catch (\Exception $e) {
                        $this->logger->info($e->getMessage());
                    }
                } else {
                    try {
                        /****For get Attribute Options ****/
                        $option = $this->_getAttributeOptionsForEdit($attributeInfo->getAttributeId(), $values);

                        $this->attributeManagement->assign(
                            $this->entityType,
                            $attributeSetId,
                            $attributeGroupId,
                            $mageAttrCode,
                            $attributeInfo->getAttributeId()
                        );
                        if (isset($option['value'])) {
                            $attr = $this->productAttribute->get($attributeInfo->getAttributeCode());
                            $attr->setOption($option);
                            $this->productAttribute->save($attr);
                        }
                    } catch (\Exception $e) {
                        $this->logger->info('Create createSuperAttrMagento : '.$e->getMessage());
                    }
                    $option = [];
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Create createSuperAttrMagento : '.$e->getMessage());
            $mageSupAttrs = [];
        }
        return $mapAttr;
    }

    /**
     * _getAttributeOptionsForEdit
     * @param $mageAttrId string
     * @param $values array of spicification/variations options
     * @return array of prepared all options of attribute
     */
    private function _getAttributeOptionsForEdit($mageAttrId, $values)
    {
        $attributeOptions = $this->attrOptionCollectionFactory->create()
                                            ->setPositionOrder('asc')
                                            ->setAttributeFilter($mageAttrId)
                                            ->setStoreFilter(0)->load();
        $optionsValues = [];
        foreach ($attributeOptions as $kay => $attributeOption) {
            array_push($optionsValues, strtolower($attributeOption->getDefaultValue()));
        }
        $allStores = $this->storeManager->getStores();
        $option = [];
        $option['attribute_id'] = $mageAttrId;
        foreach ($values as $key => $value) {
            if (in_array(strtolower($value), $optionsValues) === false && $value != '' && $value != ' ') {
                $option['value']['wk'.$value][0] = $value;
                foreach ($allStores as $store) {
                    $option['value']['wk'.$value][$store->getId()] = $value;
                }
            }
        }
        return $option;
    }

    /**
     * getAttributeGroupId
     * @param $groupName
     */
    private function getAttributeGroupId($groupName, $attributeSetId)
    {
        $group = $this->attrGroupCollection->create()
                                        ->addFieldToFilter('attribute_group_name', $groupName)
                                        ->addFieldToFilter('attribute_set_id', $attributeSetId)
                                        ->setPageSize(1)->getFirstItem();
        if (!$group->getAttributeGroupId()) {
            $data = [
                'attribute_group_name' => $groupName,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_code' => md5($groupName)
            ];
            $group = $group->setData($data)->save();
        }
        return $group->getId();
    }

    /**
     * getMageSupperAttribute for get Attribute label with its value
     * @param array $attrVals
     * @param array
     */
    private function _getSupAttrWithValue($attrVals)
    {
        $mageSupAttrs =[];
        $attrVals = isset($attrVals[0]) ? $attrVals : [$attrVals];
        foreach ($attrVals as $variation) {
            $attrNameVal = $variation['VariationSpecifics']['NameValueList'];
            if (isset($attrNameVal[0])) {
                foreach ($attrNameVal as $nameValueList) {
                    if ($nameValueList['Value'] == '') {
                        continue;
                    }
                    if (isset($mageSupAttrs[$nameValueList['Name']])) {
                        if (in_array($nameValueList['Value'], $mageSupAttrs[$nameValueList['Name']]) === false) {
                            $tempArr = $mageSupAttrs[$nameValueList['Name']];
                            array_push($tempArr, $nameValueList['Value']);
                            $mageSupAttrs[$nameValueList['Name']] = $tempArr;
                        }
                    } else {
                        $mageSupAttrs[$nameValueList['Name']] = [$nameValueList['Value']];
                    }
                }
            } else {
                if ($attrNameVal['Value'] == '') {
                    continue;
                }
                if (isset($mageSupAttrs[$attrNameVal['Name']])) {
                    if (in_array($attrNameVal['Value'], $mageSupAttrs[$attrNameVal['Name']]) === false) {
                        $tempArr = $mageSupAttrs[$attrNameVal['Name']];
                        array_push($tempArr, $attrNameVal['Value']);
                        $mageSupAttrs[$attrNameVal['Name']] = $tempArr;
                    }
                } else {
                    $mageSupAttrs[$attrNameVal['Name']] = [$attrNameVal['Value']];
                }
            }
        }
        return $mageSupAttrs;
    }

    /**
     * getAttributeOprionsByAttrId
     * @param int $attributeId
     * @return AttrOptionCollectionFactory
     */
    private function _getAttributeOprionsByAttrId($attributeId)
    {
        $attrOptions = $this->attrOptionCollectionFactory
                                        ->create()
                                        ->setPositionOrder('asc')
                                        ->setAttributeFilter($attributeId)
                                        ->setStoreFilter(0)
                                        ->load();
        return $attrOptions;
    }

    /*Get Magento Atribute Option Value*/
    /**
     * @param string $mageAttrCode
     * @param string $optionLabel
     *
     * @return string
     */
    public function getAttributeOptValue($mageAttrCode, $optionLabel)
    {
        $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
        if ($attributeInfo) {
            $attributeOptions = $this->attributeFactory->create()
                                     ->load($attributeInfo->getAttributeId())
                                     ->getSource()->getAllOptions(false);

            foreach ($attributeOptions as $kay => $attributeOption) {
                if ($attributeOption['label'] == $optionLabel) {
                    return $attributeOption;
                }
            }
        }
        return false;
    }

    /**
     * Save image in store.
     *
     * @param string $inPath
     * @param string $outPath
     */
    public function saveImage($inPath, $outPath)
    {
        try {
            $browserStr = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 '
                                    .'(KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $inPath);
            curl_setopt($ch, CURLOPT_USERAGENT, $browserStr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $file = fopen($outPath, 'w');
            if ($file === false) {
                $this->logger->info('saveImage : unable to open file');
                unlink($outPath);

                return false;
            }
            fwrite($file, $response);
            fclose($file);
            return true;
        } catch (\Exception $e) {
            $this->logger->info('saveImage : '.$e->getMessage());

            return false;
        }
    }

    /**
     * Add Images To Product.
     * @param int          $productId
     * @param string|array $images
     * @param int          $profileId
     */
    public function addImages($productId, $images)
    {
        $product = $this->product->create()->load($productId);
        foreach ($images['images'] as $image) {
            $image = trim($image);
            if ($image != '') {
                $imgPath = $this->_getNewImagePath($image, $product->getSku());
                $this->saveImage($image, $imgPath);
                if (file_exists($imgPath) && filesize($imgPath) > 0) {
                    if (function_exists('exif_imagetype')) {
                        $isPicture = exif_imagetype($imgPath) ? true : false;
                        if ($isPicture) {
                            $product->addImageToMediaGallery(
                                $imgPath,
                                ['image', 'small_image', 'thumbnail'],
                                false,
                                false
                            );
                            $product->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * _getNewImagePath
     * @param string $imageUrl
     * @param string $productSku
     * @return string
     */

    private function _getNewImagePath($imageUrl, $productSku)
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                                ->getAbsolutePath().'import/multiebaystoremageconnect/';
        $imgSrcExplode = explode('?', $imageUrl);
        $imgSrcExplode = explode('/', $imgSrcExplode[0]);
        $imageType = substr(strrchr($imgSrcExplode[count($imgSrcExplode) - 1], '.'), 1);
        $imgPath = $path.md5($imgSrcExplode[count($imgSrcExplode) - 1].$productSku)
                            .'.'.strtolower($imageType);
        return $imgPath;
    }

    /**
     * get count of imported items.
     * @param string $itemType
     * @return int
     */
    public function getTotalImportedCount($itemType, $ruleId)
    {
        $tempProCollection = $this->importedTmpProductRepository
        ->getCollectionByProductTypeAndRuleId($itemType, $ruleId);

        return $tempProCollection->getSize();
    }

    /**
     * Save Simple Product.
     * @param array $proData
     * @return array
     */
    public function saveSimpleProduct($proDataReq, $isAssociateProduct = 0, $attributeValues = [], $assocatedPro = [])
    {
        $defaultWebsiteId = $this->getDefaultWebsite();
        $proData = $proDataReq->getParams();
        // $proDataReq->clearParams();
        $result = ['error' => 0];
        $hasWeight = 1;
        $categoryIds = [];
        $sku = $isAssociateProduct ? $assocatedPro['sku'] : $proData['sku'];
        if ($this->isValidSku($proData['sku'])) {
            $wholeData = [
                            'form_key' => $this->formkey->getFormKey(),
                            'type' => $proData['type_id'],
                            'new-variations-attribute-set-id' => $proData['attribute_set_id'],
                            'set' => $proData['attribute_set_id']
                        ];
            $wholeData['product']['website_ids'] = [$defaultWebsiteId];
            $wholeData['product']['name'] = $proData['name'];
            $wholeData['product']['sku'] = $proData['sku'];
            $wholeData['product']['price'] = $proData['price'];
            $wholeData['product']['tax_class_id'] = $proData['tax_class_id'];
            $wholeData['product']['quantity_and_stock_status']['qty'] = isset($proData['stock'])?$proData['stock']: 1;
            $wholeData['product']['quantity_and_stock_status']['is_in_stock'] = $proData['is_in_stock'];
            $wholeData['product']['product_has_weight'] = $proData['weight'] ? true : false;
            $wholeData['product']['weight'] = $proData['weight'];
            $wholeData['product']['category_ids'] = $proData['category'];
            $wholeData['product']['description'] = $proData['description'];
            $wholeData['product']['status'] = $proData['status'];
            $wholeData['product']['visibility'] = 4;
            $wholeData['product']['stock_data']['manage_stock'] = 1;
            $wholeData['product']['stock_data']['use_config_manage_stock'] = 1;
            $wholeData['product']['stock_data']['qty'] = isset($proData['stock']) ? $proData['stock'] : 1;

            if (isset($proData['supperattr']) && count($proData['supperattr'])) {
                $wholeData['product']['supperattr'] = $proData['supperattr'];
                foreach ($proData['supperattr'] as $mageAttrCode) {
                    $attrInfo = $this->_getAttributeInfo($mageAttrCode);
                    if ($attrInfo) {
                        $wholeData['product']['attributes'][] = $attrInfo->getAttributeId();
                    }
                }
            }
            if (isset($proData['specification'])) {
                $brandCategory = ['433'=>'4','661'=>'6','688'=>'7','852'=>'8','483'=>'5'];
                $ebay_brand = isset($proData['specification']['ebay_brand']) ? $proData['specification']['ebay_brand'] : '' ;
                if (isset($brandCategory[$ebay_brand]) && $brandCategory[$proData['specification']['ebay_brand']] !="") {
                    $category = $brandCategory[$ebay_brand];
                } else {
                    $category = 9;
                }
                $wholeData['product']['category_ids'] = array_push($proData['category'], $category);
                foreach ($proData['specification'] as $attrCode => $value) {
                    $wholeData['product'][$attrCode] = $value;
                }
            }
            if ($isAssociateProduct == 1) {
                foreach ($attributeValues as $code => $value) {
                    $wholeData['product'][$code] = $value;
                }
                $wholeData['product']['visibility'] = 1;
                if (count($assocatedPro) > 0) {
                    $wholeData['type'] = 'simple';
                    $wholeData['product']['weight'] = $assocatedPro['weight'];
                    $wholeData['product']['sku'] = $assocatedPro['sku'];
                    $wholeData['product']['price'] = (float) $assocatedPro['price'];
                    $wholeData['product']['tax_class_id'] = trim($assocatedPro['tax_class_id']);
                    $wholeData['product']['quantity_and_stock_status']['qty'] = $assocatedPro['qty'];
                    $wholeData['product']['quantity_and_stock_status']['is_in_stock'] = 1;
                }
            }
            try {
                foreach ($wholeData as $key => $value) {
                    $proDataReq->setPostValue($key, $value);
                }
                $productId = (int) $this->saveProduct->saveProductData($proDataReq);
                isset($proData['image_data']) ? $this->addImages($productId, $proData['image_data']):'';
            } catch (\Execption $e) {
                $productId = 0;
            }

            $result = $productId ? ['error' => 0, 'product_id' => $productId]:
                                    [
                                        'error' => 1,
                                        'msg' => 'Skipped '.$proData['name'].'. error in importing product.'
                                    ];
        } elseif (isset($proData['revise']) && $proData['revise']) {
            $this->logger->info(' data revise saveSimple ');
            $this->logger->info(json_encode($proData));
            $productId = $this->updateMageProduct($proDataReq, $isAssociateProduct, $attributeValues, $assocatedPro);
            $result = $productId ? ['error' => 0, 'product_id' => $productId]:
            [
                'error' => 1,
                'msg' => 'Skipped '.$proData['name'].'. error in importing product.'
            ];
        } else {
            $result['error'] = 1;
            $result['msg'] = 'Skipped '.$proData['name'].". sku '".$proData['sku']."' already exist.";
        }
        return $result;
    }

    /**
     * revise product
     *
     * @param array $proDataReq
     * @param bool $isAssociateProduct
     * @param array $attributeValues
     * @param array $assocatedPro
     * @return void
     */
    public function updateMageProduct($proDataReq, $isAssociateProduct, $attributeValues, $assocatedPro)
    {
        try {
            $proData = $proDataReq->getParams();
            $proDataReq->clearParams();
            $this->logger->info(" revse ebay item in updateMageProduct ");
            $this->logger->info(json_encode($proData));
            $result = ['error' => 0];
            $hasWeight = 1;
            $categoryIds = [];
            $product = $this->productRepository->get($proData['sku']);
            $productId = $proData['id'];
            $wholeData['id'] = $productId;
            $wholeData['name'] = $proData['name'];
            $wholeData['sku'] = $proData['sku'];
            $wholeData['price'] = $proData['price'];
            $wholeData['tax_class_id'] = $proData['tax_class_id'];
            $wholeData['quantity_and_stock_status']['qty'] = isset($proData['stock']) ?
                                                                                    $proData['stock'] : 1;
            $wholeData['quantity_and_stock_status']['is_in_stock'] = $proData['is_in_stock'];
            $wholeData['product_has_weight'] = $proData['weight'] ? true : false;
            $wholeData['weight'] = $proData['weight'];
            $wholeData['category_ids'] = $proData['category'];
            $wholeData['description'] = $proData['description'];
            $wholeData['status'] = $proData['status'];
            $wholeData['visibility'] = 4;
            $wholeData['website_ids'][$this->getCurrentWebsiteId()] = 1;
            $wholeData['store_id'] = 0;
            $wholeData['stock_data']['manage_stock'] = 1;
            $wholeData['stock_data']['use_config_manage_stock'] = 1;
            $wholeData['stock_data']['qty'] = isset($proData['stock']) ? $proData['stock'] : 1;
            // if (isset($proData['supperattr']) && count($proData['supperattr'])) {
            //     $wholeData['supperattr'] = $proData['supperattr'];
            //     foreach ($proData['supperattr'] as $mageAttrCode) {
            //         $attrInfo = $this->_getAttributeInfo($mageAttrCode);
            //         if ($attrInfo) {
            //             $wholeData['attributes'][] = $attrInfo->getAttributeId();
            //         }
            //     }
            // }

            if ($isAssociateProduct == 1) {
                // foreach ($attributeValues as $code => $value) {
                //     $wholeData[$code] = $value;
                // }
                $wholeData['visibility'] = 1;
                if (count($assocatedPro) > 0) {
                    $product = $this->productRepository->get($assocatedPro['sku']);
                    $productId = $product->getId();
                    $wholeData['id'] = $productId;
                    $wholeData['type'] = 'simple';
                    $wholeData['weight'] = $assocatedPro['weight'];
                    $wholeData['sku'] = $assocatedPro['sku'];
                    $wholeData['price'] = (float) $assocatedPro['price'];
                    $wholeData['tax_class_id'] = trim($assocatedPro['tax_class_id']);
                    $wholeData['quantity_and_stock_status']['qty'] = $assocatedPro['qty'];
                    $wholeData['quantity_and_stock_status']['is_in_stock'] = 1;
                    $wholeData['website_ids'][$this->getCurrentWebsiteId()] = 1;
                    $wholeData['store_id'] = 0;
                }
            }
            try {
                $this->logger->info("updateMageProduct : going to update  ");
                $this->logger->info(json_encode($wholeData));
                $product->addData($wholeData);
                $this->productRepository->save($product, $saveOptions = false);

                if (isset($proData['image_data'])) {
                    $product = $this->product->create()->load($productId);
                    $existingMediaGalleryEntries = $product->getMediaGalleryEntries();
                    foreach ($existingMediaGalleryEntries as $key => $entry) {
                        unset($existingMediaGalleryEntries[$key]);
                    }
                    $product->setMediaGalleryEntries($existingMediaGalleryEntries);
                    $this->productRepository->save($product);
                    $this->addImages($productId, $proData['image_data']);
                }
            } catch (\Execption $e) {
                $this->logger->info('Product updateMageProduct error : '.$e->getMessage());
                $productId = 0;
            }
            return $productId;
        } catch (\Exception $e) {
            $this->logger->info('Product updateMageProduct : '.$e->getMessage());
        }
    }
    /**
     * Save Configurable Product.
     * @param array $proData
     * @return array
     */
    public function saveConfigProduct($proDataReq)
    {
        $proData = $proDataReq->getParams();
        $proDataReq->clearParams();
        $finalResult = ['error' => 0];
        $attributes = [];
        $associatedProductIds = [];
        $flag = true;
        $error = 0;
        $attributeCodetemp = '';
        try {
            if (count($proData['supperattr'])) {
                foreach ($proData['supperattr'] as $attributeCode) {
                    $attributeCode = trim($attributeCode);
                    $attributeId = $this->isValidAttribute($attributeCode);
                    if ($attributeId) {
                        $attributes[] = $attributeId;
                    } else {
                        $flag = false;
                        $attributeCodetemp = $attributeCodetemp.$attributeCode.',';
                        break;
                    }
                }
            } else {
                $flag = false;
            }
            if ($flag) {
                $errors = [];
                foreach ($proData as $key => $value) {
                    $proDataReq->setPostValue($key, $value);
                }
                $configResult = $this->addAssociatedProduct($proDataReq);
                $errorCount = 0;
                foreach ($configResult as $res) {
                    if (isset($res['error']) && $res['error'] == 1) {
                        ++$error;
                        $errors[] = $res['msg'];
                    } else {
                        $associatedProductIds[] = $res;
                    }
                }
                if (count($associatedProductIds) > 0) {
                    $proData['is_in_stock'] = 1;
                    foreach ($proData as $key => $value) {
                        $proDataReq->setPostValue($key, $value);
                    }
                    $result = $this->saveSimpleProduct($proDataReq);

                    if ($result['error'] == 0) {
                        $productId = $result['product_id'];
                        $this->completeConfigProduct($productId, $associatedProductIds, $attributes);
                        $finalResult['product_id'] = $productId;
                        $finalResult['ebay_sku'] = $proData['sku'];
                    } else {
                        $finalResult['error'] = 1;
                        $finalResult['msg'] = $result['msg'];
                    }
                } else {
                    $finalResult['error'] = 1;
                    $msg = 'Unable to create associated products.';
                    $finalResult['msg'] = implode('<br>', $errors);
                    $finalResult['msg'] = $msg.$finalResult['msg'];
                }
                if ($error > 0) {
                    if (count($associatedProductIds) == 0) {
                        $msg = 'Unable to create associated products.<br>';
                        $finalResult['msg'] = implode('<br>', $errors);
                        $finalResult['msg'] = $msg.$finalResult['msg'];
                    } else {
                        $finalResult['msg'] = implode('<br>', $errors);
                    }
                }
            } else {
                $finalResult['msg'] = 'Some of super attribute is not valid for product '.$proData['name'];
                $finalResult['error'] = true;
            }
        } catch (\Exception $e) {
            $finalResult = [
                'error' => true,
                 'msg' => 'Some of super attribute is not valid for product'
            ];
            $this->logger->info('Helper Data saveConfigProduct : '.$e->getMessage());
        }
        return $finalResult;
    }

    /**
     * Create Associated Product of Configurable Product.
     * @param int $customerId
     * @param int $profileId
     * @param int $row
     * @return array
     */
    public function addAssociatedProduct($proDataReq)
    {
        $proData = $proDataReq->getParams();
        $result = [];
        $parentData = [];
        $attributeValues = [];
        foreach ($proData['assocate_pro'] as $assocatedPro) {
            $flag = true;
            foreach ($proData['supperattr'] as $supAttrCode) {
                if (!isset($assocatedPro[$supAttrCode])) {
                    $flag = false;
                    break;
                } else {
                    $attributeValues[$supAttrCode] = $assocatedPro[$supAttrCode];
                }
            }
            if ($flag) {
                foreach ($proData as $key => $value) {
                    $proDataReq->setPostValue($key, $value);
                }
                $proInfo = $this->saveSimpleProduct($proDataReq, 1, $attributeValues, $assocatedPro);

                if (isset($proInfo['product_id'])) {
                    $result[] = $proInfo['product_id'];
                } else {
                    $this->logger->info('addAssociatedProduct : '.$proInfo['msg']);
                    $result['msg'] = $proInfo['msg'];
                    $result['error'] = 1;
                    continue;
                }
                $this->registry->unregister('product');
                $this->registry->unregister('current_product');
                $this->registry->unregister('current_store');
            } else {
                $result['msg'] = __('Some of super attribute is Not Valid for product ').$proData['name'];
                $result['error'] = 1;

                return $result;
            }
        }

        return $result;
    }

    /**
     * Add Associated Product to Configurabel Product After Creating Products.
     * @param int   $productId
     * @param array $associatedProductIds
     * @param array $attributes
     */
    public function completeConfigProduct($productId, $associatedProductIds, $attributes)
    {

        try {
            $product = $this->product->create()->load($productId);
            if ($product->getTypeId() != 'configurable') {
                $count = 0;
                foreach ($attributes as $attributeId) {
                    $data = [
                        'attribute_id' => $attributeId,
                        'product_id' => $productId,
                        'position' => $count
                    ];
                    ++$count;
                    $this->configurableAttributeModel->setData($data)->save();
                }
                $ebayConfig = $this->geteBayConfiguration($this->ruleId);
                $attributeSetId = $ebayConfig['attribute_set'];

                $product->setTypeId('configurable');
                $product->setAffectConfigurableProductAttributes($attributeSetId);
                $this->configurableProTypeModel->setUsedProductAttributeIds($attributes, $product);
                $product->setNewVariationsAttributeSetId($attributeSetId);
                $product->setAssociatedProductIds($associatedProductIds);
                $product->setCanSaveConfigurableAttributes(1);
            }
            $product->save();
            // reindex catalog search index
            $indexer = $this->indexerFactory->create()->load(self::CATALOGSEARCH_FULLTEXT);
            $indexer->reindexAll();
        } catch (\Exception $e) {
            $this->logger->info('Helper Data completeConfigProduct : '.$e->getMessage());
        }
    }

    /**
     * Create Imported eBay Order On Your Store.
     * @param array $orderData
     * @return array
     */
    public function createMageOrder($orderData)
    {
        try {
            $productNameError = '';
            if (!$orderData['shipping_address']['street'] || !$orderData['shipping_address']['country_id']) {
                return ['error' => 1,'msg' => __('order id ').$orderData['ebay_order_id'].__(' not contain address')];
            }
            $storeId = $this->getDefaultStoreForOrderSync();
            $store = $this->storeManager->getStore($storeId);
            // create ebay customer
            $customer = $this->createEbayCustomer($orderData);

            // prepare cart for eBay order
            $cartId = $this->_cartManagementInterface->createEmptyCart();
            $quote = $this->_cartRepositoryInterface->get($cartId);

            $quote->setStore($store);

            $customer = $this->_customerRepository->getById($customer->getEntityId());
            $quote->setCurrency();
            $quote->assignCustomer($customer);
            foreach ($orderData['items'] as $item) {
                $product = $this->product->create()->load($item['product_id']);
                $productNameError = $productNameError .' '. $product->getName().'( SKU : '.$product->getSku().')';
                $product->setPrice($item['price']);
                $quote->addProduct(
                    $product,
                    (int)$item['qty']
                );
            }
            //Set Address to quote
            $quote->getBillingAddress()->addData($orderData['shipping_address']);
            $quote->getShippingAddress()->addData($orderData['shipping_address']);

            // Collect Rates and Set Shipping & Payment Method
            $shipmethod = 'wk_multiebayship_wk_multiebayship';
            // Collect Rates and Set Shipping & Payment Method
            $this->_shippingRate
                ->setCode('wk_multiebayship_wk_multiebayship')
                ->getPrice(1);

            //store shipping data in session
            $this->backendSession->setMultiEbayShipDetail($orderData['shipping_service']);
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                            ->collectShippingRates()
                            ->setShippingMethod('wk_multiebayship_wk_multiebayship');
            $quote->getShippingAddress()->addShippingRate($this->_shippingRate);

            $quote->setPaymentMethod('checkmo');
            $quote->setInventoryProcessed(false);

            // Set Sales Order Payment
            $quote->getPayment()->importData(['method' => 'checkmo']);

            $quote->save();
            // Collect Totals & Save Quote
            $quote->collectTotals();
            // Create Order From Quote
            $quote = $this->_cartRepositoryInterface->get($quote->getId());
            $orderId = $this->_cartManagementInterface->placeOrder($quote->getId());
            $order = $this->_order->load($orderId);

            $ebayDefaultSettings = $this->getEbayDefaultSettings();
            $order->setStatus($ebayDefaultSettings['DefaultOrderStatus'])->save();
            $order->setState($ebayDefaultSettings['DefaultOrderStatus'])->save();
            $order->setEmailSent(0);
            $incrementId = $order->getRealOrderId();
            // Resource Clean-Up
            $quote = $customer = $service = null;
            if ($order->getEntityId()) {
                $result['order_id'] = $order->getRealOrderId();
            } else {
                $result = [
                    'error' => 1,
                    'msg' => __('order id ').$orderData['ebay_order_id'].__(' not created on your store')
                ];
            }
        } catch (\Exception $e) {
            $this->logger->info('Helper Data createMageOrder : '.$e->getMessage());
            $errorMsg = empty($productNameError) ? $e->getMessage() : $productNameError. ' is out of stock. please increase the stock to create order.';
            $result = [
                'error' => 1,
                'msg' => $errorMsg,
                'product_ids' => json_encode($orderData['items'])
            ];
        }
        return $result;
    }

    /**
     * create eBay customer if not exist
     *
     * @return object
     */
    public function createEbayCustomer($orderData)
    {
        $storeId = $this->getDefaultStoreForOrderSync();
        $store = $this->storeManager->getStore($storeId);
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);
        if (!$customer->getEntityId()) {
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email'])
                    ->setPassword($orderData['email']);
            $customer->save();
        }
        return $customer;
    }

    /**
     * Check for Valid Sku to Upload Product.
     * @param int|string $sku
     * @return bool
     */
    public function isValidSku($sku)
    {
        if ($sku == '') {
            return false;
        } else {
            return $this->product->create()->getIdBySku($sku) ? false : true;
        }
    }

    /**
     * Check Attribute Code is Valid or Not for Configurable Product.
     *
     * @param string $attributeCode
     *
     * @return bool
     */
    public function isValidAttribute($attributeCode)
    {
        $attribute = $this->attributeFactory->create()->getCollection()
                                           ->addFieldToFilter('attribute_code', ['eq' => $attributeCode])
                                           ->addFieldToFilter('frontend_input', 'select')
                                           ->getFirstItem();

        return $attribute->getId() ? $attribute->getId() : false;
    }

    /**
     * return Congigurable associated product id.
     * @param object $productId
     * @param array  $nameValueList
     * @return bool|int
     */
    public function getConfAssoProductId($productId, $nameValueList)
    {
        $assocateProId = false;
        $product = $this->product->create()->load($productId);
        $optionsData = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        $superAttrList = [];
        $superAttrOptions = [];
        $attributeValues = [];
        foreach ($optionsData as $option) {
            $superAttrList[] = [
                'name' => $option['frontend_label'],
                'code' => $option['attribute_code'],
                'id' => $option['attribute_id']
            ];
            $superAttrOptions[$option['attribute_id']] = $option['options'];
            foreach ($nameValueList as $nameValue) {
                if ($nameValue['Name'] == $option['frontend_label']) {
                    foreach ($option['options'] as $attrOpt) {
                        if ($nameValue['Value'] == $attrOpt['label']) {
                            $attributeValues[$option['attribute_id']] = $attrOpt['value'];
                        }
                    }
                }
            }
        }

        if (count($attributeValues) == count($nameValueList)) {
            $assPro = $this->configurableProTypeModel->getProductByAttributes($attributeValues, $product);
            $assocateProId = $assPro->getEntityId();
        }
        return $assocateProId;
    }


    /**
     * return Congigurable products variation for eBay.
     * @param object $product
     * @param array  $nameValueListTemp
     * @return string|array
     */
    public function getProductVariationForEbay($product, $nameValueListTemp)
    {
        $optionsDataList = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        $nameValueList = [];
        $superAttrList = [];
        $variation = '';
        foreach ($optionsDataList as $optionsData) {
            $flag = array_search($optionsData['frontend_label'], array_column($nameValueListTemp, 'Name'));
            if ($flag) {
                $optionsData['frontend_label'] = $optionsData['frontend_label'].' Custom';
            }
            $allValueList = [];
            foreach ($optionsData['values'] as $value) {
                $allValueList[] = $value['default_label'];
            }

            $nameValueList[] = ['Name' => $optionsData['frontend_label'],'Value' => $allValueList];
            $superAttrList[] = ['Name' => $optionsData['frontend_label'],'Code' => $optionsData['attribute_code']];
        }

        $associateProList = $this->configurableProTypeModel->getChildrenIds($product->getEntityId());
        if (isset($associateProList[0]) && !empty($associateProList[0])) {
            $proVariation = [];
            $pictures = [];
            foreach ($associateProList[0] as $associateProId) {
                $assNameValueList = [];
                $associatePro = $this->product->create()->load($associateProId);
                foreach ($superAttrList as $superAttr) {
                    $value = $associatePro->getAttributeText($superAttr['Code']);
                    $assNameValueList[] = ['Name' => $superAttr['Name'], 'Value' => $value];
                }

                $qty = $this->stockStateInterface->getStockQty($associateProId);
                $eBayDefaultSetting = $this->getEbayDefaultSettings();
                $variationProductListingDetails = $this->getVariationProductListingDetails($associatePro);
                $variationProductListingDetails['NameValueList'] =  $assNameValueList;
                $proVariation[] = [
                    'SKU' => strtolower(str_replace(' ', '', $associatePro->getSku())),
                    'StartPrice' => $associatePro->getPrice(),
                    'Quantity' => $qty ? $qty : $eBayDefaultSetting['DefaultProQty'],
                    'VariationSpecifics' => ['NameValueList' => $assNameValueList],
                    'VariationProductListingDetails' => $variationProductListingDetails
                ];
                $picturesTmp = $this->getImageDetailForVariationsProduct($assNameValueList, $associatePro);

                if (!empty($picturesTmp)) {
                    $pictures[] = $picturesTmp;
                }
            }
            $variation['Pictures'] = $pictures;
            $variation['VariationSpecificsSet'] = ['NameValueList' => $nameValueList];
            $variation['Variation'] = $proVariation;
        }
        return $variation;
    }

    /**
     * get ProductListingDetails
     * @param Webkul\Ebaymagentoconnect\Model\Ebaycategorymap $catMapedRecord
     * @return array
     */
    private function getVariationProductListingDetails($associatePro)
    {
        $listingDetail = [];
        $listingDetail['EAN'] = $associatePro->getEan() ? $associatePro->getEan() : 'Non applicabile';
        //$listingDetail['UPC'] = $associatePro->getUpc() ? $associatePro->getUpc() : 'Non applicabile';
        return $listingDetail;
    }

    /**
     * getImageDetailForVariationsProduct
     * @param array $assNameValueList
     * @param Magento/Catalog/Model/Product
     * @return array $pictures
     */
    private function getImageDetailForVariationsProduct($assNameValueList, $associatePro)
    {
        $pictures = [];
        $pictureUrl = $this->getPictureUrl($associatePro);
        if (!empty($pictureUrl)) {
            foreach ($assNameValueList as $valueDetail) {
                $pictures[] = [
                    'VariationSpecificName'=> $valueDetail['Name'] ,
                    'VariationSpecificPictureSet' => [
                        'PictureURL' => $pictureUrl['PictureURL'],
                        'VariationSpecificValue' => $valueDetail['Value']
                    ]
                ];
            }
        }
        return $pictures;
    }

    /**
     * getPictureUrl use for get magento product image url.
     * @param object $product
     * @return array
     */
    public function getPictureUrl($product)
    {
        $_productImagesList = $product->getMediaGalleryImages();
        $pictureUrl = [];
        $pictureDetails = '';
        $imgCount = 1;
        foreach ($_productImagesList as $_image) {
            $galleryUrl = $_image->getUrl();
            $pictureUrl[] = $_image->getUrl();
            if ($imgCount++ > 11) {
                break;
            }
        }

        if (count($pictureUrl)) {
            $pictureDetails = [
                                'GalleryType' => 'Gallery',
                                'GalleryURL' => $galleryUrl,
                                'PhotoDisplay' => 'PicturePack',
                                'PictureURL' => $pictureUrl,
                            ];
        }
        return $pictureDetails;
    }

    /**
     * @param $mageAttrCode string
     * @param $labels array of options label according to store
     * @param $values array of spicification/variations options
     * @return array of prepared all options of attribute
     */
    private function _getAttributeOptions($mageAttrCode, $labels, $values)
    {
        $allStores = $this->storeManager->getStores();
        $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
        $option = [];
        if ($attributeInfo) {
            $attribute = $this->attributeFactory->create()->load($attributeInfo->getAttributeId());
            $attribute->setStoreLabels($labels)->save();
            $option['attribute_id'] = $attribute->getAttributeId();
            foreach ($values as $key => $value) {
                $option['value']['wk'.$value][0] = $value;
                foreach ($allStores as $store) {
                    $option['value']['wk'.$value][$store->getId()] = $value;
                }
            }
        }
        return $option;
    }

    /**
     * get top level magento category list
     * @return array
     */
    public function getTopLevelMageCategory()
    {
        return $this->rootCat->tomageCatArray();
    }

    /**
     * get top level ebay categories list
     * @return array
     */
    public function getTopLevelEbayCategory()
    {
        return $this->rootCat->toebayCatArray();
    }


    /**
     * saveObject
     *
     */
    private function saveObject($object)
    {
        $object->save();
    }

    /**
     * createEanUpcAttribute
     * @param attributenName
     * @return void
     */
    public function createEanUpcAttribute($attrCode, $attributeSetId = null)
    {
        $attributeLabel = ['ean' => __('EAN Number'), 'upc' => __('UPC Number')];
        $attributeInfo = $this->_getAttributeInfo($attrCode);
        $attributeGroupId = $this->getAttributeGroupId('Ebay Specification', $attributeSetId);
        if ($attributeInfo === false) {
            $attributeScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE;
            $attribute = $this->attributeFactory->create();
            $attrData = [
                'entity_type_id' => $this->entityTypeId,
                'attribute_code' => $attrCode,
                'frontend_label' => [0 => $attributeLabel[$attrCode]],
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'backend' => '',
                'frontend' => '',
                'source' => '',
                'global' => $attributeScope,
                'visible' => true,
                'required' => false,
                'is_user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_html_allowed_on_front' => true,
                'visible_in_advanced_search' => false,
                'unique' => false,
            ];
            try {
                $attribute->setData($attrData);
                $attribute->save();
            } catch (\Exception $e) {
                $this->logger->info('Create createEanUpcAttribute : '.$e->getMessage());
            }
        } else {
            try {
                $this->attributeManagement->assign(
                    $this->entityType,
                    $attributeSetId,
                    $attributeGroupId,
                    $attrCode,
                    $attributeInfo->getAttributeId()
                );
            } catch (\Exception $e) {
                $this->logger->info('assign createEanUpcAttribute : '.$e->getMessage());
            }
        }
    }

    /**
     * @param object createProSpecificationAttribute
     * @return string
     */
    public function createProSpecificationAttribute($eBaySpecificationList, $attributeSetId = null)
    {
        $allStores = $this->storeManager->getStores();

        $attributeGroupId = $this->getAttributeGroupId('Ebay Specification', $attributeSetId);
        $mageAttrCodeList = [];
        foreach ($eBaySpecificationList as $eBaySpecification) {
            if (is_array($eBaySpecification['Value'])) {
                $input = 'multiselect';
                $type = 'varchar';
                $values = $eBaySpecification['Value'];
            } else {
                $input = 'select';
                $type = 'int';
                $values = [0 => $eBaySpecification['Value']];
            }
            $attributeCode = str_replace(' ', '_', $eBaySpecification['Name']);
            $attributeCode = preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
            $mageAttrCode = substr('ebay_'.strtolower($attributeCode), 0, 30);
            $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
            if ($attributeInfo === false) {
                $attributeScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE;
                $attribute = $this->attributeFactory->create();
                $attrLabel = $eBaySpecification['Name'];
                $attrData = [
                    'entity_type_id' => $this->entityTypeId,
                    'attribute_code' => $mageAttrCode,
                    'frontend_label' => [0 => $attrLabel],
                    'attribute_group_id' => $attributeGroupId,
                    'attribute_set_id' => $attributeSetId,
                    'backend_type' => $type,
                    'frontend_input' => $input,
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'frontend' => '',
                    'source' => '',
                    'global' => $attributeScope,
                    'visible' => true,
                    'required' => false,
                    'is_user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_in_advanced_search' => false,
                    'unique' => false,
                ];

                if ($input != 'text') {
                    $labels = [];
                    foreach ($allStores as $store) {
                        $labels[$store->getId()] = $eBaySpecification['Name'];
                    }
                    $option = $this->_getAttributeOptions($mageAttrCode, $labels, $values);
                    $attrData['option'] = $option;
                }
                try {
                    $attribute->setData($attrData);
                    $attribute->save();
                } catch (\Exception $e) {
                    $this->logger->info('Create createProductAttribute : '.$e->getMessage());
                }
            } else {
                if ($input != 'text') {
                    try {
                        $option = $this->_getAttributeOptionsForEdit($attributeInfo->getAttributeId(), $values);
                        $this->attributeManagement->assign(
                            $this->entityType,
                            $attributeSetId,
                            $attributeGroupId,
                            $mageAttrCode,
                            $attributeInfo->getAttributeId()
                        );
                        if (isset($option['value'])) {
                            $attr = $this->productAttribute->get($attributeInfo->getAttributeCode());
                            $attr->setOption($option);
                            $this->productAttribute->save($attr);
                        }
                    } catch (\Exception $e) {
                        $this->logger->info('update createProductAttribute : '.$e->getMessage());
                    }
                }
            }
            $optionValues = $this->getAttrOptionValue($mageAttrCode, $eBaySpecification['Value']);
            $mageAttrCodeList[$mageAttrCode] = $optionValues;
        }
        return $mageAttrCodeList;
    }

    /**
     * getAttrOptionValue
     * @param string $mageAttrCode
     * @param string $value
     * @return int|false
     */
    public function getAttrOptionValue($mageAttrCode, $value)
    {
        $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
        if ($attributeInfo) {
            $attributeOptions = $this->attrOptionCollectionFactory->create()
                                                ->setPositionOrder('asc')
                                                ->setAttributeFilter($attributeInfo->getAttributeId())
                                                ->setStoreFilter(0)->load();
            $optionsValues = [];
            $valueList = is_array($value) ? $value : [$value];
            $attributeOptionsArray = [];
            foreach ($attributeOptions as $attributeOption) {
                $attributeOptionsArray[$attributeOption->getOptionId()] = $attributeOption->getDefaultValue();
            }
            $optionIds = '';
            foreach ($valueList as $value) {
                foreach ($attributeOptionsArray as $key => $attrOptDefaultValue) {
                    if (strcmp($value, $attrOptDefaultValue) == 0) {
                        $optionIds = $optionIds.$key.',';
                    }
                }
            }
            return substr(trim($optionIds), 0, -1);
        }
        return false;
    }

    /**
     * disable plateform event notification
     *
     * @param object $client
     * @param integer $sellerId
     * @return void
     */
    public function disableEventNotification($client, $sellerId = 0)
    {
        $params = ['Version' => self::_WSDL_VERSION_,
            'ApplicationDeliveryPreferences' => [
            'ApplicationEnable' => 'Disable'
            ],];

        $results = $client->SetNotificationPreferences($params);
    }

    /**
     * enable event notification
     *
     * @param object $client
     * @param integer $sellerId
     * @return void
     */
    public function enableEventNotification($client, $sellerId = 0, $eBayEvents)
    {
        try {
            $storeId = $this->storeManager->getDefaultStoreView()->getStoreId();
            $pageUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
            $pageUrl = $pageUrl. 'multiebaystoremageconnect/eventnotification/ebaylistner';
            $params = ['Version' => self::_WSDL_VERSION_,
            'ApplicationDeliveryPreferences' => [
            'ApplicationURL' => $pageUrl,]];

            $results = $client->SetNotificationPreferences($params);
            if ($results->Ack == 'Success') {
                $setRealEvent = $this->_setRealTimeUpdateNotificationEvent($client, $pageUrl, $sellerId, $eBayEvents);
                if ($setRealEvent->Ack =='Success') {
                    $responce = [
                                'notification' => __('Successfully Set Notification Events')
                    ];
                } else {
                    $responce = [
                                'error_msg' => $setRealEvent->Errors->LongMessage
                    ];
                }
            } else {
                $responce = [
                            'error_msg' => $results->Errors->LongMessage
                ];
            }
        } catch (\Exception $e) {
            $this->logger->info('data enableEventNotification : '.$e->getMessage());
        }
    }

    /**
     * enable some event for ebay
     *
     * @param object $client
     * @param string $applicationUrl
     * @param int $sellerId
     * @return void
     */
    private function _setRealTimeUpdateNotificationEvent($client, $applicationUrl, $sellerId, $eBayEvents)
    {
        $customerEmail = $this->getDefaultTransEmailId();

        $subEbayEvents = [];
        foreach ($eBayEvents as $event) {
            $subEbayEvents[] = [
                                    'EventType' => $event,
                                    'EventEnable' => 'Enable'
                                ];
        }
        $params = ['Version' => self::_WSDL_VERSION_, 'WarningLevel' => 'High',
            'ApplicationDeliveryPreferences' => ['ApplicationEnable' => 'Enable',
                                                      'ApplicationURL' => $applicationUrl,
                                                      'DeviceType' => 'Platform',
                                                      'AlertEmail'=>'mailto://'.$customerEmail,
                                                      'AlertEnable'=> 'Enable',
                                                      'PayloadVersion'=> self::_WSDL_VERSION_],
            'UserDeliveryPreferenceArray' => [
                'NotificationEnable' => $subEbayEvents
            ]
        ];

        $result = $client->SetNotificationPreferences($params);
        return $result;
    }

    /**
     * get account info by ebay user id
     *
     * @param string $userId
     * @return int
     */
    public function getSellerIdByeBayUserId($userId)
    {
        $sellerId = 0;
        $ebaySellerAcc = $this->ebayAccountsRepository->getByUserId($userId);

        if ($ebaySellerAcc->getSize()) {
            foreach ($ebaySellerAcc as $account) {
                return $account->getEntityId();
            }
        }
        return $sellerId;
    }

    /**
     * disable magento product
     *
     * @param int $sellerId
     * @param int $mageProId
     * @return void
     */
    public function ebayItemClosed($sellerId, $mageProId)
    {
        $this->logger->info(' about to disable product id '.$mageProId);
        $product = $this->productRepository->getById($mageProId, true, 0, true);
        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
        $this->productRepository->save($product);
    }

    /**
     * update ebay maento product
     *
     * @param int $sellerId
     * @param boolean $client
     * @param int $ebayItemId
     * @return void
     */
    public function updateItemQtyAtMage($sellerId, $client = false, $ebayItemId)
    {
        try {
            if (empty($client)) {
                $client = $this->getEbayAPI($sellerId);
            }
            if ($mappCol = $this->ebayIdExist($sellerId, $ebayItemId)) {
                $mageProId = $mappCol->getMagentoProId();
                $productType = $mappCol->getProductType();
                $results = $this->geteBayItemDetails($client, $ebayItemId);
                $productStock = $this->productRepository->getById($mageProId);

                if ($results) {
                    if ($productType === 'configurable') {
                        $variationData = $this->getVariationDataOfEbayItem($results);
                        $associateProducts = $this->getChildOfConfigurable($productStock);
                        foreach ($associateProducts as $child) {
                            $childSku = $child->getSku();
                            $availQty = $this->getUpdatedItemQty($childSku, $variationData);
                            $stockItem = $this->stockRegistry->getStockItem($child->getId());
                            $stockItem->setData('qty', $availQty);
                            $this->stockRegistry->updateStockItemBySku($childSku, $stockItem);
                        }
                    } else {
                        $availQty = $results->Item->Quantity - $results->Item->SellingStatus->QuantitySold;
                        $stockItem = $this->stockRegistry->getStockItem($mageProId);
                        $stockItem->setData('qty', $availQty);
                        $this->stockRegistry->updateStockItemBySku($productStock->getSku(), $stockItem);
                        $this->logger->addError('updateItemQtyAtMage none none'.$availQty);
                    }
                    return true;
                }
                return false;
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->addError('updateItemQtyAtMage '. $e->getMessage());
        }
    }

    /**
     * check ebay item exist or not
     *
     * @param int $sellerId
     * @param int $ebayItemId
     * @return object | bool
     */
    public function ebayIdExist($sellerId, $ebayItemId)
    {
        try {
            $mapProColl = $this->productmapFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('rule_id', ['eq'=>$sellerId])
                    ->addFieldToFilter('ebay_pro_id', ['eq'=>$ebayItemId]);
            if ($mapProColl->getSize()) {
                return $mapProColl->getFirstItem();
            }
            return 0;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data isOrderIdExist '. $e->getMessage());
        }
    }

    /**
     * get ebay item details
     *
     * @param object $client
     * @param string $ebayItemId
     * @return bool|object
     */
    public function geteBayItemDetails($client, $ebayItemId)
    {
        try {
            $params = ['Version' => self::_WSDL_VERSION_, 'DetailLevel' => 'ReturnAll', 'ItemID' => $ebayItemId, 'IncludeItemSpecifics' => true];
            $results = $client->GetItem($params);
            if (isset($results->Ack) && ((string) $results->Ack == 'Success' || (string) $results->Ack == 'Warning')) {
                return $results;
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data geteBayItemDetails '. $e->getMessage());
        }
    }

    /**
     * get variation data of ebay item
     *
     * @param object $ebayResponse
     * @return array
     */
    public function getVariationDataOfEbayItem($ebayResponse)
    {
        try {
            $wholeVariation = json_decode(json_encode($ebayResponse), true);
            $ebayVariationData = [];
            if (isset($wholeVariation['Item']['Variations']['Variation'])) {
                $itemVariation = isset($wholeVariation['Item']['Variations']['Variation'][0]) ? $wholeVariation['Item']['Variations']['Variation'] :[0 => $wholeVariation['Item']['Variations']['Variation']];

                foreach ($itemVariation as $variant) {
                    $ebayVariationData[] = [
                        'sku' => isset($variant['SKU']) ? $variant['SKU'] : '',
                        'qty' => $variant['Quantity'] -  $variant['SellingStatus']['QuantitySold'],
                        'price' => $variant['StartPrice']['_'],
                        'VariationSpecifics' => json_encode($variant['VariationSpecifics'])
                    ];
                }
            }
            return $ebayVariationData;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getVariationDataOfEbayItem '. $e->getMessage());
        }
    }

    /**
     * get child product of configurable product
     *
     * @param object $product
     * @return object
     */
    public function getChildOfConfigurable($product)
    {
        try {
                $productTypeInstance = $product->getTypeInstance();
                $usedProducts = $productTypeInstance->getUsedProducts($product);
                return $usedProducts;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getChildOfConfigurable '. $e->getMessage());
        }
    }

    /**
     * update item qty
     *
     * @param string $childSku
     * @param array $variationData
     * @return int
     */
    public function getUpdatedItemQty($childSku, $variationData)
    {
        try {
            $qty = 0;
            foreach ($variationData as $variation) {
                if ($variation['sku'] === $childSku) {
                    $qty = $variation['qty'];
                    break;
                }
            }
            return $qty;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getUpdatedItemQty '. $e->getMessage());
        }
    }

    /**
     * get order ids by item id
     *
     * @param object $client
     * @param int $itemId
     * @return void
     */
    public function getOrderIdsByItemId($client, $itemId)
    {
        try {
            $params = ['Version' => self::_WSDL_VERSION_,
                        'IncludeContainingOrder' => true,
                        'ItemID' => $itemId, ];
            $results = $client->GetItemTransactions($params);
            $orderIds = [];
            if (is_object($results->TransactionArray->Transaction)) {
                $data = $results->TransactionArray->Transaction;
                $orderIds[] = $data->ContainingOrder->OrderID;
            } else {
                foreach ($results->TransactionArray->Transaction as $data) {
                    $orderIds[] = $data->ContainingOrder->OrderID;
                }
            }
            return $orderIds;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getOrderIdsByItemId '. $e->getMessage());
        }
    }

    /**
     * get ebay order info
     *
     * @param int $orderId
     * @param object $client
     * @return void
     */
    public function getOrderInfo($orderId, $client)
    {
        try {
            $data = [
                'Version' => self::_WSDL_VERSION_,
                'WarningLevel' => 'High'
            ];

            $datetime = $client->GeteBayOfficialTime($data);
            $currentDate = $datetime->Timestamp;
            $dt = new \DateTime($currentDate);
            $dt->modify('-89 day');
            $endTime = $dt->format('Y-m-d\TH:i:s.u\Z');
            $params = [
                'Version' => self::_WSDL_VERSION_,
                'DetailLevel' => 'ReturnAll',
                'OrderIDArray' => ['OrderID' => $orderid],
                'Pagination' => [
                    'EntriesPerPage' => '100',
                    'PageNumber' => 1
                ],
                'CreateTimeFrom' => $endTime,
                'CreateTimeTo' => $currentDate,
                'OrderRole' => 'Seller',
            ];
            $results = $client->GetOrders($params);
            return $results;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getOrderInfo '. $e->getMessage());
        }
    }

    /**
     * [addRealTimeOrderByOrderId from eBay to prestashop through listner].
     *
     * @param [string] $id_order [eBay order ID]
     */
    public function addRealTimeOrderByOrderId($ebayUserId, $client, $orderIds)
    {
        foreach ($orderids as $orderId) {
            if (!$this->isOrderIdExist($ebayUserId, $orderId)) {
                $results = $this->getOrderInfo($orderId, $client);
                try {
                    if (isset($results->OrderArray->Order)) {
                        $eBayOrders = json_decode(
                            json_encode($results->OrderArray->Order),
                            true
                        );
                        $eBayOrders = isset($eBayOrders[0]) ?
                                        $eBayOrders : [0 => $eBayOrders];
                        $tempOrder = $this->_objectManager('Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData')
                        ->ManageOrderRawData($eBayOrders, $this->sellerId, false, true);
                        $this->logger->info(' order temp data '.json_encode($tempOrder));
                        $orderData = $this->createMageOrder($tempOrder);
                        $this->logger->info(' created order data '.json_encode($orderData));
                        if (isset($orderData['order_id']) && $orderData['order_id']) {
                            $data = [
                                'ebay_order_id' => $tempOrder['ebay_order_id'],
                                'mage_order_id' => $orderData['order_id'],
                                'status' => $tempOrder['order_status'],
                                'rule_id'   => $this->sellerId
                            ];
                            $record = $this->orderMapRecord;
                            $record->setData($data)->save();
                        }
                    }
                } catch (\Exception $e) {
                    $this->logger->info('Helper Data addRealTimeOrderByOrderId '. $e->getMessage());
                }
            } else {
                $this->logger->info(' Order Id already Mapped '. $orderId);
            }
        }
    }

    /**
     * check order exist or not.
     *
     * @param string $ebayUserId
     * @param int $orderId
     * @return boolean
     */
    public function isOrderIdExist($ebayUserId, $orderId)
    {
        try {
            $this->sellerId = $this->getSellerIdByeBayUserId($ebayUserId);
            $mapOrderColl = $this->ordermapRepository->getByAccountIdnOrderId($this->sellerId, $orderId);
            if ($mapOrderColl->getSize()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->info('Helper Data isOrderIdExist '. $e->getMessage());
        }
    }

    /**
     * getProductDescription
     */
    public function getProductDescription($product, $eBayDefaultSetting, $templateId)
    {
        try {
            if ($eBayDefaultSetting['useTemplate']) {
                $templateDetail = $this->listingTemplate->create()->load($templateId);

                $templateContent = $templateDetail->getTemplateContent();
                $mapAttributes = $this->jsonHelper->jsonDecode($templateDetail->getMappedAttribute());
                foreach ($mapAttributes as $key => $value) {
                    $attrValue = $product->getResource()->getAttribute($value['mage_attr']);
                    if ($attrValue) {
                        $attrValue = $attrValue->getFrontend()->getValue($product);
                        $templateContent = str_replace('#'.$value['temp_var'], $attrValue, $templateContent);
                    }
                }
                $description = $templateContent;
            } else {
                $description = $product->getDescription();
            }
            $description = $this->templateProcessor->filter($description);
            return $this->isDesWithHtml() ? $description : $this->filterManager->stripTags($description);
        } catch (\Exception $e) {
            $this->logger->addError('getProductDescription');
        }
    }

    /**
     * get mapped category data
     *
     * @param int $ruleId
     * @return void
     */
    public function getMappedCategoryData($ruleId)
    {
        $ebayExistCatColl = $this->ebayCategoryMapRepository->getCollectionByRuleId($ruleId);

        if ($ebayExistCatColl->getSize()) {
            return $ebayExistCatColl->toArray();
        }
        return false;
    }

    /**
     * getBundleProductVariationForEbay
     * @param Magento/Catalog/Model/Product $product,
     * @param int $defaultProQty,
     * @return array
     */
    public function getBundleProductVariationForEbay($product, $defaultProQty)
    {
        $options = $this->getBundleProductOptions($product);
        $tempNameValueLists = [];
        $nameValueLists = [];
        foreach ($options as $k => $option) {
            $allvalue = [];
            $allTmpValue = [];
            foreach ($option['selections'] as $value) {
                $allvalue[] = ['lable' => $value['name'], 'pro_id' => $value['entity_id']];
                $allTmpValue[] = $value['name'];
            }
            $tempNameValueLists[] = ['title' => $option['default_title'], 'values' => $allvalue, 'id' => $k];
            $nameValueLists[] = ['Name' => $option['default_title'], 'Value' => $allTmpValue];
        }
        $proVariations = $this->variationsForeBay->getVariations($tempNameValueLists, true);
        $proVariationList = [];
        foreach ($proVariations as $key => $proVariationData) {
            $assNameValueList = [];
            $sku = "";
            $stock = [];
            $price = $product->getPriceType() == 1 ? $product->getPrice() : 0;

            foreach ($proVariationData as $optionKey => $value) {
                $qty = $this->stockStateInterface->getStockQty($value['pro_id']);
                array_push($stock, $qty);
                $associatePro = $this->productRepository->getById($value['pro_id']);
                $sku .= $associatePro->getSku().'-b-';
                $price += $product->getPriceType() == 1 ? 0 : $associatePro->getPrice();
                $assNameValueList[] = [
                    'Name' => $nameValueLists[$optionKey - 1]['Name'],
                    'Value' => $value['lable']
                ];
            }
            $price = $this->getPriceAfterAppliedRule($price, 'export');
            $sku = substr($sku, 0, -3);
            $variationProductListingDetails = ['EAN' => 'Non applicabile', 'NameValueList' => $assNameValueList];

            $proVariationList[] = [
                'SKU' => $sku,
                'StartPrice' => $price,
                'Quantity' => $stock ? min($stock) : $defaultProQty,
                'VariationSpecifics' => ['NameValueList' => $assNameValueList],
                'VariationProductListingDetails' => $variationProductListingDetails
            ];
        }

        $variation['VariationSpecificsSet'] = ['NameValueList' => array_values($nameValueLists)];
        $variation['Variation'] = $proVariationList;
        return !empty($nameValueLists)? $variation : '';
    }

    /**
     * getBundleProductOptions
     * @param Magento/Catalog/Model/Product $product,
     * @return $BundleProductOptions
     */
    public function getBundleProductOptions($product)
    {
        $typeInstance = $product->getTypeInstance(true);
        $selectionCollection = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
        $optionCollection = $typeInstance->getOptionsCollection($product);
        $skipSaleableCheck = $this->productHelper->getSkipSaleableCheck();
        return $optionCollection->appendSelections($selectionCollection, false, $skipSaleableCheck);
    }

/**
 * getGroupedProductVariationForEbay
 * @param Magento/Catalog/Model/Product $product,
 * @param int $defaultProQty,
 * @return array
 */
    public function getGroupedProductVariationForEbay($product, $defaultProQty)
    {
        $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
        $allValue = [];
        foreach ($associatedProducts as $assPro) {
            $allValue[] = $assPro->getName();
            $stock = $this->stockStateInterface->getStockQty($assPro->getEntityId());
            $proVariationList[] = [
                'SKU' => $assPro->getSku(),
                'StartPrice' => $this->getPriceAfterAppliedRule($assPro->getPrice(), 'export'),
                'Quantity' => $stock ? $stock : $defaultProQty,
                'VariationSpecifics' => ['NameValueList' => [['Name' => 'Options', 'Value' => $assPro->getName()]]],
                'VariationProductListingDetails' => [
                    'EAN' => 'Non applicabile',
                    'NameValueList' => [['Name' => 'Options', 'Value' => $assPro->getName()]]
                ]
            ];
        }
        $nameValueLists[] = ['Name' => 'Options', 'Value' => $allValue];
        $variation['VariationSpecificsSet'] = ['NameValueList' => array_values($nameValueLists)];
        $variation['Variation'] = $proVariationList;
        return !empty($nameValueLists)? $variation : '';
    }

    /**
     * getOptionsIneBayVariationsFormat
     * @param \Magento\Catalog\Model\Product $product
     * @param int $defaultProQty,
     * @return array
     */
    public function getOptionsIneBayVariationsFormat($product, $defaultProQty)
    {
        $optionsList = $this->productOptions->getProductOptionCollection($product)
                                        ->addFieldToFilter('type', ['in' => ['radio', 'drop_down']]);
        $proVariations = $this->variationsForeBay->getVariations($optionsList);
        $proVariation = [];
        $nameValueList = [];
        $stock = $this->stockStateInterface->getStockQty($product->getEntityId());
        $stock = $stock ? $stock : $defaultProQty;
        foreach ($proVariations as $key => $productData) {
            $assNameValueList = [];
            $startPrice = $product->getPrice();
            foreach ($productData as $optionId => $optNameValue) {
                $assNameValueList[] = [
                    'Name' => $optNameValue['variation_title'],
                    'Value' => $optNameValue['default_title']
                ];
                $startPrice = (float)$startPrice + (float) $optNameValue['default_price'];
                $nameValueList[$optionId]['Value'][] = $optNameValue['default_title'];
                $nameValueList[$optionId]['Value'] = array_values(array_unique($nameValueList[$optionId]['Value']));
                $nameValueList[$optionId]['Name'] = $optNameValue['variation_title'];
            }

            $variationProductListingDetails = ['EAN' => 'Non applicabile', 'NameValueList' => $assNameValueList];
            /** apply price rules */
            $startPrice = $this->getPriceAfterAppliedRule($startPrice, 'export');

            $proVariation[] = [
                'SKU' => strtolower(str_replace(' ', '', $product->getSku()).'-'.($key+1)),
                'StartPrice' => $startPrice,
                'Quantity' => $stock,
                'VariationSpecifics' => ['NameValueList' => $assNameValueList],
                'VariationProductListingDetails' => $variationProductListingDetails
            ];
        }
        $variation['VariationSpecificsSet'] = ['NameValueList' => array_values($nameValueList)];
        $variation['Variation'] = $proVariation;
        return !empty($nameValueList)? $variation : '';
    }

    /**
     * save data in table
     * @param  array $completeWellFormedData
     * @return null
     */
    public function InsertDataInBulk($completeWellFormedData = [])
    {
        try {
            if (!empty($completeWellFormedData)) {
                $numberOfRecond = 500;
                $indexNumber = 0;
                $allCount = count($completeWellFormedData);
                if (count($completeWellFormedData) > $numberOfRecond) {
                    while (count($completeWellFormedData) > $indexNumber) {
                        $slicedArray = [];
                        if (count($completeWellFormedData) > ($indexNumber+$numberOfRecond)) {
                            $slicedArray = array_slice($completeWellFormedData, $indexNumber, $numberOfRecond);

                            $this->dbStorage->insertMultiple('wk_multiebay_categories', $slicedArray);
                            $indexNumber = $indexNumber + $numberOfRecond;
                        } else {
                            $remainingIndexes = $allCount -  $indexNumber;
                            $slicedArray = array_slice($completeWellFormedData, $indexNumber, $remainingIndexes);
                            $this->dbStorage->insertMultiple('wk_multiebay_categories', $slicedArray);
                            $indexNumber = $indexNumber + $remainingIndexes;
                            break;
                        }
                    }
                } else {
                    $this->dbStorage->insertMultiple('wk_multiebay_categories', $completeWellFormedData);
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('ManageProductRawData InsertDataInBulk : '.$e->getMessage());
        }
    }

    /**
     * getProductRepository
     * @return \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public function getProductRepository()
    {
        return $this->productRepository;
    }

    /**
     * get amazon price rule by price
     *
     * @param integer $price
     * @return object
     */
    public function getPriceRuleByPrice($price)
    {
        $eBayPriceRule = $this->priceRule
                ->create()
                ->getCollection()
                ->addFieldToFilter('ebay_account_id', ['eq' => $this->ruleId])
                ->addFieldToFilter('price_from', ['lteq' => round($price)])
                ->addFieldToFilter('price_to', ['gteq' => round($price)])
                ->addFieldToFilter('status', ['eq' => 1])->setPageSize(1);
        if ($eBayPriceRule->getSize()) {
            return $eBayPriceRule->getFirstItem();
        }
        return false;
    }

    /**
     * get price after applied price rule
     *
     * @param object $ruleData
     * @param int $price
     * @param string $process
     * @return void
     */
    public function getPriceAfterAppliedRule($price, $process)
    {
        try {
            $ruleData = $this->getPriceRuleByPrice($price);
            if ($price && $ruleData) {
                if ($ruleData->getOperationType() === 'Fixed') {
                    $price = $this->getFixedPriceCalculation($ruleData, $price, $process);
                } else {
                    $price = $this->getPercentPriceCalculation($ruleData, $price, $process);
                }
            }
            return $price;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getPriceAfterAppliedRule : '.$e->getMessage());
        }
    }

    /**
     * done fixed price rule calcuation
     *
     * @param object $ruleData
     * @param int $price
     * @param string $process
     * @return int
     */
    public function getFixedPriceCalculation($ruleData, $price, $process)
    {
        try {
            if ($ruleData->getOperation() === 'Increase') {
                if ($process === $this->config['price_rule_on']) {
                    $price = $price + $ruleData->getPrice();
                } else {
                    $price = $price - $ruleData->getPrice();
                }
            } else {
                if ($process === $this->config['price_rule_on']) {
                    $price = $price - $ruleData->getPrice();
                } else {
                    $price = $price + $ruleData->getPrice();
                }
            }
            return $price;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getFixedPriceCalculation : '.$e->getMessage());
        }
    }

    /**
     * done percent price rule calcuation
     *
     * @param object $ruleData
     * @param int $price
     * @param string $process
     * @return int
     */
    public function getPercentPriceCalculation($ruleData, $price, $process)
    {
        try {
            $percentPrice = ($price * $ruleData->getPrice())/100;
            if ($ruleData->getOperation() === 'Increase') {
                if ($process === $this->config['price_rule_on']) {
                    $price = $price + $percentPrice;
                } else {
                    $price = $price - $percentPrice;
                }
            } else {
                if ($process === $this->config['price_rule_on']) {
                    $price = $price - $percentPrice;
                } else {
                    $price = $price + $percentPrice;
                }
            }
            return $price;
        } catch (\Exception $e) {
            $this->logger->info('Helper Data getPercentPriceCalculation : '.$e->getMessage());
        }
    }
}
