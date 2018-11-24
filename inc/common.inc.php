<?php
//按路径生成文件夹
function mkpath($path) {
	$dirs=array();
	$path=preg_replace('/(\/){2,}|(\\\){1,}/','/',$path); //only forward-slash
	$dirs=explode("/",$path);
	$path="";
	foreach ($dirs as $element) {
		$path.=$element."/";
		if(!is_dir($path)) {
			if(!mkdir($path)){ echo "something was wrong at : ".$path; return 0; }
		}
	}
	//echo("<B>".$path."</B> successfully created");
}

//生成文件,如果目录不存在则尝试建立,包括子目录
function filewrite($file, $content, $method) {
	$path = explode('/', $file);
	array_pop($path);
	$path = implode('/', $path);
	if (!file_exists($file)) mkpath($path);
	$fp = fopen($file, $method);
	fwrite($fp, $content);
	fclose($fp);
}

function get_url_content($url, $charset) {
	if(function_exists('file_get_contents')) {
		$data = file_get_contents($url);
	}
	else {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
	}
	$encode_arr = array('UTF-8','ASCII','GBK','GB2312','BIG5','JIS','eucjp-win','sjis-win','EUC-JP');
	$encoded = mb_detect_encoding($data, $encode_arr);
	$data = mb_convert_encoding($data, $charset, $encoded);
	return $data;
}

//截取中文字符,一个汉字按照3个字节计算
function utf8strcut($string, $length, $endfix, $charset) {
	if (strlen($string) <= $length) return $string;
	return $string = mb_strcut($string, 0, $length - strlen($endfix), $charset).$endfix;
}
?>