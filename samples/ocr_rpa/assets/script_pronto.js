/* Copyright 2016 dodat */
/*---------------------------------------------------------------------------*
 * JavaScript - Class
 * 共通
 *---------------------------------------------------------------------------*/
// if(!a) alert("a=null/undefined/0/''/false");

/*---------------------------------------------------------------------------*
 * Initialize
 *---------------------------------------------------------------------------*/
//if(typeof Object.create !== "function"){
	Object.create = function(o){
		var F = function(){};
		F.prototype = o;
		var f = new F();
		if($.isFunction(f.init)){
			f.init((arguments.length > 1) ? arguments[1] : undefined);
		}
		return f;
	};
//}

// デバッガ
var PRDebug = {
	level_: 0, // 0:No display 1:display

	setLevel: function(level){
		this.level_ = level;
	},

	alert: function(msg){
		if(this.level_ == 1) alert(msg.replace(/[\n\r]/g,"\r\n"));
	}
};
var prdebug = Object.create(PRDebug);
prdebug.setLevel(1);

/*---------------------------------------------------------------------------*
 * Ajax - class
 *---------------------------------------------------------------------------*/
// インジケータ
var PRIndicator = {
	id_: null,
	message_: null,
	state_: 0, // 0:disabled,1:enabled
	mkstate_: 0, // 0:disabled,1:enabled
	indZ_: 2215, // indicator zIndex (2010)
	maskZ_: 2210, // masked zIndex (2000)

	init: function(){
		this.id_ = "prTag_indicator";
		this.message_ = "";
	},

	setId: function(id){
		this.id_ = id;
	},

	setMessage: function(message){
		this.message_ = message;
	},

	setZIndex: function(indZ,maskZ){
		this.indZ_ = indZ;
		this.maskZ_ = maskZ;
	},

	// インジケータ表示
	enable: function(){
		if(this.state_ == 1) return;
		this.mask();
		$("#" + this.id_ + "_msg").html(this.message_);
		var ele = $("#" + this.id_);
		var pos = pr_getCoordinates(ele);
		ele.css({"display":"block","left":pos[0]+"px","top":pos[1]+"px","zIndex":this.indZ_});
		this.state_ = 1;
	},

	// インジケータ非表示
	disable: function(hasMask){
		if(this.state_ == 0) return;
		var ele = $("#" + this.id_);
		ele.css({"display":"none","zIndex":"0"});
		this.state_ = 0;
		if(!hasMask) this.unmask();
	},

	mask: function(){
		if(this.mkstate_ == 1) return;
		var w = document.documentElement.scrollWidth;
		var h = document.documentElement.scrollHeight;
		var ele = $("#prTag_maskAll");
		ele.css({"width":w+"px","height":h+"px","zIndex":this.maskZ_,"opacity":"0.50","filter":"alpha(opacity=50)"});
		this.mkstate_ = 1;
	},

	unmask: function(){
		if(this.mkstate_ == 0) return;
		var ele = $("#prTag_maskAll");
		ele.css({"width":"0","height":"0","zIndex":"0","opacity":"0.0","filter":"alpha(opacity=0)"});
		this.mkstate_ = 0;
	}
};

