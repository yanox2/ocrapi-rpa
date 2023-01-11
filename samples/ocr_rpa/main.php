<?php
/**
 * コントローラ
 */
require_once('defines.inc');

// Viewへのパラメタ
$v_imageURI = null; // 表示する画像のURI
$v_items = null; // 表示する単語座標
$v_errmsg = null; // 表示するエラーメッセージ

// 初期値
$action = 1; // 1:API 2:Cache
$svcType = 1; // 1:Google 2:Microsoft OCR API 3:Microsoft Read API
$cacheNo = 1; // 保存 or 読込のキャッシュ番号

// 単語座標読み込み
$isPost = ($_SERVER['REQUEST_METHOD'] == 'POST');
$ses = new OCRSession();
if($isPost){
	$ses->load();
}else{
	$ses->init();
}
$v_items = $ses->getItems();

// POSTパラメタ取得
if($isPost){
	$action = intval($_POST['post_action']); // 1:API 2:Cache
	$svcType = intval($_POST['post_svctype']); // 1:Google 2:Microsoft OCR API 3:Microsoft Read API
	$cacheNo = intval($_POST['post_cacheno']); // 保存 or 読込のキャッシュ番号
}
$v_imageURI = OCRCache::imageURI($svcType, $cacheNo);
$v_imageURI .= '?t='.time();

// POST処理

// パラメタチェック
if(!$svcType){
	$v_errmsg = 'サービスを指定してください。';
}
if(($action == 2)&&($cacheNo == 0)){
	$v_errmsg = 'キャッシュ番号を選択してください。';
}
if((!$isPost)||($v_errmsg)){
	include('main.tpl');
	exit();
}

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

// OCR読み込み処理
$model = new OCRModel($svc, $svcType);
try{
	if($action == 1){ // Call API
		$model->api('post_image', $cacheNo);
	}else{ // Cache
		$model->cache($cacheNo);
	}
	for($i=1; $i<4; $i++){
		$item = $ses->getItem($i);
		$pwords = $model->getPointedWords($item->sx, $item->sy, $item->mx, $item->my, $cacheNo);
		$item->text = $pwords['text'];
		$ses->setItem($i, $item);
	}

}catch(Exception $e){
	$v_errmsg = $e->getMessage();
}

// セション保存
$ses->save();

// View
include('main.tpl');

?>