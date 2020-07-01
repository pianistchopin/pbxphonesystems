<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Products extends Action
{

    /**
     * @param Context     $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context
    ) {
    
        parent::__construct($context);
    }

    public function execute()
    {
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization
                    ->isAllowed(
                        'Webkul_MultiEbayStoreMageConnect::Products'
                    );
    }
}
