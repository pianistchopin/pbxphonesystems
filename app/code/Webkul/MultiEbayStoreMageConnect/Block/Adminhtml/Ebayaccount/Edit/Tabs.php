<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ebayaccount_tab');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Ebay Account Information'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $id = $this->getRequest()->getParam('id');
        $block = 'Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab\EbayAccount';
        $mapCategory = 'Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab\MapCategoryGrid';
        $this->addTab(
            'ebayaccount',
            [
                'label' => __('Connect eBay Account'),
                'content' => $this->getLayout()->createBlock($block, 'ebay_account_info')->toHtml()
            ]
        );
        if ($id) {
            $this->addTab(
                'general_configuration',
                [
                    'label' => __('General Configuration'),
                    'title' => __('General Configuration'),
                    'content' => $this->getLayout()->createBlock(
                        'Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab\GeneralConfiguration'
                    )->toHtml(),
                    'active' => false
                ]
            );
            $this->addTab(
                'listing_configuration',
                [
                    'label' => __('Listing Configuration'),
                    'title' => __('Listing Configuration'),
                    'content' => $this->getLayout()->createBlock(
                        'Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab\ListingConfiguration'
                    )->toHtml(),
                    'active' => false
                ]
            );
            $this->addTab(
                'mapcategory',
                [
                    'label' => __('Map Category'),
                    'url'       => $this->getUrl('*/categories/mapcategory', ['_current' => true]),
                    'class'     => 'ajax',
                    'title'     => __('Map Account Category'),
                ]
            );
            $this->addTab(
                'mapproduct',
                [
                    'label' => __('Map Product'),
                    'url'       => $this->getUrl('*/products/map', ['_current' => true]),
                    'class'     => 'ajax',
                    'title'     => __('Map Product'),
                ]
            );
            if ($this->helper->getConfigValue('multiebaystoremageconnect/import_status/order_import_enable')){
                $this->addTab(
                    'maporder',
                    [
                        'label' => __('Map Order'),
                        'url'       => $this->getUrl('*/ebayorder/index', ['_current' => true]),
                        'class'     => 'ajax',
                        'title'     => __('Map Order'),
                    ]
                );
            }
            $this->addTab(
                'importinebay',
                [
                    'label' => __('Export To eBay'),
                    'url'       => $this->getUrl('*/products/importinebay', ['_current' => true]),
                    'class'     => 'ajax',
                    'title'     => __('Export To eBay'),
                ]
            );
        }
        return parent::_prepareLayout();
    }
}
