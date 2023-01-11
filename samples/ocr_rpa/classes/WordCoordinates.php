<?php
/**
 * 単語座標エンティティ
 */

class WordCoordinates{
	public $sx; // 左上X座標
	public $sy; // 左上Y座標
	public $mx; // 右下X座標
	public $my; // 右下Y座標
	public $text; // 単語

	public function __construct(array $coords, string $text=null){
		$this->sx = $coords['sx'];
		$this->sy = $coords['sy'];
		$this->mx = $coords['mx'];
		$this->my = $coords['my'];
		$this->text = $text;
	}

	public function toString(): string{
		$str = $this->sx.','.$this->sy.'<br>'.$this->mx.','.$this->my;
		return $str;
	}
}
?>