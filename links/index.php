<?php
/*******************************************************************************
*  Title: LinkMan reciprocal link manager
*  Version: 1.5 @ May 22, 2008
*  Author: Klemen Stirn
*  Website: http://www.phpjunkyard.com
********************************************************************************
*  COPYRIGHT NOTICE
*  Copyright 2004-2008 Klemen Stirn. All Rights Reserved.
*
*  This script may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.
*
*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden. Using this code, in part or full,
*  to create competing scripts or products is expressly forbidden.
*
*  Obtain permission before redistributing this software over the Internet
*  or in any other medium. In all cases copyright and header must remain
*  intact. This Copyright is in full effect in any country that has
*  International Trade Agreements with the United States of America or
*  with the European Union.
*
*  Removing any of the copyright notices without purchasing a license
*  is illegal! To remove PHPJunkyard copyright notice you must purchase a
*  license for this script. For more information on how to obtain a license
*  please visit the site below:
*  http://www.phpjunkyard.com/copyright-removal.php
*******************************************************************************/

define('IN_SCRIPT',1);
require('settings.php');
require_once ('config.php');

require_once('header.txt');

echo '<div id="content">';

$lines = file($settings['linkfile']);

$print_featured = 0;
$first = 1;
$i = 0;
$featuredlinks = '';
$reciprocallinks = '';

foreach ($lines as $thisline)
{
    $thisline=trim($thisline);
    if (!empty($thisline))
    {
        $i++;
        list($name,$email,$title,$url,$recurl,$description,$featured,$pr)=explode($settings['delimiter'],$thisline);

        $show_url = $settings['show_url'] ? $url : '';

        if ($settings['show_pr'] == 1)
        {
            if (empty($pr)) {$pr=0;}
            $pr_code = '<img src="img/pr'.$pr.'.gif" width="40" height="5" alt="Google PageRank: '.$pr.'/10" border="0" style="vertical-align: middle;">';
        }
        else
        {
            $pr_code = '';
        }

        if ($settings['show_thumbshots'])
        {
            $thumb_code = '<img src="'.$settings['thumb_url'].rawurlencode($url).'" style="vertical-align: middle;" border="1" width="120" height="90" alt="'.$title.' 缩略图">';
        }
        else
        {
            $thumb_code = '';
        }

        if ($featured == 1)
        {

            if ($print_featured == 0)
            {
                $print_featured = 1;
                $first = 0;
            }

            $url      = ($settings['clean'] != 1) ? $url : 'go.php?url='.rawurlencode($url);
            $nofollow = ($settings['use_nofollow']==1) ? 'rel="nofollow"' : '';

            $featuredlinks .= '<li>'.$thumb_code.'<br />'.$pr_code.'&nbsp;<strong><a href="'.$url.'" target="_blank" '.$nofollow.'>'.$title.'</a></strong><br /><span style="color:#666;">'.$show_url.'<br />'.$description.'</span></li>';
        }
        else
        {
            if ($settings['show_thumbshots']!=1)
            {
                $thumb_code = '';
            }

            if ($print_featured == 1)
            {
                $print_featured = 0;
                $first = 1;
            }

            if ($first == 1)
            {
                $first = 0;
            }

            $url      = ($settings['clean'] == 0) ? $url : 'go.php?url='.rawurlencode($url);
            $nofollow = $settings['use_nofollow'] ? 'rel="nofollow"' : '';

            $reciprocallinks .= '<li>'.$thumb_code.$pr_code.'&nbsp;<strong><a href="'.$url.'" target="_blank" '.$nofollow.'>'.$title.'</a></strong><span style="color:#666;">&nbsp;'.$show_url.'&nbsp;'.$description.'</span></li>';
        }
    }
}

/* Close the table if at least one link is printed out */
if ($i)
{
	echo '<div id="sidebar">
			<div class="item">
				<div class="sidebar_top"><div class="right"></div></div>
				<h3>特色链接</h3>
				<ul>'
					.$featuredlinks.'
				</ul>
				<div class="sidebar_bottom"><div class="right"></div></div>		
			</div>
		</div>';
	echo '<div id="page">
			<div class="item">';
	if ($settings['show_form']) echo '<h3><a href="#addlink">提交你的网站 (实时自动加入)</a></h3>';
	echo '		<h4>友情链接</h4>
				<h5>本站所有友情链接全部加上了&lt;strong&gt;标签, 对贵站的SEO作用非常好! 是广大站长不可多得的自助友情链接.</h5>
				<ul>'
				.$reciprocallinks.'
				</ul>
			</div>
		</div>';
}
else
{
    echo '<p>还没有链接!</p>';
}