// Ajaxクラス
var PRAjax = {
	// variables
	indicator_: null,
	formId_: null, // <form>のID
	isJSONP_: false, // JSONP
	hasFile_: false, // ファイルアップロード
	headers_: null,

	callback_: null,

	// public methods
	// 初期処理
	init: function(param){
		this.indicator_ = Object.create(PRIndicator);
		this.formId_ = param.formId;
		if(param.isJSONP) this.isJSONP_ = param.isJSONP;
		if(param.hasFile) this.hasFile_ = param.hasFile;
	},

	// インジケータ設定
	setIndicator: function(indicator){
		this.indicator_ = indicator;
	},

	// 非同期コールバック設定
	setCallback: function(cb){
		this.callback_ = cb;
	},

	// HTTPヘッダ設定
	setHeader: function(headers){
		this.headers_ = headers;
	},

	get: function(){
	},

	post: function(submitId){
		var isAsync = true;
		if(this.callback_ == null) isAsync = false;
		this.indicator_.enable();
		$form = $("#" + this.formId_);
		var data = $form.serialize();
		if(submitId) data += "&submitId=" + submitId;
		var param = {url:$form.attr("action"),type:$form.attr("method"),async:isAsync,cache:false,timeout:30000,context:this};
/*
		if(this.isJSONP){
			param = Object.assign(param,{dataType:"jsonp",jsonp:"jcbk"});
		}else{
			param = Object.assign(param,{dataType:"json"});
		}
		if(this.hasFile_){
			var fd = new FormData($form[0]);
			fd.append("submitId",submitId);
			param = Object.assign(param,{contentType:false,processData:false,data:fd});
		}else{
			param = Object.assign(param,{data:data});
		}
*/
		if(this.isJSONP){
			if(this.hasFile_){
				var fd = new FormData($form[0]);
				fd.append("submitId",submitId);
				param = {url:$form.attr("action"),type:$form.attr("method"),async:isAsync,cache:false,timeout:30000,context:this,
					dataType:"jsonp",jsonp:"jcbk",contentType:false,processData:false,data:fd};
			}else{
				param = {url:$form.attr("action"),type:$form.attr("method"),async:isAsync,cache:false,timeout:30000,context:this,
					dataType:"jsonp",jsonp:"jcbk",data:data};
			}
		}else{
			if(this.hasFile_){
				var fd = new FormData($form[0]);
				fd.append("submitId",submitId);
				param = {url:$form.attr("action"),type:$form.attr("method"),async:isAsync,cache:false,timeout:30000,context:this,
					dataType:"json",contentType:false,processData:false,data:fd};
			}else{
				param = {url:$form.attr("action"),type:$form.attr("method"),async:isAsync,cache:false,timeout:30000,context:this,
					dataType:"json",data:data};
			}
		}
		if(this.headers_){
			param = Object.assign(param,{headers:this.headers_});
		}

		var rvals = new Array(3);
		$.ajax(param).done(function(res,status,xhr){
			this.indicator_.disable(true);
			if(res.message) prdebug.alert(res.message);
			if(res.confirm){
				if(!window.confirm(res.confirm)) return;
			}
			if(res.reload){
				kpScroll();
				location.reload(true);
				return;
			}
			if(res.location !== undefined){
				window.location.href = res.location;
				return;
			}
			this.indicator_.unmask();
			rvals = [status,res,null];
			if(this.callback_ != null) this.callback_(rvals);

		}).fail(function(xhr,status,error){
			this.indicator_.disable();
			var msg = "システムとの通信中にエラーが発生しました。";
			msg += " (" + xhr.status + " " + status + " " + error.message + ")";
			this._error(-1,msg);
			rvals = [status,null,error];
			if(this.callback_ != null) this.callback_(rvals);

		}).always(function(xhr,status,error){
			this.indicator_.disable();
		});
		if(this.callback_ == null) return rvals;
	},

	// private methods
	// エラー処理
	_error: function(rc,msg){
		prdebug.alert(msg + " code=" + rc);
		//button.attr("disabled",true);
		//$form = $(this);
		//button.removeAttr("disabled");
	}
};

