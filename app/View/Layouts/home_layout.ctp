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
            echo $this->Html->css(array( "home/css/icon.css", "home/css/index.css" ));
            //script output
            echo $this->element('common/js');
            echo $this->Html->script(array( "home/index.js" ));
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

								<?php echo $this->Form->create('DatUser', array('id' => 'login-form'));?>
                                    <input type="text" name="data[DatUser][username]" id="username" placeholder="ユーザ名">
                                    <input type="password" name="data[DatUser][password]" id="password" placeholder="パスワード">
                                    <button>ログイン！</button>
                                    <div id="options">
                                        <ul>
                                            <li>
                                                <input type="checkbox" name="remember_me" id="remember_me">
                                                <label for="remember_me">次回からパスワードを入力しない</label>
                                            </li>
                                            <li>
                                                <a href="">パスワードを忘れた？</a>
                                            </li>
                                        </ul>
                                    </div>
								<?php echo $this->Form->end();?>

                                <?php echo $this->Form->create('DatUser', array('url' => array('controller' => 'DatUsers', 'action' => 'provision'),'id' => 'sign-up-form', 'class' => 'mt30'));?>
                                    <input type="text" name="data[TmpUser][temp_email]" id="email" placeholder="E-mail">
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