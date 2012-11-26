<html>
	<head>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>
			function requestAPI(method, api, data) {
				$.ajax({
					type: method,
					async:false,
					url: "http://" + "<?php echo $_SERVER["HTTP_HOST"]?>" + "/Phorest/" + api,
					data: data,
					processData: false,
					dataType: "json",
					cache: false,
					error: function(XMLHttpRequest, textStatus, errorThrown){
						Alert("通信に失敗しました。\nネットワーク環境を確認して再度お試しください。");
		    		},
					success: function(res){
						alert(res);
					}
				});
				return false;
		    }
		</script>
	</head>
	<body>

	<h2>DatAlbums</h2>
		<a href="javascript: void(0)" onClick="requestAPI('get', 'datalbums', 'user_id : 33');">get:datalbumss</a>
		<a href="javascript: void(0)" onClick="requestAPI('post', 'datalbums', '{ \'albumName\':\'test4\',\'description\':\'test_description\'}');">post:datalbumss</a>
		<a href="javascript: void(0)" onClick="requestAPI('put', 'datalbums', 'user_id=33');">put:datalbumss</a>
		<a href="javascript: void(0)" onClick="requestAPI('delete', 'datalbums', '{ user_id:33 }');">delete:datalbumss</a>
	<h2>DatPhotos</h2>
		<a href="javascript: void(0)" onClick="requestAPI('get', 'DatPhotos', '{ user_id:33 }');">get:datphotos</a>
		<a href="javascript: void(0)" onClick="requestAPI('post', 'DatPhotos', '{ user_id:33 }');">post:datphotos</a>
		<a href="javascript: void(0)" onClick="requestAPI('put', 'DatPhotos', '{ user_id:33 }');">put:datphotos</a>
		<a href="javascript: void(0)" onClick="requestAPI('delete', 'DatPhotos', '{ user_id:33 }');">delete:datphotos</a>
</html>
<?php
