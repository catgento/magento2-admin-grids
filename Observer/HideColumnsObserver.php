<?php

namespace Catgento\AdminGrids\Observer;

use Hyva\Admin\Model\GridSourceType\RepositorySourceType\HyvaGridEventContainer;
use Hyva\Admin\ViewModel\HyvaGrid\ColumnDefinitionInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class HideColumnsObserver implements ObserverInterface
{
    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;
    private CollectionFactory $attributeFactory;

    private $defaultColumnsToHide = array(
        'description',
        'short_description',
        'special_price',
        'special_from_date',
        'special_to_date',
        'manufacturer',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'news_from_date',
        'news_to_date',
        'new_theme',
        'active_from',
        'active_to',
        'layout',
        'country_of_manufacture',
        'new_layout',
        'url_key',
        'msrp',
        'gift_message_available',
        'tax_class',
        'media_gallery'
    );

    /**
     * HideColumnsObserver constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $attributeFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $attributeFactory
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->attributeFactory = $attributeFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        /** @var HyvaGridEventContainer $container */
        /** @var ColumnDefinitionInterface[] $columnsMap */
        $container = $observer->getData('data_container');

        if ($container) {
            $columnsMap = $container->getContainerData();
            $userDefinedAttributesArray = $this->getUserDefinedAttributesToHide();

            foreach ($columnsMap as $key => $columnDefinition) {
                if (in_array($columnDefinition->getKey(), array_merge($this->defaultColumnsToHide, $userDefinedAttributesArray))) {
                    $valuesToReplace = array('initiallyHidden' => 'true');
                    $replacedColumn = $columnsMap[$key]->merge($valuesToReplace);
                    $columnsMap[$key] = $replacedColumn;
                }
            }

            $container->replaceContainerData($columnsMap);
        }
    }

    public function getConfigValue(string $field): string
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    public function getStoreId(): int
    {
        return $this->storeManager->getStore()->getId();
    }

    public function getUserDefinedAttributesToHide(): array
    {
        $userDefinedAttributes = $this->attributeFactory->create()->addFieldToFilter('is_user_defined',1);
        $userDefinedAttributesArray = array();

        foreach ($userDefinedAttributes as $userDefinedAttribute) {
            $userDefinedAttributesArray[] = $userDefinedAttribute->getAttributeCode();
        }

        return $userDefinedAttributesArray;
    }
}
