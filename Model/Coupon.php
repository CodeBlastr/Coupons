<?php
/**
 * @todo			Move this entire thing to the credits plugin.  Instead of a coupon to redeem credits we would just have a credit entry in the database where the is_claimed field is not checked.   This entire coupon plugin needs to be deleted. 
 * @todo			We need a code and a pin number (not just a code).  That will allow us to have hard tangible gift cards with a code, but you'd also need a pin to redeem it. 
 */
class Coupon extends CouponsAppModel {
	var $name = 'Coupon';
	var $belongsTo = array(
		'User' => array(
			'className' => 'Users.User'
		)
	);
	
/* generateCoupon
 * Generates Coupon and makes a new entry to database to Coupon
 * @param array data
 * return boolean
 */
	function generateCoupon($data = null){
		$code =  $this->generateRandomCode();
		// Checks if coupon with same code already exists
		if($this->ifCodeExists($code)){
			$this->generateCoupon($data);
		} else {
			$data['Coupon']['code'] = $code;
			if($this->save($data)){
				return true;
			} else {
				return false;
			}
		}
	}
	
/* generateRandomCode
 * Generates a random Code of length 5
 * return code
 */
	function generateRandomCode() {
	    $length = 8;
	    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	    $code = "";    
		for ($i = 0; $i < $length; $i++) {
	        $code .= $characters[mt_rand(0, strlen($characters)-1)];
	    }
		return $code;
	}
	
/* checkCodeExists
 * Check whether code with same string exists
 * @param code
 * return boolean
 */
	function ifCodeExists($code = null){
		$code = $this->find('first', array('conditions' => array('Coupon.code' => $code)));
		if(!empty($code)){
			return true;
		} else {
			return false;
		}
	}
	
/* redeemCoupon
 * check if user tries to redeem valid code
 * @param array data
 * return boolean
 */
	function redeemCoupon($userId = null, $data = null){
		$status = true;
		$code = $data['Coupon']['code'];
		
		// Checks code no , dates and status of coupon
		$couponData = $this->find('first', array('conditions' => array('Coupon.code' => $code, 
							'Coupon.start_date <= NOW()', 'Coupon.end_date >= NOW()' , 'is_active' => 1)));
		if(!empty($couponData)){
			try{
				//Prepares data for Coupon
				$this->request->data['Coupon']['id'] = $couponData['Coupon']['id'];
				$this->request->data['Coupon']['user_id'] = $userId;
				$this->request->data['Coupon']['is_active'] = 0;
				$this->request->data['Coupon']['redeem_date'] = date('Y:m:d H:i:s');
				$couponValue = $couponData['Coupon']['value'];
				if(!($this->save($this->request->data))){
					throw new Exception(__d('Coupon Data not Saved', true));
				} else {
					$this->updateUserCredits($couponValue, $userId);
				}
			} catch(Exception $e){
				$this->request->data['Coupon']['user_id'] = NULL;
				$this->request->data['Coupon']['is_active'] = 1;
				$this->request->data['Coupon']['redeem_date'] = NULL;
				$this->save($this->request->data);
				$status = false;
			}
		} else {
			$status = false;
		}
		return $status;
	}
	
	
	/*
	 * This is the new redeemCoupon with better formatted syntax. 
	 * throw should be in the model for the most part, and the catch should be done in the controller, from what I've seen in best practices.   (I did the model part here, but didn't update the controller part)
	 * Please update this function by going through it and comparing it to the one above, and getting rid of the one above, then catching errors in the controller for output. 
	function redeemCoupon($userId = null, $data = null){
		// this predeclaration stuff makes it so that you get weird results sometimes, Especially when you haven't thought of every way it could or should be set to true.  Don't use this syntax at all. 
		// $status = true;
		// I also don't like renaming variables that only get used once.  If its used more than once I get it, but other than that its not really needed. I commented this out, and replaced the one instance of $code, below with $data['Coupon']['code']. 
		//$code = $data['Coupon']['code'];
		
		// Checks code no , dates and status of coupon
		$couponData = $this->find('first', array('conditions' => array('Coupon.code' => $data['Coupon']['code'], 
							'Coupon.start_date <= NOW()', 'Coupon.end_date >= NOW()' , 'is_active' => 1)));
		if(!empty($couponData)){
			//Prepares data for Coupon
			$this->request->data['Coupon']['id'] = $couponData['Coupon']['id'];
			$this->request->data['Coupon']['user_id'] = $userId;
			$this->request->data['Coupon']['is_active'] = 0;
			$this->request->data['Coupon']['redeem_date'] = date('Y:m:d H:i:s');
			$couponValue = $couponData['Coupon']['value'];
			if($this->save($this->request->data)){  // this was reversed from the one above
				try {
					$this->updateUserCredits($couponValue, $userId);
					return true;
				} catch(Exception $e){
					// rollback here if you need to (but if the save failed you wouldn't need to, because it would already have the values you have in the above  catch statement
					throw new Exception($e->getMessage());
				}
			} 			
		} else {
			// these stink, and are an opportunity to do some error reporting that is being missed
			// $status = false;
			//  INSTEAD it should have a throw exception here too...
			throw new Exception(__d('No coupon found.', true));
		}
		// this is now unecessary
		// return $status;
	}*/
	




/*	updateUserCredits
 * 	update user credits based on coupon value
 *  @param array data
 *  return boolean
 */
	function updateUserCredits($credits = null, $userId){
		$creditData = $this->User->find('first' , array('conditions' => 
												array('User.id' => $userId)
											)) ;
		$data['User']['credit_total'] = $creditData['User']['credit_total'] + $credits; 
		$data['User']['id'] = $userId;
		if(!($this->User->save($data))){
			throw new Exception(__d('Credits not Saved', true));
		}
	}
	
}
?>
