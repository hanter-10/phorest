<?php

App::uses('Component', 'Controller');

class ConvertComponent extends Component {

	public function doConvertObjectToArray( $requestObject = null, $model = '', $optionData = null) {

		$arrayData = array();
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
}