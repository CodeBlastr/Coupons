<div class="redeemCoupons form">
<?php echo $this->Form->create('Coupon');?>
	<fieldset>
		<legend><?php __('Redeem Coupon'); ?></legend>
	<?php
		echo $this->Form->input('code');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>