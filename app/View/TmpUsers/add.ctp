<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('DatUser'); ?>
    <fieldset>
        <legend><?php echo __('Please enter your username and password'); ?></legend>
    <?php
    	echo $this->Form->input('username');
        echo $this->Form->input('password');
        echo $this->Form->hidden('status',array('value' => 1));
    ?>
    </fieldset>
	<?php echo $this->Form->end(__('ログイン'));?>
</div>