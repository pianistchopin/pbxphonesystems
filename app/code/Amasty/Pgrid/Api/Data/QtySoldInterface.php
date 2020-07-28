<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Api\Data;

/**#@+
 * Constants defined for keys of data array
 */
interface QtySoldInterface
{
    const PRODUCT_ID = 'product_id';
    const QTY_SOLD = 'qty_sold';

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return int
     */
    public function getQtySold();

    /**
     * @param int $productId
     * @return \Amasty\Pgrid\Api\Data\QtySoldInterface
     */
    public function setProductId($productId);

    /**
     * @param int $qtySold
     * @return \Amasty\Pgrid\Api\Data\QtySoldInterface
     */
    public function setQtySold($qtySold);
}
