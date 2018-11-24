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
if (!function_exists("stripos")) {
  function stripos($str,$needle) {
   return strpos(strtolower($str),strtolower($needle));
  }
}

define('IN_SCRIPT',1);
require('settings.php');
require('config.php');
//ini_set('user_agent', 'LinkMan '.$settings['verzija'].' by http://www.phpjunkyard.com');

/* Accepting any more links? */
$lines = @file($settings['linkfile']);
if (count($lines)>$settings['max_links'])
{
    problem('我们暂时不接受新的链接, 给您带来不便我们深感抱歉!');
}

/* Check user input */
$name  = pj_input($_POST['name'],'请输入您的姓名!');
$email = pj_input($_POST['email'],'请输入您的 email 地址!');
if (!preg_match('/([\w\-]+\@[\w\-]+\.[\w\-]+)/',$email))
{
    problem('请输入有效的 email 地址!');
}
$title = pj_input($_POST['title'],'请输入您的网站标题!');
if (strlen($title)>50)
{
    problem('标题太长! 最多输入50个字符!');
}

$url   = pj_input($_POST['url'],'请输入您网站的 URL!');
if (!(preg_match('/(http:\/\/+[\w\-]+\.[\w\-]+)/i',$url)))
{
    problem('请输入有效的 URL!');
}

$recurl = pj_input($_POST['recurl'],'请输入反向链接URL, 上面有链向我们的链接的页面!');
if (!(preg_match('/(http:\/\/+[\w\-]+\.[\w\-]+)/i',$recurl)))
{
    problem('请输入有效的反向链接URL, 上面有链向我们的链接的页面!');
}

/* Compare URL and Reciprocal page URL */
$parsed_url = parse_url($url);
$parsed_rec = parse_url($recurl);
if ($parsed_url['host'] != $parsed_rec['host'])
{
    problem('反向链接必须放置在您提交的同一个域名下!');
}

$url    = str_replace('&amp;','&',$url);
$recurl = str_replace('&amp;','&',$recurl);

$description = pj_input($_POST['description'],'请输入您网站的简短描述!');
if (strlen($description)>200)
{
    problem('描述太长! 最多输入200个字符!');
}

/* Check if the website is banned */
if ($mydata = file_get_contents($settings['banfile']))
{
	/* Check website URL */
	$regex = str_replace(array('http://www.','http://'),'http://(www\.)?',$url);
	$regex = preg_replace('/index\.[^\/]+$/','',$regex);
	$regex = str_replace('/','\/',rtrim($regex,'/'));
	$regex = '/'.$regex.'\/?(index\.[^\/]+)?/i';
	if (preg_match($regex,$mydata))
	{
		problem('该网站已被屏蔽!');
	}

	/* Check reciprocal link URL */
	$regex = str_replace(array('http://www.','http://'),'http://(www\.)?',$recurl);
	$regex = preg_replace('/index\.[^\/]+$/','',$regex);
	$regex = str_replace('/','\/',rtrim($regex,'/'));
	$regex = '/'.$regex.'\/?(index\.[^\/]+)?/i';
	if (preg_match($regex,$mydata))
	{
		problem('该网站已被屏蔽!');
	}

	unset($mydata);
}

