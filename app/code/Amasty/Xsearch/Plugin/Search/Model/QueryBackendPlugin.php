<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Plugin\Search\Model;

use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class QueryBackendPlugin
 */
class QueryBackendPlugin
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterLoad($subject, $result)
    {
        $relatedTerms = [];
        if ($subject->getId()) {
            /**
             * @var \Magento\Search\Model\ResourceModel\Query $resource
             */
            $resource = $subject->getResource();
            $connection = $resource->getConnection();
            $select = $connection->select()->from(
                $resource->getTable('amasty_xsearch_related_term'),
                ['related_term_id', 'position'])
                ->where('term_id = ?', $subject->getId());
            $relatedTerms = $connection->fetchPairs($select);
        }

        $subject->setRelatedTerms($relatedTerms);
        return $result;
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterSave($subject, $result)
    {
        /**
         * @var \Magento\Search\Model\ResourceModel\Query $resource
         */
        $resource = $subject->getResource();
        $connection = $resource->getConnection();
        $whereCondition = $connection->quoteInto( 'term_id = ?', $subject->getId());
        $connection->delete($resource->getTable('amasty_xsearch_related_term'), $whereCondition);
        $terms = $this->jsonSerializer->unserialize($subject->getRelatedTerms());
        if (is_array($terms) && !empty($terms)) {
            $insertData = [];
            foreach ($terms as $termId => $position) {
                $insertData[] = [
                    'term_id' => $subject->getId(),
                    'related_term_id'  => $termId,
                    'position'  => $position
                ];
            }
            if ($insertData) {
                $connection->insertOnDuplicate(
                    $resource->getTable('amasty_xsearch_related_term'),
                    $insertData, ['position']
                );
            }
        }

        return $result;
    }
}
