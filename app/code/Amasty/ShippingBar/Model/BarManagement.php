<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model;

use Amasty\ShippingBar\Api\BarManagementInterface;
use Amasty\ShippingBar\Api\ProfileRepositoryInterface;
use Amasty\ShippingBar\Model\ResourceModel\Label\CollectionFactory;
use Amasty\ShippingBar\UI\OptionsProviders\Pages;
use Amasty\ShippingBar\UI\OptionsProviders\Positions;
use Magento\Store\Model\ScopeInterface;

class BarManagement implements BarManagementInterface
{
    const ALLOWED_TOP = [
        Positions::TOP_FIXED,
        Positions::TOP_UNFIXED
    ];

    const ALLOWED_BOTTOM = [
        Positions::BOTTOM_FIXED,
        Positions::BOTTOM_UNFIXED
    ];

    const ENABLE_PATH = 'amasty_shipbar/general/enable';

    const FREE_SHIPPING_GOAL_PATH = 'carriers/freeshipping/free_shipping_subtotal';

    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        ProfileRepositoryInterface\Proxy $profileRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    ) {
        $this->profileRepository = $profileRepository;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $position
     *
     * @return array
     */
    public function getPosition($position)
    {
        switch ($position) {
            case 'top':
                return self::ALLOWED_TOP;
            case 'bottom':
                return self::ALLOWED_BOTTOM;
        }

        return [];
    }

    /**
     * @param \Magento\Framework\App\Request\Http|\Magento\Framework\App\RequestInterface $request
     *
     * @return string|null
     */
    public function getPage($request)
    {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();

        switch ($module) {
            case 'cms':
                if ($controller == 'index') {
                    return Pages::HOME;
                }
                break;
            case 'catalog':
                return $controller;
            case 'catalogsearch':
                return Pages::SEARCH;
            case 'checkout':
                if ($controller == 'index') {
                    return Pages::CHECKOUT;
                }

                return $controller;
        }

        return Pages::OTHER;
    }

    /**
     * @inheritdoc
     */
    public function getFilledData($customerGroup, $page, $position)
    {
        $store = $this->storeManager->getStore();

        if (!$this->scopeConfig->getValue(self::ENABLE_PATH, ScopeInterface::SCOPE_STORE, $store)) {
            return false;
        }

        if ($page == Pages::CHECKOUT) { //Shipping Bar is not allowed on Checkout page for security reasons.
            return false;
        }

        /** @var \Amasty\ShippingBar\Api\Data\ProfileInterface $barProfile */
        $barProfile = $this->profileRepository->getByParams($store->getId(), $customerGroup, $page, $position);

        if (!$barProfile->getProfileId()) {
            return false;
        }

        /** @var \Amasty\ShippingBar\Model\ResourceModel\Label\Collection $labelCollection */
        $labelCollection = $this->collectionFactory->create();

        $labelCollection->addProfileFilter($barProfile->getProfileId())->addStoreFilter($store->getId());
        $items = $labelCollection->getItems();
        $data = [];

        /** @var \Amasty\ShippingBar\Api\Data\LabelInterface $item */
        foreach ($items as $item) {
            $data[$item->getAction()] = $item->getLabel();
        }

        $baseGoal = $barProfile->getGoal();
        if ($barProfile->getGoalSource() == \Amasty\ShippingBar\UI\OptionsProviders\GoalSource::USE_FREE_SHIP_CONFIG) {
            $baseGoal = (float)$this->scopeConfig->getValue(
                static::FREE_SHIPPING_GOAL_PATH,
                ScopeInterface::SCOPE_WEBSITE,
                $store->getWebsiteId()
            );
        }
        $goal = $store->getBaseCurrency()->convert($baseGoal, $store->getCurrentCurrency());
        $barProfile->setGoal(round($goal, 2));
        $barProfile->setLabels($data);

        return $barProfile;
    }
}
