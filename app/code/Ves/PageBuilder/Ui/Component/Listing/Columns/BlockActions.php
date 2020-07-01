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
namespace Ves\PageBuilder\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Ves\PageBuilder\Block\Adminhtml\Blockbuilder\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Class BrandActions
 */
class BlockActions extends Column
{
    /** Url Path */
    const BLOCK_URL_PATH_EDIT = 'vespagebuilder/blockbuilder/edit';
    const BLOCK_URL_PATH_DELETE = 'vespagebuilder/blockbuilder/delete';
    const BLOCK_URL_PATH_CONVERTTEMPLATE = 'vespagebuilder/blockbuilder/convertTemplate';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param ContextInterface   $context            
     * @param UiComponentFactory $uiComponentFactory 
     * @param UrlBuilder         $actionUrlBuilder   
     * @param UrlInterface       $urlBuilder         
     * @param array              $components         
     * @param array              $data               
     * @param [type]             $editUrl            
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = [],
        $editUrl = self::BLOCK_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['block_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['block_id' => $item['block_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::BLOCK_URL_PATH_DELETE, ['block_id' => $item['block_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.title }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?')
                        ]
                    ];
                    $item[$name]['converttemplate'] = [
                        'href' => $this->urlBuilder->getUrl(self::BLOCK_URL_PATH_CONVERTTEMPLATE, ['block_id' => $item['block_id']]),
                        'label' => __('Convert Template')
                    ];
                }
                if (isset($item['block_id'])) {
                    $storeId = $this->_storeManager->getDefaultStoreView()->getStoreId();
                    $store = $this->_storeManager->getStore($storeId);
                    $store_code = $store->getCode();
                    $item[$name]['preview'] = [
                        'href' => $this->_storeManager->getStore($storeId)->getUrl(
                            "vespagebuilder/preview/index/block_id/".$item['block_id']),
                        'target' => "_BLANK",
                        'label' => __('Preview')
                    ];
                }
            }
        }
        return $dataSource;
    }
}