<?php
/**
 * モデル
 */

class OCRModel{

	private $service_ = null; // OCRサービス
	private $type_ = 0; // OCRサービスタイプ
	private $cache_ = null; // キャッシュ

	/**
	 * コンストラクタ
	 *
	 * @param OCRService $svc OCRサービス
	 * @param int $type キャッシュタイプ
	 */
	public function __construct(OCRService $svc, int $type){
		$this->service_ = $svc;
		$this->type_ = $type;
		$this->cache_ = new OCRCache(OCR_CACHE_PATH);
	}

	/**
	 * 画像のアップロード処理を行い、キャッシュディレクトリに一時保存する。
	 *
	 * @param string $postname 画像アップロードのPOST名
	 * @param int $cacheNo キャッシュする番号（0ならキャッシュしない）
	 * @return string アップロードされた画像のURI
	 */
	public function api(string $postname, int $cacheNo): string{
		$imgPath = $this->cache_->upload($postname);
		$this->service_->callAPI($imgPath);
		$result = $this->service_->getResult();
		if($cacheNo > 0){
			$this->cache_->save($result, $this->type_, $cacheNo);
			$imageURI = $this->cache_->getImageURI($this->type_, $cacheNo);
		}else{
			$imageURI = $this->cache_->getImageURI();
		}
		return $imageURI;
	}

	/**
	 * キャッシュを読み込む。
	 *
	 * @param int $cacheNo 読み込むキャッシュ番号
	 * @return string アップロードされた画像のURI
	 */
	public function cache(int $cacheNo): string{
		$result = $this->cache_->load($this->type_, $cacheNo);
		$this->service_->setResult($result);
		$imageURI = $this->cache_->getImageURI($this->type_, $cacheNo);
		return $imageURI;
	}

	public function getResult(){
		return $this->service_->getResult();
	}

	public function getAllTexts(){
		return $this->service_->getAllTexts();
	}

	public function getElements(){
		return $this->service_->getElements();
	}

	public function getWords(){
		return $this->service_->getWords();
	}

	/**
	 * キャッシュを読み込む。
	 *
	 * @param int $sx 選択範囲の左上X座標
	 * @param int $sy 選択範囲の左上Y座標
	 * @param int $mx 選択範囲の右下X座標
	 * @param int $my 選択範囲の右下Y座標
	 * @param int $cacheNo 読み込むキャッシュ番号
	 * @return array 指定された座標範囲にある単語を文字列連結したテキスト ["text"=>"単語1単語2・・・", "debug"=>"デバッグ文字列"]
	 */
	public function getPointedWords(int $sx, int $sy, int $mx, int $my, int $cacheNo): array{
		$result = $this->cache_->load($this->type_, $cacheNo);
		$this->service_->setResult($result);
		$pwords = $this->service_->getPointedWords($sx, $sy, $mx, $my);
		return $pwords;
	}
}
?>