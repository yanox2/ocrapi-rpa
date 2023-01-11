<?php
/**
 * ビュー
 */

// $v_imageURI = null; // 表示する画像のURI
// $v_items = array; // 各項目の単語座標
// $v_errmsg = null; // 表示するエラーメッセージ

// $svcType = 0; // サービスタイプ
// $cacheNo = 0; // キャッシュ番号
?>
<!doctype html>
<html lang="ja">
<!-- 文字コード判別 -->
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href="assets/bootstrap.min.css" rel="stylesheet">
<link href="assets/animate.css" rel="stylesheet">
<link href="assets/spinners.css" rel="stylesheet">
<script src="assets/jquery-3.2.1.min.js"></script>
<script src="assets/bootstrap.min.js"></script>
<script src="assets/script_pronto.js"></script>
<script src="assets/ocr.js?t=<?=time();?>"></script>
<style>
#imagePanel{
	position: relative;
	width: 100%;
	height: 500px;
	border: 1px solid silver;
	overflow: scroll;
}
#ocrCanvas{
	position: absolute;
	left: 0;
	top: 0;
	z-index: 20;
}
.btn{
	font-size: 12px;
}
.spacer{
	margin: 10px 0;
}
.inputPanel{
	padding: 10px;
	background-color: lightyellow;
}
.r-in, .c-in{
	line-height: 14px;
}
.prSt_maskAll{
	position: absolute;
	left: 0px;
	top: 0px;
	background: white;
	opacity: 0.0;
	filter: alpha(opacity=0);
	z-index: 0;
}
.prSt_indicator{
	display: none;
	position: absolute;
	text-align: left;
	font-size: 100px;
	color: gray;
}
</style>
<title>ocr_test</title>
</head>
<body>
<div id="prTag_maskAll" class="prSt_maskAll"></div>
<div id="prTag_indicator" class="prSt_indicator">
 <span class="loading dots2"></span><span id="prTag_indicator_msg" style="margin-left:10px;"></span>
</div>

<div class="container">
<div class="row">
<div class="col-md-12 main" style="margin:10px 0;">
<?php
if($v_errmsg){
?>
<div class="alert alert-danger" role="alert"><?=$v_errmsg?></div>
<?php } ?>
<div class="row">

<div class="col-md-7">
<div id="imagePanel" title="明細画像">
 <img id="ocrImage" src="<?=$v_imageURI?>">
 <canvas id="ocrCanvas"></canvas>
</div><!-- 明細画像 -->
<div class="spacer"></div>
<div title="デバッグ">
 <div id="results" class="text-break mb-3 border border-dark" style="height:100px; overflow-y:scroll;"></div>
</div><!-- デバッグ -->
</div><!-- col-md-7 -->

<div class="col-md-5">
<div title="入力" class="inputPanel" style="padding: 0;">
<form role="form">
<table class="table no-margins no-borders align-middle">
<tbody>
<tr>
 <td class="col-md-2">
  <input id="ItemNo1" type="radio" name="ItemNo" value="1" class="form-check-input" checked>
  <label class="form-check-label" for="ItemNo1" style="font-size:12px;">項目1</label>
 </td>
 <td class="col-md-8"><input id="post_Item1" type="text" name="post_Item1" class="form-control" placeholder="項目1" value="<?=$v_items[1]->text?>"></td>
 <td class="col-md-2"><span id="Coords1" style="font-size:9px;"><?=$v_items[1]->toString()?></span></td>
</tr>
<tr>
 <td>
  <input id="ItemNo2" type="radio" name="ItemNo" value="2" class="form-check-input">
  <label class="form-check-label" for="ItemNo2" style="font-size:12px;">項目2</label>
 </td>
 <td><input id="post_Item2" type="text" name="post_Item2" class="form-control" placeholder="項目2" value="<?=$v_items[2]->text?>"></td>
 <td><span id="Coords2" style="font-size:9px;"><?=$v_items[2]->toString()?></span></td>
