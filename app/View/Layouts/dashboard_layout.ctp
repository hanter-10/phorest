<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Phorest</title>
        <meta name="description" content="demo">
        <meta name="owner" content="<?php echo $meta_data?>">
        <meta name="keywords" content="demo,demo">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
            //css output
            echo $this->element('common/css');
            echo $this->Html->css(array( "dashboard/css/bootstrap.css","dashboard/css/bootstrap-responsive.css","dashboard/css/icon.css","dashboard/css/index.css" ));
            //script output
            echo $this->element('common/js');
            echo $this->Html->script(array( "common/underscore1.4.2-min.js",DASHBORD_DS_INDEX_JS ));
        ?>

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
                <h1 id="site-name"></h1>
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