/* Check title and description for possible spam problems */
if (!isset($settings['verzija'])) {die('此代码不能胡乱使用');}
function pj_checkHTML($html) {
	$html = strtolower($html);
	$spam_words_hard = pj_spam_words_hard();
	foreach ($spam_words_hard as $sw) {
		if (strpos($html,$sw)!==false) { return false; }
	}
	return true;
}
function pj_checkTitleDesc($title, $desc, $url, $super) {
	$title = strtolower($title);
	$desc = strtolower($desc);
	$superlatives = pj_superlatives();
	$spam_words_soft = pj_spam_words_soft();
	$spam_words_hard = pj_spam_words_hard();
	if ($super) {
		foreach ($superlatives as $sw) {
			if (strpos($title,$sw)!==false || strpos($desc,$sw)!==false) {
				return 'superlatives';
			}
		}
	}
	foreach ($spam_words_hard as $sw) {
		if (strpos($title,$sw)!==false || strpos($desc,$sw)!==false) { return 'text'; }
		if (strpos($url,$sw)!==false) { return 'url'; }
	}
	$myscore = 0;
	foreach ($spam_words_soft as $sw) {
		if (strpos($title,$sw)!==false || strpos($desc,$sw)!==false) { $myscore+=50; }
	}
	if ($myscore > 100) { return 'text'; }
	return true;
}
function pj_spam_words_hard() {
	global $settings;
	//$arr1 = file($settings['spamfile']);
	$fp = fopen($settings['spamfile'], 'rb');
	$content = fread($fp, filesize($settings['spamfile']));
	fclose($fp);
	$arr1 = explode("\r\n", $content);
	$arr2 = array( 'adipex','advicer','baccarrat','blackjack','bllogspot','booker','byob','carbohydrate','car-rental-e-site','car-rentals-e-site', 'carisoprodol','casino','casinos','cialis','coolcoolhu','coolhu','credit-report-4u','cwas','cyclen','cyclobenzaprine', 'dating-e-site','day-trading','debt','debt-consolidation-consultant','drug','discreetordering','duty-free','dutyfree','equityloans','financing', 'fioricet','flowers-leading-site','freenet-shopping','freenet','gambling','gay','health-insurancedeals-4u','homeequityloans','homefinance', 'holdem','holdempoker','holdemsoftware','holdemtexasturbowilson','hotel-dealse-site','hotele-site','hotelse-site','incest', 'insurance-quotesdeals-4u','insurancedeals-4u','jrcreations','levitra','loan','macinstruct','mortgage-4-u','mortgagequotes','online-gambling', 'onlinegambling-4u','ottawavalleyag','ownsthis','palm-texas-holdem-game','paxil','penis','pharmacy','phentermine','poker','poker-chip', 'rental-car-e-site','roulette','sex','shemale','slot-machine', 'texas-holdem','thorcarlson','top-site','top-e-site','tramadol','trim-spa','ultram','valeofglamorganconservatives','viagra', 'vioxx','xanax','zolus ','replica' );
	$arr = $arr1 + $arr2;
	return $arr;
}
function pj_spam_words_soft() {
	global $settings;
	//$arr1 = file($settings['spamfile']);
	$fp = fopen($settings['spamfile'], 'rb');
	$content = fread($fp, filesize($settings['spamfile']));
	fclose($fp);
	$arr1 = explode("\r\n", $content);
	$arr2 = array( 'affordable','ambien','bargain','buy','chatroom','cheap','insurance','investment','poze', 'pre-approved','soma','taboo','teen','wholesale' );
	$arr = $arr1 + $arr2;
	return $arr;
}
function pj_superlatives() {
	global $settings;
	//$arr1 = file($settings['superlativesfile']);
	$fp = fopen($settings['superlativesfile'], 'rb');
	$content = fread($fp, filesize($settings['superlativesfile']));
	fclose($fp);
	$arr1 = explode("\r\n", $content);
	$arr2 = array( 'ablest','achiest','acutest','airworthiest','airiest','amplest','angriest','aptest','artiest','ashiest', 'worst','baggiest','baldest','balkiest','balmiest','barest','battiest','beadiest','beastliest','beefiest','best', 'biggest','bitterest','blackest','blandest','blankest','bleakest','bleariest','blindest','blithest','blondest', 'bloodthirstiest','bloodiest','blotchiest','blowziest','bluest','bluntest','blurriest','boggiest','boldest','bonniest', 'boniest','bossiest','botchiest','bounciest','brainiest','brambliest','brashest','brassiest','brattiest','bravest', 'brawniest','breathiest','breeziest','briefest','brightest','briniest','briskest','bristliest','broadest','broodiest', 'brownest','bruskest','bubbliest','bulgiest','bulkiest','bumpiest','bunchiest','burliest','bushiest','busiest', 'cagiest','calmest','campiest','canniest','catchiest','cattiest','chalkiest','chanciest','chastest','chattiest', 'cheapest','cheekiest','cheeriest','cheesiest','chewiest','chilliest','chintziest','choicest','choosiest','choppiest', 'chubbiest','chummiest','chunkiest','civilest','clammiest','classiest','cleanest','cleanliest','clearest','cleverest', 'clingiest','cliquiest','cloddiest','closest','cloudiest','clumpiest','clumsiest','coarsest','coldest','comeliest', 'comfiest','commonest','coolest','corkiest','corniest','costliest','coyest','coziest','crabbiest','crackliest', 'craftiest','craggiest','crankiest','crassest','crawliest','craziest','creakiest','creamiest','creepiest','crinkliest', 'crispest','crispiest','croakiest','crossest','croupiest','cruelest','crummiest','crunchiest','crustiest','cuddliest', 'curliest','curtest','curviest','cushiest','cutest','daffiest','daftest','daintiest','dampest','dandiest', 'dankest','darkest','deadest','deadliest','deafest','dearest','deepest','deftest','demurest','densest', 'dewiest','diciest','dimmest','dingiest','dippiest','direst','dirtiest','dizziest','dopiest','dottiest', 'doughtiest','doughiest','dowdiest','downiest','drabbest','draftiest','dreamiest','dreariest','dressiest','droopiest', 'drowsiest','driest','duckiest','dullest','dumbest','dumpiest','duskiest','dustiest','earliest','earthiest', 'easiest','edgiest','eeriest','emptiest','faintest','fairest','falsest','fanciest','farthest','furthest', 'fastest','fattest','fattiest','faultiest','feeblest','fewest','fiercest','fieriest','filmiest','filthiest', 'finest','firmest','fishiest','fittest','flabbiest','flakiest','flashiest','flattest','fleeciest','fleetest', 'flightiest','flimsiest','flippest','floppiest','fluffiest','foamiest','foggiest','folksiest','fondest','foolhardiest', 'foulest','foxiest','frailest','frankest','freakiest','freest','freshest','friendliest','frizziest','frostiest', 'frothiest','frumpiest','fullest','funkiest','funniest','furriest','fussiest','fuzziest','gabbiest','gamest', 'gamiest','gaudiest','gauntest','gauziest','gawkiest','gentlest','germiest','ghastliest','giddiest','gladdest', 'glairiest','glassiest','gleamiest','glibbest','gloomiest','glossiest','gluiest','glummest','godliest','best', 'goodliest','gooiest','goofiest','goriest','goutiest','grabbiest','grainiest','grandest','grassiest','gravest', 'grayest','greasiest','greatest','greediest','greenest','greyest','grimmest','grimiest','grippiest','gripiest', 'grisliest','grittiest','grizzliest','groggiest','grooviest','grossest','grouchiest','grubbiest','gruffest','grumpiest', 'grungiest','guiltiest','gummiest','gushiest','gustiest','gutsiest','hairiest','hammiest','handsomest','handiest', 'happiest','hardest','hardiest','harshest','hastiest','haughtiest','haziest','headiest','healthiest','heartiest', 'heaviest','heftiest','highest','hippest','hoarsest','hokiest','holiest','homeliest','homiest','hottest', 'huffiest','hugest','humblest','hungriest','huskiest','ickiest','iciest','idlest','illest','inkiest', 'itchiest','jauntiest','jazziest','jerkiest','jolliest','jounciest','juiciest','jumpiest','keenest','kindest', 'kindliest','kingliest','knobbiest','knottiest','kookiest','laciest','lamest','lankest','lankiest','largest', 'latest','laxest','laziest','leafiest','leakiest','leanest','leeriest','lengthiest','lightest','likeliest', 'lintiest','lithest','littlest','liveliest','loamiest','loftiest','loneliest','longest','looniest','loosest', 'lordliest','loudest','lousiest','loveliest','lowest','lowliest','luckiest','lumpiest','maddest','mangiest', 'manliest','marshiest','maturest','mealiest','meanest','measliest','meatiest','meekest','mellowest','merest', 'merriest','messiest','mightiest','mildest','milkiest','mintiest','minutest','mistiest','moistest','moldiest', 'moodiest','mopiest','mossiest','mouldiest','mousiest','mouthiest','muckiest','muddiest','muggiest','murkiest', 'mushiest','mustiest','naivest','narrowest','nastiest','nattiest','naughtiest','nearest','neatest','neediest', 'nerviest','newest','newsiest','nicest','niftiest','nimblest','nippiest','noblest','noisiest','nosiest', 'numbest','nuttiest','obscurest','oddest','oiliest','eldest','oldest','ooziest','palest','palmiest', 'paltriest','pastiest','patchiest','pearliest','pebbliest','peppiest','perkiest','pertest','peskiest','pettiest', 'phlegmiest','phoniest','pickiest','piggiest','pimpliest','pinkest','pithiest','plainest','pluckiest','plumpest', 'plumiest','plushest','pointiest','pokiest','politest','poorest','porkiest','portliest','poshest','prettiest', 'prickliest','primmest','prissiest','profoundest','prosiest','proudest','pudgiest','puffiest','pulpiest','punchiest', 'puniest','purest','pushiest','quaintest','quakiest','queasiest','quickest','quietest','quirkiest','rainiest', 'rangiest','rankest','rarest','raspiest','rattiest','rawest','readiest','realest','reddest','reediest', 'remotest','richest','ripest','riskiest','ritziest','rockiest','roomiest','rosiest','rottenest','roughest', 'rowdiest','ruddiest','rudest','runniest','runtiest','rustiest','saddest','safest','sagest','saggiest', 'saintliest','saltiest','sandiest','sanest','sappiest','sassiest','sauciest','scabbiest','scaliest','scantiest', 'scarcest','scariest','schmaltziest','scraggliest','scrappiest','scratchiest','scrawniest','screechiest','scrimpiest','scrubbiest', 'scruffiest','scurviest','securest','seemliest','severest','shabbiest','shadiest','shaggiest','shakiest','shallowest', 'shapeliest','sharpest','shiftiest','shiniest','shoddiest','shortest','showiest','shrewdest','shrillest','shyest', 'sickest','sickliest','sightliest','silkiest','silliest','siltiest','simplest','sincerest','sketchiest','skinniest', 'slangiest','slaphappiest','sleekest','sleepiest','sleetiest','slenderest','slickest','slightest','slimmest','slimiest', 'slipperiest','sloppiest','sloshiest','slouchiest','slowest','sludgiest','slushiest','sliest','slyest','smallest', 'smartest','smeariest','smelliest','smoggiest','smokiest','smoothest','smudgiest','smuggest','snakiest','snappiest', 'snarliest','sneakiest','sneeziest','snidest','snippiest','snobbiest','snoopiest','snootiest','snowiest','snuggest', 'soapiest','soberest','softest','soggiest','solidest','soonest','sootiest','soppiest','sorest','sorriest', 'soundest','soupiest','sourest','sparest','sparsest','speediest','spiciest','spiffiest','spikiest','spindliest', 'spiniest','splashiest','splotchiest','spongiest','sportiest','spottiest','sprightliest','springiest','spriest','spryest', 'spunkiest','squabbiest','squarest','squashiest','squattiest','squeakiest','squirmiest','stagiest','staidest','stalest', 'starchiest','starkest','starriest','stateliest','steadiest','stealthiest','steamiest','steeliest','steepest','sternest', 'stickiest','stiffest','stillest','stingiest','stinkiest','stockiest','stodgiest','stoniest','stormiest','stoutest', 'straggliest','straightest','strangest','streakiest','stretchiest','strictest','stringiest','strongest','stubbornest','stubbiest', 'stuffiest','stumpiest','stupidest','sturdiest','sudsiest','sulkiest','sultriest','sunniest','supplest','surest', 'surliest','sveltest','swampiest','swankiest','swarthiest','sweatiest','sweetest','swiftest','tackiest','talkiest', 'tallest','tamest','tannest','tangiest','tardiest','tartest','tastiest','tautest','tawdriest','tawniest', 'teariest','teeniest','tensest','tersest','testiest','tetchiest','thickest','thinnest','thirstiest','thorniest', 'threadiest','thriftiest','throatiest','tidiest','tightest','tinniest','tiniest','tipsiest','toothiest','touchiest', 'toughest','trendiest','trickiest','trimmest','tritest','truest','trustiest','twangiest','tweediest','ugliest', 'unhealthiest','unruliest','vaguest','vilest','wackiest','wannest','warmest','wartiest','wariest','waviest', 'waxiest','weakest','wealthiest','weariest','weediest','weepiest','weightiest','weirdest','wettest','wheeziest', 'whiniest','whitest','widest','wildest','wiliest','windiest','wintriest','wiriest','wisest','wispiest', 'wittiest','wobbliest','woodsiest','woodiest','woolliest','wooziest','wordiest','worldliest','wormiest','worthiest', 'wriggliest','wriest','yeastiest','youngest','yummiest','zaniest','zippiest','最棒的','最好的','最强的','最多的' );
	$arr = $arr1 + $arr2;
	return $arr;
} 
if ($settings['spam_filter'])
{
    $test = pj_checkTitleDesc($title, $description, $url, $settings['superlatives']);
    if ($test === true)
    {
        $test = '';
    }
    elseif ($test == 'superlatives')
    {
        problem('请不要在网站标题和描述中使用这一类形容词 (best, biggest, cheapest, largest)!');
    }
    elseif ($test == 'text')
    {
        problem('您的链接没有通过 SPAM 测试, 我们不得不拒绝.');
    }
    elseif ($test == 'url')
    {
        problem('您的链接没有通过 SPAM 测试, 我们不得不拒绝.');
    }
}

