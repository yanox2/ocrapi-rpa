<?php
/* Copyright 2013 dodat */
/*---------------------------------------------------------------------------*
 * Class
 *      その他のユーティリティ
 *---------------------------------------------------------------------------*/
namespace PR;

class Misc{

	const WEEK_SUN = 0;
	const WEEK_MON = 1;
	const WEEK_TUE = 2;
	const WEEK_WED = 3;
	const WEEK_THU = 4;
	const WEEK_FRI = 5;
	const WEEK_SAT = 6;

	private static $REGEX = null;

	public static function loadFunc(){
		self::$REGEX = 
        '`https?+:(?://(?:(?:[-.0-9_a-z~]|%[0-9a-f][0-9a-f]' .
        '|[!$&-,:;=])*+@)?+(?:\[(?:(?:[0-9a-f]{1,4}:){6}(?:' .
        '[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:\d|[1-9]\d|1\d{2}|2' .
        '[0-4]\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25' .
        '[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?' .
        ':\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5]))|::(?:[0-9a-f' .
        ']{1,4}:){5}(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:\d|[1' .
        '-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?:\d|[1-9]\d|1\d{' .
        '2}|2[0-4]\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\\' .
        'd|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])' .
        ')|(?:[0-9a-f]{1,4})?+::(?:[0-9a-f]{1,4}:){4}(?:[0-' .
        '9a-f]{1,4}:[0-9a-f]{1,4}|(?:\d|[1-9]\d|1\d{2}|2[0-' .
        '4]\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-' .
        '5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?:\d' .
        '|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5]))|(?:(?:[0-9a-f]{' .
        '1,4}:)?+[0-9a-f]{1,4})?+::(?:[0-9a-f]{1,4}:){3}(?:' .
        '[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:\d|[1-9]\d|1\d{2}|2' .
        '[0-4]\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25' .
        '[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?' .
        ':\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5]))|(?:(?:[0-9a-' .
        'f]{1,4}:){0,2}[0-9a-f]{1,4})?+::(?:[0-9a-f]{1,4}:)' .
        '{2}(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:\d|[1-9]\d|1\\' .
        'd{2}|2[0-4]\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4' .
        ']\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5' .
        '])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5]))|(?:(?:' .
        '[0-9a-f]{1,4}:){0,3}[0-9a-f]{1,4})?+::[0-9a-f]{1,4' .
        '}:(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:\d|[1-9]\d|1\d' .
        '{2}|2[0-4]\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]' .
        '\d|25[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5]' .
        ')\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5]))|(?:(?:[' .
        '0-9a-f]{1,4}:){0,4}[0-9a-f]{1,4})?+::(?:[0-9a-f]{1' .
        ',4}:[0-9a-f]{1,4}|(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25' .
        '[0-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?' .
        ':\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?:\d|[1-9]\\' .
        'd|1\d{2}|2[0-4]\d|25[0-5]))|(?:(?:[0-9a-f]{1,4}:){' .
        '0,5}[0-9a-f]{1,4})?+::[0-9a-f]{1,4}|(?:(?:[0-9a-f]' .
        '{1,4}:){0,6}[0-9a-f]{1,4})?+::|v[0-9a-f]++\.[!$&-.' .
        '0-;=_a-z~]++)\]|(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0' .
        '-5])\.(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?:\\' .
        'd|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(?:\d|[1-9]\d|' .
        '1\d{2}|2[0-4]\d|25[0-5])|(?:[-.0-9_a-z~]|%[0-9a-f]' .
        '[0-9a-f]|[!$&-,;=])*+)(?::\d*+)?+(?:/(?:[-.0-9_a-z' .
        '~]|%[0-9a-f][0-9a-f]|[!$&-,:;=@])*+)*+|/(?:(?:[-.0' .
        '-9_a-z~]|%[0-9a-f][0-9a-f]|[!$&-,:;=@])++(?:/(?:[-' .
        '.0-9_a-z~]|%[0-9a-f][0-9a-f]|[!$&-,:;=@])*+)*+)?+|' .
        '(?:[-.0-9_a-z~]|%[0-9a-f][0-9a-f]|[!$&-,:;=@])++(?' .
        ':/(?:[-.0-9_a-z~]|%[0-9a-f][0-9a-f]|[!$&-,:;=@])*+' .
        ')*+)?+(?:\?+(?:[-.0-9_a-z~]|%[0-9a-f][0-9a-f]|[!$&' .
        '-,/:;=?+@])*+)?+(?:#(?:[-.0-9_a-z~]|%[0-9a-f][0-9a' .
        '-f]|[!$&-,/:;=?+@])*+)?+`i';
	}

/*---------------------------------------------------------------------------*
 * 文字列操作
 *---------------------------------------------------------------------------*/
	public static function getShortText($rsText,$riLength=0){
		$len = $riLength;
		if($len == 0) $len = 25;
		$text = mb_substr($rsText,0,$len);
		if(mb_strlen($text) == $len) $text .= '…';
		return $text;
	}

