<?php
class CouponsController extends CouponsAppController {

	var $name = 'Coupons';

	function index() {
		$this->Coupon->recursive = 1;
		$this->set('coupons', $this->paginate());
		
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid coupon', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('coupon', $this->Coupon->read(null, $id));
	}

	
	function add() {
		if (!empty($this->data)) {
			if($this->Coupon->generateCoupon($this->data)){
				$this->Session->setFlash(__('The coupon has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The coupon has not been saved, Please try again', true));
			}
		}
	}

	/* redeem_coupon
	 * adds coupon value to user credits
	 */
	function redeem_coupon(){
		if(!empty($this->data)){
			if($this->Coupon->redeemCoupon($this->Auth->user('id'), $this->data)){
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The coupon cannot be redeemed', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid coupon', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if($this->data['Coupon']['is_active'] == 1){
				$this->data['Coupon']['redeem_date'] = NULL;
				$this->data['Coupon']['user_id'] = NULL;
			}
			if ($this->Coupon->save($this->data)) {
				$this->Session->setFlash(__('The coupon has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The coupon could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Coupon->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for coupon', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Coupon->delete($id)) {
			$this->Session->setFlash(__('Coupon deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Coupon was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
