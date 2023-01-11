<?php
 /**
 * Microsoft Computer Vision Read APIサービス
 */

class MSReadService extends BaseService{

	// APIのURL
	private const APIURL = 'https://'.OCR_MICROSOFT_CUSTOMDOMAIN.'.cognitiveservices.azure.com/vision/v3.2/read/analyze';

	// @Override - OCRService
	protected function api(string $imgPath): ?string{
		// POST Body
		$data = file_get_contents($imgPath);

		// リクエスト
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL=>self::APIURL.'?language=ja&model-version=latest',
				CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,
				CURLOPT_HTTPHEADER=>array(
					'Content-Type: application/octet-stream',
					'Ocp-Apim-Subscription-Key: '.OCR_MICROSOFT_APIKEY
		        ),
				CURLOPT_HEADER=>true,
				CURLOPT_POST=>true,
				CURLOPT_POSTFIELDS=>$data,
				CURLOPT_RETURNTRANSFER=>true,
				CURLOPT_TIMEOUT=>15
			)
		);
		$res = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);

		// APIエラーチェック
		if(($info['http_code'] != 200)&&($info['http_code'] != 202)){
			throw new Exception('API呼び出しでエラーが発生しました。code='.$info['http_code']);
		}
		$header = substr($res, 0, $info['header_size']);
		$payload = substr($res, $info['header_size']);
		$json = null;
		if($payload) $json = json_decode($payload, true);
		if(($json)&&(array_key_exists('error', $json))){
			$msg = 'code='.$json['error']['code'].' msg='.$json['error']['message'];
			throw new Exception('API呼び出しでエラーが発生しました。'.$msg);
		}

		// 結果取得URL
		$spos = strpos($header, 'Operation-Location: ') + strlen('Operation-Location: ');
		$epos = strpos($header, "\n", $spos);
		if($spos === false){
			throw new Exception('API呼び出しでエラーが発生しました。Operation-Locationが見つかりません。');
		}
		$location = trim(substr($header, $spos, $epos-$spos));

		// 結果を取得
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL=>$location,
				CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,
				CURLOPT_CUSTOMREQUEST=>'GET',
				CURLOPT_HTTPHEADER=>array(
					'Ocp-Apim-Subscription-Key: '.OCR_MICROSOFT_APIKEY
		        ),
				CURLOPT_RETURNTRANSFER=>true,
				CURLOPT_TIMEOUT=>15
			)
		);

		$result = null;
		$loop = false;
		do{
			$result = curl_exec($curl);
			$info = curl_getinfo($curl);
			if(($info['http_code'] != 200)&&($info['http_code'] != 202)){
				throw new Exception('API呼び出しでエラーが発生しました。code='.$info['http_code']);
			}
			$json = json_decode($result, true);
			if(!array_key_exists('status', $json)){
				throw new Exception('API呼び出しでエラーが発生しました（no status）。');
			}
			if($json['status'] == 'running'){
				$loop = true;
			}else if($json['status'] == 'succeeded'){
				$loop = false;
			}else{
				throw new Exception('API呼び出しでエラーが発生しました（no status）。');
			}
			sleep(3);
		}while($loop);
		curl_close($curl);
		return $result;
	}

	// @Override - BaseService
	protected function parse(): void{
		if(!$this->sResult) return;

		$data = json_decode($this->sResult, true);
		$analy = $data['analyzeResult'];
		if(!$analy) return;
		if(!array_key_exists('readResults', $analy)) return;
		$results = $analy['readResults'];

		$this->sAllTexts = '';
		$this->aElements = array();
		$this->aWords = array();
		foreach($results as $result){
			if(!array_key_exists('lines', $result)) continue;
			$lines = $result['lines'];
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
		$regions = $data['analyzeResult']['readResults'];
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
			$vertices = $items['boundingBox'];
			$sx = $vertices[0];
			$sy = $vertices[1];
			$mx = $vertices[4];
			$my = $vertices[5];
		}
		$coords = array('sx'=>$sx, 'sy'=>$sy, 'mx'=>$mx, 'my'=>$my);
		return $coords;
	}
}
?>