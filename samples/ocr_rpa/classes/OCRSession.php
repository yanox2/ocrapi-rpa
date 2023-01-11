<?php
 /**
 * セションクラス
 */

class OCRSession{

	private $aItems_ = array(); // [itemNo1=>WordCoorinates1, itemNo2=>WordCoordinates2, ・・・]

	public function __construct(){
		session_start();
	}

	public function init(): void{
		$item = array('sx'=>636,'sy'=>1,'mx'=>754,'my'=>27);
		$this->aItems_[1] = new WordCoordinates($item, '2021年05月12日');
		$item = array('sx'=>38,'sy'=>147,'mx'=>321,'my'=>185);
		$this->aItems_[2] = new WordCoordinates($item, '見積もりテスト株式会社御中');
		$item = array('sx'=>680,'sy'=>806,'mx'=>741,'my'=>827);
		$this->aItems_[3] = new WordCoordinates($item, '660,000');
		$this->save();
	}

	public function load(): void{
		$this->aItems_[1] = $_SESSION['ses_item1'];
		$this->aItems_[2] = $_SESSION['ses_item2'];
		$this->aItems_[3] = $_SESSION['ses_item3'];
	}

	public function save(): void{
		$_SESSION['ses_item1'] = $this->aItems_[1];
		$_SESSION['ses_item2'] = $this->aItems_[2];
		$_SESSION['ses_item3'] = $this->aItems_[3];
	}

	public function getItems(): ?array{
		return $this->aItems_;
	}

	public function getItem(int $no): ?WordCoordinates{
		return $this->aItems_[$no];
	}

	public function setItem(int $no, WordCoordinates $item): void{
		$this->aItems_[$no] = $item;
	}
}
?>