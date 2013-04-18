//命名空間やアプリで使うプロパティなどを設定しておく

$(document).ready(function(){

    var mvc;
    $.app = {};
    $.app.Backbone = {};  mvc = $.app.Backbone;
    $.app.Events = _.extend({}, Backbone.Events);
    $.app.properties =
    {
    	root:                   "http://phorest.ligtest.info/",
        coverimg:               "http://phorest.ligtest.info/css/management_center/images/cover.png",
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
        uploadControlPanel:     $('#upload-control-panel'),
        upPhoto:                $("#up-photo"),
        caption:                $("#caption"),
        uploadArea:             $('#uploadArea')
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

            this.clickable();
            this.fileDrop();
        },



        clickable : function()
        {
            var
            $photoFiles = $('#photoFiles'),
            $uploadAreaContainer = $("#uploadAreaContainer"),
            $imgContainer = $("#imgContainer");

            $.app.properties.upPhoto.click(function(e,isSysClick){
                console.log( e.data,'ddddddddddddddd' );
                var
                $this = $(this),
                isActived = $this.hasClass('active');

                if( !isActived ){
                    $this.addClass('active');
                    $imgContainer.hide();
                    $uploadAreaContainer.fadeIn();
                }else{
                    if(!isSysClick){ return false; }
                    $(this).removeClass('active');
                    $uploadAreaContainer.hide();
                    $imgContainer.fadeIn();
                }


            });

            var
            $main = $('#main'),
            $userPanel = $("#user-panel"),
            $userPanelHover = $("#user-panel-hover");

            $userPanelHover.on('click',function(){
                if($userPanelHover.hasClass('active')){
                    $userPanelHover.removeClass('active');
                    $userPanel.fadeOut(200);
                    $main.off('click',fadeOutPanel);
                }else{
                    $userPanelHover.addClass('active');
                    $userPanel.fadeIn(200);
                    $main.one('click',fadeOutPanel);
                }
            });

            function fadeOutPanel(){
                if($userPanelHover.hasClass('active')){
                    $userPanelHover.removeClass('active');
                    $userPanel.fadeOut(200);
                }
            }

            //------------------------アルバムのアクティブ状態----------------------------
            $( '#albums' ).on( 'click' , '.cover' ,function()
            {
                $("#albums .album").removeClass("active");
                $(this).parent().addClass("active");
            }
            );

            //------------------------アルバムの追加-----------------------------

            $('#upload-btn').click(function(){
                $photoFiles.click();
            });

        },

        fileDrop: function()
        {
            $("#uploadAreaContainer").dropfile({
				url:   'http://phorest.ligtest.info/uploads/',
                inputID: 'photoFiles',
                accept: ['image/jpeg','image/png','image/gif'],
                dragEnter: function(){
                    $(this).addClass('dragOver');
                    $.app.properties.uploadArea.addClass('active');
                },
                dragLeave: function(){
                    $(this).removeClass('dragOver');
                    $.app.properties.uploadArea.removeClass('active');
                },
                drop: function(files,upload)
                {
                    $(this).removeClass('dragOver');
                    $.app.properties.uploadArea.removeClass('active');
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
                    if(responseText.errorMsg){
                        alert(responseText.errorMsg);
                        $el.remove();
                        return false;
                    }
                    $.extend(model.attributes,newAttributes);
                    model.id = model.attributes.id;
                    mvc.PhotoCollectionView_right_instance.collection.add(model,{silent: true});

                    $el.find('img')[0].src = model.get('thumUrl');
                    console.log( 'loaded' );
                },
                allLoaded: function()
                {
                    $.app.Events.trigger('allUploaded');
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
            $processbar = $('<div class="processbar"><div class="currentbar"></div></div>'),
            $imgPlaceholder = $('<div class="img-placeholder">'),
            photoModel = new mvc.PhotoModel({
                photoName: files[i].name,
                relatedAlbum: "tempAlbum"
            }),
            photoView = new mvc.PhotoView({model:photoModel}),
            photoEl = photoView.render().el,
            $photoEl = $(photoEl);


            var
            $img = $photoEl.find('img').hide().after($imgPlaceholder),
            $filename = $photoEl.find('.filename').hide().after($processbar);

            $.app.properties.photos_right.push(photoEl);
            views.push(photoView);

            //------------------
            $("#uploadAreaContainer .photoCollection").append(photoEl);
            $.app.properties.photosPanel.trigger('resize');
            (function( $$filename, $$imgPlaceholder, $$processbar ){
                $img.load(function(){
                    $this = $(this).attr({height:null,width:null});
                    $$filename.show();
                    $$imgPlaceholder.remove();
                    $$processbar.remove();
                        if(this.width/this.height > 150/113) { //横長
                            $this.attr({height:null,width:150});
                        }else{
                            $this.attr({height:113,width:null});
                        }
                        TweenMax.set( $this, {css:{opacity:0, display:'block', scale:0.3}} );
                        TweenMax.to( $this, 0.4, { css:{opacity:1,scale:1},ease:Back.easeOut} );
                    });
            })( $filename, $imgPlaceholder, $processbar );

        }
        return views;
    }



   //-----------------------------------


   //-----------------------------------

});