// Ajaxフォーム基底クラス
var PRFormAjax = {
	// variables
	ajax_: null,
	formId_: null, // <form>のID
	showId_: null, // Active/InActiveとなるフォームエリアのID
	current_: null, // カレントリスナ
	submitId_: null, // submitされたID
	state_: 0,
	lis_: null,

	// override methods (your implementation)
	// abstract:フォームアクティブ
	active: function(res){
		if(!this.showId_) return;
		if($("#"+this.showId_).get(0)){
			$("#"+this.showId_).css({"display":"block"});
			//$("#"+this.showId_).modal("show");
		}
	},

	// abstract:フォームインアクティブ
	inactive: function(){
		if(!this.showId_) return;
		if($("#"+this.showId_).get(0)){
			$("#"+this.showId_).css({"display":"none"});
			//$("#"+this.showId_).modal("none");
		}
	},

	// public methods
	// 初期処理
	init: function(param){
		this.formId_ = param.formId;
		if(param.showId) this.showId_ = param.showId;
		var hasFile = false;
		if(param.hasFile) hasFile = param.hasFile;
		this.ajax_ = Object.create(PRAjax,{"formId":param.formId,"hasFile":hasFile});
		var actId = null;
		if(param.actId) actId = param.actId;
		var async = true;
		if(param.async) async = param.async;
		var lis = Object.create(PRFormAjaxListener,{"actId":actId,"async":async});
		this.addListener(lis);
	},

	// インジケータ設定
	setIndicator: function(indicator){
		this.ajax_.setIndicator(indicator);
	},

	// HTTPヘッダ設定
	setHeader: function(headers){
		this.ajax_.setHeader(headers);
	},

	// submitボタンの追加
	addSubmit: function(id,type,hover){
		if(type == 0){ // onClick
			var ele = $("#"+id);
			if(hover){
				ele.hover(
					function(e){
						ele.css("cursor","pointer");
						if(hover == 2) ele.css("opacity","0.6");
					},
					function(e){
						ele.css("cursor","default");
						if(hover == 2) ele.css("opacity","1.0");
					}
				);
			}
			ele.click({me:this},function(e){
				e.preventDefault();
				var me = e.data.me;
				me.submitId_ = e.target.id;
				var rc = me.current_.beforeSubmit(e);
				if(!rc) return false;
				$("#"+me.formId_).submit();
			});
		}else{ // onChange
			$("#"+id).change({me:this},function(e){
				e.preventDefault();
				var me = e.data.me;
				me.submitId_ = e.target.id;
				$("#"+me.formId_).submit();
			});
		}
	},

	// リスナの追加
	addListener: function(listener,res){
		if(listener.actId){
			$("#"+listener.actId).click({me:this},function(e){
				var me = e.data.me;
				var lis = listener;
				if(lis.state == 0) return;
				if(lis.async){ // 非同期
					if(lis.reqId){
						if(res) lis.domUpdate(res);
						var cb = me.callback.bind(me);
						lis.setCallback(cb);
						lis.request(e);
					}else{
						lis.preshow(res);
						me.active(res);
						me.current_ = lis;
						me.state_ = 1;
					}
					me.lis_ = lis;
				}else{ // 同期
					if(lis.reqId){
						if(res) lis.domUpdate(res);
						var rvals = lis.request(e);
						if(rvals[0] != "success"){
							lis.onError(rvals[2]);
							return;
						}
						res = rvals[1];
					}
					lis.preshow(res);
					me.active(res);
					me.current_ = lis;
					me.state_ = 1;
				}
				return false; // e.preventDefault() & e.stopPropagation()
			});
			if(this.showId_){
				$(document).click({me:this},function(e){
					if(!me) return;
					var me = e.data.me;
					if(me.state_ == 1){
						me.inactive();
						me.state_ = 0;
					}
				});
				$("#"+this.showId_).click(function(e){
					e.stopPropagation();
				});
			}
		}else{
			// actIdがない場合はすぐにフォームに値をセット
			listener.preshow(res);
			this.current_ = listener;
			this.state_ = 1;
		}
		if((res)&&(res.submit)){
			this.addSubmit(res.submit.submitId,res.submit.type,res.submit.hover);
		}
	},

	// request callback
	callback: function(rvals){
		if(rvals[0] != "success"){
			this.lis_.onError(rvals[2]);
			return;
		}
		var res = rvals[1];
		this.lis_.preshow(res);
		this.active(res);
		this.current_ = this.lis_;
		this.state_ = 1;
	},

	// submit処理
	listen: function(){
		$("#"+this.formId_).submit({me:this},function(e){
			e.preventDefault();
			var me = e.data.me;
			var lis = me.current_;
			var submitId = me.submitId_;
			me.submitId_ = null;
			if(lis.state == 0) return;
			var rc = lis.prebefore(e,submitId);
			if(!rc) return false;
			var msg = lis.getMessage(submitId);
			if(msg){
				if(!window.confirm(msg)) return false;
			}
			if(lis.state == 2) return;

			if(lis.async){
				var cb = me._callback.bind(me);
				me.ajax_.setCallback(cb);
				me.ajax_.post(submitId);
				me.lis_ = lis;
			}else{
				var rvals = me.ajax_.post(submitId);
				if(rvals[0] == "success"){
					var res = rvals[1];
					lis.preafter(res);
					if((res.rcode == 0)&&(!res.stay)) me.inactive();
				}else{
					lis.onError(rvals[2]);
				}
			}
		});
	},

	// private methods
	// submit callback
	_callback: function(rvals){
		if(rvals[0] == "success"){
			var res = rvals[1];
			this.lis_.preafter(res);
			if((res.rcode == 0)&&(!res.stay)) this.inactive();
		}else{
			this.lis_.onError(rvals[2]);
		}
	},

	// エラー処理
	_error: function(rc,msg){
	}
};

