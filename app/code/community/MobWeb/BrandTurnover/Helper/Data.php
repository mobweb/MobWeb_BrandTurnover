<?php

class MobWeb_BrandTurnover_Helper_Data extends Mage_Core_Helper_Abstract
{
	public $log_file = 'mobweb_brandturnover.log';

	// Returns an array with the brand IDs and the turnover limit specified
	// to receive a coupon for this brand
	public function getTurnoverLimits()
	{
		// Get the brand specific turnover for each brand as specified
		// in the admin panel
		$brands = 5;
		$brand_specific_turnover = array();
		for($i=0;$i<=10;$i++) {
			$brand = Mage::getStoreConfig('brandturnover/limits/brand' . $i);
			$brand_limit = Mage::getStoreConfig('brandturnover/limits/brand' . $i . '_limit');
			if($brand && $brand_limit) {
				$brand_specific_turnover[$brand] = $brand_limit;
			}
		}

		return $brand_specific_turnover;
	}

	public function log($msg)
	{
		Mage::log($msg, NULL, $this->log_file);
	}
}