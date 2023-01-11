<?php
/**
 * 定義 & 初期処理
 */

// ディレクトリセパレータ
define('DS', DIRECTORY_SEPARATOR);

// API Key & カスタムドメイン
define('OCR_GOOGLE_APIKEY', '');
define('OCR_MICROSOFT_APIKEY', '');
define('OCR_MICROSOFT_CUSTOMDOMAIN', '');

// キャッシュデータを保存するディレクトリ
define('OCR_CACHE_PATH', 'cache');

// オートロード
spl_autoload_register(function($class){
	$array = explode('\\', $class);
	if(count($array) == 2) $class = $array[1];
	$path = 'classes'.DS;
	if(is_readable($path.$class.'.php')){
		require_once($path.$class.'.php');
	}else if(is_readable($path.$class.'.cls')){ // クラス
		require_once($path.$class.'.cls');
	}else if(is_readable($path.$class.'.inf')){ // インターフェース
		require_once($path.$class.'.inf');
	}
});
\PR\Misc::loadFunc();

?>
