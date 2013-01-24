$(function(){

   var mvc = $.app.Backbone;
   /*
   ******************************* Model *******************************
   */
   mvc.PhotoModel = Backbone.Model.extend({
      initialize : function()
      {
         //this.on('change',this.update,this);
      },
      defaults : 
      {
         photoName:     undefined, //写真名
         relatedAlbum:  undefined, //どのアルバムに入っているか
         imgUrl:        undefined, //元画像のurl
         thumUrl:  undefined  //サムネイルのurl
      },
      update : function()
      {
         var 
         data = JSON.stringify(this.changedAttributes()),
         options = {data:data},
         method = 'update';

         Backbone.sync( method ,this, options);
      }

   });

   /*
   **************************** Collection ****************************
   */
   mvc.PhotoCollection = Backbone.Collection.extend({
      url:     'http://localhost:8888/phorest/datphotos/',
//      url:     'http://localhost:81/Phorest/datphotos/',
//    url:     'http://development/phorest/datphotos/',
//    url:     'http://pk-brs.xsrv.jp/datphotos/',
      model:   mvc.PhotoModel
   });

   mvc.AlbumModel = Backbone.Model.extend({
      initialize : function()
      {
         // this.photoCollection = photoCollection;
         // this.on('change',this.update,this);
      },
      defaults :
      {
         albumName:  undefined,  //アルバム名
         status:     undefined,  //公開状態 public | private
         photoCount: undefined,  //このアルバム内にある写真の枚数
         photos:    []          //このアルバム内の全ての写真
      },
      update : function()
      {
         var 
         data = JSON.stringify(this.changedAttributes()),
         options = {data:data},
         method = 'update';

         Backbone.sync( method ,this, options);
      }
   });

   /*
   ******************************* 宇宙の始まり *******************************
   */
   mvc.AlbumCollection = Backbone.Collection.extend({
      url:     'http://localhost:8888/phorest/datalbums/',
//      url:     'http://localhost:81/Phorest/datalbums/',
//    url:     'http://development/phorest/datalbums/',
//    url:     'http://pk-brs.xsrv.jp/datalbums/',
      model:   mvc.AlbumModel,
      initialize : function()
      {
         
      },
      parse : function(albumArr)
      {
         var parsedJSON = [];
         _.each(albumArr,function(album,index){
            parsedJSON[index] = { photos : album.DatPhoto };
            _.extend( parsedJSON[index],album.DatAlbum );
         });

         console.log(parsedJSON);
         return parsedJSON;
      }

   });

});