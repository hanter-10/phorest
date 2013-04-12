<?php if ($step == 2) : ?>

	<div id="step2">
	    <h2>メールを送信しました</h2>
	    <p>
	        <?php if ( isset( $email ) ) echo $email; ?>に確認メールをお送りしました。<br>
	        メールに記載されているURLをクリックして登録を完了させて下さい。
	    </p>
	</div>

<?php elseif ($step == 3) : ?>

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
	        <?php echo $this->Form->text('DatUser.sitename', array('placeholder' => 'サイト名　（日本語可）', 'label' => false, 'div' => false, 'error'=>false)); ?>
	        <?php echo $this->Form->textarea('DatUser.sitename', array('class' => 'last', 'placeholder' => 'サイト名説明', 'label' => false, 'div' => false, 'error'=>false)); ?>
	    </div>
	    <input type="submit" value="登録" class="btn">
	<?php echo $this->Form->end();?>

<?php elseif ($step == 4) : ?>

	<div id="step4">
	    <h2>登録完了しました！</h2>
	    <p>
	        管理画面へ移動してPhorestを初めてみましょう！
	    </p>
	    <a href="<?php echo $this->Html->webroot('control-panel/')?>" class="btn">管理画面へ</a>
	</div>

<?php endif; ?>