if ($settings['autosubmit'])
{
    session_start();
    if (empty($_SESSION['checked']))
    {
        $_SESSION['checked']  = 'N';
        $_SESSION['secnum']   = rand(10000,99999);
        $_SESSION['checksum'] = $_SESSION['secnum'].$settings['filter_sum'].date('dmy');
    }
    if ($_SESSION['checked'] == 'N')
    {
        print_secimg();
    }
    elseif ($_SESSION['checked'] == $settings['filter_sum'])
    {
        $_SESSION['checked'] = 'N';
        $secnumber = pj_isNumber($_POST['secnumber']);
        if(empty($secnumber))
        {
            print_secimg(1);
        }
        if (!check_secnum($secnumber,$_SESSION['checksum']))
        {
            print_secimg(2);
        }
    }
    else
    {
        problem('内部脚本错误. 参数错误!');
    }
}

/* Check for duplicate links */
if ($settings['block_duplicates'])
{
    $mydata = file_get_contents($settings['linkfile']);

    /* Check website URL */
    $regex = str_replace(array('http://www.','http://'),'http://(www\.)?',$url);
    $regex = preg_replace('/index\.[^\/]+$/','',$regex);
    $regex = str_replace('/','\/',rtrim($regex,'/'));
    $regex = '/'.$regex.'\/?(index\.[^\/]+)?\s/i';
    if (preg_match($regex,$mydata))
    {
        problem('请不要多次重复提交同一个网址!');
    }

    /* Check reciprocal link URL */
    $regex = str_replace(array('http://www.','http://'),'http://(www\.)?',$recurl);
    $regex = preg_replace('/index\.[^\/]+$/','',$regex);
    $regex = str_replace('/','\/',rtrim($regex,'/'));
    $regex = '/'.$regex.'\/?(index\.[^\/]+)?\s/i';
    if (preg_match($regex,$mydata))
    {
        problem('请不要多次提交使用一个反向链接的多个网站!');
    }

    unset($mydata);
}

