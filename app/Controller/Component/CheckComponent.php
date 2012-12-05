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
}