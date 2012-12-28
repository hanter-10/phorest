<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>demo</title>
        <meta name="description" content="demo">
        <meta name="owner" content="<?php echo $meta_data?>">
        <meta name="keywords" content="demo,demo">
        <link rel="shortcut icon" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>images/favicon.ico">
        <link id="reset_css" rel="stylesheet" type="text/css" href="css/reset.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/frontsite/icon.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/frontsite/slideshow.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/frontsite/select-styler.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/frontsite/index.css" media="all" />

        <!--[if lt IE 9]>
              <script type="text/javascript" src="js/html5shiv.js"></script>
              <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
              <script src="js/css3-mediaqueries.js"></script>
        <![endif]-->

        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/loadCSS.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/TweenMax.min.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/underscore1.4.2-min.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/jquery-tools.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/jquery.slideshow.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/screenfull.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/jquery.select-styler.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/frontsite/index.js"></script>

        <script type="text/template" id="temp_album">
            <figure class="album">
                <div class="wrapper"><img class="cover" src="<%=thumUrl%>" alt="cover"></div>
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
                        <a href="" id="logo"><img height="14" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>images/common/logo_white.png" alt="logo"></a>
                        <span class="title">This is Photoshop's version  of Lorem Ipsum. Proin gravida</span>
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