/* Get HTML code of the reciprocal link URL */
$html = get_url_content($recurl, $settings['charset']) or problem('无法打开远程 URL!');
//$html = strtolower($html);
$html = preg_replace("'<!--[/!]*?[^<>]*?>'si", "", $html);
//echo $html;
$site_url = strtolower($settings['site_url']);

/* Block links with the meta "robots" noindex or nofollow tags? */
if ($settings['block_meta_rob']==1 && preg_match('/<meta([^>]+)(noindex|nofollow)(.*)>/siU',$html,$meta))
{
    problem(
        '请不要将反向链接放在具有 robots noindex or nofollow 标签的页面:<br />'.
        htmlspecialchars($meta[0])
    );
}

$found    = 0;
$nofollow = 0;
if ($settings['checkrec'] == 'strict') {
	if (stripos($html, '<strong><a href="'.$settings['site_url'].'" title="'.$settings['site_desc'].'" target="_blank">'.$settings['site_title'].'</a></strong>') !== false || stripos($html, '<strong><a href="'.$settings['site_url'].'" title="'.$settings['site_desc'].'">'.$settings['site_title'].'</a></strong>') !== false) $found = 1;
}
else {
if (preg_match_all('/<a\s[^>]*href=([\"\']??)([^" >]*?)\\1([^>]*)>/siU', $html, $matches, PREG_SET_ORDER)) {
    foreach($matches as $match)
    {
        if ($match[2] == $settings['site_url'] || $match[2] == $settings['site_url'].'/')
        {
            $found = 1;
            if (strstr($match[3],'nofollow'))
            {
                $nofollow = 1;
            }
            break;
        }
    }
}
}

