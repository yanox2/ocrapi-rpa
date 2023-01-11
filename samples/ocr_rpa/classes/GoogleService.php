<?php
/**
 * Google Cloud Vision APIサービス
 */

class GoogleService extends BaseService{

	// APIのURL
	private const APIURL = 'https://vision.googleapis.com/v1/images:annotate';

	// @Override - BaseService
	protected function api(string $imgPath): ?string{
		// POST Body
		$data = file_get_contents($imgPath);
		$req = array('requests'=>array(array(
			'image'=>array(
				'content'=>base64_encode($data),
			),
			'features'=>array(
				array(
					'type'=>'TEXT_DETECTION',
					'maxResults'=>10,
				),
			),
			'imageContext'=>array(
				'languageHints'=>array('ja'),
			)
		)));
		$json = json_encode($req);

		// リクエスト
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => self::APIURL.'?key='.OCR_GOOGLE_APIKEY,
				CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,
				CURLOPT_HTTPHEADER=>array('Content-Type: application/json'),
				CURLOPT_POST=>true,
				CURLOPT_POSTFIELDS=>$json,
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
		$anno = $data['responses'][0]['textAnnotations'];
		if(!$anno) return;

		// 全文
		$this->sAllTexts = $anno[0]['description'];

		// 単語
		$this->aWords = array();
		$len = count($anno);
		for($i=1; $i<$len; $i++){
			$this->aWords[] = $anno[$i]['description'];
		}

		// 要素（段落）
		$this->aElements = array();
		$blocks = $data['responses'][0]['fullTextAnnotation']['pages'][0]['blocks'];
		if(!$blocks) return;
		foreach($blocks as $block){
			if(!array_key_exists('paragraphs', $block)) continue;
			$paragraphs = $block['paragraphs'];
			foreach($paragraphs as $paragraph){
				if(!array_key_exists('words', $paragraph)) continue;
				$words = $paragraph['words'];
				$text = '';
				foreach($words as $word){
					if(!array_key_exists('symbols', $word)) continue;
					$symbols = $word['symbols'];
					foreach($symbols as $symbol){
						$text .= $symbol['text'];
					}
				}
				$this->aElements[] = $text;
			}
		}
	}

	// @Override - BaseService
	protected function getWordsWithCoords(string $result): ?array{
		$data = json_decode($result, true);
		$blocks = $data['responses'][0]['fullTextAnnotation']['pages'][0]['blocks'];
		if(!$blocks) return null;

		$wordcoords = array();
		foreach($blocks as $block){
			if(!array_key_exists('paragraphs', $block)) continue;
			$paragraphs = $block['paragraphs'];
			foreach($paragraphs as $paragraph){
				if(!array_key_exists('words', $paragraph)) continue;
				$words = $paragraph['words'];
				foreach($words as $word){
					$coords = $this->_getCoordinates($word);
					if(!array_key_exists('symbols', $word)) continue;
					$symbols = $word['symbols'];
					$text = '';
					foreach($symbols as $symbol){
						$scds = $this->_getCoordinates($symbol);
						if(($scds['sx'] < $coords['sx'])||($scds['sy'] < $coords['sy'])
							||($scds['mx'] > $coords['mx'])||($scds['my'] > $coords['my'])){
							//print '範囲外？'.$coords['sx'].' '.$coords['sy'].' '.$coords['mx'].' '.$coords['my']."<br>\n";
						}
						$text .= $symbol['text'];
					}
					$wordcoords[] = new WordCoordinates($coords, $text);
				}
			}
		}
		return $wordcoords;
	}

	/**
	 * boundingBoxの四隅の座標を取得する。
	 *
	 * @param array $items wordsやsymbolsなどの要素
	 * @return array boundingBoxの四隅の座標 ["sx"=>左上X, "sy"=>左上Y, "mx"=>右下X, "my"=>右下Y];
	 */
	private function _getCoordinates($items): array{
		$sx=10000;$sy=10000;$mx=0;$my=0;
		if(\PR\InKey($items,array('boundingBox', 'vertices'))){
			$vertices = $items['boundingBox']['vertices'];
			foreach($vertices as $vertice){
				if($vertice['x'] < $sx) $sx = $vertice['x'];
				if($vertice['x'] > $mx) $mx = $vertice['x'];
				if($vertice['y'] < $sy) $sy = $vertice['y'];
				if($vertice['y'] > $my) $my = $vertice['y'];
			}
		}
		$coords = array('sx'=>$sx, 'sy'=>$sy, 'mx'=>$mx, 'my'=>$my);
		return $coords;
	}
}
?>