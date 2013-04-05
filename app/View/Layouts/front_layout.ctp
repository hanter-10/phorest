<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>demo</title>
        <meta name="description" content="demo">
        <meta name="owner" content="<?php echo $meta_data?>">
        <?php if ( isset( $preview_mode ) ) : ?>
        	<meta name="previewMod" content="<?php echo $preview_mode; ?>">
        <?php endif; ?>
        <meta name="keywords" content="demo,demo">

        <?php
            $scripts = array(
                "common/jquery-ui.js",
                "common/jquery.mousewheel.min.js",
                "common/jquery.mCustomScrollbar.js",
                "common/underscore.js",
                "common/backbone.js",
                "common/TweenMax.min.js",
                "common/jquery-tools.js",
                "common/jquery.slideshow.js",
                "common/screenfull.js",
                FRONTSITE_DS_INDEX_JS
                );
            echo $this->element('common/css');
            echo $this->Html->css(array( "frontsite/css/icon.css","common/slideshow.css","frontsite/css/index.css","frontsite/css/jquery.mCustomScrollbar.css" ));

            echo $this->element('common/js');
            echo $this->Html->script( $scripts );
        ?>


        <script type="text/template" id="temp_album">
            <figure class="album">
                <div class="wrapper"><img class="cover" src="<%=thumUrl%>" alt="cover" height="150" width="150"></div>
                <figcaption><%=albumName%></figcaption>
            </figure>
        </script>

    </head>
    <body>
        <div id="albums">
            <div id="albumsContainer">

            </div>
        </div>
        <div id="wrapper">


            <div id="main">
                <div id="controller">
                    <div id="prevbtn">
                        <div id="leftArrow"></div>
                    </div>
                    <div id="nextbtn">
                        <div id="rightArrow"></div>
                    </div>
                </div>
            </div>

            <div id="click-receiver"></div>

            <footer id="footer">
                <div class="upperpart">
                    <div class="fl">
                        <a href="" id="logo"><?php echo $this->Html->image('common/logo_white.png') ?></a>
                        <ul class="nav">
                            <li class="home"><a href=""><?php echo $this->Html->image('frontsite/home_icon.png'); ?></a></li>
                            <li class="album-name"> </li>
                            <li class="photo-name"> </li>
                        </ul>
                        <!-- <span class="title">This is Photoshop's version  of Lorem Ipsum. Proin gravida</span> -->
                    </div>
                    <div class="fr">
                        <button class="icon-cog" id="config">設定</button>
                        <button class="icon-picture" id="show-photos">写真</button>
                        <button class="icon-grid-view" id="show-albums">アルバム</button>
                    </div>
                </div>
                <div class="underpart">
                    <div id="indicator"></div>
                    <div id="img-container">

                    </div>
                </div>
                <ul id="controlPanel">
                    <li>
                        <div class="left">写真ごとの再生秒数</div>
                        <div class="right" unselectable="on">
                            <span class="current">5秒</span>
                            <ul class="options">
                                <li data-type="duration" data-value="2">2秒</li>
                                <li data-type="duration" data-value="3">3秒</li>
                                <li data-type="duration" data-value="5" class="current">5秒</li>
                                <li data-type="duration" data-value="15">15秒</li>
                                <li data-type="duration" data-value="20">20秒</li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <div class="left">エフェクト</div>
                        <div class="right" unselectable="on">
                            <span class="current">フェード</span>
                            <ul class="options">
                                <li data-type="effect" data-value="fade" class="current">フェード</li>
                                <li data-type="effect" data-value="page">ページ</li>
                                <li data-type="effect" data-value="slide">スライド</li>
                                <li data-type="effect" data-value="zoom">拡大</li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <div class="left">写真表示方式</div>
                        <div class="right" unselectable="on">
                            <span class="current">自動</span>
                            <ul class="options">
                                <li data-type="size" data-value="auto" class="current">自動</li>
                                <li data-type="size" data-value="cover">カバー</li>
                                <li data-type="size" data-value="contain">フル</li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <div class="left">全画面表示</div>
                        <div class="right" unselectable="on">
                            <span class="current">OFF</span>
                            <ul class="options">
                                <li data-type="fullscreen" data-value="on">ON</li>
                                <li data-type="fullscreen" data-value="off" class="current">OFF</li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </footer>

        </div>

    </body>
</html>