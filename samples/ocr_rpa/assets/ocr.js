/**
 * OCR処理
 * OCR - JavaScript
 */

/**
 * キャンバスサイズを画像サイズにあわせる。
 * @param string canvasId キャンバスのID
 * @param string imageId 画像のID
 */
function ocr_resizeCanvas(canvasId, imageId){
	$(window).on("load", function(){
		var ocrImage = $("#"+imageId);
		var ocrCanvas = $("#"+canvasId);
		var w = ocrImage.width();
		var h = ocrImage.height();
		ocrCanvas.attr("width", w);
		ocrCanvas.attr("height", h);
	});
}

/**
 * 選択範囲の文字列を取得しテキストボックスを赤くする（モーダルウィンドウを表示する）
 * @param string formId 選択範囲の座標をPOSTするフォームのID
 * @param string modalId レスポンス取得後に表示するモーダルウィンドウのID
 */
function ocr_setForm(formId, modalId){
	var BaseLis = {
		after: function(res){
			if(res.rcode < 0){
				if(res.message) alert(res.rcode+" "+res.message);
				return;
			}
			var chk = $("input[name=ItemNo]:checked").val();
			$("#post_Item1").css({border:"1px solid #ccc"});
			$("#post_Item2").css({border:"1px solid #ccc"});
			$("#post_Item3").css({border:"1px solid #ccc"});
			var crrt = null;
			if(chk == "1"){
				crrt = $("#post_Item1");
			}else if(chk == "2"){
				crrt = $("#post_Item2");
			}else if(chk == "3"){
				crrt = $("#post_Item3");
			}
			crrt.css({border:"1px solid red"});
			if(modalId){
				$("#"+modalId).modal("show");
				$("#"+modalId+"_text").val(res.text);
			}
		}
	}
	var MyLis = $.extend({}, PRFormAjaxListener, BaseLis);

	var form = Object.create(PRFormAjax, {"formId":formId});
	var lis = Object.create(MyLis, {});
	form.addListener(lis);
	form.listen();
}

/**
 * 画像の矩形切り取り
 * @param string canvasId 切り取りを行う元画像の背景キャンバスのID
 * @param string imageId 切り取りを行う元画像のID
 * @param string previewId 切り取り結果を表示するキャンバスのID
 * @param string formId 切り取り後にPOSTするフォームのID
 */
function ocr_setCanvas(canvasId, imageId, previewId, formId){
	var canvas = $("#"+canvasId);
	var ctx = canvas[0].getContext("2d");
	var sx;var sy;var mx;var my;

	canvas.mousedown(function(e){
		var railhead = e.target.getBoundingClientRect();
		sx = e.clientX - railhead.left;
		sy = e.clientY - railhead.top;
		var width = canvas.width();
		var height = canvas.height();
		canvas.bind("mousemove", function(e2){
			var railhead = e2.target.getBoundingClientRect();
			mx = e2.clientX - railhead.left;
			my = e2.clientY - railhead.top;
			ctx.clearRect(0, 0, width, height);
			ctx.strokeRect(sx, sy, mx-sx, my-sy);
		});
	});

	canvas.mouseup(function(){
		var width = mx - sx;
		var height = my - sy;
		$("#image_posSX").val(sx);
		$("#image_posSY").val(sy);
		$("#image_posMX").val(mx);
		$("#image_posMY").val(my);
		var itemno = $('input:radio[name="ItemNo"]:checked').val();
		$("#post_itemno").val(itemno);
		canvas.unbind("mousemove");

		var p_canvas = $("#"+previewId);
		var pw = p_canvas.width();
		var ph = p_canvas.height();
		var p_ctx = p_canvas[0].getContext("2d");
		var img = new Image();
		var imgSrc = $("#"+imageId).attr("src");
		img.src = imgSrc + "?" + (new Date).getHours();
		p_canvas.attr('width', width);
		p_canvas.attr('height', height);
		img.onload = function(){
			p_ctx.clearRect(0, 0, pw, ph);
			p_ctx.drawImage(img, sx, sy, width, height, 0, 0, width, height);
		}
		if(formId) $("#"+formId).submit();
	});
}