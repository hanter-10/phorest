<?php

App::uses('Component', 'Controller');

class CheckComponent extends Component {

	/**
	 * 配列のキーと対象モデルのカラムのチェック
	 *
	 * @param unknown $requestData
	 * @param unknown $columns
	 * @return boolean
	 */
	public function doCheckArrayKeyToModel( $requestData = array(), $columns = array() ) {

		// 配列のキーを取得
		$keys = array_keys($requestData);

		// 例外カラムチェック
		foreach ($keys as $key) {
			if ( array_search( $key, $columns ) === FALSE ) {
				return false;
			}
		}
		return true;
	}

	public function doCheckUploadAction ( $_file = array()) {

		try
		{
			// 画像送信しようとした場合
			if ($_file['file']['tmp_name'] != '') {

// 				// ファイル種類チェック
// 				if ($_file['file']['type'] != 'image/pjpeg' && $_file['file']['type'] != 'image/jpeg') {
// 					return false;
// 				}
// 				// ファイルサイズチェック
// 				if ($_file['file']['size'] > 2048*1024) {
// 					return false;
// 				}
				// フォームからの送信でない
				if( !is_uploaded_file($_file['file']['tmp_name'])) {
					return false;
				}
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}
}