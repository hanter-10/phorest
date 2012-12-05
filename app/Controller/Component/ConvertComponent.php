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


	/**
	 * MODEL配列をQuery用にカスタマイズするメソッド
	 *
	 *	[Request]
	 *	  MODEL配列	：$arrrayData[MODEL名]
	 *	  モデル名		：'モデル名'
	 *	  制御カラム配列	：$arrayColumn( 'column1', 'column2', … )
	 *	[Response]
	 *	  array( 'モデル名.カラム名' => "'" . カラム値 . "'", … )
	 *
	 * @param array $requestData
	 * @param string $model
	 * @param array $columns
	 * @return multitype:array
	 */
	public function doConvertArrayKeyToQueryArray( $requestData = array(), $model = null, $columns = array() ) {

		$arrayData = array();
		// 配列のキーを取得
		$keys = array_keys($requestData);

		// 対象フィールドセット
		foreach ($keys as $key) {
			if ( array_search( $key, $columns ) !== FALSE ) {
				$arrayData["{$model}.{$key}"] =  "'" . $requestData["$key"] . "'";
			}
		}
		return $arrayData;
	}
}