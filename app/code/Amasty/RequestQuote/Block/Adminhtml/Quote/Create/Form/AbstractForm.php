<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Form;

use Magento\Framework\Pricing\PriceCurrencyInterface;

abstract class AbstractForm extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\AbstractCreate
{
    /**
     * Form factory
     *
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * Data Form object
     *
     * @var \Magento\Framework\Data\Form
     */
    protected $_form;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        array $data = []
    ) {
        $this->_formFactory = $formFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        parent::__construct($context, $sessionQuote, $priceCurrency, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Renderer\Element::class,
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Renderer\Fieldset::class,
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element::class,
                $this->getNameInLayout() . '_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * @return \Magento\Framework\Data\Form
     */
    public function getForm()
    {
        if ($this->_form === null) {
            $this->_form = $this->_formFactory->create();
            $this->_prepareForm();
        }
        return $this->_form;
    }

    /**
     * @return $this
     */
    abstract protected function _prepareForm();

    /**
     * @return array
     */
    protected function _getAdditionalFormElementTypes()
    {
        return [
            'file' => \Magento\Customer\Block\Adminhtml\Form\Element\File::class,
            'image' => \Magento\Customer\Block\Adminhtml\Form\Element\Image::class,
            'boolean' => \Magento\Customer\Block\Adminhtml\Form\Element\Boolean::class
        ];
    }

    /**
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        return [
            'region' => $this->getLayout()->createBlock(
                \Magento\Customer\Block\Adminhtml\Edit\Renderer\Region::class
            )
        ];
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return $this
     */
    protected function _addAdditionalFormElementData(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this;
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $attributes
     * @param \Magento\Framework\Data\Form\AbstractForm $form
     * @return $this
     */
    protected function _addAttributesToForm($attributes, \Magento\Framework\Data\Form\AbstractForm $form)
    {
        // add additional form types
        $types = $this->_getAdditionalFormElementTypes();
        foreach ($types as $type => $className) {
            $form->addType($type, $className);
        }
        $renderers = $this->_getAdditionalFormElementRenderers();

        foreach ($attributes as $attribute) {
            $inputType = $attribute->getFrontendInput();

            if ($inputType) {
                $element = $form->addField(
                    $attribute->getAttributeCode(),
                    $inputType,
                    $this->getFieldConfig($attribute)
                );
                if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
                $element->setEntityAttribute($attribute);
                $this->_addAdditionalFormElementData($element);

                if (!empty($renderers[$attribute->getAttributeCode()])) {
                    $element->setRenderer($renderers[$attribute->getAttributeCode()]);
                }

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $options = [];
                    foreach ($attribute->getOptions() as $optionData) {
                        $data = $this->dataObjectProcessor->buildOutputDataArray(
                            $optionData,
                            \Magento\Customer\Api\Data\OptionInterface::class
                        );
                        foreach ($data as $key => $value) {
                            if (is_array($value)) {
                                unset($data[$key]);
                                $data['value'] = $value;
                            }
                        }
                        $options[] = $data;
                    }
                    $element->setValues($options);
                } elseif ($inputType == 'date') {
                    $format = $this->_localeDate->getDateFormat(
                        \IntlDateFormatter::SHORT
                    );
                    $element->setDateFormat($format);
                }
            }
        }

        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return [];
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     *
     * @return array
     */
    public function getFieldConfig($attribute)
    {
        return [
            'name' => $attribute->getAttributeCode(),
            'label' => __($attribute->getStoreLabel()),
            'class' => $attribute->getFrontendClass(),
            'required' => $attribute->isRequired()
        ];
    }
}
