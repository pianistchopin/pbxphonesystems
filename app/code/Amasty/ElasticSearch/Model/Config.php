<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model;

use Amasty\ElasticSearch\Model\Source\CustomAnalyzer;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
    const ELASTIC_SEARCH_SECTION_NAME = 'amasty_elastic/';
    const DEBUG_SECTION_NAME = 'debug/';
    const ELASTIC_SEARCH_ENGINE = 'amasty_elastic';
    const CONFIG_ENGINE_PATH = 'amasty_elastic/connection/engine';
    const ELASTIC_SEARCH_TYPE_DOCUMENT = 'document';
    const ELASTIC_SEARCH_DEFAULT_ENTITY_TYPE = 'product';
    const ELASTIC_SEARCH_DEFAULT_TIMEOUT = 15;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config\QuerySettings
     */
    private $querySettings;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Config\QuerySettings $querySettings
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->querySettings = $querySettings;
    }

    /**
     * @param string $attributeCode
     * @return array|null
     */
    public function getQuerySettingByAttributeCode($attributeCode)
    {
        return $this->querySettings->getConfigValue($attributeCode);
    }

    /**
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::ELASTIC_SEARCH_SECTION_NAME . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getGeneralConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @return bool
     */
    public function getDebugConfig($path)
    {
        return $this->scopeConfig->getValue(
            self::ELASTIC_SEARCH_SECTION_NAME . self::DEBUG_SECTION_NAME . $path
        );
    }

    /**
     * @return string
     */
    public function getEngine()
    {
        return (string)$this->getGeneralConfig(self::CONFIG_ENGINE_PATH);
    }

    /**
     * @return bool
     */
    public function isElasticEngine()
    {
        return $this->getEngine() == self::ELASTIC_SEARCH_ENGINE;
    }

    /**
     * @param array $testData
     * @return array
     */
    public function prepareConnectionData($testData = [])
    {
        $defaultData = [
            'hostname' => $this->getModuleConfig('connection/server_hostname') ?: 'localhost',
            'port' => $this->getModuleConfig('connection/server_port') ?: '9200',
            'index' => $this->getModuleConfig('connection/index_prefix') ?: 'magento2',
            'enableAuth' => $this->getModuleConfig('connection/enable_auth') ?: 0,
            'username' => $this->getModuleConfig('connection/username'),
            'password' => $this->getModuleConfig('connection/password'),
            'timeout' => $this->getModuleConfig('connection/server_timeout') ? : self::ELASTIC_SEARCH_DEFAULT_TIMEOUT,
        ];
        $data = array_merge($defaultData, $testData);

        return $data;
    }

    /**
     * @param string $indexType
     * @param string|int $storeId
     * @return string
     */
    public function getIndexName($indexType, $storeId)
    {
        if ($indexType == 'catalogsearch_fulltext') {
            $indexType = 'product';
        }

        return $this->getModuleConfig('connection/index_prefix') . '_' . $indexType . '_' . $storeId;
    }

    /**
     * @return mixed
     */
    public function getIndexPrefix()
    {
        return $this->getModuleConfig('connection/index_prefix');
    }

    /**
     * get ElasticSearch entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return self::ELASTIC_SEARCH_TYPE_DOCUMENT;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function allowSpecialChars($storeId = null)
    {
        return (bool)$this->getModuleConfig('catalog/long_tail')
            && !$this->useCustomAnalyzer($storeId);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function getUsePredefinedStopwords($storeId = null)
    {
        return (bool)$this->getModuleConfig('index/use_language_stopwords', $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getStopWordsLanguage($storeId = null)
    {
        return $this->getModuleConfig('index/stopwords_language', $storeId);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function useCustomAnalyzer($storeId = null)
    {
        return $this->getModuleConfig('connection/custom_analyzer', $storeId) != CustomAnalyzer::DISABLED;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function getUsePredefinedStemming($storeId = null)
    {
        return (bool)$this->getModuleConfig('index/use_language_stemmed_words', $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getStemmedLanguage($storeId = null)
    {
        return $this->getModuleConfig('index/stemmed_words_language', $storeId);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function getAnalyzerType($storeId = null)
    {
        return $this->getModuleConfig('connection/custom_analyzer', $storeId);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function getUseNoriReadingForm($storeId)
    {
        return $this->getModuleConfig('index/use_nori_reading_form', $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getNoriTokenMode($storeId)
    {
        return $this->getModuleConfig('index/nori_token_mode', $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getKuromojiReadingForm($storeId)
    {
        return $this->getModuleConfig('index/kuromoji_reading_form', $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getKoromojiTokenMode($storeId)
    {
        return $this->getModuleConfig('index/kuromoji_token_mode', $storeId);
    }
}
