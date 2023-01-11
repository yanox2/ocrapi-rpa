<?php
 /**
 * キャッシュクラス
 */

class OCRCache{

	// メンバ変数の型宣言はphp 7.4以降

	// 直近にアップロードされた画像のデフォルトのファイル名
	private const DEFAULT_FILENAME_ = 'uploaded.jpg';

	// キャッシュディレクトリ
	private $cacheDir_ = null;

	/**
	 * コンストラクタ
	 *
	 * @param string $path キャッシュデータを保存するディレクトリ
	 */
	public function __construct(string $path){
		$this->cacheDir_ = $path;
	}

	/**
	 * 画像のアップロード処理を行い、キャッシュディレクトリに一時保存する。
	 *
	 * @param string $postname POST名
	 * @return string アップロードされた画像ファイルのパス名
	 * @throws Exception アップロード処理に失敗した場合
	 */
	public function upload(string $postname): string{
		$postFile = $_FILES[$postname]['name'];
		$tmpFile = $_FILES[$postname]['tmp_name'];
		$size = $_FILES[$postname]['size'];
		$error = $_FILES[$postname]['error'];
		if($size == 0) throw new Exception('アップロードに失敗しました。error='.$error);
		if($size > 4096000) throw new Exception('ファイルサイズが大きすぎます。');

		$path = $this->cacheDir_.DS.self::DEFAULT_FILENAME_;
		$rc = move_uploaded_file($tmpFile, $path);
		if($rc === false)
			throw new Exception('アップロードに失敗しました。error=move_uploaded_file');
		return $path;
	}

	/**
	 * 画像ファイルのURIを返す。
	 * キャッシュ番号の指定がない場合は直近にアップロードされた画像ファイルのURIを返す。
	 *
	 * @param int $svcType サービスタイプ
	 * @param int $cacheNo キャッシュ番号
	 * @return string 画像ファイルのURI
	 */
	public function getImageURI(int $svcType=0, int $cacheNo=0): string{
		$uri = $this->cacheDir_.DS;
		if(($svcType)&&($cacheNo)){
			$uri .= $svcType.'_'.$cacheNo.'.jpg';
		}else{
			$uri .= self::DEFAULT_FILENAME_;
		}
		return $uri;
	}

	/**
	 * 画像ファイルのURIを返す。
	 *
	 * @param int $svcType サービスタイプ
	 * @param int $cacheNo キャッシュ番号
	 * @return string 画像ファイルのURI
	 */
	public static function imageURI(int $svcType, int $cacheNo): string{
		return OCR_CACHE_PATH.DS.$svcType.'_'.$cacheNo.'.jpg';
	}

	/**
	 * キャッシュデータを取得する。
	 *
	 * @param int $svcType サービスタイプ
	 * @param int $cacheNo キャッシュ番号
	 * @return string キャッシュデータ
	 * @throws Exception キャッシュデータがない場合
	 */
	public function load(int $svcType, int $cacheNo): string{
		$path = $this->cacheDir_.DS.$svcType.'_'.$cacheNo.'.cache';
		if(!is_readable($path)) throw new Exception('キャッシュがありません。');
		require($path);
		return base64_decode($RESULT_STR);
	}

	/**
	 * キャッシュデータを保存する。
	 *
	 * @param int $data キャッシュするデータ内容
	 * @param int $svcType サービスタイプ
	 * @param int $cacheNo キャッシュ番号
	 * @throws Exception キャッシュファイルの生成に失敗した場合
	 */
	public function save(string $data, int $svcType, int $cacheNo): void{
		// 結果のキャッシュ
		$path = $this->cacheDir_.DS.$svcType.'_'.$cacheNo.'.cache';
		$fp = fopen($path, 'w');
		fwrite($fp, '<?php'."\n");
		fwrite($fp, '$RESULT_STR = \''.base64_encode($data)."';\n");
		fwrite($fp, '?>'."\n");
		fclose($fp);

		// 画像のキャッシュ
		$src = $this->cacheDir_.DS.self::DEFAULT_FILENAME_;
		$dest = $this->cacheDir_.DS.$svcType.'_'.$cacheNo.'.jpg';
		$cmd = 'cp '.$src.' '.$dest;
		$rc = system($cmd);
		if($rc === false) throw new Exception('ファイルのコピーに失敗しました。cmd='.$cmd);
	}
}
?>