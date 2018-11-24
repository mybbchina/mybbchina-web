<?php
$settings['charset'] = 'utf-8'; //网站编码
$settings['checkrec'] = ''; //反向链接检测, 默认为严格检测strict, 如果放松条件, 请将此值设为其它或留空
$settings['spamfile'] = 'spam_tts.dat';
$settings['superlativesfile'] = 'superlatives.dat';

if (!function_exists("stripos")) {
  function stripos($str,$needle,$offset=0)
  {
     return strpos(strtolower($str),strtolower($needle),$offset);
  }
}
?>