	public static function getFillStr($rsStr,$riVisLen=0,$rsFill='*'){
		$vlen = $riVisLen;
		$len = strlen($rsStr);
		$str = '';
		for($i=0; $i<$len; $i++){
			$chr = $rsFill;
			if($i < $vlen) $chr = substr($rsStr,$i,1);
			$str .= $chr;
		}
		return $str;
	}

	public static function createString($riLength){
		$len = $riLength;
		$str = '';
		$ascii = ord('a');
		for($i=0; $i<$len; $i++){
			$shift = mt_rand(0,26+9);
			if($shift < 26){
				$str .= chr($ascii + $shift);
			}else{
				$str .= strval($shift - 26);
			}
		}
		return $str;
	}

	public static function trim($rsStr,$rsMask=null){
		//$str = preg_replace('/^[ 　]+/u','',$rsStr);
		//$str = preg_replace('/[ 　]+$/u','',$str);
		$str = mb_convert_kana($rsStr,'s','UTF-8');
		if(empty($rsMask)){
			$str = trim($str);
		}else{
			$str = trim($str,$rsMask);
		}

		return $str;
	}

	public static function ordSuffix($n){
		$str = "$n";
		$t = ($n > 9) ? substr($str,-2,1) : 0;
		$u = substr($str,-1);
		if($t==1){
			return $str.'th';
		}else{
			switch($u){
				case 1: return $str.'st';
				case 2: return $str.'nd';
				case 3: return $str.'rd';
				default: return $str.'th';
			}
		}
	}

	// $raArray['key1']['key2']['key3'] $aKeys = array('key1','key2','key3');
	public static function chkArrayKey($raArray,$raKeys){
		if(empty($raArray)) return false;
		$chks = $raArray;
		foreach($raKeys as $key){
			if(empty($chks)) return false;
			if(!array_key_exists($key,$chks)) return false;
			$chks = $chks[$key];
		}
		return true;
	}

	public static function linkify($str,$buf='',$ekey=null){
		$newStr = preg_replace_callback(self::$REGEX,function($matches) use ($buf,$ekey){
			$uri = urlencode($matches[0]);
			if(!empty($ekey)) $uri = Crypt::encrypt($uri,$ekey);
		    return '<a href="'.$buf.$uri.'" target="_blank">'.$matches[0].'</a>';
		},$str);
		return $newStr;
	}

	public static function parentheses($str,$buf=''){
		$newStr = preg_replace_callback(self::$REGEX,function($matches) use ($buf){
			$uri = $matches[0];
		    return '<'.$uri.'>';
		},$str);
		return $newStr;
	}

	public static function charType($msg,$types,$more=null){
		$pattern = '';
		// 半角記号
		// !"#$%&'()=~|-^\@[;:],./`{+*}<>?_
		// #$%&-@,./_
		//
		if(in_array('all',$types)) $pattern .= 'a-zA-Zａ-ｚＡ-Ｚ0-9０-９ぁ-んーァ-ヶー一-龠！”＃＄％＆’（）＝～｜－＾￥＠［；：］、。／‘｛＋＊｝＜＞？＿!\"#\$%&\'\(\)=~\|\-\^\\\\@\[;:\],\.\/`\{\+\*\}<>\?_\s\t　';
		if(in_array('set1',$types)) $pattern .= 'a-zA-Zａ-ｚＡ-Ｚ0-9０-９ぁ-んーァ-ヶー一-龠＃＄％＆－＠、。／＿#\$%&\-@,\.\/_\s\t　';
		if(in_array('alpha',$types)) $pattern .= 'a-zA-Z';
		if(in_array('alphaZ',$types)) $pattern .= 'ａ-ｚＡ-Ｚ';
		if(in_array('numeric',$types)) $pattern .= '0-9';
		if(in_array('numericZ',$types)) $pattern .= '０-９';
		if(in_array('kana',$types)) $pattern .= 'ぁ-んーァ-ヶー一-龠';
		if(in_array('symbolSZ',$types)) $pattern .= '＃＄％＆－＠、。／＿';
		if(in_array('symbolS',$types)) $pattern .= '#\$%&\-@,\.\/_';
		if(in_array('symbolZ',$types)) $pattern .= '！”＃＄％＆’（）＝～｜－＾￥＠［；：］、。／‘｛＋＊｝＜＞？＿';
		if(in_array('symbol',$types)) $pattern .= '!\"#\$%&\'\(\)=~\|\-\^\\\\@\[;:\],\.\/`\{\+\*\}<>\?_';
		if(in_array('space',$types)) $pattern .= '\s\t　';
		if($more) $pattern .= $more;

		$pattern = "/^[".$pattern."]+$/u";
		if(preg_match($pattern,$msg)) return true;
		return false;
	}

/*---------------------------------------------------------------------------*
 * 日付・時間関連
 *---------------------------------------------------------------------------*/
	// unixtimeの取得（日付）
	public static function getDate($riDate,$riDay=0,$riMon=0,$riYear=0){
		$iYear = date('Y',$riDate);
		$iMon = date('m',$riDate);
		$iDay = date('d',$riDate);
		$dDate = mktime(0,0,0,$iMon+$riMon,$iDay+$riDay,$iYear+$riYear);
		return $dDate;
	}

