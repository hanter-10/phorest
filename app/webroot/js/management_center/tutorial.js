(function(){

var 
tuStr = 
'<section id="tu-photos" class="arrow-b">\
	<h1>写真領域</h1>\
	<p>アルバム写真を格納する場所です。</p>\
</section>\
\
<section id="tu-uploadArea" class="arrow-b">\
	<h1>アップロード領域</h1>\
	<p>「アップロード」ボタンをクリックするか、ドロップしてアップロードする場所です。</p>\
</section>\
\
<section id="tu-user-panel" class="arrow-t">\
	<h1>アカウント設定</h1>\
	<p>パスワードやサイト名など、アカウントにまつわる設定はこちらから。</p>\
</section>\
\
<section id="tu-albums" class="arrow-l">\
	<h1>アルバム領域</h1>\
	<p>全てのアルバムの一覧です。写真をドロップして、アルバムからアルバムへの移動が可能。</p>\
</section>\
\
<div id="tu-ok">\
	<a href="" target="_blank">「15秒動画で分かる操作方法」を見る</a>\
	<div class="okbtn">案内画面を閉じる</div>\
</div>',
$mask = $('<div id="mask"> ').hide().appendTo('body');
$mask.append(tuStr);
//step1
setTimeout(function(){ 
	$mask.fadeIn(500 ,function(){  });
} , 500 );


$mask.find('.okbtn').click(function(){
	$mask.fadeOut(800);
});


})();