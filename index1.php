<?php
require_once './inc/config.inc.php';
require_once './inc/common.inc.php';
require_once './inc/feedback.php';

$channel = $_GET['channel'] ? $_GET['channel'] : '';
//$template_path = explode("/", $channel);
//echo $template_path[0];
/*
switch (count($template_path)) {
	case 1 :
		if ($template_path[0]) {
			$body_file = $template_path[0].'/body.html';
		} else {
			$body_file = 'body.html';
		}
		break;
	case 2 :
		$body_file = $template_path[0].'/'.$template_path[1].'-body.html';
		break;
	default :
		$body_file = 'body.html';
		break;
}
*/
$path = $cache_folder."/".$channel;
$cachefile = $path."/index.html";
if ($debug == "on" || time() - filemtime($cachefile) > $gs_time) {
switch ($channel) {
	case '' :
		$title = '中文MyBB - 欢迎访问 MyBB中文站 - 免费的 PHP 和 MySQL 论坛软件';
		$homepage = 'active';
		break;
	case 'about' :
		$title = '中文MyBB - 关于 中文MyBB';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">Contact</a></li>
				<li><a href="'.$siteurl.'/about/mybb.html">About MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community.html">About the Community</a></li>
				<li><a href="'.$siteurl.'/about/team.html">About the Team</a></li>
				<li><a href="'.$siteurl.'/about/license.html">MyBB License</a></li>
				<li><a href="'.$siteurl.'/about/donations.html">Support MyBB</a></li>';
		break;
	case 'about/mybb' :
		$title = '中文MyBB - 关于 中文MyBB: 中文MyBB';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">Contact11</a></li>
				<li><a href="'.$siteurl.'/about/mybb.html"><strong>About MyBB &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/community.html">About the Community</a></li>
				<li><a href="'.$siteurl.'/about/team.html">About the Team</a></li>
				<li><a href="'.$siteurl.'/about/license.html">MyBB License</a></li>
				<li><a href="'.$siteurl.'/about/donations.html">Support MyBB</a></li>';
		break;
	case 'about/community' :
		$title = '中文MyBB - 关于 中文MyBB: 社区';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">Contact</a></li>
				<li><a href="'.$siteurl.'/about/mybb.html">About MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community.html"><strong>About the Community &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/team.html">About the Team</a></li>
				<li><a href="'.$siteurl.'/about/license.html">MyBB License</a></li>
				<li><a href="'.$siteurl.'/about/donations.html">Support MyBB</a></li>';
		break;
	case 'about/team' :
		$title = '中文MyBB - 关于 中文MyBB: 团队';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">Contact</a></li>
				<li><a href="'.$siteurl.'/about/mybb.html">About MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community.html">About the Community</a></li>
				<li><a href="'.$siteurl.'/about/team.html"><strong>About the Team &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/license.html">MyBB License</a></li>
				<li><a href="'.$siteurl.'/about/donations.html">Support MyBB</a></li>';
		break;
	case 'about/license' :
		$title = '中文MyBB - 许可协议';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">Contact</a></li>
				<li><a href="'.$siteurl.'/about/mybb.html">About MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community.html">About the Community</a></li>
				<li><a href="'.$siteurl.'/about/team.html">About the Team</a></li>
				<li><a href="'.$siteurl.'/about/license.html"><strong>MyBB License &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/donations.html">Support MyBB</a></li>';
		break;
	case 'about/donations' :
		$title = '中文MyBB - 关于 中文MyBB: 捐赠';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">Contact</a></li>
				<li><a href="'.$siteurl.'/about/mybb.html">About MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community.html">About the Community</a></li>
				<li><a href="'.$siteurl.'/about/team.html">About the Team</a></li>
				<li><a href="'.$siteurl.'/about/license.html">MyBB License</a></li>
				<li><a href="'.$siteurl.'/about/donations.html"><strong>Support MyBB &raquo;</strong></a></li>';
		break;
	case 'downloads' :
		$title = '中文MyBB - 下载';
		$downloads = 'active';
		break;
	case 'features' :
		$title = '中文MyBB - 欢迎访问 MyBB中文站 - 免费的 PHP 和 MySQL 论坛软件';
		$homepage = 'active';
		break;
	case 'support' :
		$title = '中文MyBB - 欢迎访问 MyBB中文站 - 免费的 PHP 和 MySQL 论坛软件';
		$homepage = 'active';
		break;
	default :
		$title = '中文MyBB - 欢迎访问 MyBB中文站 - 免费的 PHP 和 MySQL 论坛软件';
		$homepage = 'active';
		break;
}
		$homepage = $homepage ? $homepage : 'default';
		$about = $about ? $about : 'default';
		$downloads = $downloads ? $downloads : 'default';
		$community = $community ? $community : 'default';
		$features = $features ? $features : 'default';
		$support = $support ? $support : 'default';
$userfeedback = $feedback[rand(0, 5)];
//加载
$body_file = $channel.'./body.html';
$fp = fopen('head.html', 'rb');
$content = fread($fp, filesize('head.html'));
$fp = fopen($body_file, 'rb');
$content .= fread($fp, filesize($body_file));
$fp = fopen('foot.html', 'rb');
$content .= fread($fp, filesize('foot.html'));
fclose($fp);
//替换
$content = str_replace('[mybbchina:siteurl]', $siteurl, $content);
$content = str_replace('[mybbchina:title]', $title, $content);
$content = str_replace('[mybbchina:homepage]', $homepage, $content);
$content = str_replace('[mybbchina:about]', $about, $content);
$content = str_replace('[mybbchina:downloads]', $downloads, $content);
$content = str_replace('[mybbchina:community]', $community, $content);
$content = str_replace('[mybbchina:features]', $features, $content);
$content = str_replace('[mybbchina:support]', $support, $content);
$content = str_replace('[mybbchina:nav]', $nav, $content);
$content = str_replace('[mybbchina:userfeedback]', $userfeedback, $content);
$content = str_replace('[mybbchina:copyrightdate]', date('Y'), $content);
//生成文件夹及子文件夹
if (!file_exists($cachefile)) mkpath($path);
//生成缓存文件
$fp = fopen($cachefile, "wb");
fwrite($fp, $content);
fclose($fp);
}
if (file_exists($cachefile)) require_once $cachefile;
?>