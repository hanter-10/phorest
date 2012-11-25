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
      photos : {},
      previewImg : $("#preview-img"),
      imgContainer : $("#imgContainer"),
      previewImg : $("#preview-img"),
      albumsPanel:$("#albums-panel"),
      photoesPanel : $("#photoes-panel"),
      albumsPanelWidth : $("#albums-panel").outerWidth(),
      albumEdge : $("#albums-panel").outerWidth(),
      photoesEdge : $("#albums-panel").outerWidth() + $("#photoes-panel").outerWidth(),
      photoCollections : $("#photoCollections"),
      albumNameInput : $('#album-name-input')
   };


   $.app.methods =
   {
    init : function()
    {
      var 
         _this = this, 
         $albums = $.app.properties.albums,
         $photoes = $.app.properties.photoCollections;

         if(!$.utilities.greaterThanLion()) //lion以上のバージョンはデフォルトのスクロールバーで大丈夫
         {
            $albums.mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true }});
            $photoes.mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true }});
         }
         
         if(!$.utilities.CSSSupports("box-shadow")) $('body').addClass("no-box-shadow")
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
            // $.app.properties.photoes.html('');
            $.app.properties.albums.mCustomScrollbar("update");
         });

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