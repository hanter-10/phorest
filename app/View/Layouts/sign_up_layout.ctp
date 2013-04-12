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
            echo $this->Html->css(array( "common/bootstrap.css","sign_up/css/index.css" ));
            //script output
            echo $this->element('common/js');
            echo $this->Html->script(array( "common/bootstrap.min.js","home/index.js" ));
        ?>

    </head>
    <body>
        <div id="wrapper">
            <header id="header">
                <a href="<?php echo $this->Html->webroot('');?>" id="logo"><?php echo $this->Html->image('common/logo_white.png') ?></a>
                <ul id="nav">
                    <li id="about"><a href="">ABOUT</a></li>
                    <li id="contact"><a href="">CONTACT</a></li>
                </ul>
            </header>

			<?php echo $content_for_layout; ?>

        </div>

    </body>
</html>