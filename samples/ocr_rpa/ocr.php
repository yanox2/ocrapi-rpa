<?php
/**
 * コントローラ
 * 範囲指定された部分の文字列を取得する
 */
require_once('defines.inc');

// POSTパラメタ取得
$svcType = intval($_POST['post_svctype']); // 1:Google 2:Microsoft OCR API 3:Microsoft Read API
$cacheNo = intval($_POST['post_cacheno']); // 保存 or 読込のキャッシュ番号
$sx = $_POST['image_posSX'];
$sy = $_POST['image_posSY'];
$mx = $_POST['image_posMX'];
$my = $_POST['image_posMY'];
$itemno = intval($_POST['post_itemno']);

// OCRサービス振り分け
$parser = new LinerWordParser();
$svc = null; // OCRService
if($svcType == 1){ // Google Cloud Vision API
	$svc = new GoogleService($parser);
}else if($svcType == 2){ // Microsoft Computer Vision OCR API
	$svc = new MSOCRService($parser);
}else if($svcType == 3){ // Microsoft Computer Vision Read API
	$svc = new MSReadService($parser);
}

// 座標取得
$model = new OCRModel($svc, $svcType);
try{
	$pwords = $model->getPointedWords($sx, $sy, $mx, $my, $cacheNo);
	$cwords = $svc->getCoords();

}catch(Exception $e){
	$v_errmsg = $e->getMessage();
}
if($v_errmsg){
	resAjax(-1, $v_errmsg);
	exit();
}

// セション保存
$ses = new OCRSession();
$ses->load();
$item = new WordCoordinates(array('sx'=>$sx, 'sy'=>$sy, 'mx'=>$mx, 'my'=>$my), $pwords['text']);
$ses->setItem($itemno, $item);
$ses->save();

// レスポンス
$results = $pwords['debug'];
$results .= var_export($cwords, true);
$replaces = array(
	'post_Item'.$itemno=>$item->text,
	'Coords'.$itemno=>$item->toString(),
	"image_posSX"=>-1,
	"image_posSY"=>-1,
	"image_posMX"=>-1,
	"image_posMY"=>-1,
	'results'=>$results
);

$res = array();
$res['text'] = $pwords['text'];
$res['dom'] = array();
$res['dom']['replace'] = $replaces;
resAjax(0, null, $res);

/**
 * Ajax通信のレスポンスを返す。
 *
 * @param int $riCode エラーコード
 * @param string $rsMessage メッセージ
 * @param array $raRes レスポンスデータ配列 [key1=>value1, key2=>value2, ・・・]
 * @param array $raHeaders HTMLヘッダ挿入データ [[name1=>value1, name2=>value2, ・・・]]
 */
function resAjax($riCode, $rsMessage, $raRes=null, $raHeaders=null){
	if($raRes == null) $raRes = array();
	$raRes['rcode'] = $riCode;
	$raRes['message'] = $rsMessage;
	$str='';$end='';
	if(!empty($_GET['jcbk'])){
		$str .= $_GET['jcbk'].'(';
		$end = ')';
	}
	$str .= json_encode($raRes,JSON_UNESCAPED_UNICODE).$end;
	if(empty($raHeaders)){
		header('Content-Type: application/json');
	}else{
		foreach($raHeaders as $header){
			header($header);
		}
	}
	print $str;
}
?>