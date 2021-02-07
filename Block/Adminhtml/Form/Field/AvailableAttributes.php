<?php


namespace Catgento\AdminGrids\Block\Adminhtml\Form\Field;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class AvailableAttributes extends Select
{
    protected $attributeFactory;

    public function __construct(
        Context $context,
        CollectionFactory $attributeFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->attributeFactory = $attributeFactory;
    }

    public function _toHtml()
    {
        $attributeCollection = $this->attributeFactory->create()->addFieldToFilter('is_user_defined',1);

        if (!$this->getOptions()) {
            foreach ($attributeCollection as $attribute) {
                $this->addOption($attribute->getAttributeCode(), $attribute->getFrontendLabel());
            }
        }
        return parent::_toHtml();
    }
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
