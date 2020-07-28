<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Search;

class Page extends AbstractSearch
{
    const CATEGORY_BLOCK_PAGE = 'page';

    /**
     * @return string
     */
    public function getBlockType()
    {
        return self::CATEGORY_BLOCK_PAGE;
    }

    /**
     * @inheritdoc
     */
    protected function generateCollection()
    {
        return parent::generateCollection()
            ->addSearchFilter($this->getQuery()->getQueryText())
            ->addStoreFilter($this->_storeManager->getStore())
            ->addFieldToFilter('is_active', 1)
            ->setPageSize($this->getLimit());
    }

    /**
     * @inheritdoc
     */
    public function getName(\Magento\Framework\DataObject $item)
    {
        return $this->generateName($item->getTitle());
    }

    /**
     * @inheritdoc
     */
    public function getDescription(\Magento\Framework\DataObject $page)
    {
        $content = preg_replace(
            '|<style[^>]*?>(.*?)</style>|si',
            '',
            html_entity_decode($page->getContent())
        );
        $content = preg_replace(
            '|<script[^>]*?>(.*?)</script>|si',
            '',
            html_entity_decode($content)
        );
        $descStripped = $this->stripTags(html_entity_decode($content), null, true);
        $this->replaceVariables($descStripped);

        return $this->getHighlightText($descStripped);
    }
}
