<?php
require_once './inc/config.inc.php';
require_once './inc/common.inc.php';
require_once './inc/feedback.php';

//$fid = array(2,3,4,5,7,8,9,10,12,13,15,16,17,18);

$url = $_SERVER['HTTP_REFERER'];
//$url = $siteurl.'/';
$url = explode('/', $url);
$surl = explode('/', $siteurl);
$url_limit = count($url);
$surl_limit = count($surl);
//echo $url_limit;
//echo $surl_limit;
for ($i=$surl_limit; $i<$url_limit-1; $i++) {
	$channel .= $url[$i].'/';
}
$channel = substr($channel, 0, -1);
if (!$_SERVER['HTTP_REFERER']) $channel = $_GET['channel'];
//echo $_SERVER['HTTP_REFERER'];
//echo $channel;

$path = "./".$channel;
$file = $path."/index.html";
if ($debug == "on" || time() - filemtime($file) > $gs_time) {
switch ($channel) {
	case '' :
		$title = 'MyBB中文站';
		$homepage = 'active';
		break;
	case 'about' :
		$title = 'MyBB中文站 - 关于 中文MyBB';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">联系方式</a></li>
				<li><a href="'.$siteurl.'/about/mybb">关于 中文MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community">关于社区</a></li>
				<li><a href="'.$siteurl.'/about/team">关于团队</a></li>
				<li><a href="'.$siteurl.'/about/license">中文MyBB 许可</a></li>
				<li><a href="'.$siteurl.'/about/donations">支持 中文MyBB</a></li>';
		break;
	case 'about/mybb' :
		$title = 'MyBB中文站 - 关于 中文MyBB: 中文MyBB';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">联系方式</a></li>
				<li><a href="'.$siteurl.'/about/mybb"><strong>关于 中文MyBB &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/community">关于社区</a></li>
				<li><a href="'.$siteurl.'/about/team">关于团队</a></li>
				<li><a href="'.$siteurl.'/about/license">中文MyBB 许可</a></li>
				<li><a href="'.$siteurl.'/about/donations">支持 中文MyBB</a></li>';
		break;
	case 'about/community' :
		$title = 'MyBB中文站 - 关于 中文MyBB: 社区';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">联系方式</a></li>
				<li><a href="'.$siteurl.'/about/mybb">关于 中文MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community"><strong>关于社区 &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/team">关于团队</a></li>
				<li><a href="'.$siteurl.'/about/license">中文MyBB 许可</a></li>
				<li><a href="'.$siteurl.'/about/donations">支持 中文MyBB</a></li>';
		break;
	case 'about/team' :
		$title = 'MyBB中文站 - 关于 中文MyBB: 团队';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">联系方式</a></li>
				<li><a href="'.$siteurl.'/about/mybb">关于 中文MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community">关于社区</a></li>
				<li><a href="'.$siteurl.'/about/team"><strong>关于团队 &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/license">中文MyBB 许可</a></li>
				<li><a href="'.$siteurl.'/about/donations">支持 中文MyBB</a></li>';
		break;
	case 'about/license' :
		$title = 'MyBB中文站 - 许可协议';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">联系方式</a></li>
				<li><a href="'.$siteurl.'/about/mybb">关于 中文MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community">关于社区</a></li>
				<li><a href="'.$siteurl.'/about/team">关于团队</a></li>
				<li><a href="'.$siteurl.'/about/license"><strong>中文MyBB 许可 &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/about/donations">支持 中文MyBB</a></li>';
		break;
	case 'about/donations' :
		$title = 'MyBB中文站 - 关于 中文MyBB: 捐赠';
		$about = 'active';
		$nav = '<li><a href="'.$siteurl.'/contact">联系方式</a></li>
				<li><a href="'.$siteurl.'/about/mybb">关于 中文MyBB</a></li>
				<li><a href="'.$siteurl.'/about/community">关于社区</a></li>
				<li><a href="'.$siteurl.'/about/team">关于团队</a></li>
				<li><a href="'.$siteurl.'/about/license">中文MyBB 许可</a></li>
				<li><a href="'.$siteurl.'/about/donations"><strong>支持 中文MyBB &raquo;</strong></a></li>';
		break;
	case 'downloads' :
		$title = 'MyBB中文站 - 下载';
		$downloads = 'active';
		break;
	case 'downloads/translations' :
		$title = 'MyBB中文站 - 下载 - 官方翻译语言文件';
		$downloads = 'active';
		break;
	case 'features' :
		$title = 'MyBB中文站 - 功能';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour">更新内容?</a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour' :
		$title = 'MyBB中文站 - MyBB 1.4 功能浏览';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/admin-cp' :
		$title = 'MyBB中文站 - 功能浏览: 管理员控制面板';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/mod-cp' :
		$title = 'MyBB中文站 - 功能浏览: 版主控制面板';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/themes' :
		$title = 'MyBB中文站 - 功能浏览: 内置主题模板编辑器';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/quick-reply' :
		$title = 'MyBB中文站 - 功能浏览: 快速回复';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/db-support' :
		$title = 'MyBB中文站 - 功能浏览: 数据库支持';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/calendar' :
		$title = 'MyBB中文站 - 功能浏览: 日历';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/warning' :
		$title = 'MyBB中文站 - 功能浏览: 警告';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/promotions' :
		$title = 'MyBB中文站 - 功能浏览: 晋升';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/bot-detection' :
		$title = 'MyBB中文站 - 功能浏览: 搜索引擎蜘蛛/爬虫探测';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/sef-urls' :
		$title = 'MyBB中文站 - 功能浏览: 搜索引擎友好的URL';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/mass-mail' :
		$title = 'MyBB中文站 - 功能浏览: 群发邮件';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/plugins' :
		$title = 'MyBB中文站 - 功能浏览: 插件';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/tasks' :
		$title = 'MyBB中文站 - 功能浏览: 任务';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/ui' :
		$title = 'MyBB中文站 - 功能浏览: 用户界面';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/feature-tour/performance' :
		$title = 'MyBB中文站 - 功能浏览: 性能';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/whats-new' :
		$title = 'MyBB中文站 - MyBB 1.4 有什么更新';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour"><strong>更新内容? &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/ease-of-use' :
		$title = 'MyBB中文站 - 功能亮点: 简单易用';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour">更新内容?</a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use"><strong>简单易用 &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/administration' :
		$title = 'MyBB中文站 - 功能亮点: 管理员控制面板';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour">更新内容?</a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration"><strong>管理 &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/plugin-system' :
		$title = 'MyBB中文站 - 功能亮点: 插件系统';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour">更新内容?</a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system"><strong>插件系统 &raquo;</strong></a></li>
				<li><a href="'.$siteurl.'/features/more-features">功能概况</a></li>';
		break;
	case 'features/more-features' :
		$title = 'MyBB中文站 - 功能亮点: 概况';
		$features = 'active';
		$nav = '<li><a href="'.$siteurl.'/features/feature-tour">更新内容?</a></li>
				<li><a href="'.$siteurl.'/features/ease-of-use">简单易用</a></li>
				<li><a href="'.$siteurl.'/features/administration">管理</a></li>
				<li><a href="'.$siteurl.'/features/plugin-system">插件系统</a></li>
				<li><a href="'.$siteurl.'/features/more-features"><strong>功能概况 &raquo;</strong></a></li>';
		break;
	case 'support' :
		$title = 'MyBB中文站 - 支持';
		$support = 'active';
		break;
	default :
		$title = 'MyBB中文站';
		//$homepage = 'active';
		break;
}
		$homepage = $homepage ? $homepage : 'default';
		$about = $about ? $about : 'default';
		$downloads = $downloads ? $downloads : 'default';
		$community = $community ? $community : 'default';
		$features = $features ? $features : 'default';
		$support = $support ? $support : 'default';
$userfeedback = $feedback[rand(0, count($feedback))];
//远程获取dy
$indexnews = get_url_content('http://community.mybbchina.net/dy.php?limit=4&fid=2,3,4,5,7,8,9,10,12,13,15,16,17,18'/*.$fid[rand(0, count($fid)-1)]*/, $charset);
//加载
$channel = $channel ? $channel : 'index';
$head_file = $template_folder.'/head.html';
$body_file = $template_folder.'/'.str_replace('/', '_', $channel).'_body.html';
$foot_file = $template_folder.'/foot.html';
$license_file = $template_folder.'/license.txt';
$fp = fopen($head_file, 'rb');
$content = fread($fp, filesize($head_file));
$fp = fopen($body_file, 'rb');
$content .= fread($fp, filesize($body_file));
$fp = fopen($foot_file, 'rb');
$content .= fread($fp, filesize($foot_file));
$fp = fopen($license_file, 'rb');
$license = fread($fp, filesize($license_file));
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
$content = str_replace('[mybbchina:index news]', $indexnews, $content);
$content = str_replace('[mybbchina:copyrightdate]', date('Y'), $content);
//压缩html代码
$content = str_replace("\r\n", '', $content);
$content = str_replace("\r", '', $content);
$content = str_replace("\n", '', $content);
$content = str_replace("\t", '', $content);
$content = preg_replace("'  '", '', $content);
//加载license
$content = str_replace('[mybbchina:license]', $license, $content);
//生成文件夹及子文件夹
if (!file_exists($file)) mkpath($path);
//生成缓存文件
$fp = fopen($file, "wb");
fwrite($fp, $content);
fclose($fp);
}
//if (file_exists($file)) require_once $file;
?>