if ($found == 0)
{
	if ($settings['checkrec'] == 'strict') {
    problem(
        '在您的反向链接页面 (<a href="'.$recurl.'">'.
        $recurl.'</a>), 没有找到我们的完整链接代码!<br>
		<textarea rows="4" cols="60" onfocus="this.select()">&lt;strong&gt;&lt;a href=&quot;'.$settings['site_url'].'&quot; title=&quot;'.htmlspecialchars($settings['site_desc']).'&quot; target=&quot;_blank&quot;&gt;'.htmlspecialchars($settings['site_title']).'&lt;/a&gt;&lt;/strong&gt;</textarea>
		<br>请确定您在提交链接之前已经正确放置了我们的链接(不要修改链接代码)!'
    );
	}
	else {
    problem(
        '在您的反向链接页面 (<a href="'.$recurl.'">'.
        $recurl.'</a>), 没有找到我们的链接 (<a href="'.$settings['site_url'].'">'.$settings['site_url'].
        '</a>)!<br><br>请确定您在提交链接之前已经正确放置了我们的链接(不要修改链接代码)!'
    );
	}
}

/* Block links with rel="nofollow" attribute? */
if ($settings['block_nofollow'] && $nofollow == 1)
{
    problem('请不要对反向链接使用 rel=&quot;nofollow&quot; 链接属性!');
}