// AjaxフォームリスナーInterface
var PRFormAjaxListenerInf = {
	// variables
	actId: null, // フォームをアクティブにするID
	reqId: null, // リクエスト用フォームのID
	state: 1, // 0:inactive（何もしない） 1:active 2:no submit
	async: true, // true:非同期 false:同期
	items: null, // 送信データ（JSON）

	// override methods (your implementation)
	getMessage: function(msg){}, // 確認メッセージの取得
	show: function(event){}, // フォーム表示
	before: function(event,submitId){}, // 送信前処理
	after: function(res){}, // 送信後処理
	request: function(event){}, // フォームデータリクエスト
	onError: function(error){}, // エラー処理

	// public methods
	init: function(param){}, // 初期処理
	setCallback: function(cb){}, // 非同期コールバック

	// for PRFormAjax
	preshow: function(){}, // フォームデータ埋め込み
	prebefore: function(res){}, // 送信前処理
	preafter: function(res){} // DOM編集
};

// Ajaxフォームリスナー基底クラス
var PRFormAjaxListener = {
	// variables
	actId: null,
	reqId: null,
	state: 1,
	async: true,
	dom: null,
	msg: null,

	ajax_: null,
	callback_: null,

	// 確認メッセージの取得
	getMessage: function(submitId){
		return this.msg;
	},

	// フォーム表示
	show: function(res){
	},

	// 送信前処理
	before: function(event,submitId){
	},

	// 送信後処理
	after: function(res){
	},

	// データリクエスト
	request: function(event){
		var rvals = this._request();
		return rvals;
	},

	// submit前処理
	beforeSubmit: function(event){
	},

	// DOM
	domUpdate: function(res){
		if((res)&&(res.dom)) this._domUpdate(res.dom);
	},

	// フォーム表示
	preshow: function(res){
		if(this.dom) this._domUpdate(this.dom);
		if((res)&&(res.dom)) this._domUpdate(res.dom);
		this.show(res);
	},

	// 送信前処理
	prebefore: function(event,submitId){
		var rc = this.before(event,submitId);
		if(rc == false) return false;
		return true;
	},

	// 送信後処理
	preafter: function(res){
		if(res.dom) this._domUpdate(res.dom);
		this.after(res);
	},

	// エラー処理
	onError: function(error){
	},

	init: function(param){
		if(param.actId) this.actId = param.actId;
		if(param.reqId) this.reqId = param.reqId;
		if(param.state) this.state = param.state;
		if(param.async) this.async = param.async;
		if(param.dom) this.dom = param.dom;
		if(param.hover){
			$("#"+this.actId).hover(
				function(e){
					$(this).css("cursor","pointer");
					if(param.hover == 2) $(this).css("opacity","0.6");
				},
				function(e){
					$(this).css("cursor","default");
					if(param.hover == 2) $(this).css("opacity","1.0");
				}
			);
		}
		if(param.reqId) this.ajax_ = Object.create(PRAjax,{"formId":param.reqId});
	},

	// 確認メッセージの設定
	setMessage: function(msg){
		this.msg = msg;
	},

	// 非同期コールバック設定
	setCallback: function(cb){
		this.callback_ = cb;
	},

	_request: function(){
		if(this.async){
			var cb = this._callback.bind(this);
			this.ajax_.setCallback(cb);
			this.ajax_.post();
		}else{
			var rvals = this.ajax_.post();
			return rvals;
		}
	},

	_callback: function(rvals){
		this.callback_(rvals);
	},

	// res:{dom:{append:{},prepend:{},after:{},before:{},replace:{},remove:{}}};
	_domUpdate: function(dom){
		if(dom.append){ // idの中にlastIn
			$.each(dom.append,function(id,tag){
				var ele = pr_getElementByIdName(id);
				if(ele){
					ele.append(tag);
					ele.children().slideDown();
				}
			});
		}
		if(dom.prepend){ // idの中にfirstIn
			$.each(dom.prepend,function(id,tag){
				var ele = pr_getElementByIdName(id);
				if(ele){
					ele.prepend(tag);
					ele.children().slideDown();
				}
			});
		}
		if(dom.after){ // idの直下に追加
			$.each(dom.after,function(id,tag){
				var ele = pr_getElementByIdName(id);
				if(ele) ele.after(tag);
			});
		}
		if(dom.before){ // idの直前に追加
			$.each(res.dom.before,function(id,tag){
				var ele = pr_getElementByIdName(id);
				if(ele) ele.before(tag);
			});
		}
		if(dom.replace){ // idの中を更新
			//$.each(dom.replace,function(id,tag){ $("#"+id).replaceWith(tag); });
			$.each(dom.replace,function(id,tag){
				var eles = pr_getElementByIdName(id);
				if(eles){
					$.each(eles,function(){
						var tagName = $(this).get(0).tagName;
						if(tagName == "INPUT"){
							if(($(this).attr("type") == "radio")||($(this).attr("type") == "checkbox")){
								var val = $(this).val();
								if(val == tag){
									$(this).prop("checked",true);
								}else{
									$(this).prop("checked",false);
								}
							}else{
								$(this).val(tag);
							}
						}else if((tagName == "SELECT")||(tagName == "TEXTAREA")){
							$(this).val(tag);
						}else if(tagName == "IMG"){
							$(this).attr("src",tag);
						}else{
							$(this).html(tag);
						}
					});
				}
/*
				var ele = pr_getElementByIdName(id);
				if(ele){
					if((ele.get(0).tagName == "INPUT")||(ele.get(0).tagName == "TEXTAREA")){
						ele.val(tag);
					}else if(ele.get(0).tagName == "IMG"){
						ele.attr("src",tag);
					}else{
						ele.html(tag);
					}
				}
*/
			});
		}
		if(dom.remove){ // idを無効化
			$.each(dom.remove,function(id,tag){
				var ele = pr_getElementByIdName(id);
				if(ele) ele.addClass("dfade");
			});
			this.state = 0;
		}
		if(dom.removef){ // idを削除
			if($("#"+this.actId).get(0)) $("#"+this.actId).unbind();
			$.each(dom.removef,function(id,tag){
				var ele = pr_getElementByIdName(id);
				if(ele) ele.remove();
			});
			this.state = 0;
		}
	}
};
// 継承の例
// var MyListener = $.extend({},PRFormAjaxListener,Template);
// itemsの例
// items:{"post_UserId":"5","post_Name":"test5","post_EMail":"test5@test.co.jp","post_Attr1":""}

