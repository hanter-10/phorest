$(document).ready(function(){
   
   $.utilities = 
   {
      greaterThanLion : function()
      {
         var greaterThanLion = false;
         try
         {
            greaterThanLion = navigator.userAgent.match(/Mac OS X (10_\d)/)[1].replace("_","")>=107;
         }catch(e){}
         return greaterThanLion;
      },

      CSSSupports : function(cssProp)
      {
         var
         div = document.createElement('div'),  
         vendors = 'Khtml Ms O Moz Webkit'.split(' '),  
         len = vendors.length;
         if ( cssProp in div.style ) return true;

         cssProp = cssProp.replace(/^[a-z]/, function(val) {  
            return val.toUpperCase();  
         });  
         while(len--) {  
            if ( vendors[len] + cssProp in div.style ) {  
               return true;  
            }  
         }  
         return false;
      }
   };


   $.app = {};
   $.app.Backbone = {};
   $.app.properties = 
   {
      headerHeight : $("#header").outerHeight(true),
      albumControlBarHeight : $("#album-control-bar").outerHeight(true),
      albums : $("#albums"),
      photoes : $("#photoes"),
      photo : $("#photoes .photo"),
      previewImg : $("#preview-img"),
      imgContainer : $("#imgContainer"),
      previewImgSize : {width:0,height:0},
      winWidth : $("window").width(),
      albumsPanelWidth : $("#albums-panel").outerWidth(),
      albumEdge : $("#albums-panel").outerWidth(),
      photoesEdge : $("#albums-panel").outerWidth() + $("#photoes-panel").outerWidth(),
      photoesControlPanel : $("#photoes-control-panel"),
      albumNameInput : $('#album-name-input')
   };


   $.app.methods =
   {
   	init : function()
   	{
   		var _this = this, $albums = $.app.properties.albums, $photoes = $.app.properties.photoes, $previewPanel = $("#preview-panel"), $imgContainer = $.app.properties.imgContainer;
         $.app.properties.previewImg.load(function(){
            $.app.properties.previewImgSize.width = this.width;
            $.app.properties.previewImgSize.height = this.height;
            _this.imgAdaptation.adapt($imgContainer);
            $.app.properties.previewImg.show();
         });
         
         if(!$.utilities.greaterThanLion()) //lion以上のバージョンはデフォルトのスクロールバーで大丈夫
         {
            $albums.mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true }});
            $photoes.mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true }});
         }
         
         if(!$.utilities.CSSSupports("box-shadow")) $('body').addClass("no-box-shadow")
         // $( "#photoes .photo" ).draggable();
   		this.resizable();
         this.liquid();
         this.clickable();
         this.selectable.init( $("#photoes .photo"), $.app.properties.photoes );
         this.dnd.enable();
         this.fileDrop();


   	},

   	liquid : function()
   	{
   		var
         _this = this,
   		headerHeight =  $.app.properties.headerHeight,
   		albumControlBarHeight = $.app.properties.albumControlBarHeight,
   		photoesControlPanel = $("#photoes-control-panel").outerHeight(true),
         captionHeight = $("#caption").outerHeight(),
   		winHeight, winWidth,
   		$albums = $.app.properties.albums, $photoes = $.app.properties.photoes, $previewPanel = $("#preview-panel"), $photoesPanel = $("#photoes-panel"), $imgContainer = $.app.properties.imgContainer;


   		function resize()
   		{
   			winHeight = $(window).height();
   			winWidth = $(window).width();
            $.app.properties.winWidth = winWidth;

   			$albums.height( winHeight-headerHeight-albumControlBarHeight-1 );
   			$photoes.outerHeight( winHeight-headerHeight-photoesControlPanel-1 );
   			$previewPanel.outerHeight( winHeight-headerHeight-1 ).outerWidth( winWidth - $.app.properties.albumsPanelWidth - $photoesPanel.outerWidth());
            $imgContainer.outerHeight( winHeight-headerHeight-captionHeight-1 );
            _this.imgAdaptation.adapt($imgContainer);

   		}


   		$(window).resize(resize).resize();

   	},

      layout :
      {
         // margin = 24
         photoWidth : 150 + 24,
         photoContainerWidth : $.app.properties.photoes.width(),
         howManyPerRow : function()
         {
            return Math.floor(this.photoContainerWidth / this.photoWidth);
         }
      },

      imgAdaptation : 
      {
         adapt : function ($container)
         {
            var
            containerWidth = $container.width(),
            containerHeight = $container.height(),
            HV = this.getHV($.app.properties.previewImgSize.width, $.app.properties.previewImgSize.height),
            fitWhichDir = this.fitWhichDir({width:containerWidth, height:containerHeight}, $.app.properties.previewImgSize);

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

         getHV : function (w,h) 
         {
            var direction = "H";
            if(w/h==1)
            {
               return "square";
            }

            if(w/h>1)
            {
               return "H";
            }
            else
            {
               return "V";
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
      },

      resizable : function()
      {
         var
         $photoesPanel = $("#photoes-panel"),
         $photo = $.app.properties.photo,
         $previewPanel = $("#preview-panel"),
         $previewImg = $("#preview-img"),
         _this = this;


         $photoesPanel.resizable({ minWidth: 375 , axis: "x" , handles: "e" });
         $photoesPanel.bind( "resize", function(event, ui){

            var 
            newWidth = $.app.properties.photoes.width(),
            newWidth_viewPanel = $.app.properties.winWidth - $.app.properties.albumsPanelWidth - (newWidth+24);
            photoWidth = _this.layout.photoWidth-24;
            
            _this.layout.photoContainerWidth = newWidth;

            var 
            perRowCount = _this.layout.howManyPerRow(),
            new_margin = Math.floor( (  (newWidth) -(photoWidth * perRowCount)  ) / (perRowCount*2) ) ;

            // console.log(perRowCount)
            

            $photo.css("margin","0 "+new_margin+"px");
            $previewPanel.width(newWidth_viewPanel);
            // console.log(new_margin)

            
         });
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
            $.app.properties.photoes.html('');
            $.app.properties.albums.mCustomScrollbar("update");
         });

      },

      selectable : 
      {
         containerElem : undefined,
         init : function ($elem, $containerElem) 
         {
            var _this=this;

            this.containerElem = $containerElem;
            $elem.click( function(event)
            {
               if(event.ctrlKey || event.metaKey)
               {
                  _this.setElem(this, "ctrl");
               }
               else if(event.shiftKey)
               {
                  _this.setElem(this, "shift");
               }
               else
               {
                  _this.setElem(this, "null");
                  console.log("x")
               }
            } );
         },

         setElem : function (elem, ctrlOrShift) 
         {
            var $elem = $(elem);

            if(ctrlOrShift=="ctrl") //ctrl
            {
               if($elem.hasClass("selectedElem"))
               {
                  $elem.removeClass("selectedElem");
               }
               else
               {
                  $elem.addClass("selectedElem");
               }
            }
            else if(ctrlOrShift=="shift") //shift
            {
               var
               $firstElem = $(".selectedElem"),
               selDirection = "down",
               beSelected;

               $elem.addClass("activing"); //add class to the clicked element for researching the direction of selection
               if( $firstElem.prevAll(".activing").length )  selDirection = "up";

               if(selDirection == "down")
               {
                  beSelected = $firstElem.nextUntil(elem);
               }
               else
               {
                  beSelected = $firstElem.prevUntil(elem);
               }

               $(".selectedElem",this.containerElem).removeClass("selectedElem");
               beSelected = beSelected.add($elem).add($firstElem);
               beSelected.addClass("selectedElem");
               $(elem).removeClass("activing"); 

            }
            else
            {
               $(".selectedElem",this.containerElem).removeClass("selectedElem");
               $elem.addClass("selectedElem");
            }
         }
   
      },

      dnd : 
      {
         selectedElem : undefined,
         position : undefined,
         firstMove : true,
         onlyOne : false,
         countIcon : $('#countIcon'),
         countIconContainer : $('#countIconContainer'),
         albumHeight : $("#albums .album").outerHeight(true),
         lastDragOverElem : $("#albums>.active"),
         enable : function()
         {
            var _this = this;
            this.init( $("#photoes .photo img"), [{dropArea:"album", dropFn:function(e){
               //何番目のアルバムにドロップしているかを割り出す
               var dndElem = _this.getNthElem(e);
               dndElem.removeClass("dragOver");
               console.log(dndElem);
               if(dndElem.hasClass("active")) //現在アクティブ中のフォルダに落とした場合、falseを返し、何もしない
               {
                  return false;
               }
               return true;
            },dragOverFn:function(e){
               //何番目のアルバムにドロップしているかを割り出す
               var dndElem = _this.getNthElem(e);
               _this.lastDragOverElem.removeClass("dragOver");
               _this.lastDragOverElem = dndElem.addClass("dragOver");
               //console.log(dndElem);
            }}] );
         },
         update : function()
         {
            this.enable();
         },
         init:function(elem,dropInfo)  //dropInfo -> [ { dropArea:".demo", dropFn:function(){} } ,{...} ]
         {
          $(elem).mousedown(  {dropInfo:dropInfo, _this:this},this.mousedown  );

         },
         mousedown:function(e)
         {
            var 
            _this = e.data._this,
            dropInfo = e.data.dropInfo;

            //mousemoveのイベントをセットする
            $(document).mousemove({ _this:_this , dropInfo:dropInfo },_this.mousemove);
            $(document).mouseup({ _this:_this, dropInfo:dropInfo },_this.mouseup);

            //現在選択されている写真のセットを更新
            var $photo = $(this).parents('.photo');
            if($photo.hasClass("selectedElem"))
            {
               _this.selectedElem = $("#photoes .selectedElem");
               _this.onlyOne = false;
            }
            else
            {
               _this.onlyOne = true;
               _this.selectedElem = $photo;
            }

            //現在選択されている写真の数を設定する  &  選択iconを出す
            _this.countIcon.text(_this.selectedElem.length);

            //カーソルの現在地を更新
            _this.areaDetecting(e.pageX,e.pageY);

         },
         mousemove:function(e)
         {
            var 
            _this = e.data._this,
            dropInfo = e.data.dropInfo;

            if(_this.firstMove)
            {
               _this.countIconContainer.show();
               if(_this.onlyOne)
               {
                  $("#photoes .selectedElem").removeClass("selectedElem");
                  _this.selectedElem.addClass("selectedElem");
               }
            }
            else
            {
               _this.firstMove=false;
            }
            //area detecting
            _this.areaDetecting(e.pageX,e.pageY);
            
            //track counticon
            _this.countIconContainer.css({left:e.pageX-35, top:e.pageY-35 });

            //移動時の関数を実行する
            for(var len=dropInfo.length,i=0; i<len; i++)
            {
               var dropArea = dropInfo[i].dropArea,  dragOverFn = dropInfo[i].dragOverFn;
               if(dropArea==_this.position)
               {
                  dragOverFn(e);
               }
               
            }
            //console.log(_this.position)
         },
         mouseup:function(e)
         {
            var 
            droped = false,
            dropInfo = e.data.dropInfo, // array
            _this = e.data._this;


            $(document).unbind("mousemove",e.data._this.mousemove);
            $(this).unbind(e);

            //ドロップ時の関数を実行する
            for(var len=dropInfo.length,i=0; i<len; i++)
            {
               var dropArea = dropInfo[i].dropArea,  dropFn = dropInfo[i].dropFn;
               if(dropArea==_this.position)
               {
                  //dropFnの戻り値は true or false
                  droped = dropFn(e);
               }
               
            }
            if(droped)
            {
               _this.countIconContainer.effect("drop",{direction:"up"});
               _this.selectedElem.fadeOut();
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
            photoesEdge = $.app.properties.photoesEdge;

            if(pageX<albumEdge) // in album area
            {
               this.position='album';
               
            }
            else if(pageX>albumEdge && pageX<photoesEdge)
            {
               this.position='photoesArea';
            }
         },
         getNthElem : function(event)
         {
            //何番目のアルバムかを割り出す
            var 
            y = event.pageY - $.app.properties.headerHeight + $.app.properties.albums.scrollTop(),
            albumHeight = this.albumHeight,
            nth = Math.ceil(y/albumHeight),
            nthElem = $("#albums .album").eq(nth-1);
            return nthElem;
         }

      },

      fileDrop: function()
      {
      $('#uploadAreaContainer').filedrop({
          fallback_id: 'photoFiles',   // an identifier of a standard file input element
          url: 'upload.php',              // upload handler, handles each file separately
          paramname: 'userfile',          // POST parameter name used on serverside to reference file
          withCredentials: true,          // make a cross-origin request with cookies
          data: {
              param1: 'value1',           // send POST variables
              param2: function(){
                  return calculated_data; // calculate data at time of upload
              },
          },
          headers: {          // Send additional request headers
              'header': 'value'
          },
          error: function(err, file) {
              switch(err) {
                  case 'BrowserNotSupported':
                      alert('browser does not support html5 drag and drop')
                      break;
                  case 'TooManyFiles':
                      // user uploaded more than 'maxfiles'
                      break;
                  case 'FileTooLarge':
                      // program encountered a file whose size is greater than 'maxfilesize'
                      // FileTooLarge also has access to the file which was too large
                      // use file.name to reference the filename of the culprit file
                      break;
                  case 'FileTypeNotAllowed':
                      // The file type is not in the specified list 'allowedfiletypes'
                  default:
                      break;
              }
          },
          allowedfiletypes: ['image/jpeg','image/png','image/gif'],   // filetypes allowed by Content-Type.  Empty array means no restrictions
          maxfiles: 25,
          maxfilesize: 20,    // max file size in MBs
          dragEnter: function() {
             
          },
          dragOver: function(e) {
              // user dragging files over #dropzone
              $("#uploadArea").addClass("active");
          },
          dragLeave: function() {
              // user dragging files out of #dropzone
              $("#uploadArea").removeClass("active");
          },
          docOver: function() {
              // user dragging files anywhere inside the browser document window
          },
          docLeave: function() {
              // user dragging files out of the browser document window
          },
          drop: function(e) {
              // user drops file
              $("#uploadArea").removeClass("active").hide();
              var
              files = e.dataTransfer.files,
              // oFReader = new FileReader(),
              rFilter = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

              if (files.length === 0) { return; }
              
              for(var i=0,length=files.length; i<length; i++)
              {
               var oFReader = new FileReader(); oFReader.onload = function(e) { $("#uploadAreaContainer").append("<img height='113' draggable='false' src="+e.target.result+" />"); };
               var oFile =files[i];
               if (!rFilter.test(oFile.type)) { alert("You must select a valid image file!"); return; }
               oFReader.readAsDataURL(oFile);
              }
          },
          uploadStarted: function(i, file, len){
              // a file began uploading
              // i = index => 0, 1, 2, 3, 4 etc
              // file is the actual file of the index
              // len = total files user dropped
          },
          uploadFinished: function(i, file, response, time) {
              // response is the data you got back from server in JSON format.
          },
          progressUpdated: function(i, file, progress) {
              // this function is used for large files and updates intermittently
              // progress is the integer value of file being uploaded percentage to completion
              console.log(i,progress)
          },
          globalProgressUpdated: function(progress) {
              // progress for all the files uploaded on the current instance (percentage)
              // ex: $('#progress div').width(progress+"%");
          },
          speedUpdated: function(i, file, speed) {
              // speed in kb/s
          },
          rename: function(name) {
              // name in string format
              // must return alternate name as string
          },
          beforeEach: function(file) {
              // file is a file object
              // return false to cancel upload
          },
          beforeSend: function(file, i, done) {
              // file is a file object
              // i is the file index
              // call done() to start the upload
          },
          afterAll: function() {
              // runs after all files have been uploaded or otherwise dealt with
          }
      });
      }


   };

   $.app.methods.init();




   //-----------------------------------
   $.mouseIsOutside = function(event) 
   {
       var r;
       var c;
       
       try {
           c = event.currentTarget;
           r = event.relatedTarget;
           
           // DOM3 Core: r がウィンドウ外にあるか、または c の内部にない
           return ! r ||
                  ! r.isSameNode (c) &&
                  0 === (r.compareDocumentPosition (c) & Node.DOCUMENT_POSITION_CONTAINS);
       }
       catch (err1) {
           try {
               c = this;
               r = event.toElement;
               
               // MSHTML: r がウィンドウ外にあるか、または c の内部にない
               return ! r ||
                      c !== r &&
                      ! c.contains (r);
           }
           catch (err2) {
               c = event.currentTarget;
               r = event.relatedTarget;
               
               // DOM1: r が c の内部にない
               while ((r = r.parentNode)) if (r === c) break;
               return ! r;
           }
       }
   }

   //-----------------------------------
   
});