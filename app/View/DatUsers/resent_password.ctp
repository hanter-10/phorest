	<?php echo $this->Form->create('DatUser', array('url' => array('controller' => 'DatUsers', 'action' => 'add'),'id' => 'step3'));?>
	    <h2>あとちょっとで完了です。</h2>
	    <div class="container">
	    	<?php if ( isset ( $error_message ) ) : ?>
	    		<div class="error-message"><?php echo $error_message; ?></div>
	    	<?php endif; ?>
	        <input id="email" name="data[DatUser][email]" class="passed" type="text" value="<?php if ( isset( $email ) ) echo $email; ?>（確認済み）" disabled="disabled">
	        <?php echo $this->Form->error('DatUser.username'); ?>
	        <div class="row">
	            <span id="hostname">phorest.jp/</span>
	            <?php echo $this->Form->text('DatUser.username', array('placeholder' => 'uername　(英数)', 'label' => false, 'div' => false, 'error'=>false)); ?>
	        </div>
	        <?php echo $this->Form->error('DatUser.password'); ?>
	        <?php echo $this->Form->text('DatUser.password', array('type' => 'password', 'placeholder' => 'パスワード　（半角英数字４文字以上）', 'label' => false, 'div' => false, 'error'=>false)); ?>
	        <?php echo $this->Form->error('DatUser.sitename'); ?>
	        <?php echo $this->Form->text('DatUser.sitename', array('class' => 'last', 'placeholder' => 'サイト名　（日本語可）', 'label' => false, 'div' => false, 'error'=>false)); ?>
	    </div>
	    <input type="submit" value="登録" class="btn">
	<?php echo $this->Form->end();?>