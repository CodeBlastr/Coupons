<div class="coupons form">
<?php echo $this->Form->create('Coupon');?>
	<fieldset>
		<legend><?php echo __('Add Coupon'); ?></legend>
	<?php
		echo $this->Form->input('amount');
		echo $this->Form->input('start_date');
		echo $this->Form->input('end_date');
		echo $this->Form->input('value');
		echo $this->Form->input('description');
		echo $this->Form->input('is_active', array('type' => 'hidden', 'value' => 1));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<?php 
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Coupons',
		'items' => array(
			$this->Html->link(__('List Coupons', true), array('action' => 'index')),
			)
		),
	)));
?>