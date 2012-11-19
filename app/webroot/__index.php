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
        <script type="text/javascript" src="js/backbone0.9.2-min.js"></script>
        <script type="text/javascript" src="js/main-controller.js"></script>
        <script type="text/javascript" src="js/MVC/model.js"></script>
        <script type="text/javascript" src="js/MVC/view.js"></script>
        <script type="text/javascript" src="js/MVC/router.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <!-- template -->
        <script type="text/template" id="temp_photo">
            <div class="imgTable"><div class="imgCell"><img src="<%=imgUrl%>" height="113" draggable="false"></div></div>
            <input type="text" class="filename" value="<%=photoName%>">
        </script>

        <script type="text/template" id="temp_album">
            <div class="cover">
            <div class="imgContainer"><img src="images/cover" alt="cover" draggable="false"></div>
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
                            写真をアップロード
                        </div>
                        <div id="user-panel-hover">
                            <span id="username">xipx</span>▼
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

                        <div class="album active">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name" title="dreamscape">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status off">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status off">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status">非公開</span>
                        </div>
                        <div class="album">
                            <div class="cover">
                                <img src="images/cover" alt="cover" draggable="false" width="102" height="102">
                                <span class="album-name">風景</span>
                            </div>
                            <span class="status off">非公開</span>
                        </div>


                    </div>

                    <div id="album-control-bar">
                        <span id="add-album">追加＋</span>
                        <span id="remove-album">削除</span>
                    </div>
                    <div id="arrow"></div>
                </div>
                <!-- albums-panel (end) -->

                <div id="photoes-panel">
                    <ul id="photoes-control-panel">
                        <li class="row prevent-select">
                            <span class="icon-eye" id="preview">プレビュー</span>
                            <span class="icon-cancel-2" id="delete-photo">写真を削除</span>
                        </li>
                        <li class="row">
                            <input type="text" id="album-name-input" value="風景">
                        </li>
                        <li class="row">
                            <label for="status-check">アルバムを公開</label>
                            <input type="checkbox" name="status-check" id="status-check">
                        </li>
                    </ul>

                    <div id="photoes">
                        <div id="droparea">
                            <div class="text">
                                <span class="small">ここに写真をドロップして</span>
                                <span class="large">写真を追加</span>
                            </div>
                        </div>
                        <div class="photo">
                            <div class="imgTable"><div class="imgCell"><img src="images/sample.jpg" width="150" height="113" draggable="false"/></div></div>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <div class="imgTable"><div class="imgCell"><img src="images/sample2.jpg" width="88" height="113" draggable="false"/></div></div>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                        <div class="photo">
                            <img src="images/sample.jpg" width="150" height="113" draggable="false"/>
                            <input type="text" class="filename" value="sample">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>
                <!-- photoes-panel (end) -->

                <div id="preview-panel">
                    <div id="uploadAreaContainer">
                        <div id="uploadArea">
                            <div class="text prevent-select">
                                <span class="small">ここに写真をドロップして</span>
                                <span class="large">アップロード</span>
                            </div>
                            <input type="file" name="photoFiles" id="photoFiles" multiple="multiple" accept="image/jpeg,image/png,image/gif">
                        </div>
                    </div>
                    <figure>
                        <figcaption id="caption">風景１</figcaption>
                        <div id="imgContainer">
                            <img id="preview-img" src="images/larg-img.jpg" alt="large-image">
                        </div>
                    </figure>
                </div>
            </div>
            
            <footer id="footer">
                
            </footer>
            
        </div>

    </body>
</html>