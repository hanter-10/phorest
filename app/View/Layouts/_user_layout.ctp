<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Phorest</title>
        <meta name="description" content="demo">
        <meta name="owner" content="<?php echo $meta_data?>">
        <meta name="keywords" content="demo,demo">
        <?php
            $script = array(
                "common/TweenMax.min.js",
                "common/jquery-ui.js",
                "common/jquery.mousewheel.min.js",
                "common/jquery.mCustomScrollbar.js",
                "common/mousetrap.min.js",
                "management_center/jquery.dropfile.js",
                "common/underscore.js",
                "management_center/backbone_099.js",
                "common/bootstrap.min.js",
                MANAGEMENT_DS_APP_JS,
                "management_center/UI.js",
                MANAGEMENT_DS_MVC_DS_MODEL_JS,
                MANAGEMENT_DS_MVC_DS_VIEW_JS,
                MANAGEMENT_DS_MVC_DS_ROUTER_JS
                );

            $css = array(
                "management_center/css/jquery-ui-1.8.24.custom.css",
                "management_center/css/jquery.mCustomScrollbar.css",
                "management_center/css/icon.css",
                "common/bootstrap.css",
                "management_center/css/index.css"
                );

            //css output
            echo $this->element('common/css');
            echo $this->Html->css( $css );

            //js output
            echo $this->element('common/js');
            echo $this->Html->script( $script );

            //urls
// 			$dashboard_url = $this->Html->url(array('controller'=>'DashBoards','action'=>'index',$meta_data));
			$dashboard_url = $this->Html->webroot($meta_data);
        ?>



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
                    <a href="<?php echo $dashboard_url; ?>" target="blank">
                        <?php echo $this->Html->image( 'common/logo.png', array('alt'=>'logo', 'id'=>'logo') );?>
                    </a>
                    <div class="right-side">
                        <div id="up-photo" class="icon-upload prevent-select">
                            アップロード
                        </div>
                        <div id="user-panel-hover" class="icon-user">
                            <span id="username"><?php echo $meta_data?></span>
                        </div>
                    </div>
                </div>
                <form id="user-panel">
                    <div class="triangle"></div>
                    <ul class="base">
                        <li class="first">
                            <h3>基本情報</h3>
                            <ul class="sub-ul">
                                <li><input type="text" name="sitename" placeholder="サイト名" title="サイト名" value="<?php if ( isset( $user['DatUser']['sitename'] ) ) echo $user['DatUser']['sitename'];?>"></li>
                                <li><input type="text" name="email" placeholder="E-mail" title="E-mail" value="<?php if ( isset( $user['DatUser']['email'] ) ) echo $user['DatUser']['email'];?>"></li>
                                <li><textarea name="intro" data-toggle="tooltip" placeholder="サイト説明(150文字以内)" title="サイト説明（150文字以内）" value="<?php if ( isset( $user['DatUser']['intro'] ) ) echo $user['DatUser']['intro']; ?>" ></textarea></li>
                            </ul>
                        </li>

                        <li>
                            <h3>パスワード</h3>
                            <ul class="sub-ul">
                                <li><input data-toggle="tooltip" type="password" name="old_password" placeholder="古いパスワード" title="古いパスワード"</li>
                                <li><input data-toggle="tooltip" type="password" name="new_password" placeholder="新しいパスワード" title="新しいパスワード"</li>
                            </ul>
                        </li>
                    </ul>
                    <div class="btnGroup">
                        <a class="ok">変更する</a>
                        <a class="cancel">戻す</a>
                    </div>
                    <a id="logout" href="<?php echo $this->Html->webroot; ?>DatUsers/logout">ログアウト</a>
                </form>
            </header>

            <div id="main">
                <div id="albums-panel" class="prevent-select">

                    <div id="albums">


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
                            <a class="icon-eye" id="preview" href="#" target="_blank">プレビュー</a>
                            <span class="icon-cancel" id="delete-photo">写真を削除</span>
                        </li>
                        <li class="row">
                            <input type="text" id="album-name-input" value="風景">
                        </li>
                        <li class="row">
                            <input type="checkbox" name="status-check" id="status-check">
                            <label for="status-check">アルバムを公開</label>
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
                        <div id="uploadedPhotos"></div>
                    </div>
                    <figure id="imgContainer">
                        <div class="displayAstable">
                            <div class="displayAsCell">
                                <figcaption id="caption"></figcaption>
                                <?php echo $this->Html->image('management_center/empty-album.png', array("id"=>"preview-img", "alt"=>"large-img")); ?>

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