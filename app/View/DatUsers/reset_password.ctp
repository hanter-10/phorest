	<?php echo $this->Form->create('DatUser', array('url' => array('controller' => 'DatUsers', 'action' => 'reset_password'),'id' => 'step3'));?>
	    <h2 class="resend_password">新しいパスワードを入力して下さい</h2>
	    <div class="container">
	    	<?php if ( isset ( $error_message ) ) : ?>
	    		<div class="error-message"><?php echo $error_message; ?></div>
	    	<?php endif; ?>
	    	<?php echo $this->Form->text('DatUser.oldpassword', array('type' => 'text','placeholder' => '発行されたパスワード','title' => '発行されたパスワード', 'label' => false, 'div' => false, 'error'=>false)); ?>
	    	<?php echo $this->Form->error('DatUser.password'); ?>
	        <?php echo $this->Form->text('DatUser.password', array('type' => 'password','placeholder' => '新しいパスワード','title' => '新しいパスワード', 'label' => false, 'div' => false, 'error'=>false)); ?>
	    </div>
	    <input type="submit" value="パスワードをリセット" class="btn">
	<?php echo $this->Form->end();?>