if ($settings['show_form'])
{
    if ($i < $settings['max_links'])
    {
    ?>
	<div id="page">
		<div class="item">
			<h3><a name="addlink"></a>&nbsp;<br /><strong>提交你的网站 (无需审核自动加入)
</strong></h3>
			<h4><strong>&raquo; 第1步：在你的网站首页添加我们的链接</strong></h4>
			<ul>
				<li>网站标题: <?php echo htmlspecialchars($settings['site_title']); ?></li>
				<li>网站 URL: <?php echo $settings['site_url']; ?></li>
				<li>网站描述: <?php echo htmlspecialchars($settings['site_desc']); ?></li>
				<li>文字链接: <a href="<?php echo $settings['site_url']; ?>" title="<?php echo htmlspecialchars($settings['site_desc']); ?>" target="_blank"><?php echo htmlspecialchars($settings['site_title']); ?></a></li>
<?php
if ($settings['checkrec'] == 'strict') {
?>
				<li>启用严格检测模式，以下代码请直接复制粘贴, <span style="color:red;">请勿修改</span>!</li>
<?php
}
else {
?>
				<li>可以直接复制以下代码</li>
<?php
}
?>
				<li><textarea rows="4" cols="60" onfocus="this.select()">&lt;strong&gt;&lt;a href=&quot;<?php echo $settings['site_url']; ?>&quot; title=&quot;<?php echo htmlspecialchars($settings['site_desc']); ?>&quot; target=&quot;_blank&quot;&gt;<?php echo htmlspecialchars($settings['site_title']); ?>&lt;/a&gt;&lt;/strong&gt;</textarea></li>
			</ul>
		</div>
		<div class="item">
			<h4><strong>&raquo; 第2步：提交你的链接</strong></h4>
			<p>所有字段必须填写，在提交下面的表格前请先完成 <strong>第1步</strong>，系统将自动检查你的网站上是否有我们的链接。<br />
			<span style="color:red;">*</span> 要求：你的网站首页 Google PR 值需要 <?=$settings['min_pr']?> 或以上。如果你的网站PR为<?=$settings['min_pr']+1?>或以上，请联系QQ(87164871)，交换首页链接。</p>
			<p><form method="post" action="addlink.php">
    			<table border="0">
    			<tr>
   				<td><strong>姓名:</strong></td>
   				<td><input type="text" name="name" size="40" maxlength="50"></td>
   				</tr>
   				<tr>
    			<td><strong>E-mail:</strong></td>
    			<td><input type="text" name="email" size="40" maxlength="50"></td>
    			</tr>
    			<tr>
    			<td><strong>网站标题:</strong></td>
    			<td><input type="text" name="title" size="40" maxlength="50"></td>
    			</tr>
    			<tr>
    			<td><strong>网站 URL:</strong></td>
    			<td><input type="text" name="url" maxlength="100" value="http://" size="40"></td>
    			</tr>
    			<tr>
    			<td><strong>反向链接 URL:</strong></td>
    			<td><input type="text" name="recurl" maxlength="100" value="http://" size="40"></td>
    			</tr>
    			<tr>
    			<td><strong>网站描述:</strong></td>
    			<td><input type="text" name="description" maxlength="200" size="60"></td>
    			</tr>
    			</table></p>
    			<p align="center"><input type="submit" value=" 提交链接 "></p>
    			</form>
				<p><span style="color:red;font-size:14px">注</span>：请确保我们的链接在你的网站上有效并符合要求（无 nofollow 标签），系统将随机检查我们的链接是否存在，如果不存在将自动从本页删除你的链接。
</p>
		</div>
	</div>
    <?php
    } // End if $settings['max_links'] < $i
    else
    {
    ?>
	<div id="page">
		<div class="item">
			<h3>&nbsp;<br /><strong>提交你的网站 (无需审核自动加入)</strong></h3>
			<h4><i>非常抱歉, 目前我们不接受新的友情链接申请</i></h4>
		</div>
	</div>
    <?php
    }
} // End if $settings['show_form']
echo '</div>';
require_once('footer.txt');
?>
