<?php
/**
 * OCRサービスインターフェース
 */

interface OCRService{

	/**
	 * テキスト抽出を行う画像ファイルを読み込み、APIを呼び出して結果を取得する。
	 * 何らかの原因で結果を取得できなかった場合は、nullを返すか例外を投げる。
	 *
	 * @param string $imgPath 画像ファイル名（フルパス指定）
	 * @throws Exception API呼び出しに失敗した場合
	 */
	public function callAPI(string $imgPath): void;

	/**
	 * すでに取得済みの画像解析結果を本インスタンスに設定する（キャッシュ読み込み用）。
	 *
	 * @param string $result 画像解析結果（レスポンス結果）
	 */
	public function setResult(string $result): void;

	/**
	 * 設定された画像解析結果を返す。
	 *
	 * @return string|null 画像解析結果
	 */
	public function getResult(): ?string;

	/**
	 * 抽出されたテキスト全文を返す。
	 *
	 * @return string テキスト文字列
	 */
	public function getAllTexts(): string;

	/**
	 * 抽出されたテキストを要素ごとの配列データにして返す。
	 * （Googleでは段落データ、Microsoftでは行データ）
	 *
	 * @return array テキスト要素の配列 ["element1", "element2", ・・・]
	 */
	public function getElements(): array;

	/**
	 * 抽出されたテキストを単語ごとの配列データにして返す。
	 *
	 * @return array 単語の配列 ["word1", "word2", ・・・]
	 */
	public function getWords(): array;

	/**
	 * 指定された座標範囲の単語を抽出する。
	 *
	 * @param int $sx 左上のX座標
	 * @param int $sy 左上のY座標
	 * @param int $mx 右下のX座標
	 * @param int $my 右下のY座標
	 * @return array 指定された座標範囲にある単語を文字列連結したテキスト ["text"=>"単語1単語2・・・", "debug"=>"デバッグ文字列"]
	 */
	public function getPointedWords(int $sx, int $sy, int $mx, int $my): array;
}
?>