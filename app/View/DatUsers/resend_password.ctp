	<?php echo $this->Form->create('DatUser', array('url' => array('controller' => 'DatUsers', 'action' => 'add'),'id' => 'step3'));?>
	    <h2 class="resend_password">ご登録のE-mailを入力して下さい</h2>
	    <div class="container">
	    	<?php if ( isset ( $error_message ) ) : ?>
	    		<div class="error-message"><?php echo $error_message; ?></div>
	    	<?php endif; ?>
	        <?php echo $this->Form->text('DatUser.sitename', array('placeholder' => 'E-mail','title' => 'E-mail', 'label' => false, 'div' => false, 'error'=>false)); ?>
	    </div>
	    <input type="submit" value="パスワードを再発行" class="btn">
	<?php echo $this->Form->end();?>