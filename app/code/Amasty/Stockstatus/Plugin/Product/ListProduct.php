<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Plugin\Product;

class ListProduct
{
    /**
     * @var \Amasty\Stockstatus\Helper\Data
     */
    private $helper;

    public function __construct(
        \Amasty\Stockstatus\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function aroundGetProductDetailsHtml(
        $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $html = $proceed($product);
        if ($this->helper->getModuleConfig('display/display_on_category')) {
            $status = $this->helper->showStockStatus($product, 1, 1);
            if ($status != '') {
                $status = '<div class="amstockstatus-category">' .
                    $status . $this->helper->getInfoBlock() .
                    '</div>';
            }
            $html .= $status;
        }

        return $html;
    }

    public function afterToHtml(
        $subject,
        $result
    ) {
        if ($this->helper->getModuleConfig('display/display_on_category')) {
            $result .= '
                <script type="text/javascript">
                    require([
                        "jquery"
                    ], function($) {
                        $(".amstockstatus").each(function(i, item) {
                            var parent = $(item).parents(".item").first();
                            parent.find(".actions .stock").remove();
                        })
                    });
                </script>
            ';
        }

        return $result;
    }
}
