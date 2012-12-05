<?php

App::uses('Component', 'Controller');

class CheckComponent extends Component {

	public function doCheckArrayKeyToModel( $requestData = array(), $Column = array() ) {

		// 配列のキーを取得
		$keys = array_keys($requestData);

		// 例外カラムチェック
		foreach ($keys as $key) {
			if ( array_search( $key, $modelColumn ) === FALSE ) {
				return false;
			}
		}
		return true;
	}
}