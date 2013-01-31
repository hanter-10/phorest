<?php if ($step == 2) : ?>

	<div id="step2">
	    <h2>メールを送信しました</h2>
	    <p>
	        xipx@hotmail.comに確認メールをお送りしました。<br>
	        メールに記載されているURLをクリックして登録を完了させて下さい。
	    </p>
	</div>

<?php elseif ($step == 3) : ?>

	<?php echo $this->Form->create('DatUser', array('url' => array('controller' => 'DatUsers', 'action' => 'add'),'id' => 'step3'));?>
	    <h2>あとちょっとで完了です。</h2>
	    <div class="container">
	        <input id="email" class="passed" type="text" value="<?php if(isset($email)) echo $email; ?>（確認済み）" disabled="disabled">
	        <div class="row">
	            <span id="hostname">phorest.jp/</span>
	            <input type="text" name="data[DatUser][username]" placeholder="uername　(英数)">
	        </div>
	        <input type="password" name="data[DatUser][password]" placeholder="パスワード　（半角英数字４文字以上）">
	        <input class="last" type="text" name="data[DatUser][sitename]" placeholder="サイト名　（日本語可）">
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