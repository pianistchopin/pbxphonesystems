<?php
namespace Ves\PageBuilder\Model\Enterprise;

use Magento\Framework\App\ProductMetadataInterface;

/**
 * Factory which creates Classes from Enterprise
 * Class ClassFactory
 * @package FireGento\FastSimpleImport\Model\Enterprise
 */
class VersionFeaturesFactory
{

    const EDITION_ENTERPRISE = 'Enterprise';
    const EDITION_COMMUNITY = 'Community';
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * VersionFeaturesFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata
    )
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param string $featureName
     * @return mixed|null
     */
    public function isEnterprise()
    {
        if ($this->productMetadata->getEdition() == self::EDITION_COMMUNITY) {
            return false;
        }
        return true;
    }
}