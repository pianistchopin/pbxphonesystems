<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    BSS_HtmlSiteMap
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\HtmlSiteMap\Controller\Index;
 
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    public $response;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $url;

    /**
     * @var \Bss\HtmlSiteMap\Helper\Data
     */
    public $helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Bss\HtmlSiteMap\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Bss\HtmlSiteMap\Helper\Data $helper
    ) {
        $this->resultFactory = $context->getResultFactory();
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->url = $context->getUrl();
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $redirectUrl= $this->url->getUrl('');
        $isEnabled = $this->helper->getEnable();
        $title = $this->helper->getTitleSiteMap();
        $description = $this->helper->getDescriptionSitemap();
        $keywords = $this->helper->getKeywordsSitemap();
        $metaTitle = $this->helper->getMetaTitleSitemap();
        if ($isEnabled == 1) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__($title));
            $resultPage->getConfig()->setDescription($description);
            $resultPage->getConfig()->setKeywords($keywords);
            $resultPage->getConfig()->setMetadata('meta_title', $metaTitle);
            return $resultPage;
        } else {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($redirectUrl);
        }
    }
}
