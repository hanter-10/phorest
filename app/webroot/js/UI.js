$(document).ready(function(){
   console.log( 'ui loaded' );
var dnd = (function(){

var dndClass = function( $container,selector )
{
    var properties =
    {
    $selectedElem:      undefined, //現在選択されてる要素の集合(jquery object)
    position:           undefined,
    firstMove:          true,
    isNewSelection:     false,
    $container:         $container,
    selector:           selector,
    }

    _.each(properties,function(value,key){
        this[key]=value;
    },this);
}
//アルバムはインスタンス間で共有するので、クラスに束縛する
dndClass.prototype.lastDragOverElem = $("#albums>.active");
//その他の共通プロパティ
dndClass.prototype.albumHeight           = 193; //$("#albums .album").outerHeight(true);
dndClass.prototype.countIcon             = $('#countIcon');
dndClass.prototype.countIconContainer    = $('#countIconContainer');

//methods

var methods =
{
    enable : function(options)
    {
        /*options=
        [
            {
                dropArea:   'album',
                events:     
                {
                    onDrop:     function(dndElem, $selectedElem){}, //dndElemはドロップされた要素(album)
                    onDragOver: function(dndElem, $selectedElem){}
                }
            },
            {...}
        ]*/
        this.options = options;
        this.$container.on('mousedown', this.selector, {_this:this}, this.mousedown );
    },

    mousedown:function(e)
    {
        e.preventDefault();
        var _this = e.data._this;

        //mousemoveのイベントをセットする
        $(document).mousemove({_this:_this},_this.mousemove);
        $(document).mouseup({_this:_this},_this.mouseup);

        //現在選択されている写真のセットを更新
        var $photo = $(this).parents('.photo');
        if($photo.hasClass("selectedElem"))
        {
            //複数の写真が選択された場合
            _this.isNewSelection = false;
            _this.$selectedElem = $(".selectedElem",_this.$container);
        }
        else
        {
            //一枚の写真が選択された場合
            _this.isNewSelection = true;
            _this.$selectedElem = $photo;
            
        }

        //現在選択されている写真の数を設定する  &  選択iconを出す
        _this.countIcon.text(_this.$selectedElem.length);

        //カーソルの現在地を更新
        _this.areaDetecting(e.pageX,e.pageY);

    },

    mousemove:function(e)
    {
        e.preventDefault();
        var 
        _this = e.data._this,
        options = _this.options;

        if(_this.firstMove)
        {
            _this.countIconContainer.show();
            if(_this.isNewSelection)
            {
               $(".selectedElem",_this.$container).removeClass("selectedElem");
               _this.$selectedElem.addClass("selectedElem");
            }
        }
        else
        {
            _this.firstMove=false;
        }
        //area detecting
        _this.areaDetecting(e.pageX,e.pageY);

        //track count icon
        _this.countIconContainer.css({left:e.pageX-35, top:e.pageY-35 });

        //移動時のイベントハンドラを実行する
        for(var len=options.length,i=0; i<len; i++)
        {
            var 
            dropArea = options[i].dropArea,
            onDragOver = options[i].events.onDragOver;

            if(dropArea==_this.position)
            {
                if(_this.position=='album')
                {
                    var 
                    $dndElem = _this.getNthElem(e), //何番目のアルバムにドロップしているかを割り出す
                    $selectedElem = _this.$selectedElem;

                    onDragOver.call(_this,$dndElem,$selectedElem);
                }
                else //albumじゃないほうは$dndElemは要らない
                {
                    onDragOver.call(_this,$selectedElem);
                }
            }
        }
        //console.log(_this.position)
    },

    mouseup:function(e)
    {
        e.preventDefault();
        var 
        droped = false, //ドロップが成功したかどうかを表すフラグ、成功時と失敗時に合わせて、count iconの消え方を変える
        _this = e.data._this;
        options = _this.options, // array


        $(document).unbind("mousemove",_this.mousemove);
        $(this).unbind(e); //unbind mouseup

        //ドロップ時のイベント・ハンドラを実行する
        for(var len=options.length,i=0; i<len; i++)
        {
            var 
            dropArea = options[i].dropArea,
            onDrop = options[i].events.onDrop;

            if(dropArea==_this.position)
            {
                if( _this.position=='album' )
                {
                    var 
                    $dndElem = _this.getNthElem(e), //何番目のアルバムにドロップしているかを割り出す
                    $selectedElem = _this.$selectedElem;

                    droped = onDrop($dndElem,$selectedElem);
                }
                else //albumじゃないほうは$dndElemは要らない
                {
                    droped = onDrop(_this.$selectedElem);
                }
            }
        }
        //
        if(droped) //ドロップが成功した場合
        {
            _this.countIconContainer.effect("drop",{direction:"up"});
            // _this.selectedElem.fadeOut();
        }
        else
        {
            _this.countIconContainer.hide();
        }

        _this.firstMove=true;
        console.log("up")
    },

    areaDetecting : function(pageX,pageY)
    {
        var 
        albumEdge = $.app.properties.albumEdge,
        photosEdge = $.app.properties.photosEdge;

        if(pageX<albumEdge) // in album area
        {
           this.position='album';
           
        }
        else if(pageX>albumEdge && pageX<photosEdge)
        {
           this.position='photosArea';
        }
        else
        {
            this.position='uploadArea'
        }
    },
    getNthElem : function(event)
    {
        //何番目のアルバムかを割り出す
        var 
        scrollTop = $.app.properties.albums._scrollTop(),
        y = event.pageY - $.app.properties.headerHeight + scrollTop,
        albumHeight = this.albumHeight,
        nth = Math.ceil(y/albumHeight),
        nthElem = $("#albums .album").eq(nth-1);
        return nthElem;
    }
}

_.extend( dndClass.prototype, methods);

return dndClass;

})();


//..........................................
$.app.UI={};
var UI = $.app.UI;


UI.methods = 
{
   init : function()
   {
      this.setLayout.init();
   },
   dndClass : dnd,
   setLayout :
   {
      properties :
      {
         photoWidth : 150 + 24 // margin = 24
      },

      init : function()
      {
         this.AdaptUI(); //startup here
         this.adaptPreviewImg.init();
      },

      AdaptUI : function()
      {
         var
         _this                   = this,
         headerHeight            = $.app.properties.headerHeight,
         albumControlBarHeight   = $.app.properties.albumControlBarHeight,
         photosControlPanel     = $("#photos-control-panel").outerHeight(true),
         captionHeight           = $("#caption").outerHeight(),
         $albums              = $.app.properties.albums,
         $photoCollections    = $.app.properties.photoCollections,
         $previewPanel        = $("#preview-panel"),
         $photosPanel        = $("#photos-panel"),
         $imgContainer        = $.app.properties.imgContainer,
         $previewImg          = $("#preview-img"),
         winHeight, winWidth;//すべてのresizeCallbackで使う共通の変数


         function adaptUIParts() //Window.resize時に各UI部品を最適化
         {
            winHeight = $(window).height();
            winWidth = $(window).width();

            $albums.height( winHeight-headerHeight-albumControlBarHeight-1 );
            $photoCollections.outerHeight( winHeight-headerHeight-photosControlPanel-1 );

            //previewPanel
            $previewPanel
            .outerHeight( winHeight-headerHeight-1 )
            .outerWidth( winWidth - $.app.properties.albumsPanelWidth - $photosPanel.outerWidth());
            
            //imgContainer
            // $imgContainer.outerHeight( winHeight-headerHeight-1 );
            _this.adaptPreviewImg.adapt();

            var maxWidth = winWidth - ($.app.properties.albumsPanelWidth + 414);
            try { $photosPanel.resizable( "option", { maxWidth: maxWidth } ); }catch(e){}
         }

         function resizableUI() //UI.resize時に各UI部品を最適化
         {
            var maxWidth = winWidth - ($.app.properties.albumsPanelWidth + 414);
            //jQuery UIのresizableを使い、パネルの幅をドラッグで変えられるようにする
            $photosPanel.resizable({ minWidth: 375, maxWidth: maxWidth , axis: "x" , handles: "e" });

            //パネルのresize時に写真のアイコンのmarginを揃える (注: windowのresizeではない)
            $photosPanel.on( "resize", function(event, ui){
               var
               $photos = $.app.properties.photos,
               $photos_right = $.app.properties.photos_right,
               newWidth_photoCollections = $.app.properties.photoCollections.width(),
               newWidth_previewPanel = winWidth - $.app.properties.albumsPanelWidth - newWidth_photoCollections - 2; //2 = border
               $previewPanel.width(newWidth_previewPanel);

               var
               newWidth_photoCollections_right = $.app.properties.photoCollections_right.width(),
               photoWidth = _this.properties.photoWidth-24; //24はmargin、marginを除いて計算する必要がある
               

               var 
               perRowCount = _this.howManyPhotosPerRow(newWidth_photoCollections), //marginの長さも含めて、一行に何枚入るか
               perRowCount_right = _this.howManyPhotosPerRow(newWidth_photoCollections_right), //marginの長さも含めて、一行に何枚入るか
               new_margin = Math.floor( (  (newWidth_photoCollections) -(photoWidth * perRowCount)  ) / (perRowCount*2) ) ;
               new_margin_right = Math.floor( (  (newWidth_photoCollections_right) -(photoWidth * perRowCount_right)  ) / (perRowCount_right*2) ) ;

               // console.log(perRowCount)

               $photos.css("margin","0 "+new_margin+"px");
               $photos_right.css("margin","0 "+new_margin_right+"px");
               
               // console.log(new_margin)
               
            });

            $photosPanel.on( "resizestop" , function(){
                $.app.properties.photosEdge = $.app.properties.albumEdge + $("#photos-panel").outerWidth();
                // console.log( $.app.properties.photosEdge );
            });
         }

         $(window).resize(adaptUIParts).resize();
         resizableUI();
      },

      howManyPhotosPerRow : function(photoCollectionsWidth)
      {
         return Math.floor(photoCollectionsWidth / this.properties.photoWidth);
      },

      adaptPreviewImg :
      {
         properties :
         {
            imgContainer : $("#imgContainer"),
            previewImgSize : {width:0,height:0},
            titleHeight : $("#caption").outerHeight()
         },

         init : function()
         {
            var _this = this;

            $.app.properties.previewImg.load(function(){
               _this.properties.previewImgSize.width = this.width;
               _this.properties.previewImgSize.height = this.height;
               _this.adapt();
               _this.properties.imgContainer.show();
            });
         },
         adapt : function()
         {
            var
            containerWidth = this.properties.imgContainer.width(),
            containerHeight = this.properties.imgContainer.height() - this.properties.titleHeight,
            fitWhichDir = this.fitWhichDir({width:containerWidth, height:containerHeight}, this.properties.previewImgSize);

            if(fitWhichDir=="V")
            {
               $.app.properties.previewImg.css("width","");
               $.app.properties.previewImg.height(containerHeight);
            }
            else
            {
               $.app.properties.previewImg.css("height","");
               $.app.properties.previewImg.width(containerWidth);
            }
         },
         fitWhichDir : function (containerWH, imgWH) 
         {
            if( containerWH.width/containerWH.height > imgWH.width/imgWH.height ) //containerのほうが横に長い
            {
               return "V"; //縦に合わせる
            }
            else
            {
               return "H"; //横に合わせる
            }
         }
      }

   }

};



   



   
   

   //-----------------------------------
   
});