</tr>
<tr>
 <td>
  <input id="ItemNo3" type="radio" name="ItemNo" value="3" class="form-check-input">
  <label class="form-check-label" for="ItemNo3" style="font-size:12px;">項目3</label>
 </td>
 <td><input id="post_Item3" type="text" name="post_Item3" class="form-control" placeholder="項目3" value="<?=$v_items[3]->text?>"></td>
 <td><span id="Coords3" style="font-size:9px;"><?=$v_items[3]->toString()?></span></td>
</tr>
</tbody>
</table>
</form>
</div><!-- 入力 -->

<div class="spacer"></div>

<div title="画像選択範囲">
 <div class="text-break mb-3 border border-dark" style="height:100px; background-color:aliceblue; overflow:scroll;">
  <canvas id="ocrPreview"></canvas>
 </div>
</div><!-- 画像選択範囲 -->

<div class="spacer"></div>

<div title="OCRデータ読み込み" style="font-size:12px;">
<a data-bs-toggle="collapse" href="#collapse1">API呼び出し</a>
<div id="collapse1" class="collapse">
<div title="API呼び出し" class="inputPanel">
<div class="fw-bold">API 呼び出し</div>
<form name="form_api" enctype="multipart/form-data" method="post" action="">
<div>
<?php
$checked = ($svcType == 1) ? ' checked' : '';
?>
<div class="form-check form-check-inline">
 <input id="svc1" type="radio" name="post_svctype" value="1" class="form-check-input"<?=$checked?>>
 <label class="form-check-label" for="svc1">Google</label>
</div>
<?php
$checked = ($svcType == 2) ? ' checked' : '';
?>
<div class="form-check form-check-inline">
 <input id="svc2" type="radio" name="post_svctype" value="2" class="form-check-input"<?=$checked?>>
 <label class="form-check-label" for="svc2">Microsoft OCR</label>
</div>
<?php
$checked = ($svcType == 3) ? ' checked' : '';
?>
<div class="form-check form-check-inline">
 <input id="svc3" type="radio" name="post_svctype" value="3" class="form-check-input"<?=$checked?>>
 <label class="form-check-label" for="svc3">Microsoft Read</label>
</div>
</div>
<div class="input-group">
 <input type="file" name="post_image" class="form-control" style="font-size:12px;">
</div>
<div class="input-group">
<label class="input-group-text" style="font-size:12px;">キャッシュ</label>
<select name="post_cacheno" class="form-select form-select-sm">
<?php
$selected = ($cacheNo == 0) ? ' selected' : '';
?>
 <option value="0"<?=$selected?>>しない</option>
<?php
for($i=1; $i<6; $i++){
	$selected = ($cacheNo == $i) ? ' selected' : '';
?>
 <option value="<?=$i?>"<?=$selected?>><?=$i?></option>
<?php } ?>
</select>
<div>
 <button type="submit" name="post_action" class="btn btn-secondary" value="1" onClick="javascript:return false;">API</button>
</div>
</div>
</form>
</div><!-- API呼び出し -->
</div><!-- collapse1 -->

<div class="spacer"></div>

<div title="CACHE" class="inputPanel">
<div class="fw-bold">キャッシュ読み込み</div>

<form title="Google" name="form_cache" method="post" action="main.php">
<div class="">
<div class="">Google</div>
<?php
for($i=1; $i<6; $i++){
	$checked = (($svcType == 1)&&($cacheNo == $i)) ? ' checked' : '';
?>
<div class="form-check form-check-inline">
 <input id="cache1<?=$i?>" type="radio" name="post_cacheno" value="<?=$i?>" class="form-check-input"<?=$checked?>>
 <label class="form-check-label" for="cache1<?=$i?>">C<?=$i?></label>
</div>
<?php } ?>
<div class="d-inline-block">
 <button type="submit" name="post_action" value="2" class="btn btn-secondary">Cache</button>
</div>
<input type="hidden" name="post_svctype" value="1">
</div>
</form><!-- Google -->

