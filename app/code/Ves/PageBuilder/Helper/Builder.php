<?php
/**
 * Google Optimizer Scripts Helper
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ves\PageBuilder\Helper;

class Builder extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\GoogleOptimizer\Model\Code $code
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
        )
    {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    public function generateHtml(\Ves\PageBuilder\Model\Block $block_model, $settings = []) {

        $block_name = isset($settings['block_name'])?$settings['block_name']:'builder_content_block';
        $template = isset($settings['template'])?$settings['template']:'builder/page.phtml';
        $resultLayout = $this->resultLayoutFactory->create();
        $builder_content_block = $resultLayout->getLayout()->createBlock(
                'Ves\PageBuilder\Block\Builder\Template',
                $block_name
            );
        $builder_content_block->setPageProfile($block_model);
        $builder_content_block->setTemplate($template);
        $template_content = $builder_content_block->toHtml();
        $template_content = $this->minifyHtml($template_content);

        return $template_content;
    }

    public function minifyHtml($data) {
        $data = trim(preg_replace('/\t+/', '', $data));
        //$data = preg_replace('/\s+/S', " ", $data);
        return $data;
    }

}
