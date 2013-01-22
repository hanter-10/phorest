//命名空間やアプリで使うプロパティなどを設定しておく

$(document).ready(function(){

    var mvc;
    $.app = {};
    $.app.Backbone = {};  mvc = $.app.Backbone;
    $.app.properties = 
    {
    	root:                   "http://localhost:8888/phorest/",
//    	root:                   "http://localhost:81/Phorest/",
    // root:                   "http://development/phorest/",
//    	root:                   "http://pk-brs.xsrv.jp/",
        coverimg:               "http://localhost:8888/phorest/images/cover.png",
//        coverimg:               "http://localhost:81/Phorest/images/cover.png",
//        coverimg:               "http://development/phorest/images/cover.png",
//        coverimg:               "http://pk-brs.xsrv.jp/images/cover.png",
        headerHeight:           $("#header").outerHeight(true),
        albumControlBarHeight:  $("#album-control-bar").outerHeight(true),
        albums:                 $("#albums"),
        photos:                 $('#photoCollections .photoCollection .photo'),
        photos_right:           $("#uploadAreaContainer .photoCollection .photo"),
        previewImg:             $("#preview-img"),
        imgContainer:           $("#imgContainer"),
        previewImg:             $("#preview-img"),
        albumsPanel:            $("#albums-panel"),
        photosPanel:            $("#photos-panel"),
        albumsPanelWidth:       $("#albums-panel").outerWidth(),
        albumEdge:              $("#albums-panel").outerWidth(),
        photosEdge:             $("#albums-panel").outerWidth() + $("#photos-panel").outerWidth(),
        photoCollections:       $("#photoCollections"),
        albumNameInput:         $('#album-name-input'),
        albumStatusInput:       $('#status-check'),
        photoCollections_right: $('#uploadAreaContainer'),
        uploadArea:             $('#uploadArea'),
        uploadControlPanel:     $('#upload-control-panel')
    };


    $.app.methods =
    {
        init : function()
        {
            var 
            _this = this, 
            $albums = $.app.properties.albums,
            $photos = $.app.properties.photoCollections;

            //customize scrollbar
            $albums.mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true }});
            $photos.mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true }});
            $('#uploadedPhotos').mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true }});
            //add scrollTop method
            $.app.properties.albums._scrollTop = function(){ var st = parseInt(this.find('.mCSB_container').css('top')); return -st; }

            //if(!$.utilities.CSSSupports("box-shadow")) $('body').addClass("no-box-shadow")
            this.clickable();
            this.fileDrop();
        },



        clickable : function()
        {
            var
            $photoFiles = $('#photoFiles'),
            $uploadAreaContainer = $("#uploadAreaContainer"),
            $imgContainer = $("#imgContainer");

            $("#up-photo").toggle
            (
            function(){ $(this).toggleClass('hover');$uploadAreaContainer.show(); $imgContainer.hide(); },
            function(){ $(this).toggleClass('hover');$imgContainer.show(); $uploadAreaContainer.hide(); }
            );

            var
            $userPanel = $("#user-panel"),
            $userPanelHover = $("#user-panel-hover");

            $userPanelHover.toggle(function(){
                $userPanelHover.toggleClass('hover');
                $userPanel.fadeIn();
            },function(){
                $userPanelHover.toggleClass('hover');
                $userPanel.fadeOut();
            });

            //------------------------アルバムのアクティブ状態----------------------------
            $("#albums .cover").live("click",function()
            {
                $("#albums .album").removeClass("active");
                $(this).parent().addClass("active");
            }
            );

            //------------------------アルバムの追加-----------------------------
            /*$("#add-album").click(function()
            {
            var $albums = $.app.properties.albums;
            $newAlbum = $('<div class="album"><div class="cover"><img src="images/cover" alt="cover" draggable="false" width="102" height="102"><span class="album-name">風景</span></div><span class="status">非公開</span></div>');
            $newAlbum.appendTo($albums);
            $albums.scrollTo($newAlbum,{easing:"easeOutQuart" , duration:200});
            $("#albums .album").removeClass("active");
            $newAlbum.addClass("active");
            // $.app.properties.photos.html('');
            $.app.properties.albums.mCustomScrollbar("update");
            });*/

            $('#upload-btn').click(function(){
                $photoFiles.click();
            });

        },

        fileDrop: function()
        {
            $("#uploadAreaContainer").dropfile({
//                url: './upload/',
                url:   'http://localhost:8888/Phorest/uploads/',
//                url:   'http://localhost:81/Phorest/uploads/',
//				url:   'http://development/phorest/uploads/',
//				url:   'http://pk-brs.xsrv.jp/uploads/',
                inputID: 'photoFiles',
                accept: ['image/jpeg','image/png','image/gif'],
                drop: function(files,upload)
                {
                    $.app.properties.uploadArea.hide();  $.app.properties.uploadControlPanel.show();
                    var views;
                    views = renderPic(files); //画像を表示する
                    $.each(files,function(index,file){
                        var data = { view: views[index] };
                        upload(file,data);
                    });
                },
                progress: function(e,percentage)
                {
                    var $el = e.data.view.$el;
                    $el.find('.currentbar').width(percentage+"%");
                    // console.log( percentage+"%" );
                },
                load: function(e,responseText)
                {
                    // console.log( e.data );
                    var
                    view = e.data.view,
                    $el = view.$el,
                    model = view.model,
                    newAttributes = responseText;
                    $el.find('.processbar').remove();
                    $el.find('.filename').show();
                    $.extend(model.attributes,newAttributes);

                    mvc.PhotoCollectionView_right_instance.collection.add(model);

                    console.log( 'loaded' );
                },
                allLoaded: function()
                {
                    console.log( 'all uploaded' );
                }
            });
        }

    };

    


   //---------------- functions -------------------

   function renderPic(files)
   {
        var 
        url,
        views=[];

        for(i=0,len=files.length; i<len; i++)
        {
            var 
            processbar = $('<div class="processbar"><div class="currentbar"></div></div>'),
            photoModel = new mvc.PhotoModel({
                photoName: files[i].name,
                relatedAlbum: "tempAlbum"
            }),
            photoView = new mvc.PhotoView({model:photoModel}),
            photoEl = photoView.render().el;
            $(photoEl).find('.filename').hide().after(processbar);
            $.app.properties.photos_right.push(photoEl);
            views.push(photoView);
            // console.log( photoView.model,"mmm" );
            if(window.URL){
                url = window.URL.createObjectURL(files[i]);
                photoView.model.set({imgUrl:url},{silent: true});
                $("#uploadAreaContainer .photoCollection").append(photoEl);
                $.app.properties.photosPanel.trigger('resize');
                $(photoEl).find('img').hide().load(function(){
                    $(this).attr({height:null,width:null});
                    if(this.width/this.height > 150/113) { //横長
                        $(this).attr({height:null,width:150}).show();
                    }else{
                        $(this).attr({height:113,width:null}).show();
                    }
                })[0].src=url;

                // window.URL.revokeObjectURL(url);
            }else{
                var fr = new FileReader();
                fr.addEventListener('load',function(e){
                    url = this.result;
                    $("#uploadAreaContainer .photoCollection").append(photoEl);
                    $.app.properties.photosPanel.trigger('resize');
                    $(photoEl).find('img').hide().load(function(){
                    $(this).attr({height:null,width:null});
                        if(this.width/this.height > 150/113) { //横長
                            $(this).attr({height:null,width:150}).show();
                        }else{
                            $(this).attr({height:113,width:null}).show();
                        }
                    })[0].src=url;
                    
                });
                fr.readAsDataURL(files[i]);
            }

        }
        return views;
    }



   //-----------------------------------
   

   //-----------------------------------
   
});
