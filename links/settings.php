<?php
// SETUP YOUR LINK MANAGER
// Detailed information found in the readme.htm file
// File last modified: May 22nd 2008 (LinkMan v. 1.5)

/* Password for admin area */
$settings['apass']='admin';

/* Your website URL */
$settings['site_url']='http://www.mybbchina.net';

/* Your website title */
$settings['site_title']='myBB中文站大调';

/* Your website description */
$settings['site_desc']='MyBB中文官方网站, 免费的PHP和MySQL论坛软件系统.';

/* Show "add a link" form on the bottom of links page? 1 = YES, 0 = NO */
$settings['show_form']=1;

/* Send you an e-mail everytime someone adds a link? 1=YES, 0=NO */
$settings['notify']=0;

/* Admin e-mail */
$settings['admin_email']='you@yourdomain.com';

/* Maximum number of links */
$settings['max_links']=50;

/* Prevent automated submissions (recommended YES)? 1 = YES, 0 = NO */
$settings['autosubmit']=1;

/* Checksum - just type some digits and chars. Used to help prevent SPAM */
$settings['filter_sum']='211097';

/* Enable SPAM filter? 1=YES, 0=NO */
$settings['spam_filter']=1;

/* Block superlatives from title and description? 1=YES, 0=NO */
$settings['superlatives']=1;

/* Use normal links? 0=NORMAL, 1=REDIRECT ALL, 2=REDIRECT RECIPROCAL ONLY */
$settings['clean']=0;

/* Add rel="nofollow" attribute to links? 0=NO, 1=YES, 2=FOR RECIPROCAL ONLY */
$settings['use_nofollow']=0;

/* Where to add new links? 0 = top of list, 1 = end of list */
$settings['add_to']=1;

/* Name of the file where link URLs and other info is stored */
$settings['linkfile']='db-links.txt';

/* Name of the file where banned websites are stored */
$settings['banfile']='db-banned_websites.txt';

/* Display website URL after Title? 1=YES, 0=NO */
$settings['show_url']=1;

/* Display Google PageRank? 0=NO, 1=YES, 2=IN ADMIN PANEL ONLY */
$settings['show_pr']=1;

/* Minimum Google PageRank to accept website? A value from 0 to 10 */
$settings['min_pr']=0;

/* Minimum Google PageRank of reciprocal links page? A value from 0 to 10 */
$settings['min_pr_rec']=0;

/* Block links with rel="nofollow"? 1=YES, 0=NO */
$settings['block_nofollow']=1;

/* Block link from pages with meta robots nonidex or nofollow? 1=YES, 0=NO */
$settings['block_meta_rob']=1;

/* Block duplicate entries (same website added more than once)? 1=YES, 0=NO */
$settings['block_duplicates']=1;

/* Display website thumbnails? 0=NO, 1=YES, 2=FEATURED LINKS ONLY */
$settings['show_thumbshots']=2;

/* URL of your thumbshots service */
$settings['thumb_url']='http://open.thumbshots.org/image.pxf?url=';

/* Turn debug mode on? 1=YES, 0=NO */
$settings['debug']=0;

/* Which sections to hide by default */
$settings['hide']=array('divRate','divUpdate',);


/*******************
* DO NOT EDIT BELOW
*******************/
$settings['verzija']='1.5';
$settings['delimiter']="\t";

if (!defined('IN_SCRIPT')) {die('Invalid attempt!');}
if ($settings['debug'])
{
    error_reporting(E_ALL ^ E_NOTICE);
}
else
{
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
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
    if (preg_match("/\D/",$in) || $in=='')
    {
        if ($error)
        {
            problem($error);
        }
        else
        {
            return '0';
        }
    }
    return $in;
}
?>