/* Check Google PageRank */
if ($settings['show_pr'] || $settings['min_pr'] || $settings['min_pr_rec'])
{
    require('pagerank.php');
    $pr = getpr($url);
    $pr = empty($pr) ? 0 : $pr;

    if ($settings['min_pr'] && ($pr < $settings['min_pr']))
    {
        problem('很遗憾, 我们目前只接受 Google PageRank '.$settings['min_pr'].'/10 或更高的网站. 请几个月后重新提交!');
    }

    if ($settings['min_pr_rec'])
    {
        $pr_rec = getpr($recurl);
        $pr_rec = empty($pr_rec) ? 0 : $pr_rec;
        if ($pr_rec < $settings['min_pr_rec'])
        {
            problem('请将反向链接 <a href="'.$settings['site_url'].'">'.$settings['site_url'].'</a> 放在 Google PageRank '.$settings['min_pr_rec'].'/10 或更高的页面上.');
        }
    }
}

$replacement = "$name$settings[delimiter]$email$settings[delimiter]$title$settings[delimiter]$url$settings[delimiter]$recurl$settings[delimiter]$description$settings[delimiter]0$settings[delimiter]$pr\r\n";

if ($settings['add_to'] == 0)
{
    /* Make sure new link is added after any featured ones */
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
else
{
    $fp = fopen($settings['linkfile'],'ab') or problem('Couldn\'t open links file for appending! Please CHMOD all txt files to 666 (rw-rw-rw)!');
    flock($fp, LOCK_EX);
    fputs($fp,$replacement);
    flock($fp, LOCK_UN);
    fclose($fp);
}

if($settings['notify'] == 1)
{
$message = "您好,

刚才有人在 $settings[site_url] 提交了网站

链接详情:

姓名: $name
E-mail: $email
URL: $url
反向链接: $recurl
标题: $title
描述:
$description


信息结束

";
	$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
	$headers .= "From: ".utf8_encode_c(cleanup($name))." <$email>\n";
	$headers .= "Reply-To: ".utf8_encode_c(cleanup($name))." <$email>\n";
    mail($settings['admin_email'],utf8_encode_c(cleanup('有新链接提交 - '.$title)),$message,$headers);
}

require_once('header.txt');
?>
<p align="center"><b>您的链接已经提交!</b></p>
<p>&nbsp;</p>
<p align="center">谢谢你, 您的链接已经成功提交到我们的链接交换系统 (如果在链接首页没有看到您的链接, 请刷新)!</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="center"><a href="<?php echo $settings['site_url']; ?>">返回上一页</a></p>
<?
require_once('footer.txt');
exit();

function problem($problem) {
	global $settings;
require_once('header.txt');
echo '
    <p align="center"><font color="#FF0000"><b>错误</b></font></p>
    <p>&nbsp;</p>
    <p align="center">'.$problem.'</p>
    <p>&nbsp;</p>
    <p align="center"><a href="javascript:history.go(-1)">返回上一页</a></p>
';
require_once('footer.txt');
exit();
}

function print_secimg($message=0) {
global $settings;
$_SESSION['checked']=$settings['filter_sum'];
require_once('header.txt');
?>
<p>&nbsp;</p>

<p align="center"><b>人类验证</b></p>

<div align="center"><center>
<table border="0">
<tr>
<td>
    <form action="addlink.php?<?php echo strip_tags(SID); ?>" method="POST" name="form">

    <hr>
    <?php
    if ($message == 1)
    {
        echo '<p align="center"><font color="#FF0000"><b>请输入验证码</b></font></p>';
    }
    elseif ($message == 2)
    {
        echo '<p align="center"><font color="#FF0000"><b>验证码错误. 请重新输入</b></font></p>';
    }
    ?>

    <p>为了阻止机器人自动提交，这里是人类安全验证。请输入下面的安全验证码，然后点击“继续”按钮。</p>

    <p>&nbsp;</p>

    <p>验证码: <b><?php echo $_SESSION['secnum']; ?></b><br>
    请输入上面显示的验证码:
    <input type="text" size="7" name="secnumber" maxlength="5"></p>

    <p>&nbsp;
    <?php
    foreach ($_POST as $k=>$v)
    {
        if ($k == 'secnumber')
        {
            continue;
        }
        echo '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars(stripslashes($v)).'">';
    }
    ?>
    </p>

    <p align="center"><input type="submit" value=" 继续 "></p>

    <hr>

    </form>
</td>
</tr>
</table>
</center></div>

<p>&nbsp;</p>
<p>&nbsp;</p>

<?php
require_once('footer.txt');
exit();
}

function check_secnum($secnumber,$checksum) {
    global $settings;
    $secnumber .= $settings['filter_sum'].date('dmy');
    if ($secnumber == $checksum)
    {
        unset($_SESSION['checked']);
        return true;
    }
    else
    {
        return false;
    }
}

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

function get_url_content($url, $charset) {
	if(function_exists('file_get_contents')) {
		$data = @file_get_contents($url);
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
?>
