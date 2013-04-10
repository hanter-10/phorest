$(function(){
   var
   $currentActivePhotoCollection,
   $photoCollection_right,
   mvc = $.app.Backbone,
   $albumNameInput = $.app.properties.albumNameInput,
   $albumStatusInput = $.app.properties.albumStatusInput,
   $title = $('head title'),
   $preview = $('#preview'),
   $statusCheck = $('#status-check'),
   $deletePhoto = $('#delete-photo'),
   $userPanel = $("#user-panel"),
   $userPanelHover = $("#user-panel-hover"),
   username = $('meta[name="owner"]').attr('content');

   mvc.PhotoView = Backbone.View.extend({
      tagName:    'div',
      className:  'photo',
      template:   _.template( $('#temp_photo').html() ),
      events:
      {
         'blur .filename' : 'changeName'
      },
      initialize : function()
      {
         // var settings = arguments[1];
         // _.bindAll(this,'deletePhoto');
         // settings.deleteBtn.on('click',this.deletePhoto);
      },
      render : function()
      {
         var
         html = this.template(this.model.toJSON()),
         width = this.model.get("width"),
         height = this.model.get("height");

         this.$el.html(html);
         var $img = this.$el.find('img').data({cid:this.model.cid});

         if(width/height > 150/113) { //横長
            $img.attr({height:null,width:150});
         }else{
            $img.attr({height:113,width:null});
         }

         return this;
      },
      changeName : function(e)
      {
         var
         $input = $(e.currentTarget),
         oldName = this.model.get('photoName');
         newName = $input.val();
         if( newName=='' || oldName==newName )
         {
            $input.val(oldName);
         }
         else
         {
            // this.model.set({photoName:newName});
            this.model.save({photoName:newName}, {patch: true});
         }

      }
   });

   mvc.PhotoCollectionView = Backbone.View.extend({
      tagName : 'div',
      className : 'photoCollection',
      events :
      {
         'keypress .filename':   'unFocus',
         'click .photo':         'selectable'
      },
      rendered : false,
      appendTo : undefined,
      tempType : false, //通常タイプ
      initialize : function()
      {
         this.appendTo = $.app.properties.photoCollections.find('.mCSB_container');
         this.albumID = this.options.albumID;
         this.albumView = this.options.albumView;
         //インスタンスから引数を受け取って初期設定
         var settings = arguments[1];
         if(settings)
         {
            // console.log( 'options->',settings );
            settings.init && settings.init();
            settings.appendTo && (this.appendTo = settings.appendTo);
            settings.tempType && (this.tempType = settings.tempType);
         }

         this.collection.on('add',this.addPhotos,this);
         this.collection.on('remove',this.removePhotos,this);
         _.bindAll(this,'onDropAlbum','onDropUploadArea','onDropPhotosArea'); //The _.bindAll() must be called before the function is assigned
         var dnd = new $.app.UI.methods.dndClass(this.$el,'img'),
         options =
         [
            {
               dropArea:   'album',
               events:
               {
                  onDrop:     this.onDropAlbum, //dndElemはドロップされた要素(album)
                  onDragOver: this.onDragOverAlbum
               }
            },
            {
               dropArea:    'uploadArea',
               events:
               {
                  onDrop:     this.onDropUploadArea,
                  onDragOver: this.onDragOverUploadArea
               }
            },
            {
               dropArea:    'photosArea',
               events:
               {
                  onDrop:     this.onDropPhotosArea,
                  onDragOver: this.onDragOverPhotosArea
               }
            }
         ];
         dnd.enable(options);

         // this.onDropAlbum = _.bind(this.onDropAlbum,this);
         //this.$el.on('keypress','.filename',this.unFocus); //通常のjQueryのonを利用している
      },
      render : function(onlyRender)
      {
         //レンダリング済みであれば、それを表示すればいい
         console.log(this.rendered)
         if(this.rendered){ this.showPhotos(); return this; }
         this.rendered = true;
         //初回のレンダリング処理を行う
         // this.$el.empty();
         if(!onlyRender){
            if($currentActivePhotoCollection) //$currentActivePhotoCollectionがすでに存在していれば、隠しおく
            {
               $currentActivePhotoCollection.removeClass('active').hide();
            }
            $currentActivePhotoCollection = this.$el.addClass('active');
         }else{
            this.$el.hide();
         }

         this.$el.data('collection',this.collection);
         var _this = this;
         this.collection.each(function(photo){
            var photoView;
            photoView = new mvc.PhotoView({model:photo});

            _this.$el.append(photoView.render().el);
         });


         // insert to container
         this.appendTo.append(this.$el);
         if(onlyRender){ return this; }
         //UIが正常に働くように、プロパティを更新
         $.app.properties.photos = this.$el.find('.photo');
         $.app.properties.photosPanel.trigger('resize');
         return this;
      },
      showPhotos : function()
      {
         //現在のをhide、クリックされたのをshow、現在の値を更新
         $currentActivePhotoCollection.removeClass('active').hide();
         $currentActivePhotoCollection = this.$el;
         $currentActivePhotoCollection.addClass('active').show();
         //UIが正常に働くように、プロパティを更新
         $.app.properties.photos = this.$el.find('.photo');
         $.app.properties.photosPanel.trigger('resize');
      },
      unFocus : function(e)
      {
         if(e.keyCode == 13) //Enter押されたらchangeNameをトリガーする
         {
            e.currentTarget.blur();
         }
      },
      selectable : function(e)
      {
         if(e.target.className=='filename') { e.target.select(); }
         if(e.target.tagName!='IMG') { return this;}
         var
         ctrlOrShift,
         $photo = $(e.currentTarget), //original 'this'
         $container = this.$el;

         if(e.ctrlKey || e.metaKey)
         {
            ctrlOrShift = "ctrl";
         }
         else if(e.shiftKey)
         {
            ctrlOrShift = "shift";
         }
         else
         {
            ctrlOrShift = "null";
         }
         // console.log(this)
         //...........................

         if(ctrlOrShift=="ctrl") //ctrl
         {
            if($photo.hasClass("selectedElem"))
            {
               $photo.removeClass("selectedElem");
            }
            else
            {
               $photo.addClass("selectedElem");
            }
         }
         else if(ctrlOrShift=="shift") //shift
         {
            var
            $selectedElems = $container.find(".selectedElem"),
            $firstElem = $selectedElems.eq(0), //ctrlを押しながらクリックした要素
            selDirection = "down", //選択の方法、デフォルで下に向かって選択する
            beSelected; //選択された要素たち

            $photo.addClass("activing"); //add class to the clicked element for researching the direction of selection
            if( $firstElem.prevAll(".activing").length )  selDirection = "up";

            if(selDirection == "down")
            {
               beSelected = $firstElem.nextUntil($photo);
            }
            else
            {
               $firstElem = $selectedElems.eq($selectedElems.length-1);
               beSelected = $firstElem.prevUntil($photo);
            }

            // $(".selectedElem",$container).removeClass("selectedElem");
            beSelected = beSelected.add($photo).add($firstElem);
            beSelected.addClass("selectedElem");
            $photo.removeClass("activing");

         }
         else
         {
            var
            cid = $(e.target).data('cid'),
            model = this.collection.get(cid),
            imgUrl = model.get('imgUrl_m'),
            photoName = model.get('photoName');

            if(!this.tempType){
               if( decodeURI($.app.properties.previewImg[0].src) != imgUrl ){
                  $.app.properties.previewImg.hide().load(function(){
                     $(this).fadeIn(300);
                  })[0].src=imgUrl;
               }
               $.app.properties.caption.text(photoName);

               if($.app.properties.upPhoto.hasClass('active')){
                  $.app.properties.upPhoto.trigger("click",[true]);
               }
            }
            // console.log( imgUrl );
            $(".selectedElem",$container).removeClass("selectedElem");
            $photo.addClass("selectedElem");
         }

      },
      /* ........................... dnd ...........................*/
      onDropAlbum: function($dndElem, $selectedElem)
      {
         $dndElem.removeClass("dragOver");
         var targetAlbumView = $dndElem.data('view');

         if(!targetAlbumView.PhotoCollectionView.rendered){ //レンダリングされてなければ、renderOnlyモードでレンダリングを行う
            targetAlbumView.PhotoCollectionView.render(true);
         }
         // ドロップした先のalbumのmodel、要するに移動先toである
         var albumModel = mvc.AlbumsView_instance.collection.get($dndElem.data('cid'));
         // console.log( albumModel );
         if($dndElem.hasClass("active")) //現在アクティブ中のフォルダに落とした場合、falseを返し、何もしない
         {
            return false;
         }


         //animation
         var
         _this = this,
         dpos = $dndElem.offset(),
         len = $selectedElem.length;
         $selectedElem.each(function(index){
            var
            $this = $(this),
            $clone = $this.clone(),
            pos = $this.offset();

            $clone.css({position:"absolute", top:pos.top, left:pos.left, "z-index": 999, margin: 0}).addClass('alternative');
            $('body').append($clone);


            var
            rx = _.random(60, 200)*0.01,
            ry = _.random(60, 130)*0.01,
            dx = pos.left - dpos.left,
            dy = Math.abs(pos.top - dpos.top),
            fpx = dx/4+dpos.left + dy/5 * rx,
            fpy = Math.abs(dpos.top-dy/2) * ry,
            p1 = {left:fpx, top:fpy, scaleX:1,scaleY:1,opacity:1},
            p2 = {left:dpos.left, top:dpos.top, scaleX:0.3,scaleY:0.3,opacity:0},
            bezier = {
               values: [ p1, p2],
               curviness:0.3,
               autoRotate:180,
               type:"soft",
               timeResolution:8
            };

            if(--len==0){
               var
               lastone = true, //クロージャ
               cid = $this.find('img').data('cid'),
               lastPhotoModel = _this.collection.get(cid),
               thumUrl_square = lastPhotoModel.get('thumUrl_square');

               $clone.data( 'thumUrl_square', thumUrl_square );
            }
            TweenMax.to( $clone , 0.85, {delay:index*0.01,css:{bezier:bezier}, ease:Power1.easeOut, onComplete:function(){
               if( lastone ){
                  $.app.Events.trigger('moveToAlbumAreaEnd', { currentAlbumView: _this.albumView ,targetAlbumView: $dndElem.data('view') } );
               }
               $clone.remove();
            } });
         });

         this.move($selectedElem,albumModel);
         return true;
         // console.log( $dndElem,$selectedElem );
      },
      onDragOverAlbum: function($dndElem, $selectedElem)
      {

         this.lastDragOverElem.removeClass("dragOver");
         this.lastDragOverElem = $dndElem.addClass("dragOver");
      },
      onDropUploadArea: function($selectedElem)
      {
         console.log( 'upload' );
         $("#albums .dragOver").removeClass('dragOver');
      },
      onDragOverUploadArea: function($selectedElem)
      {

      },
      onDropPhotosArea: function($selectedElem)
      {
         $("#albums .dragOver").removeClass('dragOver');
         // console.log( $selectedElem[0],$selectedElem[1] );
         if($selectedElem.parents('#photoCollections').length!=0){
            return false;
         }else{
            // ドロップした先のalbumのmodel、要するに移動先toである
            var
            $to = $('#albums .album.active'),
            albumModel = mvc.AlbumsView_instance.collection.get($to.data('cid'));
            this.move($selectedElem,albumModel,true); //isPhotosAreaをtrueにすることで、moveでアニメを実行
            return true;
         }


      },
      onDragOverPhotosArea: function($selectedElem)
      {

      },
      move : function( $selectedElem, targetAlbum, isPhotosArea )
      {
         //Transactionが成功したら、移動されたモデルをdestroyし、移動先のコレクションに追加する
         //追加してもrenderはしないので、自力でviewを作ってappendする
         //そのためには、移動先のcollectionにアクセスする必要もある、それが
         //targetAlbum.PhotoCollectionView.collection
         var
         data,
         options,
         photoModels = [],
         photoIds = [],
         _this = this;

         _.each($selectedElem,function(el,index){
            var
            cid      = $('img',el).data('cid'),
            model    = this.collection.get(cid);
            photoModels[index] = model;
            photoIds[index] = model.id || model.attributes.id;
         },this);

         data = JSON.stringify({ targetAlbum:targetAlbum.id, photos:photoIds });
         options =
         {
            data : data,
            url : 'http://localhost:8888/phorest/datalbumphotorelations/'+_this.albumID
         };
         Backbone.sync('update',null,options)
         .fail(function(){
            console.log( 'faid roll back database' );
         });

         //サーバーの応答を待たずに、とりあえずアルバムを移動する

         _this.collection.remove(photoModels);
         targetAlbum.PhotoCollectionView.collection.add(photoModels);

         if(isPhotosArea){
            //UIが正常に働くように、プロパティを更新
            $.app.properties.photos_right = $.app.properties.photos_right.not($selectedElem);
            $.app.properties.photos = $.app.properties.photos.add($selectedElem);
            $.app.properties.photosPanel.trigger('resize');

            var $clones=$();
            $selectedElem.each(function(index){
               var
               $this = $(this),
               $clone = $this.clone(),
               pos = $this.offset();

               $clone.css({position:"absolute", top:pos.top, left:pos.left, "z-index": 999, margin: 0}).addClass('alternative');
               $clones = $clones.add($clone);
            });
            $clones.appendTo('body');
            $selectedElem.css('visibility','hidden');
         }


         //目的のアルバムに挿入
         targetAlbum.PhotoCollectionView.$el.prepend($selectedElem);

         //---------- animation -----------
         var w_height = $(window).height();
         if(isPhotosArea){
            var len = $selectedElem.length;
            $selectedElem.each(function(index){
               var
               $this = $(this),
               $clone = $clones.eq(index),
               curviness = 0.3,
               pos = $clone.offset(),
               dpos = $this.offset();

               if(dpos.top > w_height){
                  var rh = _.random(60,200);
                  dpos.top = w_height+rh;
                  curviness = _.random(30,100)*0.01;
               }


               var
               rx = _.random(60, 130)*0.01,
               ry = _.random(60, 130)*0.01,
               dx = pos.left - dpos.left,
               dy = Math.abs(pos.top - dpos.top),
               fpx = dpos.left-dx/4 - dy/15 * rx,
               fpy = Math.abs(dpos.top-dy/2) * ry,
               p1 = {left:fpx, top:fpy, scaleX:1.5,scaleY:1.5},
               p2 = {left:dpos.left, top:dpos.top, scaleX:1,scaleY:1},
               bezier = {
                  values: [ p1, p2],
                  curviness:curviness,
                  type:"soft",
                  timeResolution:8
               },
               t = 0.8;
               /*if (dy>1500){
                  t=1.8;
               }else if(dy>1875){
                  t=2.25;
               }else if(dy>2250){
                  t=2.7;
               }else if(dy>2625){
                  t=3.15;
               }*/
               if(--len==0){
                  var lastone = true;
               }
               TweenMax.to( $clone , t, {delay:index*0.03,css:{bezier:bezier}, ease:Back.easeOut.config(0.5), onComplete:function(){
                  $this.css('visibility', 'visible');

                  if(lastone){ //全部終わった時
                     $.app.Events.trigger('moveToPhotoAreaEnd');

                  }
                  $clone.remove();
               } });
            });
         }

      },
      addPhotos : function(models)
      {
         console.log( 'add',models );
      },
      removePhotos : function(models)
      {
         console.log("remove",models);
      }
   });



   //単体のアルバムView
   mvc.AlbumView = Backbone.View.extend({
      className:  'album',
      tagName:    'div',
      template:   _.template($('#temp_album').html()),
      events:
      {
         'click .cover': 'changeAlbum'
      },
      initialize : function()
      {
         var photoCollection = new mvc.PhotoCollection(this.model.get('photos'));
         this.PhotoCollectionView = new mvc.PhotoCollectionView({ collection:photoCollection, albumID:this.model.id, albumView:this });
         this.model.PhotoCollectionView = this.PhotoCollectionView;
         _.bindAll(this,'changeName','unFocus');
         $albumNameInput.on('keypress',this.unFocus);
         $albumNameInput.on('blur',this.changeName);
         this.model.on('destroy',this.remove,this);
         //今後利用できるよう、cidをdataにセットしておく
         this.$el.data({ cid:this.model.cid, view:this });
      },
      render : function()
      {
         var
         html,
         json = this.model.toJSON();

         json.status = json.public==1 ? "公開" : "非公開";

         html = this.template(json);

         this.$el.html(html);
         // this.$el.data('view',this);

         //アルバムのサムネールを設定する
         var
         $coverImg = this.$el.find('.coverImg'),
         thumUrl;

         this.$coverImg = $coverImg;

         try {
            thumUrl = this.model.get('photos')[0].thumUrl_square;
         }catch(e){
            thumUrl = $.app.properties.coverimg;
         }
         $coverImg.css({'background-image' : "url('"+thumUrl+"')"});
         return this;
      },
      showPhotos : function()
      {
         this.PhotoCollectionView.render();
         return this;
      },
      changeAlbum : function()
      {
         if(!this.$el.hasClass('active')) //今アクテイブ中のアルバムではなく、新たにクリックしたアルバムなら
         {
            var
            //status,
            albumName = this.model.get('albumName'),
            status = this.model.get('public')==1 ? true : false;


            $albumNameInput.val(albumName); //アルバムの名前の表示を更新
            $albumStatusInput.attr({checked:status}); //アルバムの公開非公開の表示を更新
            // console.log( this.model.get('albumName') );
            //$('#delete-photo').off(); //PhotoViewを生成時にイベントが累加されないように、予めイベントたちを全て削除しておく
            this.showPhotos();

            mvc.router.navigate('album/'+albumName);
            $title.text('Phorest - '+albumName);

            $preview.attr('href',$.app.properties.root + username + "/preview/albums/" + albumName);
            if( !this.PhotoCollectionView.$el.find('>.photo').length ){
               $preview.add($deletePhoto).addClass('disabled');
               $preview.attr('href',null);
               // $preview.on('click',function(){return false;});
            }else{
               $preview.add($deletePhoto).removeClass('disabled');
               // $preview.off('click');
            }
            return this;
         }
      },
      unFocus : function(e)
      {
         if(e.keyCode == 13)
         {
            e.currentTarget.blur();
         }
      },
      changeName : function(e)
      {
         var
         $albumEl = this.$el,
         newVal = $albumNameInput.val(),
         oldVal = this.model.get('albumName');

         if(newVal=="")
         {
            $albumNameInput.val(oldVal); return;
         }

         if(newVal==oldVal){ return; }

         if( $albumEl.hasClass('active') )
         {
            this.model.save({ albumName: newVal},{patch: true});
            $('.album-name',$albumEl).text(newVal);
            mvc.router.navigate('album/'+newVal);
         }
      },
      remove : function()
      {
         this.$el.prev().find('.cover').click();
         this.$el.remove();
         this.PhotoCollectionView.remove();
      },
      updateCoverImage : function(){
         var
         $latestPhoto = this.PhotoCollectionView.$el.find('>.photo:first-child'),
         _this = this;

         if($latestPhoto.length!=0){ // if it's not empty
            var
            photoModel = this.getPhotoModelByEl($latestPhoto),
            thumUrl = photoModel.get('thumUrl_square');
            $('<img>').load(function(){
               _this.$coverImg.css({'background-image' : "url('"+thumUrl+"')"});
            })[0].src = thumUrl;
         }else{ //empty
            this.$coverImg.css({'background-image' : "url('"+$.app.properties.coverimg+"')"});
            $preview.add($deletePhoto).addClass('disabled');
            $preview.attr('href',null);
         }
         return this;
      },
      updatePreviewBtn : function(){
         var albumName = this.model.get('albumName');
         $preview.attr('href',$.app.properties.root + username + "/preview/albums/" + albumName);
         return this;
      },
      getPhotoModelByEl : function ($photoEl){
         var
         collection = this.PhotoCollectionView.$el.data('collection'),
         cid = $photoEl.find('img').data('cid'),
         model = collection.get(cid);
         return model;
      }
   });


   //ここから宇宙が始まるぞ
   mvc.AlbumsView = Backbone.View.extend({
      el : '#albums .mCSB_container',
      initialize : function()
      {
         this.collection.on('reset',this.render,this);

      },

      render : function()
      {
         var
         initLocation = $.app.properties.initLocation, //ループ後、最初のアルバムviewになる
         albumEls = $("<div>"), //fragment element
         clickedAlbum,
         emptyAlbumCount=0;

         this.collection.each(function(album, index){
            /*
            albumの例
            album = {
               albumName:  '風景',
               status:     'private',
               photoCount: 34,
               photos:[
                  {
                     photoName:     '桜の花',
                     relatedAlbum:  '45345ffdex',
                     imgUrl:        'http://phorest/img/434f.jpg',
                     thumbnailUrl:  'http://phorest/img/434f_thum.jpg'
                  }
               ]
            }
            */
            if(album.get('photos').length==0){
               emptyAlbumCount++;
            }

            if(!album.get('tempAlbum')) //臨時的な写真を格納するアルバムじゃなければ
            {
               var
               albumView = new mvc.AlbumView({model:album}),
               albumEl = albumView.render().el;

               //if(index==0){ firstAlbumView = albumView; } //最初のアルバムを決める
               albumEls.append(albumEl);
            }
            else
            {  //temp album
               var photoCollection = new mvc.PhotoCollection(album.get('photos'));
               mvc.PhotoCollectionView_right_instance = new mvc.PhotoCollectionView({collection:photoCollection,albumID:album.attributes.id},{tempType:true,appendTo:$('#uploadedPhotos .mCSB_container')});
               mvc.PhotoCollectionView_right_instance.render().$el.show();
               $photoCollection_right = $("#uploadAreaContainer .photoCollection");
               $photoCollection_right.data('collection',mvc.PhotoCollectionView_right_instance.collection);
               $.app.properties.photos_right = $("#uploadAreaContainer .photoCollection .photo");
               if($.app.properties.photos_right.length==0){
                  $('#upload-control-panel').hide();
               }else{
                  $('#uploadArea').hide();
               }
            }

         });

         this.$el.append( albumEls.children() );


         //最初のアルバム内の写真を表示する
         if(initLocation=="home"){  //ホームであれば最初のアルバムをクリック
            clickedAlbum = $("#albums .album .cover").eq(0).click().parent();
         }else{ //違うならそのアルバムをクリック

            var matchedAlbum = $("#albums .album").filter(function(){
               return $(this).find('.album-name').text() == initLocation;
            }).eq(0).find('.cover');

            if(matchedAlbum.length!=0){
               clickedAlbum = matchedAlbum.click().parent();
            }else{
               clickedAlbum = $("#albums .album .cover").eq(0).click().parent();
            }
         }

         var clickedAlbumModel = getAlbumModelByEl(clickedAlbum);
         if( clickedAlbumModel.get('photos').length != 0 ){
            clickedAlbumModel.PhotoCollectionView.$el.find('.photo img').eq(0).click(); //持ってるなら最初の写真をクリック
         }
         $.app.properties.upPhoto.trigger("click",[true]);

         mvc.PhotoCollectionView_right_instance.$el.show().addClass('active');
         if(emptyAlbumCount==this.collection.length){
            //$.getScript('/js/management_center/tutorial.js');
         }      }
   });


   //functions
   function syncPhotoDel($delBtn,which)
   {
      $delBtn.click(function(){
         var
         $photoCollection = which == 'left' ? $currentActivePhotoCollection : $photoCollection_right,
         $selectedElems = $photoCollection.find('.selectedElem'),
         collection = $photoCollection.data('collection');

         if($selectedElems.length==0) return;
         $selectedElems.each(function(index){
            var
            $el = $(this),
            cid = $el.find('img').data('cid'),
            model = collection.get(cid);
            model.destroy();


         });

         if(which=='left'){ //削除したらプレビュー写真を前｜次の写真に変える。
            var prev = $selectedElems.eq(0).prev();
            if(prev.length==0){
               prev = $selectedElems.eq(0).next();
            }

            prev.find('img').click();
         }

         TweenMax.to($selectedElems,0.4,{ css:{scale:0.3,opacity:0},ease:Back.easeIn,onComplete:function(){
            var albumView = getActivedAlbumView();
            $selectedElems.remove();
            albumView.updateCoverImage();
         } });

      });
   }

   syncPhotoDel($('#delete-photo'),'left');
   syncPhotoDel($('#delete-photo-right'),'right');

   function syncAlbum()
   {
      $('#add-album').click(addAlbum);
      $('#remove-album').click(delAlbum);

      function addAlbum()
      {
         var 
         $el = mvc.AlbumsView_instance.$el,
         albumName = '新規アルバム',
         newAlbumCount = $("#albums .album-name:contains('新規アルバム')").length;
         albumName+=(newAlbumCount+1);

         mvc.AlbumsView_instance.collection.create({albumName:albumName,status:0},{silent: true,success:function(model){
            model.id = model.attributes.id;
            var
            albumView = new mvc.AlbumView({model:model}),
            albumEl = albumView.render().el;
            $el.append(albumEl);
            $.app.properties.albums.mCustomScrollbar("update");
            $(albumEl).find('.cover').click().addClass('flipInX animated');
            $.app.properties.albums.mCustomScrollbar("scrollTo","bottom");
            // $(albumEl).addClass('flipInX animated');
            // console.log( albumEl );
         }});
      }

      function delAlbum()
      {
         if($('#albums .album').length==1){
            alert('これ以上アルバムを削除できません');
            return false;
         }


         var
         albumModel = getActivedAlbumModel();
         albumModel.destroy();
      }
   }


   function getActivedAlbumModel()
   {
      var
      $actived_album = $("#albums .album.active"),
      cid = $actived_album.data('cid'),
      albumModel = mvc.AlbumsView_instance.collection.get(cid);
      return albumModel;
   }

   function getActivedAlbumView()
   {
      var
      $actived_album = $("#albums .album.active"),
      albumView = $actived_album.data('view');
      return albumView;
   }

   function getAlbumModelByEl($albumEl)
   {
      var
      cid = $albumEl.data('cid'),
      albumModel = mvc.AlbumsView_instance.collection.get(cid);
      return albumModel;
   }




   function syncAlbumStatus()
   {
      $statusCheck.change(function(){
         var
         albumModel = getActivedAlbumModel(),
         status = this.checked ? 1 : 0,
         stext = status ? "公開" : "非公開";
         albumModel.save({public:status},{patch: true});

         //リアルタイムで表示を更新する
         $('#albums .album.active .status').text(stext);
      });

   }

   function catchBlankAreaClicking($container){
      $container.click(function(e){
         var
         isActived = $.app.properties.upPhoto.hasClass('active'),
         tnt = /IMG|INPUT/i,
         tn = e.target.tagName;

         if(!tnt.test(tn)){
            $('.photoCollection.active>.selectedElem',this).removeClass('selectedElem');
            if(!isActived){
               $.app.properties.upPhoto.trigger("click",[true]);
            }
         }
      });
   }

   function bindEvents(){
      //-------------- move to album-area end --------------------
      $.app.Events.on('moveToAlbumAreaEnd', function(data){
         if( data.currentAlbumView ){
            data.currentAlbumView.updateCoverImage();
         }
         data.targetAlbumView.updateCoverImage();
         if(mvc.PhotoCollectionView_right_instance.$el.find('>.photo').length==0){
            $.app.properties.uploadControlPanel.hide();
            $.app.properties.uploadArea.fadeIn();
         }
      });

      //------------------ move to photo-area end -------------------
      $.app.Events.on('moveToPhotoAreaEnd', function(data){
         //enable preview button
         $preview.add($deletePhoto).removeClass('disabled');
         // $preview.off('click');
         //hide or show upload-area
         if(mvc.PhotoCollectionView_right_instance.$el.find('>.photo').length==0){
            $.app.properties.uploadControlPanel.hide();
            $.app.properties.uploadArea.fadeIn();
         }
         //update album cover image
         $("#albums .album.active").data('view').updateCoverImage().updatePreviewBtn();
      });
   }

   function setUserPanel(){
      var
      $inputs = $userPanel.find('input'),
      $okbtn = $userPanel.find('.ok'),
      backup = {};

      $inputs.tooltip({placement:'left'});
      $inputs.each(function(){
         var
         $this = $(this),
         name = $this.attr('name'),
         val = $this.val();

         backup[name] = val;
      });

      function rollBack(){
         $inputs.each(function(){
            var
            $this = $(this),
            name = $this.attr('name'),
            val = backup[name];
            $this.val(val);
         });
      }

      $okbtn.click(function(){
         var
         url = '/phorest/DatUsers/edit/'+username,
         data = {};

         $inputs.each(function(){
            var
            $this = $(this),
            name = $this.attr('name'),
            val = $this.val();

            data[name] = val;
         });

         $.ajax({
            url: url,
            data: data,
            type: 'PATCH',
            dataType: 'json',
            success: function(response){
               if( !response.errorMsg ){
                  backup = data;
                  $userPanelHover.click();
               }else{
                  rollBack();
               }
            },
            error: function(){
               rollBack();
            }
         });
      });


      $('#user-panel .cancel').click(function(){
         rollBack();
      });

      $userPanel.submit(function(e){
         e.preventDefault();
         $okbtn.click();
         return false;
      });

   }

   function errorHandling(){
      $( document ).ajaxError(function(event, jqxhr, settings, exception) {
         alert('通信処理に失敗しました。\nページを再読み込みして再度お試しください。');
      });

      $( document ).ajaxSuccess(function(event, xhr, settings) {
         if(xhr.responseText.indexOf('errorMsg') != -1){
            var data = JSON.parse(xhr.responseText);
            alert(data.errorMsg);
         }
      });
   }

   errorHandling();
   setUserPanel();
   bindEvents();
   catchBlankAreaClicking($.app.properties.photoCollections.add('#uploadedPhotos'));

   syncAlbumStatus();
   syncAlbum();

});