// ●PRFormAjax
//  formId		フォームのId。
//  showId		activeエリア。エリア以外のクリックでinactive。
//  hasFile		multipart用。
//  actId		defaultリスナ用。
//  async		defaultリスナ用。
// ●PRFormAjaxListener
//  actId		フォームがactiveになるId。lis.show()が呼ばれform.active()が呼ばれる。
//  reqId		リクエストが発生するId。リクエスト後にlis.show()が呼ばれform.active()が呼ばれる。
//  state		0:なにもしない 1:active 2:no submit。
//  async		true:非同期 false:同期。
//  dom			show()時のDOM操作。
//  hover		actIdのホバー。

/*---------------------------------------------------------------------------*
 * メッセージ - function
 *---------------------------------------------------------------------------*/
// メッセージ表示
function pr_checkMsg(msg,url){
	if(window.confirm(msg)){
		if(url != null){
			window.location.href = url;
		}
		return true;
	}else{
		return false;
	}
}

// メッセージ付サブミット
function pr_checkMsgSubmit(msg,obj,ptype){
	var ele = $("#btn_submit");
	if(ele.size() > 0){
		var cls = ele.attr('class');
		if(cls.indexOf("dfade") != -1) return false;
	}
	if(window.confirm(msg)){
		if(ele.size() > 0) ele.addClass("dfade");
		var ele2 = $("#post_ptype");
		if((ele2.size() > 0)&&(ptype)) ele2.val(ptype);
		obj.submit();
	}
	return false;
}

// サブウィンドウ生成
function pr_subwindow(html,title,width,height){
	if(width == -1) width = 790 + 20;
	if(height == -1) height = 850;
	var str = ',menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes';
	//var str = ',menubar=yes,toolbar=yes,scrollbars=yes';
	window.open(html,title,'width=' + width + ',height=' + height + str);
	return false;
}

/*---------------------------------------------------------------------------*
 * 日付・時刻関連 - function
 *---------------------------------------------------------------------------*/
