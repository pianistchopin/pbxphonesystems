<?php


namespace ProVu\ApiManagement\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCatalog extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("provu:import");
        $this->setDescription("Imports and Updates from Provu");
        parent::configure();
    }
	
    /**
     * {@inheritdoc}
     */
	
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
		$username = 'pbxphonesys.apitest';
		$password = 'mBB:q8K~S';
		
		$this->categoryImport($username, $password);
    }
	
	public function categoryImport($username, $password)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Provu Category Management Started.');
		$server = 'https://secure.provu.co.uk/prosys/categories.php?XML=yes';
		$context = stream_context_create(array(
				'http' => array(
					'header'  => "Authorization: Basic " . base64_encode("$username:$password")
				)
			)
		);
		$data = file_get_contents($server, false, $context);
		$xml = simplexml_load_string($data);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
		$categoryLinkManagement = $objectManager->get('Magento\Catalog\Api\CategoryLinkManagementInterface');
		$categoryFactory = $objectManager->create('Magento\Catalog\Model\CategoryFactory');
		if ($xml === false) {
			$logger->error('Failed loading XML.');
			foreach(libxml_get_errors() as $error) {
				$logger->error($error->message);
			}
		} else {
			$json  = json_encode($xml);
			$catalogData = json_decode($json, true);
			$catalogData = $catalogData['category'];
			$categoryIDArray = array();
			foreach ($catalogData as $categoryElement) {
				$categoryName = $categoryElement['name'];
				if ($categoryElement['name'] == 'IP Telephone Systems') {
					$categoryName = 'PBX Systems';
				}
				$collection = $categoryFactory->create()->getCollection()->addAttributeToFilter('name', $categoryName)->setPageSize(1);
				if ($collection->getSize()) {
					$categoryId = $collection->getFirstItem()->getId();
				} else {
					$categoryId = 2;
				}
		
				$items = $categoryElement['item'];
				$categoryIds = array();
				$categoryIds[] = $categoryId;
				if (is_array($items)) {
					foreach ($items as $item) {
						$categoryIDArray[$item][] = $categoryId;
					}
				} else {
					$categoryIDArray[$items][] = $categoryId;
				}
			}
			foreach ($categoryIDArray as $key => $value) {
				$_productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')->addFieldToFilter('sku',array("eq" => $key));
				if(count($_productCollection)>0){
					$_product = $_productCollection->getFirstItem();
					$product = $objectManager->create('Magento\Catalog\Model\Product')->load($_product->getId());
					if (count($product->getCategoryIds()) > 0) {
						continue;
					} else {
						$categoryLinkManagement->assignProductToCategories($key, $value);
					}
				}
			}
		}
		$logger->info('Provu Category Management Finished.');
	}

}