<form title="Microsoft OCR" name="form_cache" method="post" action="main.php">
<div class="">
<div class="">Microsoft OCR</div>
<?php
for($i=1; $i<6; $i++){
	$checked = (($svcType == 2)&&($cacheNo == $i)) ? ' checked' : '';
?>
<div class="form-check form-check-inline">
 <input id="cache2<?=$i?>" type="radio" name="post_cacheno" value="<?=$i?>" class="form-check-input"<?=$checked?>>
 <label class="form-check-label" for="cache2<?=$i?>">C<?=$i?></label>
</div>
<?php } ?>
<div class="d-inline-block">
 <button type="submit" name="post_action" value="2" class="btn btn-secondary">Cache</button>
</div>
<input type="hidden" name="post_svctype" value="2">
</div>
</form><!-- Microsoft OCR -->

<form title="Microsoft Read" name="form_cache" method="post" action="main.php">
<div class="">
<div class="">Microsoft Read</div>
<?php
for($i=1; $i<6; $i++){
	$checked = (($svcType == 3)&&($cacheNo == $i)) ? ' checked' : '';
?>
<div class="form-check form-check-inline">
 <input id="cache3<?=$i?>" type="radio" name="post_cacheno" value="<?=$i?>" class="form-check-input"<?=$checked?>>
 <label class="form-check-label" for="cache3<?=$i?>">C<?=$i?></label>
</div>
<?php } ?>
<div class="d-inline-block">
 <button type="submit" name="post_action" value="2" class="btn btn-secondary">Cache</button>
</div>
<input type="hidden" name="post_svctype" value="3">
</div>
</form><!-- Microsoft Read -->
</div><!-- CACHE -->
</div><!-- OCRデータ読み込み -->

</div><!-- col-md-5 -->
</div><!-- row -->
</div><!-- main -->
</div><!-- row -->
</div><!-- container -->

<form title="選択範囲の文字解析" id="form_coordinates" name="form_coordinates" method="post" action="ocr.php" class="" role="form">
<input id="post_svctype" type="hidden" name="post_svctype" value="<?=$svcType?>">
<input id="post_cacheno" type="hidden" name="post_cacheno" value="<?=$cacheNo?>">
<input id="image_posSX" type="hidden" name="image_posSX" value="-1">
<input id="image_posSY" type="hidden" name="image_posSY" value="-1">
<input id="image_posMX" type="hidden" name="image_posMX" value="-1">
<input id="image_posMY" type="hidden" name="image_posMY" value="-1">
<input id="post_itemno" type="hidden" name="post_itemno" value="-1">
</form><!-- 選択範囲の文字解析 -->

<div title="モーダルウィンドウ" id="mdlEntry" class="modal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content animated fadeInDown">
<div class="modal-header">
 <span id="est_title" class="modal-title" style="font-size:16px; font-weight:600;">入力情報</span>
 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body clearfix" style="background-color: #f8fafb;">
<div class="form-group" style="xmargin-bottom:0;">
 <input id="mdlEntry_text" type="text" name="mdlEntry_text" class="form-control" placeholder="入力文字" value="">
</div>
<div class="form-group" style="margin-bottom:0;">
 <label class="radio-inline r-in"><input type="radio" name="post_State" value="1" checked> Item1 </label>
 <label class="radio-inline r-in"><input type="radio" name="post_State" value="2"> Item2 </label>
 <label class="radio-inline r-in"><input type="radio" name="post_State" value="3"> Item3 </label>
</div>
</div>
<div class="modal-footer">
 <button id="btn_modal" name="btn_modal" type="button" class="btn btn-primary">OK</button>
</div>
</div><!-- modal-content -->
</div><!-- modal-dialog -->
</div><!-- modal -->

<script type="text/JavaScript">
// window.onloadはreadyの外にしないと正常動作しない
ocr_resizeCanvas("ocrCanvas", "ocrImage");
$(function(){
	ocr_setForm("form_coordinates");
	ocr_setCanvas("ocrCanvas", "ocrImage", "ocrPreview", "form_coordinates");
});
</script>

</body>
</html>