function pr_getDate(date,day,month,year){
	if(day === undefined) day = 0;
	if(month === undefined) month = 0;
	if(year === undefined) year = 0;
	var yy = date.getFullYear();
	var mm = date.getMonth();
	var dd = date.getDate();
	date = new Date(yy+year,mm+month,dd+day);
	return date;
}

function pr_getDateOfString(str,day,month,year){
	if(day === undefined) day = 0;
	if(month === undefined) month = 0;
	if(year === undefined) year = 0;
	var date = new Date();
	var yy = date.getFullYear();
	var mm = date.getMonth();
	var dd = date.getDate();
	if(str != null){
		yy = parseInt(str.substr(0,4));
		mm = parseInt(str.substr(5,2));
		dd = parseInt(str.substr(8,2));
		mm = mm - 1;
	}
	date = new Date(yy+year,mm+month,dd+day);
	return date;
}

function pr_getDateString(date,hasTime){
	var sep = "/";
	var yy = date.getFullYear();
	var mm = date.getMonth() + 1;
	var dd = date.getDate();
	if(mm < 10) mm = "0" + mm;
	if(dd < 10) dd = "0" + dd;
	var str = yy + sep + mm + sep + dd;
	if(hasTime) str = str + " " +("0"+date.getMinutes()).slice(-2);
	return str;
}

/*---------------------------------------------------------------------------*
 * 座標 - function
 *---------------------------------------------------------------------------*/
// 画面の中心を取得
function pr_getCoordinates(ele){
	//var sz = Element.getDimensions(ele);
	var szw = ele.width();
	var szh = ele.height();
	var scx = document.body.scrollLeft || document.documentElement.scrollLeft;   
	var scy = document.body.scrollTop || document.documentElement.scrollTop;   
	var cw1 = document.documentElement.clientWidth || document.body.clientWidth || document.body.scrollWidth;
	var ch1 = document.documentElement.clientHeight || document.body.clientHeight || document.body.scrollHeight;
	var pos = new Array();
	pos[0] = Math.round((cw1/2) - (szw/2) + scx);
	pos[1] = Math.round((ch1/2) - (szh/2) + scy);
	if(pos[0] < scx+10) pos[0] = scx+10;
	if(pos[1] < scy+10) pos[1] = scy+10;

	return pos;
}

// マウス座標を取得
function pr_getMouseCoordinates(evt){
	var scx = document.body.scrollLeft || document.documentElement.scrollLeft;   
	var scy = document.body.scrollTop || document.documentElement.scrollTop;   
	var x = 0;
	var y = 0;
	if(window.opera){ // Opera
		x = evt.clientX;
		y = evt.clientY;
	}else if(document.all){ // IE4
		x = event.clientX + scx;
		y = event.clientY + scy;
		e = event;
	}else if(document.layers || document.getElementById){ // IE5,NN4,NN6,Moz
		x = evt.pageX;
		y = evt.pageY;
	}
	var pos = new Array();
	pos[0] = x;
	pos[1] = y;

	return pos;
}

/*---------------------------------------------------------------------------*
 * 初期化 - function
 *---------------------------------------------------------------------------*/
function pr_init(){
	// fade mask
	$(".dfade").click(function(e){
		//if($(this).attr("data-toggle")) this.collapse("hidden");
		return false;
	});
	$(".dfade2").click(function(e){
		//if($(this).attr("data-toggle")) this.collapse("hidden");
		return false;
	});

	// blur click focus
	$("a").focus(function(e){
		if(this.blur) this.blur();
	});
}

// reload後 元の位置
function kpScroll(){
	var re = /px=(\d+)&py=(\d+)/;
	var pos = "px=" + $(window).scrollLeft() + "&py=" + $(window).scrollTop();
	//Cookies.set("re_loc",pos,{expires:1,path:""});
	Cookies.set("re_loc",pos,{expires:1});
}
function reScroll(){
	var re = /px=(\d+)&py=(\d+)/;
	var ck = Cookies.get("re_loc");
	if((ck)&&(ck.match(re))){
		var pos = ck.match(re);
		window.scrollTo(pos[1],pos[2]);
		//Cookies.remove("re_loc",{expires:1,path:""});
		Cookies.remove("re_loc",{expires:1});
	}
}

/*---------------------------------------------------------------------------*
 * Misc - function
 *---------------------------------------------------------------------------*/
