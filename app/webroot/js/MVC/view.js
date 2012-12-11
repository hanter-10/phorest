$(function(){
console.log( 'view loaded' );
   var
   $currentActivePhotoCollection,
   mvc = $.app.Backbone,
   $albumNameInput = $.app.properties.albumNameInput,
   $title = $('head title');

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
         var settings = arguments[1];
         _.bindAll(this,'deletePhoto');
         settings.deleteBtn.on('click',this.deletePhoto);
      },
      render : function()
      {
         var html = this.template(this.model.toJSON());
         this.$el.html(html);
         this.$el.find('img').data({cid:this.model.cid});
         return this;
      },
      deletePhoto : function(e)
      {
         if(this.$el.hasClass('selectedElem')) //選択されているなら削除する
         {
            console.log('wow')
            var _el = this.$el;

            $('#delete-photo').off(e);
            this.model.destroy();
            _el.fadeOut(300,function(){ _el.remove(); });
         }

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
            this.model.set({photoName:newName});
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
         _.bindAll(this,'onDropAlbum','onDropUploadArea'); //The _.bindAll() must be called before the function is assigned
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
      render : function()
      {
         //レンダリング済みであれば、それを表示すればいい
         console.log(this.rendered)
         if(this.rendered){ this.showPhotos(); return this; }
         this.rendered = true;
         //初回のレンダリング処理を行う
         this.$el.empty();
         if($currentActivePhotoCollection) //$currentActivePhotoCollectionがすでに存在していれば、隠しおく
         {
            $currentActivePhotoCollection.hide();
         }
         $currentActivePhotoCollection = this.$el;
         var _this = this;
         // this.$el.empty();
         this.collection.each(function(photo){
            var photoView;
            if(!_this.tempType){
               photoView = new mvc.PhotoView({model:photo},{ deleteBtn:$('#delete-photo') });
            }else{
               photoView = new mvc.PhotoView({model:photo},{ deleteBtn:$('#delete-photo-right') });
            }


            _this.$el.append(photoView.render().el);
         });
         // $.app.properties.photosControlPanel.after(this.$el);
         this.appendTo.append(this.$el);
         //UIが正常に働くように、プロパティを更新
         $.app.properties.photos = this.$el.find('.photo');
         $.app.properties.photosPanel.trigger('resize');
         return this;
      },
      showPhotos : function()
      {
         //現在のをhide、クリックされたのをshow、現在の値を更新
         $currentActivePhotoCollection.hide();
         $currentActivePhotoCollection = this.$el;
         $currentActivePhotoCollection.show();
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
         if(e.target.className=='filename') { return this;}
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
            $firstElem = $(".selectedElem"), //ctrlを押しながらクリックした要素
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
               beSelected = $firstElem.prevUntil($photo);
            }

            $(".selectedElem",$container).removeClass("selectedElem");
            beSelected = beSelected.add($photo).add($firstElem);
            beSelected.addClass("selectedElem");
            $photo.removeClass("activing");

         }
         else
         {
            var
            cid = $(e.target).data('cid'),
            model = this.collection.getByCid(cid),
            imgUrl = model.get('imgUrl');
            if(!this.tempType){ $.app.properties.previewImg.load()[0].src=imgUrl; }
            // console.log( imgUrl );
            $(".selectedElem",$container).removeClass("selectedElem");
            $photo.addClass("selectedElem");
         }

      },
      /* ........................... dnd ...........................*/
      onDropAlbum: function($dndElem, $selectedElem)
      {
         $dndElem.removeClass("dragOver");

         // ドロップした先のalbumのmodel、要するに移動先toである
         var albumModel = mvc.AlbumsView_instance.collection.getByCid($dndElem.data('cid'));
         // console.log( albumModel );
         if($dndElem.hasClass("active")) //現在アクティブ中のフォルダに落とした場合、falseを返し、何もしない
         {
            return false;
         }

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
      },
      onDragOverUploadArea: function($selectedElem)
      {

      },
      onDropPhotosArea: function($selectedElem)
      {
         console.log( $selectedElem[0],$selectedElem[1] );
      },
      onDragOverPhotosArea: function($selectedElem)
      {

      },
      move : function( $selectedElem, targetAlbum )
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
            model    = this.collection.getByCid(cid);
            photoModels[index] = model;
            photoIds[index] = model.id;
         },this);

         data = JSON.stringify({ targetAlbum:targetAlbum.id, photos:photoIds });
         options =
         {
            data : data,
//            url : '/DatAlbumPhotoRelations/'+_this.albumID
            url : 'http://localhost:81/Phorest/datalbumphotorelations/'+_this.albumID
         };
         Backbone.sync('update',null,options)
         .fail(function(){
            console.log( 'faid roll back database' );
         });

         //サーバーの応答を待たずに、とりあえずアルバムを移動する

         _this.collection.remove(photoModels);
         targetAlbum.PhotoCollectionView.collection.add(photoModels);
         targetAlbum.PhotoCollectionView.$el.append($selectedElem);

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

   //イベントに特化したView
   mvc.PhotoCollectionEventHandlerView=Backbone.View.extend({
      el : $.app.properties.photos,

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
         this.PhotoCollectionView = new mvc.PhotoCollectionView({collection:photoCollection,albumID:this.model.id});
         this.model.PhotoCollectionView = this.PhotoCollectionView;
         _.bindAll(this,'changeName','unFocus');
         $albumNameInput.on('keypress',this.unFocus);
         $albumNameInput.on('blur',this.changeName);
         //今後利用できるよう、cidをdataにセットしておく
         this.$el.data({cid:this.model.cid});
      },
      render : function()
      {
         var
         html,
         json = this.model.toJSON();

         json.status = json.status ? "公開" : "非公開";

         html = this.template(json);

         this.$el.html(html);

         //アルバムのサムネールを設定する & 縦横比に合わせてサイズを最適化
         var
         $coverImg = this.$el.find('.coverImg');
         thumUrl = this.model.get('photos')[0].thumUrl;
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
            var albumName = this.model.get('albumName');

            $albumNameInput.val(albumName); //アルバムの名前の表示を更新
            // console.log( this.model.get('albumName') );
            //$('#delete-photo').off(); //PhotoViewを生成時にイベントが累加されないように、予めイベントたちを全て削除しておく
            this.showPhotos();

            mvc.router.navigate('album/'+albumName);
            $title.text('Phorest - '+albumName);
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
            this.model.set({ albumName: newVal});
            $('.album-name',$albumEl).text(newVal);
         }
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
         firstAlbumView, //ループ後、最初のアルバムviewになる
         albumEls = $("<div>"); //fragment element
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
            if(!album.get('tempAlbum')) //臨時的な写真を格納するアルバムじゃなければ
            {
               var
               albumView = new mvc.AlbumView({model:album}),
               albumEl = albumView.render().el;

               if(index==0){ firstAlbumView = albumView; } //最初のアルバムを決める
               albumEls.append(albumEl);
            }
            else
            {  //temp album
               var photoCollection = new mvc.PhotoCollection(album.get('photos'));
               $.app.properties.PhotoCollectionView_right = new mvc.PhotoCollectionView({collection:photoCollection},{tempType:true,appendTo:$('#uploadedPhotos .mCSB_container')});
               $.app.properties.PhotoCollectionView_right.render().$el.show();
               $.app.properties.photos_right = $("#uploadAreaContainer .photoCollection .photo");
               $('#uploadArea').hide();
            }

         });

         this.$el.append( albumEls.children() );
         //最初のアルバム内の写真を表示する
         firstAlbumView.showPhotos().$el.addClass('active');
         $.app.properties.PhotoCollectionView_right.$el.show();
      }
   });

});