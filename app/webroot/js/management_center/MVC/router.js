$(function(){

   //------------------------initialize--------------------------
   $.app.methods.init();
   $.app.UI.methods.init();
   //------------------------------------------------------------

   var
   mvc = $.app.Backbone,
   $title = $('head title');

   //------------------------ Get Ready ------------------------
   var
   //create models
   PhotoModel = new mvc.PhotoModel(),
   PhotoCollection = new mvc.PhotoCollection(),
   AlbumModel = new mvc.AlbumModel(),
   AlbumCollection = new mvc.AlbumCollection();


   //create views
   //view.jsで使用できるよう、どこでもアクセス出来るmvcにつけるておく
   mvc.AlbumsView_instance = new mvc.AlbumsView({collection:AlbumCollection});

   AlbumModel.photoCollection = PhotoCollection;

   AlbumCollection.fetch();


   //-----------------------------------------------------------
   var Router = Backbone.Router.extend({
      routes:
      {
         '':            'home',
         'album/:name':  'loadAlbum'
      },
      home : function()
      {
         $.app.properties.initLocation = "home";

      },
      loadAlbum : function(name)
      {
         // console.log( "loadAlbum"+name );
         name = decodeURI(name);
         $.app.properties.initLocation = name; //アルバムの名前
         /*var $coverBeClicked = $("#albums .album:contains('"+name+"')").find('.cover');
         if($coverBeClicked.length)
         {
            $coverBeClicked.click();
         }
         else
         {
            mvc.router.navigate("/", {replace: true});
         }*/

      }
   });


   // var originalSync = Backbone.sync;

   // Backbone.sync = function(method, model, options)
   // {
   //    if(method == 'PUT')
   //    {
   //       model.changedAttributes()
   //    }
   // }

   mvc.router = new Router();
//   Backbone.history.start({pushState: true, root: "/phorest/"});
//   Backbone.history.start({pushState: true, root: "/Phorest/DatUsers/"});
// Backbone.history.start({pushState: true, root: "/phorest/DatUsers/"});
 Backbone.history.start({pushState: true, root: "/phorest/control-panel/"});
// Backbone.history.start({pushState: true, root: "/DatUsers/"});
// Backbone.history.start({pushState: true, root: "/control-panel/"});

});