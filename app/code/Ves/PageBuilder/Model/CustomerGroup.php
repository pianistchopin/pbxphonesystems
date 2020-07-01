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
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
/**
 * PageBuilder Model
 */
class CustomerGroup extends \Magento\Framework\Model\AbstractModel
{	
	const CUST_GROUP_ALL = 32000;
    /**
     * Group Collection
     */
    protected $_groupCollection;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;
    /**
     * PageBuilder config node per website
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
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

     /**
     * @var GroupInterfaceFactory
     */
    protected $groupDataFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ves\PageBuilder\Model\ResourceModel\Block $resource = null,
        \Ves\PageBuilder\Model\ResourceModel\Block\Collection $resourceCollection = null,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupDataFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        \Ves\PageBuilder\Helper\Data $blockHelper,
        array $data = []
        ) {

        $this->_blockHelper = $blockHelper;
        
        $this->_storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->groupDataFactory = $groupDataFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getAllCustomersGroups() {
        $groupAll[] = $this->filterBuilder
            ->setField(GroupInterface::ID)
            ->setConditionType('neq')
            ->setValue(self::CUST_GROUP_ALL)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters($groupAll)
            ->create();
        return $this->groupRepository->getList($searchCriteria)->getItems();
    }

    public function getCustomerGroups()
    {
        $data_array = array();
        $customer_groups = $this->getAllCustomersGroups();

        foreach ($customer_groups as $item_group) {
            $data_array[] = array('value' => $item_group->getId(), 'label' => $item_group->getCode());
        }
        
        return $data_array;

    }
}