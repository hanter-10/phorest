<?php

App::uses('Component', 'Controller');

class FtpComponent extends Component {

	/**
	 * FTPサーバへ接続
	 * @return resource
	 */
	public function FtpLogin() {

		// 接続情報取得
		$ftp_config = Configure::read('ftp.config');

		// 接続を確立する
		$conn_id = ftp_connect($ftp_config['host']);

		// ユーザ名とパスワードでログインする
		$login_result = ftp_login($conn_id, $ftp_config['username'], $ftp_config['userpass']);

		return $conn_id;
	}

	/**
	 * 対象画像のアップロード処理
	 * @param unknown $conn_id
	 * @param unknown $username
	 * @param unknown $filename
	 * @param unknown $localfile
	 * @param string $size_dir
	 * @return boolean
	 */
	public function FtpUpload($conn_id, $username, $filename, $localfile, $size_dir = null)
	{
		// アップロードファイルパス作成
		if (is_null($size_dir)) {
			$remote_file = IMAGE_SERVER_DIR_PASS . $username . '/' . $filename;
		} else {
			$remote_file = IMAGE_SERVER_DIR_PASS . $username . '/' . $size_dir . '/' . $filename;
		}

		// ユーザーディレクトリの確認
		if (!@ftp_chdir($conn_id, IMAGE_SERVER_DIR_PASS . $username)) {
			// 存在しない場合は作成
			ftp_mkdir($conn_id, IMAGE_SERVER_DIR_PASS . $username);
		}

		// 各サイズディレクトリの確認
		if (!@ftp_chdir($conn_id, IMAGE_SERVER_DIR_PASS . $username . '/' . $size_dir)) {
			// 存在しない場合は作成
			ftp_mkdir($conn_id, IMAGE_SERVER_DIR_PASS . $username . '/' . $size_dir);
		}

		// ファイルをアップロードする
		if (!ftp_put($conn_id, $remote_file, $localfile, FTP_BINARY)) {
			// TODO:エラーログとか取ればいい
			return false;
		}

		return true;
	}

	/**
	 * FTPサーバとの接続切断
	 * @param unknown $conn_id
	 */
	public function FtpClose($conn_id) {
		// 接続を閉じる
		ftp_close($conn_id);
	}
}