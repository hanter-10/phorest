<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Phorest</title>
        <meta name="description" content="demo">
        <meta name="owner" content="<?php echo $meta_data?>">
        <meta name="keywords" content="demo,demo">
        <link rel="shortcut icon" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>images/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link id="reset_css" rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/dashboard/reset.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/dashboard/bootstrap.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/dashboard/bootstrap-responsive.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/dashboard/icon.css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>css/dashboard/index.css" media="all" />

        <!--[if lt IE 9]>
			<script type="text/javascript" src="js/html5shiv.js"></script>
			<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
			<script src="js/css3-mediaqueries.js"></script>
        <![endif]-->
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/dashboard/loadCSS.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/TweenMax.min.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/dashboard/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/dashboard/underscore1.4.2-min.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/dashboard/bootstrap.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/dashboard/jquery-tools.js"></script>
        <script type="text/javascript" src="<?php echo "http://" . $_SERVER["HTTP_HOST"] . $this->Html->webroot; ?>js/dashboard/index.js"></script>

        <script type="text/template" id="temp_album">
            <figure class="span3 item">
                <a href="<%=href%>"><img src="<%=thumUrl%>" alt="<%=albumName%>"></a>
                <figcaption class="album-title"><%=albumName%></figcaption>
            </figure>
        </script>

    </head>
    <body>
        <header id="header">
            <div class="container">
                <h1>とあるおっさんの写真集</h1>
                <!-- <nav>
                    <ul class="unstyled">
                        <li class="first"><a href="">Home</a></li>
                        <li><a href="">About</a></li>
                        <li class="last"><a href="">Contact</a></li>
                    </ul>
                </nav> -->
            </div>
        </header>
        <div id="main">
            <section id="albums" class="container">
                <div class="row">
                </div>
            </section>
        </div>
<!--         <footer id="footer"> -->
<!--             <div class="container"> -->
<!--                 <div class="row"> -->
<!--                     <div class="span4"> -->
<!--                         <h4>Lorem ipsum.</h4> -->
<!--                         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rem nemo.</p> -->
<!--                     </div> -->
<!--                     <div class="span4"> -->
<!--                         <h4>Lorem ipsum.</h4> -->
<!--                         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolores vel!</p> -->
<!--                     </div> -->
<!--                     <div class="span4"> -->
<!--                         <h4>Lorem ipsum.</h4> -->
<!--                         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit provident!</p> -->
<!--                     </div> -->
<!--                 </div> -->
<!--                 <div class="row"> -->
<!--                     <address class="span12"> -->
<!--                         ©2012 Phorest. All rights reserved -->
<!--                     </address> -->
<!--                 </div> -->
<!--             </div> -->
<!--         </footer> -->
    </body>
</html>