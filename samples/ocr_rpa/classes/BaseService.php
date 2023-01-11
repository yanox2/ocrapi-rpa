<?php
/**
 * OCRサービステンプレート
 */

abstract class BaseService implements OCRService{

	// 画像解析結果
	protected $sResult = null; // 画像解析結果のJSONデータ（レスポンス結果）
	protected $sAllTexts = ''; // 画像解析結果から得たすべての文字
	protected $aElements = array();// 画像解析結果を要素ごとに配列にしたもの ["element1", "element2", ・・・]
	protected $aWords = array(); // 画像解析結果を単語ごとに配列にしたもの ["word1", "word2", ・・・]
	protected $aWordCoords = array(); // 単語座標データのリスト [[WordCoordinates1, WordCoordinates2, ・・・]]

	private $oParser_ = null; // 単語精査パーサ（OriginalParser）

	/**
	 * APIを呼び出し、レスポンス結果を返す。
	 *
	 * @param string $imgPath 画像ファイル名（フルパス指定）
	 * @return string|null 画像解析結果
	 * @throws Exception API呼び出しに失敗した場合
	 */
	abstract protected function api(string $imgPath): ?string;

	/**
	 * 画像解析結果を解析し、$sAllTexts、$aElements、$aWordsに変換する。
	 */
	abstract protected function parse(): void;

	/**
	 * 画像解析結果より単語座標を取得する。
	 *
	 * @param string $result 画像解析結果（レスポンス結果）
	 * @return array|null 単語座標のリスト [[WordCoordinates1, WordCoordinates2, ・・・]]
	 */
	abstract protected function getWordsWithCoords(string $result): ?array;

	/**
	 * コンストラクタ。
	 *
	 * @param OriginalParser $parser 単語パーサ。不要であれば設定しない。
	 */
	public function __construct(OriginalParser $parser=null){
		$this->oParser_ = $parser;
	}

	// @Implements - OCRService
	final public function callAPI(string $imgPath): void{
		$this->sResult = $this->api($imgPath);
		$this->parse();
		$this->aWordCoords = $this->getWordsWithCoords($this->sResult);
		if($this->oParser_) $this->aWordCoords = $this->oParser_->reparse($this->aWordCoords);
	}

	// @Implements - OCRService
	final public function setResult(string $result): void{
		$this->sResult = $result;
		$this->parse();
		$this->aWordCoords = $this->getWordsWithCoords($this->sResult);
		if($this->oParser_) $this->aWordCoords = $this->oParser_->reparse($this->aWordCoords);
	}

	// @Implements - OCRService
	final public function getResult(): ?string{
		return $this->sResult;
	}

	// @Implements - OCRService
	final public function getAllTexts(): string{
		return $this->sAllTexts;
	}

	// @Implements - OCRService
	final public function getElements(): array{
		return $this->aElements;
	}

	// @Implements - OCRService
	final public function getWords(): array{
		return $this->aWords;
	}

	// @Implements - OCRService
	final public function getPointedWords(int $sx, int $sy, int $mx, int $my): array{
		$text = '';
		$debugStr = 'pointed: '.$sx.','.$sy.' '.$mx.','.$my.'<br>';
		foreach($this->aWordCoords as $wc){
			if(($sx <= $wc->mx)&&($sy <= $wc->my)&&($mx >= $wc->sx)&&($my >= $wc->sy)){
				$text .= $wc->text;
				$debugStr .= 'words: '.$wc->sx.','.$wc->sy.' '.$wc->mx.','.$wc->my.' '.$wc->text.'<br>';
			}
		}
		$res = array('text'=>$text, 'debug'=>$debugStr);
		return $res;
	}

	final public function getCoords(): array{
		return $this->aWordCoords;
	}
}
?>