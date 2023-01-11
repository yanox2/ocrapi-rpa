<?php
 /**
 * Microsoft Computer Vision OCR APIサービス
 */

class MSOCRService extends BaseService{

	// APIのURL
	private const APIURL = 'https://'.OCR_MICROSOFT_CUSTOMDOMAIN.'.cognitiveservices.azure.com/vision/v3.2/ocr';

	// @Override - BaseService
	protected function api(string $imgPath): ?string{
		// POST Body
		$data = file_get_contents($imgPath);

		// リクエスト
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL=>self::APIURL.'?language=ja&detectOrientation=true&model-version=latest',
				CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,
				CURLOPT_HTTPHEADER=>array(
					'Content-Type: application/octet-stream',
					'Ocp-Apim-Subscription-Key: '.OCR_MICROSOFT_APIKEY
		        ),
				CURLOPT_POST=>true,
				CURLOPT_POSTFIELDS=>$data,
				CURLOPT_RETURNTRANSFER=>true,
				CURLOPT_TIMEOUT=>15
			)
		);
		$result = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);

		// APIエラーチェック
		if(($info['http_code'] != 200)&&($info['http_code'] != 202)){
			throw new Exception('API呼び出しでエラーが発生しました。code='.$info['http_code']);
		}
		if(!$result){
			throw new Exception('API呼び出しでエラーが発生しました。結果がありません。');
		}
		$json = json_decode($result, true);
		if(($json)&&(array_key_exists('error', $json))){
			$msg = 'code='.$json['error']['code'].' msg='.$json['error']['message'];
			throw new Exception('API呼び出しでエラーが発生しました。'.$msg);
		}
		return $result;
	}

	// @Override - BaseService
	protected function parse(): void{
		if(!$this->sResult) return;

		$data = json_decode($this->sResult, true);
		$regions = $data['regions'];
		if(!$regions) return;

		$this->sAllTexts = '';
		$this->aElements = array();
		$this->aWords = array();
		foreach($regions as $reg){
			if(!array_key_exists('lines', $reg)) continue;
			$lines = $reg['lines'];
			foreach($lines as $line){
				if(!array_key_exists('words', $line)) continue;
				$words = $line['words'];
				$text = '';
				foreach($words as $word){
					$str = $word['text'];
					if(!$str) continue;
					$this->aWords[] = $str;
					$this->sAllTexts .= $str;
					$text .= $str;
				}
				$this->aElements[] = $text;
			}
		}
	}

	// @Override - BaseService
	protected function getWordsWithCoords(string $result): ?array{
		$data = json_decode($result, true);
		$regions = $data['regions'];
		if(!$regions) return null;

		$wordcoords = array();
		foreach($regions as $region){
			if(!array_key_exists('lines', $region)) continue;
			$lines = $region['lines'];
			foreach($lines as $line){
				if(!array_key_exists('words', $line)) continue;
				$words = $line['words'];
				foreach($words as $word){
					$coords = $this->_getCoordinates($word);
					$wordcoords[] = new WordCoordinates($coords, $word['text']);
				}
			}
		}
		return $wordcoords;
	}

	/**
	 * boundingBoxの四隅の座標を取得する。
	 *
	 * @param array $items wordsなどの要素
	 * @return array boundingBoxの四隅の座標 ["sx"=>左上X, "sy"=>左上Y, "mx"=>右下X, "my"=>右下Y];
	 */
	private function _getCoordinates($items): array{
		$sx=10000;$sy=10000;$mx=0;$my=0;
		if(array_key_exists('boundingBox', $items)){
			$line = $items['boundingBox'];
			$vertices = explode(',', $items['boundingBox']);
			$sx = $vertices[0];
			$mx = $sx + $vertices[2];
			$sy = $vertices[1];
			$my = $sy + $vertices[3];
		}
		$coords = array('sx'=>$sx, 'sy'=>$sy, 'mx'=>$mx, 'my'=>$my);
		return $coords;
	}
}
?>