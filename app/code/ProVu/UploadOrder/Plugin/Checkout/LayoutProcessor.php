<?php
namespace ProVu\UploadOrder\Plugin\Checkout;
class LayoutProcessor
{
    private $block;
    public function __construct(\ProVu\UploadOrder\Block\Index $block)
    {
        $this->block = $block;
    }
    //Plugin to disable the assignee checkout step if not on Gatwick stores
    public function aroundProcess($subject, $proceed, $jsLayout)
    {
        if($this->block->isRequired())
            return $proceed($jsLayout);
        unset($jsLayout['components']['checkout']['children']['steps']['children']['ponumber-step']);
        return $proceed($jsLayout);
    }
}