// get elementId or elementName
function pr_getElementByIdName(id){
	var ele = $("#"+id);
	if(!ele.get(0)){
		ele = $("[name="+id+"]");
		//ele = $("input[name="+id+"]");
		if(!ele.get(0)) return null;
	}
	return ele;
}

// htmlspecialchars
function pr_htmlspecialchars(ch){
	ch = ch.replace(/&/g,"&amp;");
	ch = ch.replace(/"/g,"&quot;");
	ch = ch.replace(/'/g,"&#039;");
	ch = ch.replace(/</g,"&lt;");
	ch = ch.replace(/>/g,"&gt;");
	return ch ;
}

// block or none
function pr_showBlock(id){
	var idc = id + "_contents";
	if(document.getElementById){
		if($("#"+idc).css("display") == "none"){
			$("#"+idc).css({"display":"block"});
			if($("#"+id)) $("#"+id).html("－");
		}else{
			$("#"+idc).css({"display":"none"});
			if($("#"+id)) $("#"+id).html("＋");
		}
	}
}

/*---------------------------------------------------------------------------*
 * 初期化処理
 *---------------------------------------------------------------------------*/
$(function(){
	pr_init();
	reScroll();
	prdebug.setLevel(0);
});

/*---------------------------------------------------------------------------*
 *
 * vendors
 *
 *---------------------------------------------------------------------------*/
/*!
 * JavaScript Cookie v2.2.0
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
	var registeredInModuleLoader = false;
	if (typeof define === 'function' && define.amd) {
		define(factory);
		registeredInModuleLoader = true;
	}
	if (typeof exports === 'object') {
		module.exports = factory();
		registeredInModuleLoader = true;
	}
	if (!registeredInModuleLoader) {
		var OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = OldCookies;
			return api;
		};
	}
}(function () {
	function extend () {
		var i = 0;
		var result = {};
		for (; i < arguments.length; i++) {
			var attributes = arguments[ i ];
			for (var key in attributes) {
				result[key] = attributes[key];
			}
		}
		return result;
	}

	function init (converter) {
		function api (key, value, attributes) {
			var result;
			if (typeof document === 'undefined') {
				return;
			}

			// Write

			if (arguments.length > 1) {
				attributes = extend({
					path: '/'
				}, api.defaults, attributes);

				if (typeof attributes.expires === 'number') {
					var expires = new Date();
					expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
					attributes.expires = expires;
				}

				// We're using "expires" because "max-age" is not supported by IE
				attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

				try {
					result = JSON.stringify(value);
					if (/^[\{\[]/.test(result)) {
						value = result;
					}
				} catch (e) {}

				if (!converter.write) {
					value = encodeURIComponent(String(value))
						.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
				} else {
					value = converter.write(value, key);
				}

				key = encodeURIComponent(String(key));
				key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
				key = key.replace(/[\(\)]/g, escape);

				var stringifiedAttributes = '';

				for (var attributeName in attributes) {
					if (!attributes[attributeName]) {
						continue;
					}
					stringifiedAttributes += '; ' + attributeName;
					if (attributes[attributeName] === true) {
						continue;
					}
					stringifiedAttributes += '=' + attributes[attributeName];
				}
				return (document.cookie = key + '=' + value + stringifiedAttributes);
			}

			// Read

			if (!key) {
				result = {};
			}

			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling "get()"
			var cookies = document.cookie ? document.cookie.split('; ') : [];
			var rdecode = /(%[0-9A-Z]{2})+/g;
			var i = 0;

			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var cookie = parts.slice(1).join('=');

				if (!this.json && cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					var name = parts[0].replace(rdecode, decodeURIComponent);
					cookie = converter.read ?
						converter.read(cookie, name) : converter(cookie, name) ||
						cookie.replace(rdecode, decodeURIComponent);

					if (this.json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) {}
					}

					if (key === name) {
						result = cookie;
						break;
					}

					if (!key) {
						result[name] = cookie;
					}
				} catch (e) {}
			}

			return result;
		}

		api.set = api;
		api.get = function (key) {
			return api.call(api, key);
		};
		api.getJSON = function () {
			return api.apply({
				json: true
			}, [].slice.call(arguments));
		};
		api.defaults = {};

		api.remove = function (key, attributes) {
			api(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.withConverter = init;

		return api;
	}

	return init(function () {});
}));
