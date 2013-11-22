<?php
class MobWeb_BrandTurnover_Adminhtml_Model_System_Config_Source_Brands
{
    public function toOptionArray()
    {
        // Find out which attribute we can get the brands from
        $attribute_code = Mage::getStoreConfig('brandturnover/configuration/brand_attribute');
        if(($attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute_code)) && $attribute->usesSource()) {
            // Get all of the options for this attribute
            $options = array();
            foreach($attribute->getSource()->getAllOptions(false) AS $option) {
                $options[] = array('label' => $option['label'], 'value' => $option['value']);
            }

            return $options;
        }
    }
}