	// unixtimeの取得（時刻）
	public static function getTime($riTime,$riDay=0,$riMon=0,$riYear=0,$riHour=0,$riMin=0,$riSec=0){
		$iYear = date('Y',$riTime);
		$iMon = date('m',$riTime);
		$iDay = date('d',$riTime);
		$iHour = date('H',$riTime);
		$iMin = date('i',$riTime);
		$iSec = date('s',$riTime);
		$dDate = mktime($iHour+$riHour,$iMin+$riMin,$iSec+$riSec,$iMon+$riMon,$iDay+$riDay,$iYear+$riYear);
		return $dDate;
	}

	// unixtimeの取得（時刻文字列）
	public static function getTimeOfString($rsTimeStr,$riDay=0,$riMon=0,$riYear=0,$riHour=0,$riMin=0,$riSec=0){
		$iYear = intval(substr($rsTimeStr,0,4));
		$iMon = intval(substr($rsTimeStr,5,2));
		$iDay = intval(substr($rsTimeStr,8,2));
		$iHour = 0;
		$iMin = 0;
		$iSec = 0;
		if(strlen($rsTimeStr) > 10){
			$iHour = intval(substr($rsTimeStr,11,2));
			$iMin = intval(substr($rsTimeStr,14,2));
		}
		if(strlen($rsTimeStr) > 16){
			$iSec = intval(substr($rsTimeStr,17,2));
		}
		$dDate = mktime($iHour+$riHour,$iMin+$riMin,$iSec+$riSec,$iMon+$riMon,$iDay+$riDay,$iYear+$riYear);
		return $dDate;
	}

	// unixtimeの取得（時刻文字列）
	public static function getTimeOfLocaleStr($rsTimeStr,$rbEN=false){
		$str = $rsTimeStr;
		if($rbEN){
			$sMon = substr($str,0,2);
			$sDay = substr($str,3,2);
			$sYear =substr($str,6,4);
			$str = $sYear.'/'.$sMon.'/'.$sDay.substr($str,11);
		}
		$dDate = \PR\Misc::getTimeOfString($str);
		return $dDate;
	}

	// 時刻文字列の取得
	public static function getTimeStr($riTime,$riLocale=1,$rbTime=false,$rbLong=false,$rsDel='/'){
		$items = explode('|',date('Y|n|j|M|H|i|s|m|d',$riTime));
		$date = '';
		$time = '';
		if($riLocale == 1){
			$date = $items[0].$rsDel.$items[7].$rsDel.$items[8];
		}else{
			$date = $items[7].$rsDel.$items[8].$rsDel.$items[0];
		}
		if($rbTime){
			$time = ' '.$items[4].':'.$items[5].':'.$items[6];
		}
		if($rbLong){
			$mon = $items[3].(((int)$items[1] == 5)? '':'.');
			if($riLocale == 1){
				$date = $items[0].$rsDel.$items[7].$rsDel.$items[8];
			}else{
				$date = $mon.' '.$items[8].', '.$items[0];
			}
		}
		$date .= $time;
		return $date;
	}

	public static function isDate($rsTimeStr){
		return true;
	}

	public static function isHoliday($riDate){
		$week = date('w',$riDate);
		if(($week == 0)||($week == 6)) return true;
		return false;
	}

