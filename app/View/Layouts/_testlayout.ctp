<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Phorest</title>
        <meta name="description" content="demo">
        <meta name="keywords" content="demo,demo">
        <link rel="shortcut icon" href="/images/favicon.ico">
        <link id="reset_css" rel="stylesheet" type="text/css" href="css/reset.css" media="all" />
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.24.custom.css" media="all" />
        <link rel="stylesheet" type="text/css" href="css/jquery.mCustomScrollbar.css" media="all" />
        <link rel="stylesheet" type="text/css" href="css/icostyle.css" media="all" />
        <link rel="stylesheet" type="text/css" href="css/index.css" media="all" />

        <!--[if lt IE 9]><script type="text/javascript" src="js/html5shiv.js"></script><![endif]-->
        <!--[if lte IE 7]><script src="js/lte-ie7.js"></script><![endif]-->
        <script type="text/javascript" src="js/loadCSS.js"></script>
        <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.9.0.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.scrollTo-1.4.3.1-min.js"></script>
        <script type="text/javascript" src="js/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" src="js/jquery.mCustomScrollbar.js"></script>
        <script type="text/javascript" src="js/jquery.filedrop.js"></script>
        <script type="text/javascript" src="js/underscore1.4.2-min.js"></script>
        <script type="text/javascript" src="js/backbone_092.js"></script>
        <script type="text/javascript" src="js/app.js"></script>
        <script type="text/javascript" src="js/UI.js"></script>
        <script type="text/javascript" src="js/MVC/model.js"></script>
        <script type="text/javascript" src="js/MVC/view.js"></script>
        <script type="text/javascript" src="js/MVC/router.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <!-- template -->
        <script type="text/template" id="temp_photo">
            <div class="imgTable"><div class="imgCell"><img src="<%=thumUrl%>" height="113" draggable="false"></div></div>
            <input type="text" class="filename" value="<%=photoName%>">
        </script>

        <script type="text/template" id="temp_album">
            <div class="cover">
            <div class="coverImg"></div>
            <span class="album-name"><%=albumName%></span>
            </div>
            <span class="status"><%=status%></span>
        </script>
    </head>
    <body>

        <div id="wrapper">
            <div id="countIconContainer">
                <div id="countIcon"></div>
            </div>
            <header id="header">
                <div class="container">
                    <img src="images/common/logo.png" alt="logo" id="logo">
                    <div class="right-side">
                        <div id="up-photo" class="icon-upload prevent-select">
                            アップロード
                        </div>
                        <div id="user-panel-hover" class="icon-user">
                            <span id="username">xipx</span>
                        </div>
                    </div>
                </div>
                <ul id="user-panel">
                    <li><a href="#">アカウント設定</a></li> 
                    <li><a href="#">ログアウト</a></li> 
                </ul>
            </header>
            
            <div id="main">
                <div id="albums-panel" class="prevent-select">

                    <div id="albums">

                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name" title="dreamscape">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        


                    </div>

                    <div id="album-control-bar">
                        <span id="add-album">追加＋</span>
                        <span id="remove-album">削除</span>
                    </div>
                    <div id="arrow"></div>
                </div>
                <!-- albums-panel (end) -->

                <div id="photos-panel">
                    <ul id="photos-control-panel">
                        <li class="row prevent-select">
                            <span class="icon-eye" id="preview">プレビュー</span>
                            <span class="icon-cancel" id="delete-photo">写真を削除</span>
                        </li>
                        <li class="row">
                            <input type="text" id="album-name-input" value="風景">
                        </li>
                        <li class="row">
                            <label for="status-check">アルバムを公開</label>
                            <input type="checkbox" name="status-check" id="status-check">
                        </li>
                    </ul>

                    <div id="photoCollections"></div>
                </div>
                <!-- photos-panel (end) -->

                <div id="preview-panel">
                    <div id="uploadAreaContainer">
                        <div id="upload-control-panel" class="prevent-select">
                            <span class="icon-upload" id="upload-btn">アップロード</span>
                            <span class="icon-cancel" id="delete-photo-right">写真を削除</span>
                        </div>
                        <div id="uploadArea">
                            <div class="text prevent-select">
                                <span class="small">ここに写真をドロップして</span>
                                <span class="large">アップロード</span>
                            </div>
                            <input type="file" name="photoFiles" id="photoFiles" multiple="multiple" accept="image/jpeg,image/png,image/gif">
                        </div>
                    </div>
                    <figure id="imgContainer">
                        <div class="displayAstable">
                            <div class="displayAsCell">
                                <figcaption id="caption">風景１</figcaption>
                                <img id="preview-img" src="images/larg-img.jpg" alt="large-image">
                            </div>
                        </div>
                    </figure>
                </div>
            </div>
            
            <footer id="footer">
                
            </footer>
            
        </div>

    </body>
</html>