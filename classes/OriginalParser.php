<?php
/**
 * 独自単語パーサ
 */

interface OriginalParser{

	/**
	 * 座標情報から単語座標を再構築する。
	 *
	 * @param array $wordcoords 座標付単語リスト配列 [[WordCoordinates1, WordCoordinates2, ・・・]]
	 * @return array|null 座標付単語リスト配列 [[WordCoordinates1, WordCoordinates2, ・・・]]
	 */
	public function reparse(array $wordcoords): ?array;
}
?>