	public function getWeekStr($riWeek,$riType=1){
		if($riType == Locale::TYPE_JA){
			$array = array('日','月','火','水','木','金','土');
		}else if($riType == Locale::TYPE_EN){
			$array = array('SUN','MON','TUE','WED','THU','FRI','SAT');
		}else{
			$array = array('日','月','火','水','木','金','土');
		}
		return $array[$riWeek];
	}

/*---------------------------------------------------------------------------*
 * 取引関連
 *---------------------------------------------------------------------------*/
	public static function moneyStr($rsStr){
		if(!is_numeric($rsStr)) return '0';
		$str = '';
		$cnt = 0;
		for($i=strlen($rsStr)-1; $i>-1; $i--){
			$chr = substr($rsStr,$i,1);
			if(($cnt%3 == 0)&&($cnt != 0)&&($chr != '-')) $str = ','.$str;
			$str = $chr.$str;
			$cnt++;
		}
		return '￥'.$str;
	}

	public static function getTax(){
		$tax = 0.05;
		$now = time();
		$tm1 = self::getTimeOfString('2014/04/01');
		$tm2 = self::getTimeOfString('2015/04/01');
		if($now > $tm1) $tax = 0.08;
		else if($now > $tm2) $tax = 0.1;
		return $tax;
	}

/*---------------------------------------------------------------------------*
 * Bit Maskを利用したオプション引数値
 *---------------------------------------------------------------------------*/
	public function getBMVal($raOpts,$riMaskPow=3){
		$cns = 1;
		$mask = pow(2,$riMaskPow) - 1;
		$val = 0;
		foreach($raOpts as $opt){
			$val |= ($opt * $cns) & $mask;
			$cns *= pow(2,$riMaskPow);
			$mask *= pow(2,$riMaskPow);
		}
		return $val;
	}

	public function getBMOpts($riVal,$riMaskPow=3){
		$cns = 1;
		$mask = pow(2,$riMaskPow) - 1;
		$opts = array(0,0,0,0,0);
		for($i=0; $i<5; $i++){
			$opts[$i] = ($riVal & $mask) / $cns;
			$cns *= pow(2,$riMaskPow);
			$mask *= pow(2,$riMaskPow);
		}
		return $opts;
	}

/*---------------------------------------------------------------------------*
 * その他
 *---------------------------------------------------------------------------*/
	public function encode($roObj,$raVars){
		foreach($raVars as $var=>$key){
			if(!Misc::hasProp($roObj,$var)) continue;
			if(!empty($roObj->{$var})) $roObj->{$var} = \PR\Crypt::encrypt($roObj->{$var},$key);
		}
	}

	public function decode($roObj,$raVars){
		foreach($raVars as $var=>$key){
			if(!Misc::hasProp($roObj,$var)) continue;
			if(!empty($roObj->{$var})) $roObj->{$var} = \PR\Crypt::decrypt($roObj->{$var},$key);
		}
	}

	public static function hasProp($roObj,$rsVar){
		if(property_exists($roObj,$rsVar)) return true;
		ELOG(SMSG('PR_E021',$roObj,$rsVar));
		return false;
	}

	public static function getOneTimeSes($rsName=null,$rsPrefix=null){
		if(!$rsPrefix) $rsPrefix = C_PR_PREFIX_SESSION;
		if(!$rsName) $rsName = 'name';
		$data = $_SESSION[$rsPrefix.$rsName];
		unset($_SESSION[$rsPrefix.$rsName]);
		return $data;
	}

	public static function setOneTimeSes($rsName=null,$rmData=null,$rsPrefix=null){
		if(!$rsPrefix) $rsPrefix = C_PR_PREFIX_SESSION;
		if(!$rmData) $rmData = uniqid($rsPrefix,true);
		if(!$rsName) $rsName = 'name';
		$_SESSION[$rsPrefix.$rsName] = $rmData;
		return $rmData;
	}
}

// htmlspecialchars
function SPCHR($rsStr,$riLen=0,$rsLink=null,$rbNoBr=false,$rsEncKey=null){
	$str = Misc::getShortText($rsStr,$riLen);
	$str = htmlspecialchars($str);
	if(!$rbNoBr) $str = nl2br($str);
	if(!empty($rsLink)) $str = Misc::linkify($str,$rsLink,$rsEncKey);
	return $str;
}

// array_key_exists
function InKey($raArray,$raKeys){
	return Misc::chkArrayKey($raArray,$raKeys);
}

?>
