<?php
 /**
 * 行把握単語パーサ
 * 行の把握と同一行内の単語精査
 */

class LinerWordParser implements OriginalParser{

	const ABS_TOLERANCE_X = 10; // X座標差
	const ABS_TOLERANCE_Y = 10; // Y座標差

	private $aSrcWordCoords_ = array(); // 元データ（単語座標データのリスト） [[WordCoordinates1, WordCoordinates2, ・・・]]
	private $aWordCoords_ = array(); // パース後の単語座標データのリスト [[WordCoordinates1, WordCoordinates2, ・・・]]
	private $aLines_ = array(); // 座標付きの単語配列を行でまとめたもの [sy1=>[sx1=>WC, sx2=>WC, ・・・],sy2=>[sx1=>wc, ・・・]]

	// @Implements - OriginalParser
	public function reparse(array $wordcoords): ?array{
		$this->aSrcWordCoords_ = $wordcoords;
		$lines = $this->_lineup($wordcoords);
		$this->aLines_ = $this->_assortWords($lines);

		// 元の形に戻す
		$this->aWordCoords_ = array();
		foreach($this->aLines_ as $words){
			foreach($words as $word){
				$this->aWordCoords_[] = $word;
			}
		}
		return $this->aWordCoords_;
	}

	/**
	 * 単語座標を行でまとめた結果を返す。
	 *
	 * @return array 座標付きの行単語配列を行でまとめたもの [sy1=>[sx1=>WC, sx2=>WC, ・・・],sy2=>[sx1=>wc, ・・・]]
	 */
	public function getLines(){
		return $this->aLines_;
	}

	/**
	 * 座標情報から単語を行ごとにまとめる。
	 *
	 * @param array $vals 座標付単語リスト配列 [[WordCoordinates1, WordCoordinates2, ・・・]]
	 * @return array 座標付きの行単語配列
	 */
	private function _lineup(array $vals): array{
		$lines = array();
		$sy = 0;
		for($i=0; $i<count($vals); $i++){
			$wc = $vals[$i];
			if($wc->chk == 1) continue;
			$sy = $wc->sy;
			$lines[$sy][$wc->sx] = $wc;
			$vals[$i]->chk = 1;
			for($j=$i+1; $j<count($vals); $j++){
				$crrt = $vals[$j];
				if($crrt->chk == 1) continue;
				$abs = abs($wc->sy - $crrt->sy);
				if($abs < self::ABS_TOLERANCE_Y){
					$lines[$sy][$crrt->sx] = $crrt;
					$vals[$j]->chk = 1;
				}
			}
			ksort($lines[$sy]);
		}
		ksort($lines);
		return $lines;
	}

	/**
	 * 座標情報から同じ行の単語をまとめる。
	 *
	 * @param array $lines 座標付きの行単語配列
	 * @return array 座標付きの行単語配列
	 */
	private function _assortWords(array $lines): array{
		$res = array();
		foreach($lines as $y=>$words){
			$res[$y] = array();
			$crrtx = -1;
			$chk = 0;
			foreach($words as $x=>$wc){ // $x = $wc->sx
				if($crrtx == -1){
					$res[$y][$x] = $wc;
					$crrtx = $x;
					$chk = $wc->mx;
					continue;
				}
				$abs = abs($x - $chk);
				if($abs < self::ABS_TOLERANCE_X){
					$res[$y][$crrtx]->text .= $wc->text;
					$chk = $wc->mx;
				}else{
					$res[$y][$x] = $wc;
					$crrtx = $x;
					$chk = $wc->mx;
				}
			}
		}
		return $res;
	}
}
?>