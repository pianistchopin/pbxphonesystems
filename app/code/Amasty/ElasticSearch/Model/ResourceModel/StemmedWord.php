<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\ResourceModel;

/**
 * Class StemmedWord
 * @package Amasty\ElasticSearch\Model\ResourceModel
 */
class StemmedWord extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_elastic_stemmed_word', 'stemmed_word_id');
    }
}
