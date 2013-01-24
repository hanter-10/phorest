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
                <a href="" id="logo"><img src="" alt=""></a>
                <ul id="nav">
                    <li><a href="">ABOUT</a></li>
                    <li><a href="">CONTACT</a></li>
                </ul>
            </header>

            <form action="" id="sign-up-form" method="POST">
                <h2>あとちょっとで完了です。</h2>
                <div class="container">
                    <div class="row">
                        <span id="hostname">phorest.jp/</span>
                        <input type="text" name="data[DatUser][username]" placeholder="uername(英数)">
                    </div>
                    <input type="text" name="data[DatUser][sitename]" placeholder="サイト名">
                    <input type="text" name="data[DatUser][intro]" placeholder="自己紹介">
                </div>
                <input type="submit" value="登録" class="submit_btn">
            </form>
        </div>

    </body>
</html>