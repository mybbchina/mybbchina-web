var IE = document.all?true:false;if (!IE) document.captureEvents(Event.MOUSEMOVE);
document.onmousemove = getMouseXY;var mousex = 0;var mousey = 0; var sdd=0; var sddn=0; var stl=0; var llink = 0;var iwt_scrollleft=0;var iwt_scrolltop=0;
function getMouseXY(e) { if (document.documentElement.scrollLeft) { iwt_scrollleft = document.documentElement.scrollLeft; } else { iwt_scrollleft=document.body.scrollLeft; }
if (document.documentElement.scrollTop) { iwt_scrolltop = document.documentElement.scrollTop; } else { iwt_scrolltop=document.body.scrollTop; }
if (IE) {mousex = event.clientX+iwt_scrollleft;mousey = event.clientY + iwt_scrolltop;  } else { mousex = e.pageX;mousey = e.pageY;  }
if (mousex < 0){mousex = 0}  if (mousey < 0){mousey = 0}
} document.write('<div id="iwebtoolthumbnaildiv" style="position:absolute;visibility:hidden;z-index:9999;top:0;left:0;"></div>');
window.onload = function () {var x = document.getElementsByTagName('a');for (var i=0;i<x.length;i++)
{ x[i].onmouseover = function() { if (!sdd && !document.cookie.match("iwt_disable")) {   if (this.getAttribute('rel') && this.getAttribute('rel').indexOf('noprev')!==-1) { } else { showdd(this.getAttribute("href")); sdd=1;}   } }
x[i].onmousemove = function() { if (!sdd && !document.cookie.match("iwt_disable")) {  if (this.getAttribute('rel') && this.getAttribute('rel').indexOf('noprev')!==-1) { } else { sdd=1; showdd(this.getAttribute("href"));}   } }
x[i].onmouseout = function() { sdd=0; setTimeout("hidedd();",500); }	}}
function showdd(link){ var chkext=link.substr(0,4);
ddomain=document.location.href;ddomain=ddomain.replace("http://","");ddomain=ddomain.replace("https://","");ddomain=ddomain.replace("www.","");var ddomain=ddomain.split('/'); var ddomain = ddomain[0];
ldomain=link;ldomain=ldomain.replace("http://","");ldomain=ldomain.replace("https://","");var ldomain=ldomain.split('/'); var ldomain = ldomain[0];ldomainc=ldomain.replace("www.","");
if (ldomainc && ddomain && chkext=="http" && !ldomainc.match(ddomain)){chg=1; if (llink!==ddomain) {var bc ="3366CC"; if (!bc) { bc="004891"; } document.getElementById('iwebtoolthumbnaildiv').innerHTML="<table width=\"120\" style=\"border-collapse: collapse;border: 1px solid #"+bc+";\" bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" onmouseover=\"sddn=1;\" onmouseout=\"sddn=0;setTimeout('hidedd();',500);\"><tr><td style=\"padding:1px 1px 1px 1px;\" bgcolor=\"#"+bc+"\"><table cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse;\" width=\"120\"><tr><td style=\"padding:1px 1px 1px 1px;\" align=\"right\"></td></tr></table></td></tr><tr><td style=\"padding:1px 1px 1px 1px;\"> <table width=\"120\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse;\"><tr><td colspan=\"2\" width=\"120\" height=\"92\" bgcolor=\"#F5F5F5\" style=\"padding:1px 1px 1px 1px;background-image: url('loading.gif'); background-repeat: no-repeat; background-position: center\" valign=\"top\"><a href=\""+link+"\" target=\"_target\"><img border=\"0\" style=\"border: solid #ffffff 1px\" onmouseover=\"this.style.borderColor='#"+bc+"'\" onmouseout=\"this.style.borderColor='#ffffff'\" alt=\"\" src=\"http://open.thumbshots.org/image.aspx?url="+encodeURIComponent(ldomain)+"\"></a></td></tr><tr><td style=\"padding:1px 1px 1px 1px;\"><div style=\"width:124;overflow:hidden;\"><a href=\""+link+"\" target=\"_target\" onmouseover=\"this.style.textDecoration='underline'\" onmouseout=\"this.style.textDecoration='none'\" style=\"font-weight:bold;text-decoration:none;font-family:verdana,tahoma,arial;font-size:8pt;color:#"+bc+";\">www."+ldomainc+"</a></div></td><td style=\"padding:1px 1px 1px 1px;\" align=\"right\"></td></tr></table></td></tr> </table>"; llink=ldomain; }
if (chg && !stl){ setTimeout("showddt();",700); }
else { if (window.innerWidth) { var cwidth=window.innerWidth; }else if (document.documentElement.clientWidth) { var cwidth=document.documentElement.clientWidth; }else if (document.body.clientWidth) { var cwidth=document.body.clientWidth; }
if (window.innerHeight) { var cheight=window.innerHeight; }else if (document.documentElement.clientHeight) { var cheight=document.documentElement.clientHeight; }else if (document.body.clientHeight) { var cheight=document.body.clientHeight; }
if ((mousex+230)>cwidth) { mousex=mousex-215; } else { mousex=mousex+5; } if ((mousey+210)>cheight+iwt_scrolltop) { mousey=mousey-200; } else { mousey=mousey+5; }
document.getElementById('iwebtoolthumbnaildiv').style.left=mousex+'px';document.getElementById('iwebtoolthumbnaildiv').style.top=mousey+'px'; }
} } function hidedd(){if (!sdd && !sddn) { document.getElementById('iwebtoolthumbnaildiv').innerHTML=""; document.getElementById('iwebtoolthumbnaildiv').style.visibility="hidden"; sdd=0; stl=0; }}
function showddt() { document.getElementById('iwebtoolthumbnaildiv').style.visibility='visible'; stl=1;
 if (window.innerWidth) { var cwidth=window.innerWidth; }else if (document.documentElement.clientWidth) { var cwidth=document.documentElement.clientWidth; }else if (document.body.clientWidth) { var cwidth=document.body.clientWidth; }
if (window.innerHeight) { var cheight=window.innerHeight; }else if (document.documentElement.clientHeight) { var cheight=document.documentElement.clientHeight; }else if (document.body.clientHeight) { var cheight=document.body.clientHeight; }
if ((mousex+230)>cwidth) { mousex=mousex-215; } else { mousex=mousex+5; } if ((mousey+210)>cheight+iwt_scrolltop) { mousey=mousey-200; } else { mousey=mousey+5; }
document.getElementById('iwebtoolthumbnaildiv').style.left=mousex+'px';document.getElementById('iwebtoolthumbnaildiv').style.top=mousey+'px'; }
function iwt_ds() { if (confirm("Would you like to disable website preview feature?")) { document.cookie = "iwt_disable=1"; } }