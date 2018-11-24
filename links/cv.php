<?php
$fp = fopen('CensorWords.txt', 'rb');
$content = fread($fp, filesize('CensorWords.txt'));
fclose($fp);

$content = str_replace("={BANNED}", "\r\n", $content);

$fp = fopen('CensorWords.txt', 'wb');
$content = fwrite($fp, $content);
fclose($fp);
?>