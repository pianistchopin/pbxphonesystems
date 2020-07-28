<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Setup\Operation;

/**
 * Convert saved rule data to new format.
 */
class ChangeMethodsFormat
{
    /**
     * @var \Amasty\Shiprestriction\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Amasty\CommonRules\Model\MethodConverter
     */
    private $methods;

    public function __construct(
        \Amasty\Shiprestriction\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Amasty\CommonRules\Model\MethodConverter $methods
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->methods = $methods;
    }

    public function execute()
    {
        $newMethods = $this->methods->getCarrierMethods();

        /** @var \Amasty\Shiprestriction\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->collectionFactory->create();
        $rules = $collection->loadData();

        /** @var \Amasty\Shiprestriction\Model\Rule $rule */
        foreach ($rules as $rule) {
            $result = [];
            $oldMethods = $rule->getMethods();

            $oldMethods = str_replace("\r\n", "\n", $oldMethods);
            $oldMethods = str_replace("\r", "\n", $oldMethods);
            $oldMethods = trim($oldMethods);

            if (empty($oldMethods)) {
                $rule->setMethods(implode(',', $result));

                continue;
            }

            $oldMethods = array_unique(explode("\n", $oldMethods));

            foreach ($oldMethods as $oldMethod) {
                $oldMethod = explode('::', $oldMethod);
                $methodPos = count($oldMethod) == 1 ? 0 : 1;
                $method = trim($oldMethod[$methodPos]);

                foreach ($newMethods as $currentKey => $currentValue) {
                    if (stripos($currentValue, $method) !== false) {
                        $result[] = $currentKey;
                    }
                }
            }

            $rule->setMethods(implode(',', $result));
        }

        $collection->save();
    }
}
