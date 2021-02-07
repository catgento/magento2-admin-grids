<?php


namespace Catgento\AdminGrids\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class Attribute
 * @package Catgento\AdminGrids\Block\System\Config\Form\Field
 */
class Attribute extends AbstractFieldArray
{
    /**
     * @var array
     */
    protected $columns = [];
    /**
     * @var
     */
    protected $productAttributesRenderer;
    /**
     * @var
     */
    protected $unitsRenderer;
    /**
     * @var bool
     */
    protected $_addAfter = true;
    /**
     * @var
     */
    protected $addButtonLabel;

    /**
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if ( $columnName == "active" ) {
            $this->columns[$columnName]['class'] = 'input-text required-entry validate-number';
        }
        return parent::renderCellTemplate($columnName);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addButtonLabel = __('Add');
    }

    /**
     *
     */
    protected function _prepareToRender()
    {
        $this->addColumn('attribute',
            [
                'label' => __('Product Attribute'),
                'class' => 'required-entry',
                'renderer' => $this->getAvailableProductAttributesRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->addButtonLabel = __('Add attribute');
    }

    /**
     * @return BlockInterface
     * @throws LocalizedException
     */
    protected function getAvailableProductAttributesRenderer()
    {
        if ( !$this->productAttributesRenderer ) {
            $this->productAttributesRenderer = $this->getLayout()->createBlock(
                '\Catgento\AdminGrids\Block\Adminhtml\Form\Field\AvailableAttributes',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->productAttributesRenderer;
    }

    /**
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $attributes = $row->getAttribute();
        $options = [];
        if ( $attributes ) {
            $options['option_' . $this->getAvailableProductAttributesRenderer()->calcOptionHash($attributes)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
