//命名空間やアプリで使うプロパティなどを設定しておく

$(document).ready(function(){

    var mvc;
    $.app = {};
    $.app.Backbone = {};  mvc = $.app.Backbone;
    $.app.properties = 
    {
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
        photoCollections_right: $('#uploadAreaContainer')
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
            $uploadAreaContainer = $("#uploadAreaContainer"),
            $fig = $("#preview-panel>figure");

            $("#up-photo").toggle
            (
            function(){ $uploadAreaContainer.show(); $fig.hide(); },
            function(){ $fig.show(); $uploadAreaContainer.hide(); }
            );

            //------------------------アルバムのアクティブ状態----------------------------
            $("#albums .cover").live
            (
            "click",
            function()
            {
            $("#albums .album").removeClass("active");
            $(this).parent().addClass("active");
            }
            );

            //------------------------アルバムの追加-----------------------------
            $("#add-album").click(function()
            {
            var $albums = $.app.properties.albums;
            $newAlbum = $('<div class="album"><div class="cover"><img src="images/cover" alt="cover" draggable="false" width="102" height="102"><span class="album-name">風景</span></div><span class="status">非公開</span></div>');
            $newAlbum.appendTo($albums);
            $albums.scrollTo($newAlbum,{easing:"easeOutQuart" , duration:200});
            $("#albums .album").removeClass("active");
            $newAlbum.addClass("active");
            // $.app.properties.photos.html('');
            $.app.properties.albums.mCustomScrollbar("update");
            });

        },

        fileDrop: function()
        {
            $("#uploadAreaContainer").dropfile({
                //url: './upload/',
				url:   'http://development/phorest/uploads/',
                inputID: 'photoFiles',
                accept: ['image/jpeg','image/png','image/gif'],
                drop: function(files,upload)
                {
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

                    $.app.properties.PhotoCollectionView_right.collection.add(model);

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
            photoView = new mvc.PhotoView({model:photoModel},{deleteBtn:$('#delete-photo')}),
            photoEl = photoView.render().el;
            $(photoEl).find('.filename').hide().after(processbar);
            $.app.properties.photos_right.push(photoEl);
            views.push(photoView);
            console.log( photoView.model,"mmm" );
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
