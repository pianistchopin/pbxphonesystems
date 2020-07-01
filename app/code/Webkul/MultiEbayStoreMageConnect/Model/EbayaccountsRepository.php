<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\EbayaccountsInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebayaccounts\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class EbayaccountsRepository implements \Webkul\MultiEbayStoreMageConnect\Api\EbayaccountsRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebayaccounts
     */
    protected $_resourceModel;

    public function __construct(
        EbayaccountsFactory $ebayAccountsFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebayaccounts\CollectionFactory $collectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebayaccounts $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_ebayAccountsFactory = $ebayAccountsFactory;
        $this->_collectionFactory = $collectionFactory;
    }
    
    /**
     * @param  int $id
     * @return object
     */
    public function getConfigurationById($id)
    {
        $ebayConfiguration = $this->_ebayAccountsFactory->create()->load($id);
        return $ebayConfiguration;
    }

    /**
     * @param  int $id
     * @return object
     */
    public function getByUserId($ebayUserId)
    {
        $ebayAccount = $this->_ebayAccountsFactory
                            ->create()
                            ->getCollection()
                            ->addFieldToFilter('ebay_user_id', ['eq'=>$ebayUserId]);
        ;
        return $ebayAccount;
    }
}
