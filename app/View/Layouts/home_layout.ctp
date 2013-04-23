<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Phorest</title>
        <meta name="description" content="demo">
        <meta name="owner" content="<?php echo $meta_data?>">
        <meta name="keywords" content="demo,demo">
        <?php
            //css output
            echo $this->element('common/css');
            echo $this->Html->css(array( "common/bootstrap.css", "home/css/icon.css", "home/css/index.css" ));
            //script output
            echo $this->element('common/js');
            echo $this->Html->script(array( "common/bootstrap.min.js", "home/index.js" ));
        ?>

    </head>
    <body>
        <div id="wrapper">
            <header id="header">
                <div id="title">
                    <h1 class="heading">写真好き？</h1>
                    <p class="subheading">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                </div>
                <div id="form-wrapper">
                	<?php if ( isset ( $error_message_login ) ) : ?>
						<div class="error-message"><?php echo $error_message_login; ?></div>
					<?php endif; ?>
					<?php if ( isset ( $error_message ) ) : ?>
						<div class="error-message"><?php echo $error_message; ?></div>
					<?php endif; ?>
					<?php echo $this->Form->error('DatUser.username'); ?>
					<?php echo $this->Form->error('DatUser.password'); ?>
					<?php echo $this->Form->error('TmpUser.temp_email', array('attributes' => array('class' => 'error-message signUp'))); ?>
                    <div id="form-container">
                        <div class="left">
                            <div id="login-tab" class="actived">
                                <span class="icon-login">ログイン</span>
                            </div>
                            <div id="sign-up-tab" class="unactived">
                                <span class="icon-user-add">新規登録</span>
                            </div>
                        </div>
                        <div class="right">
                            <div id="forms">
								<?php echo $this->Form->create('DatUser', array('url' => array('controller' => 'DatUsers', 'action' => 'login'), 'id' => 'login-form'));?>
                                    <?php echo $this->Form->text('DatUser.username', array('id' => 'username', 'placeholder' => 'ユーザー名、またはE-mail','title' => 'ユーザ名', 'label' => false, 'div' => false, 'error'=>false)); ?>
                                    <?php echo $this->Form->text('DatUser.password', array('id' => 'password"', 'type' => 'password', 'placeholder' => 'パスワード','title' => 'パスワード', 'label' => false, 'div' => false, 'error'=>false)); ?>
                                    <button>ログイン！</button>
                                    <div id="options">
                                        <ul>
                                            <li>
                                                <input type="checkbox" name="data[DatUser][remember]" id="remember_me">
                                                <label for="remember_me">次回からパスワードを入力しない</label>
                                            </li>
                                            <li>
                                                <a href="<?php echo $this->Html->url('/') . 'resend_password'; ?>">パスワードを忘れた？</a>
                                            </li>
                                        </ul>
                                    </div>
								<?php echo $this->Form->end();?>
                                <?php echo $this->Form->create('DatUser', array('url' => array('controller' => 'DatUsers', 'action' => 'provision'),'id' => 'sign-up-form', 'class' => 'mt30'));?>
                                    <?php echo $this->Form->text('TmpUser.temp_email', array('id' => 'email"', 'placeholder' => 'E-mail','title' => 'E-mail', 'label' => false, 'div' => false, 'error'=>false)); ?>
                                    <button>新規登録！</button>
                                <?php echo $this->Form->end();?>
                            </div>
                        </div>
                        <div class="triangle"></div>
                    </div>
                </div>
            </header>
        </div>

    </body>
</html>