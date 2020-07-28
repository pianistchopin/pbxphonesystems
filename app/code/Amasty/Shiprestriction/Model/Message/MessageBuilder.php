<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model\Message;

/**
 * Class MessageBuilder
 */
class MessageBuilder
{
    /**
     * @param string $message
     * @param array $products
     * @return string
     */
    public function parseMessage($message, $products)
    {
        $allProducts = implode(', ', $products);
        $lastProduct = end($products);
        $newMessage = str_replace('{all-products}', $allProducts, $message);
        $newMessage = str_replace('{last-product}', $lastProduct, $newMessage);

        return $newMessage;
    }
}
