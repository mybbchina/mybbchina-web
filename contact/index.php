<?php
session_start();
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$image_string = isset($_POST['image_string']) ? strtoupper($_POST['image_string']) : "";
$submit = $_POST['submit'];
if ($submit == 1 && $image_string == $_SESSION['secc']) {
	$_SESSION['secc'] = strtoupper(substr(sha1(time().'ddd'),0,4));
	$subject = utf8_encode_c(cleanup("来自 $name 的 $subject"));
	$message .= "\r\n";
	$message .= "name: $name \r\n";
	$message .= "email: $email \r\n";
	$fejlec .= "Content-Type: text/plain; charset=utf-8\r\n";
	$fejlec .= "From: ".utf8_encode_c(cleanup('MyBB中文站'))." <webmaster@mybbchina.net>\r\n";
	mail('netmkt@126.com', $subject, $message, $fejlec);
	$msg = '<font color=green>提交成功</font>';
	$name = '';
	$email = '';
	$subject = '';
	$message = '';
	$image_string = '';
	$submit = '';
}
else {
	$_SESSION['secc'] = strtoupper(substr(sha1(time().'ddd'),0,4));
	$msg = '<font color=red>请确保验证码填写正确</font>';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN"><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /><title>MyBB中文站 - 联系表单</title><meta name="author" content="MyBB中文团队, MyBB Group" /><meta name="copyright" content="版权 MyBB中文团队, Copyright MyBB Group." /><meta name="description" content="MyBB中文官方网站, 免费的PHP和MySQL论坛软件系统." /><meta name="keywords" content="mybb中文,mybb中文站,中文MyBB,mybbchina,mybb中国,php,mysql,论坛,讨论区,社区,bbs,免费,软件,聊天" /><link rel="stylesheet" type="text/css" href="http://www.mybbchina.net/assets/style.css?xmas" /><!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="http://www.mybbchina.net/assets/style_ie.css?zz" /><![endif]--><link rel="alternate" type="application/rss+xml" title="公告 (RSS)" href="http://community.mybbchina.net/syndication.php?fid=12&limit=5" /><link rel="alternate" type="application/atom+xml" title="公告 (Atom)" href="http://community.mybbchina.net/syndication.php?fid=12&limit=5&type=atom1.0" /><script type="text/javascript" src="http://www.mybbchina.net/jscripts/prototype.lite.js"></script><script type="text/javascript" src="http://www.mybbchina.net/jscripts/general.js"></script><script type="text/javascript" src="http://www.mybbchina.net/jscripts/moo.ajax.js"></script><script type="text/javascript" src="http://www.mybbchina.net/jscripts/lightbox.js"></script><!--[if lt IE 7]><script defer type="text/javascript" src="http://www.mybbchina.net/assets/pngfix.js"></script><![endif]--></head><body><div id="shadow"><div id="container"><!--search:no_index--><h1><span class="invisible">MyBB中文站</span></h1><div id="header"><a class="link" href="http://www.mybbchina.net/"></a><div id="search"><form action="http://community.mybbchina.net/search.php" method="post"><fieldset class="noborder"><label for="search_box"><span id="search_left">&nbsp;</span></label><input name="keywords" id="search_box" title="输入关键词" type="text" /><input value="搜索" name="submit-search" id="search_button" type="submit" /><input type="hidden" name="action" value="do_search" /><input type="hidden" name="showresults" value="threads" /><input type="hidden" name="sortordr" value="desc" /><input type="hidden" name="sortby" value="lastpost" /><input type="hidden" name="pddir" value="1" /><input type="hidden" name="postdate" value="0" /><input type="hidden" name="forums" value="all" /><input type="hidden" name="postthread" value="2" /></fieldset></form></div><div id="menu"><ul><li class="default"><a href="http://www.mybbchina.net/">首页</a></li><li class="default"><a href="http://www.mybbchina.net/about">关于 中文MyBB</a></li><li class="default"><a href="http://www.mybbchina.net/downloads">下载</a></li><li class="default"><a href="http://community.mybbchina.net">论坛社区</a></li><li class="default"><a href="http://www.mybbchina.net/features">特色</a></li><li class="default"><a href="http://www.mybbchina.net/support">支持</a></li></ul></div></div><!--/search:no_index--><div id="content"><div id="sidebar" style="margin-top: -10px;"><div class="item"><div class="sidebar_top"><div class="right"></div></div><h3><a href="http://www.mybbchina.net/contact/piracy">举报盗版</a></h3><p>如果您怀疑某论坛违反了MyBB<a href="http://www.mybbchina.net/about/license">许可协议</a>,可以将其报告给我们。</p><p><a href="http://www.mybbchina.net/contact/piracy">报告违规</a></p><div class="sidebar_bottom"><div class="right"></div></div></div></div><div id="page" style="margin-top: -10px;"><div class="item"><h3>与我们取得联系</h3><h4>通过电子邮件</h4><p>填写以下表格，您可以与MyBB组取得联系。我们需要您做的是:填写您的详细联系方式，选择适当的主题，并输入您的邮件地址。</p><p><strong>请不要使用此表与我们联系关于支持MyBB。支持请求将被忽略。</strong>要寻求MyBB支持，见<a href="http://www.mybbchina.net/support">支持</a>。</p></div><div class="item"><form method="post" action=""><p class="legend"><strong>注:</strong> 标有星号(<em>*</em>)为必填字段</p>
<p><?=$msg?></p><fieldset><div class="form_container"><dl><dt><label for="name">全名: <em>*</em></label></dt><dd><input type="text" class="textbox" name="name" id="name" size="30" value="<?=$name?>" /></dd><dt><label for="email">Email 地址: <em>*</em></label></dt><dd><input type="text" class="textbox" name="email" id="email" size="30" value="<?=$email?>" /></dd><dt><label for="subject">标题: <em>*</em></label></dt><dd><select name="subject" id="subject"><option value="<?=$subject?>"><?=$subject?></option><option value="website" >网站相关（不正确的链接，错字，请求）</option><option value="license" >许可相关（问题，品牌自由购买）</option><option value="security" >安全相关（安全漏洞的报告）</option><option value="team-position" >现有团队成员的位置</option><option value="other" >其他（请在您的邮件中说明）</option></select></dd><dt><label for="message">正文: <em>*</em></label></dt><dd><textarea name="message" id="message" cols="40" rows="12"><?=$message?></textarea></dd><dt>验证码: <em>*</em></dt><dd style="width: 475px;"><img src="image.php?<?=time()?>" border="1" alt="验证码图片" style="margin-left: 10px; float: right; vertical-align: middle;" /><p>请输入图片中的文字在下面的文本框中。</p><p><input type="text" class="textbox captcha" name="image_string" id="image_string" size="10" /></p></dd></dl></div><p class="submit"><input type="submit" value="提交" /></p><input type="hidden" name="submit" value="1" /></fieldset></form></div></div></div><!--FF_IGNORE--><div id="footer"><p>&copy; <?=date('Y')?> MyBB Group, MyBB 中文站</p><p><a href="http://www.mybbchina.net/contact">联系</a> MyBB 中文站 &mdash; <em>寻求 MyBB 中文支持, 请访问 <a href="http://www.mybbchina.net/support">支持</a> 页面</em></p><br /></div></div></div><div id="shadow_footer"></div><!--FF_END_IGNORE--><div style="visibility:hidden;"><script src="http://www.mybbchina.net/tj.js"></script></div></body></html>
<?php

function cleanup($string)
	{
		$string = str_replace(array("\r", "\n", "\r\n"), "", $string);
		$string = trim($string);
		return $string;
	}
function utf8_encode_c($string)
	{
		$charset = 'utf-8';
		$encoded_string = $string;
		if(strtolower($charset) == 'utf-8' && preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f-\xff]/', $string))
		{
			// Define start delimimter, end delimiter and spacer
			$end = "?=";
			$start = "=?" . $charset . "?B?";
			$spacer = $end . ' ' . $start;

			// Determine length of encoded text within chunks and ensure length is even (should NOT use the my_strlen functions)
			$length = 75 - strlen($start) - strlen($end);
			$length = floor($length/4) * 4;

			// Encode the string and split it into chunks with spacers after each chunk
			$encoded_string = base64_encode($encoded_string);
			$encoded_string = chunk_split($encoded_string, $length, $spacer);

			// Remove trailing spacer and add start and end delimiters
			$spacer = preg_quote($spacer);
			$encoded_string = preg_replace("/" . $spacer . "$/", "", $encoded_string);
			$encoded_string = $start . $encoded_string . $end;
		}
		return $encoded_string;
	}
?>