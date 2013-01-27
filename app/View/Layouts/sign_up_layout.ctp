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
            echo $this->Html->css(array( "sign_up/css/index.css" ));
            //script output
            echo $this->element('common/js');
            echo $this->Html->script(array( "home/index.js" ));
        ?>
       
    </head>
    <body>
        <div id="wrapper">
            <header id="header">
                <a href="" id="logo"><?php echo $this->Html->image('common/logo_white.png') ?></a>
                <ul id="nav">
                    <li id="about"><a href="">ABOUT</a></li>
                    <li id="contact"><a href="">CONTACT</a></li>
                </ul>
            </header>

            <?php if(0): ?>
            <div id="step2">
                <h2>メールを送信しました</h2>
                <p>
                    xipx@hotmail.comに確認メールをお送りしました。<br>
                    メールに記載されているURLをクリックして登録を完了させて下さい。
                </p>
            </div>
            <?php elseif(0): ?>

            <form action="" id="step3" method="POST">
                <h2>あとちょっとで完了です。</h2>
                <div class="container">
                    <input id="email" class="passed" type="text" value="xipx_osx@hotmail.com　（確認済み）" disabled="disabled">
                    <div class="row">
                        <span id="hostname">phorest.jp/</span>
                        <input type="text" name="data[DatUser][username]" placeholder="uername　(英数)">
                    </div>
                    <input type="password" name="data[DatUser][password]" placeholder="パスワード　（半角英数字４文字以上）">
                    <input class="last" type="text" name="data[DatUser][sitename]" placeholder="サイト名　（日本語可）">
                </div>
                <input type="submit" value="登録" class="btn">
            </form>

            <?php elseif(1): ?>

            <div id="step4">
                <h2>登録完了しました！</h2>
                <p>
                    管理画面へ移動してPhorestを初めてみましょう！
                </p>
                <a href="" class="btn">管理画面へ</a>
            </div>

            <?php endif; ?>
            
        </div>

    </body>
</html>