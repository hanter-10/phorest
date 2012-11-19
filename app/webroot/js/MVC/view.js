$(function(){

   var 
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
         _.bindAll(this,'deletePhoto');
         $('#delete-photo').on('click',this.deletePhoto);
      },
      render : function()
      {
         var html = this.template(this.model.toJSON());
         this.$el.html(html);
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
      id : 'photoes',
      events : 
      {
         'keypress .filename' : 'unFocus',
         'click .photo' : 'selectable'
      },
      initialize : function()
      {
         this.collection.on('reset',this.render,this);
         //this.$el.on('keypress','.filename',this.unFocus); //通常のjQueryのonを利用している
      },
      render : function()
      {
         var _this = this;
         this.$el.empty();
         this.collection.each(function(photo){
            var photoView = new mvc.PhotoView({model:photo});
            _this.$el.append(photoView.render().el);
         });
         $('#photoes').detach(); // remove but keep events 
         $.app.properties.photoesControlPanel.after(this.$el);
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
            $(".selectedElem",$container).removeClass("selectedElem");
            $photo.addClass("selectedElem");
         }

      }
   });

   //イベントに特化したView
   mvc.PhotoCollectionEventHandlerView=Backbone.View.extend({
      el : $.app.properties.photoes,
      
   });

   //単体のアルバムView
   mvc.AlbumView = Backbone.View.extend({
      className:  'album',
      tagName:    'div',
      template:   _.template($('#temp_album').html()),
      events:
      {
         'click': 'changeAlbum'
      },
      initialize : function()
      {
         var photoCollection = new mvc.PhotoCollection(this.model.get('photoes'));
         this.PhotoCollectionView = new mvc.PhotoCollectionView({collection:photoCollection});

         _.bindAll(this,'changeName','unFocus');
         $albumNameInput.on('keypress',this.unFocus);
         $albumNameInput.on('blur',this.changeName);
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
         var $img = this.$el.find('img');
         $img.on('load',function(){
            aspectRatio = this.width/this.height;
            if(aspectRatio > 1)//横長
            {
               this.height = 102;
            }
            else
            {
               this.width = 102;
            }
         })[0].src = this.model.get('photoes')[0].thumUrl; 
         return this;
      },
      showPhotoes : function()
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
            $('#delete-photo').off(); //PhotoViewを生成時にイベントが累加されないように、予めイベントたちを全て削除しておく
            this.showPhotoes();

            mvc.router.navigate('album/'+albumName);
            $title.text('Phorest - '+albumName);
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
      el : '#albums',
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
               photoes:[
                  {
                     photoName:     '桜の花',
                     relatedAlbum:  '45345ffdex',
                     imgUrl:        'http://phorest/img/434f.jpg',
                     thumbnailUrl:  'http://phorest/img/434f_thum.jpg'
                  }
               ]
            }
            */
            var
            albumView = new mvc.AlbumView({model:album}),
            albumEl = albumView.render().el;

            if(index==0){ firstAlbumView = albumView; }
            albumEls.append(albumEl);
         });

         this.$el.append( albumEls.children() );
         //最初のアルバム内の写真を表示する
         firstAlbumView.showPhotoes().$el.addClass('active');
      },
      changeAlbum : function()
      {
         var
         $photoes = $.app.properties.photoes.empty(),
         photoFragments;

         $photoes.append(photoFragments);
      }
   });

});