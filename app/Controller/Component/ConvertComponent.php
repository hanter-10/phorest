<?php

App::uses('Component', 'Controller');

class ConvertComponent extends Component {

	/**
	 *
	 * @param object $requestObject
	 * @param string $model
	 * @param array $optionData
	 * @return Ambigous <multitype:, unknown>
	 */
	public function doConvertObjectToModelArray( $requestObject = null, $model = null, $optionData = array()) {

		$arrayData = array();
		// オブジェクトデータ→配列データ格納
		foreach ( $requestObject as $key => $requestData) {
			$arrayData[$model][$key] = $requestData;
		}

		// オプションデータ格納
		if ( isset($optionData) ) {

			foreach ( $optionData as $key => $option) {
				$arrayData[$model][$key] = $option;
			}
		}
		return $arrayData;
	}


	public function doConvertArrayKeyToModel( $requestData = array(), $Column = array() ) {

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