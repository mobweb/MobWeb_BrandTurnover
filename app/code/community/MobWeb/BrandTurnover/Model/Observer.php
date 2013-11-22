<?php
class MobWeb_BrandTurnover_Model_Observer
{
    // This function is called every time an order is saved. It checks
    // the state of the order, and if it's complete, it adds the price
    // of each purchased product to the brand specific turnover for this
    // brand to the customer object. It then checks if any brand turnover
    // has now reached its limit specified by the admin
    //public function captureOrderSave(Varien_Event_Observer $observer)
    public function captureOrderSave()
    {
		//$order = $observer->getEvent()->getOrder();
		$order = Mage::getModel('sales/order')->load(41);

		// Check if the order is in the "Complete" state. If not, abort
		if($order->getState() !== Mage_Sales_Model_Order::STATE_COMPLETE) {
			return;
		}

		// Check if the order was placed by a registered account or a guest
		if($user_id = $order->getCustomerId()) {
			// Get the brand specific turnover data from the customer
			$user = Mage::getModel('customer/customer')->load($user_id);
			$brandSpecificTurnover = $user->getData('mobweb_brandturnover_turnover');

			// If data exists, unserialize it
			if($brandSpecificTurnover) {
				$brandSpecificTurnover = unserialize($brandSpecificTurnover);
			} else {
				// If no data exists, create an empty array
				$brandSpecificTurnover = array();
			}

			// Loop through every order product
			// Note: getAllVisibleItems() includes only configurable
			// products while getAllItems() also includes their
			// child simple products
			foreach($order->getAllVisibleItems() AS $product) {
				// Get the product's order value, the first variant
				// includes tax, the second one doesn't. Change to
				// whatever you prefer...
				$value = $product->getData('row_total_incl_tax');
				//$value = $product->getData('row_total');

				// Get the product's brand attribute
				$attribute_code = Mage::getStoreConfig('brandturnover/configuration/brand_attribute');
				$brand = $product->getProduct()->getData($attribute_code);

				// Check if the attribute exists
				if(!$brand) {
					Mage::helper('brandturnover')->log('Unable to load brand attribute by specified attribute code: ' . $attribtue_code);
					return;
				}

				// If no brand specific turnover exists for this brand,
				// create a new entry
				$brandSpecificTurnover[$brand] = isset($brandSpecificTurnover[$brand]) ? $brandSpecificTurnover[$brand] : '0';

				// Increase the brand specific turnover by the order value for
				// this product
				$brandSpecificTurnover[$brand] += $value;
			}

			// Check the brand specific turnover rules for a matching condition
			foreach(Mage::helper('brandturnover')->getTurnoverLimits() AS $brandId => $turnover) {
				// Check if the current brand ID was included in the
				// current order
				if(isset($brandSpecificTurnover[$brandId])) {
					// If there exists an entry, check if the turnover
					// was reached
					if($brandSpecificTurnover[$brandId] >= $turnover) {
						// Log the reaching of the turnover limit
						Mage::helper('brandturnover')->log(sprintf('Turnover limit for brand %s for user %s reached', $brandId, $user_id));

						// Decrease the customer specific turnover by
						// the turnover amount
						$brandSpecificTurnover[$brandId] -= $turnover;

						// Here you should do whatever it is you want to do once a customer reaches the turnover limit!
					}
				}
			}

			// Save the updated customer specific brand specific turnover
			$user->setData('mobweb_brandturnover_turnover', serialize($brandSpecificTurnover));
			$user->save();
		}
    }
}