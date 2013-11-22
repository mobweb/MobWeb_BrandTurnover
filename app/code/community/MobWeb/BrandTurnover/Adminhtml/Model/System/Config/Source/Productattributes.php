<?php
class MobWeb_BrandTurnover_Adminhtml_Model_System_Config_Source_Productattributes
{
    public function toOptionArray()
    {
        $attributes = Mage::getModel('catalog/product')->getAttributes();
        $attributeArray = array();

        foreach($attributes as $a) {
            foreach ($a->getEntityType()->getAttributeCodes() as $attributeName) {
                //$attributeArray[$attributeName] = $attributeName;
                $attributeArray[] = array(
                    'label' => $attributeName,
                    'value' => $attributeName
                );
            }
            break;
        }
        return $attributeArray;
    }
}