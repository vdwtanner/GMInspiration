/*
Copyright (c) 2010 http://ramui.com. All right reserved.
This product is protected by copyright and distributed under licenses restricting copying, distribution. Permission is granted to the public to download and use this script provided that this Notice and any statement of authorship are reproduced in every page on all copies of the script.
*/
function addBBcodeEvents(){
var toolBox=document.getElementById('fw_toolbox');
var td = toolBox.getElementsByTagName('LI');
for(var i in td){
if (td[i].className == "fw_tool"){
td[i].onmouseover = btnOver;
td[i].onmouseout = btnOut;
td[i].onmousedown = btnDown;
td[i].onmouseup = btnOver;
td[i].onclick = btnClick;}
if (td[i].id == "forecolor"){
td[i].onmouseover=function(){document.getElementById('color').className='colorshow';}
td[i].onmouseout=function(){document.getElementById('color').className='colorhide';}
}
}
td = toolBox.getElementsByTagName('TD');
for(var i in td){
if (td[i].className == "fw_color"){
td[i].onmouseover=function(){this.style.border="1px solid #333";}
td[i].onmouseout=function(){this.style.border="1px solid #fff";}
td[i].onclick=btnClick;
}}
}
function btnClick(){
var id=this.id;
if(id=='previewBBCode'){previewCode();return;}
var editor = document.getElementById(bbCodeEditorID);
if(document.selection){
editor.focus();
var sel = document.selection.createRange();
switch(id){
case "ol": case "ul":
sel.text='['+id+']'+formatList(sel.text)+'[/'+id+']';break;
case "b": case "i": case "u": case "s": case "code": case "quote": case "sup": case "sub": case "big": case "small":
sel.text='['+id+']'+sel.text+'[/'+id+']';
break;
case "forecolor":
sel.text='[color='+fw_currentColor+']'+sel.text+'[/color]';
break;
case "link":
var url = prompt("Enter link URL:", "http://");
if((url== null) || (textTrim(url)== "")){return;}
sel.text=((sel.text)? '[url=' + textTrim(url) + ']' + sel.text + '[/url]' : '[url]'  + textTrim(url) + '[/url]');
break;
default:
if(this.className=='fw_color'){sel.text='[color='+id+']'+sel.text+'[/color]';}
}
}
else{mozilla(editor,id);}
}
function mozilla(editor,id){
var top = editor.scrollTop;
var left = editor.scrollLeft;
var length = editor.value.length;
var start = editor.selectionStart;
var end = editor.selectionEnd;
sel = editor.value.substring(start, end);
var str;
switch(id){
case "ol": case "ul":
sel=formatList(sel);
case "b": case "i": case "u": case "s": case "code": case "quote": case "sup": case "sub": case "file": case "big": case "small":
str = '['+id+']'+sel+'[/'+id+']';
break;
case "link":
var url = prompt("Enter link URL:", "http://");
if((url== null) || (textTrim(url)== "")){return;}
str=((sel)? '[url=' + textTrim(url) + ']' + sel + '[/url]' : '[url]'  + textTrim(url) + '[/url]');
break;
default:
if(document.getElementById(id).className=='fw_color'){str='[color='+id+']'+sel+'[/color]';}
}
editor.value =  editor.value.substring(0,start) + str + editor.value.substring(end,length);
editor.scrollTop = top;
editor.scrollLeft = left;
}
function formatList(s){var items=s.split('\n');var str='';for(var i=0;i<items.length;i++){str+= '[li]' + items[i]+'[/li]\n';}return(str);}
function btnOver(){this.style.border="1px solid #0a246a";this.style.background="#b6bdaa";}
function btnOut(){this.style.background="none";this.style.border="1px solid #ffffff";}
function btnDown(){this.style.background="#aad5ff";}
function getUrl(){
var thisscript = document.getElementsByTagName('script');
for (var i=0; i<thisscript.length; i++) {if(thisscript[i].src.indexOf('bbcode.js')>=0){return(thisscript[i].src.replace('bbcode.js',''));}}return '';}
function textTrim(str){return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');}
function previewCode(){
var str=textTrim(document.getElementById(bbCodeEditorID).value).length;
if((str > maxCodeLength)||(str <10)){alert("Please write comment between 10 to "+maxCodeLength+" characters!");return;}
var url=getUrl()+'preview.php?editor='+bbCodeEditorID;
window.open(url,'Preview','width=690,height=350 ,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,copyhistory=yes');
}
if(typeof maxCodeLength == "undefined"){var maxCodeLength=10000;}
document.write("<style type=\"text/css\">\r\n#fw_toolbox{background:#ffffff;margin:0;padding:0;clear:both;}\r\n#fw_toolbox ul{list-style-type:none;margin:2px 0;padding:0;}\r\n#fw_toolbox li{display:inline;text-align:center;margin:0;padding:5px 0;vertical-align:middle;}\r\n#fw_toolbox li.fw_tool{width:21px;border:1px solid #fff;}\r\n#fw_toolbox li.fw_forecolor{width:21px;border:1px solid #fff;background:#fff;}\r\n#fw_toolbox li.fw_inactive{width:21px;padding:0;border:1px solid #fff;}\r\n#fw_toolbox li.fw_select{width:21px;border:1px solid #e2e0dc;}\r\n#fw_toolbox li.separator{width:10px;}\r\n#fw_toolbox li img{border:none;margin:0;float:none;clear:none;vertical-align:middle;padding:0;}\r\nul#color{position:absolute;}\r\n#color li{position:absolute;left:-23px;top:10px;}\r\nul.colorhide{display:none;}\r\nul.colorshow{display:inline;}\r\n#fw_color{background:#ece9d8;border:1px outset;margin:0;}\r\n#fw_color td{padding:0;border:1px solid #fff;cursor:pointer;font-size:1%;}\r\n</style>\r\n<div id=\"fw_toolbox\">\r\n<ul>\r\n<li class=\"fw_tool\" id=\"previewBBCode\"><img src=\""+getUrl()+"images/preview.gif\" title=\"Preview code\" alt=\"Preview\" /></li>\r\n<li class=\"separator\"><img src=\""+getUrl()+"images/separator.gif\" alt=\"\" /></li>\r\n<li class=\"fw_forecolor\" id=\"forecolor\"><img src=\""+getUrl()+"images/forecolor.gif\" title=\"Fore Color\" alt=\"forecolor\" />\r\n<ul id=\"color\" class=\"colorhide\"><li>\r\n<table id=\"fw_color\" cellpadding=\"1\" cellspacing=\"3\"><tr>\r\n<td class=\"fw_color\" id=\"#FAAA3C\" bgcolor=\"#faaa3c\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFCCCC\" bgcolor=\"#ffcccc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFCC99\" bgcolor=\"#ffcc99\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFFF99\" bgcolor=\"#ffff99\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFFFCC\" bgcolor=\"#ffffcc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#99FF99\" bgcolor=\"#99ff99\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#99FFFF\" bgcolor=\"#99ffff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#CCFFFF\" bgcolor=\"#ccffff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#CCCCFF\" bgcolor=\"#ccccff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFCCFF\" bgcolor=\"#ffccff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n</tr><tr>\r\n<td class=\"fw_color\" id=\"#CCCCCC\" bgcolor=\"#cccccc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FF6666\" bgcolor=\"#ff6666\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FF9966\" bgcolor=\"#ff9966\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFFF66\" bgcolor=\"#ffff66\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFFF33\" bgcolor=\"#ffff33\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#66FF99\" bgcolor=\"#66ff99\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#33FFFF\" bgcolor=\"#33ffff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#66FFFF\" bgcolor=\"#66ffff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#9999FF\" bgcolor=\"#9999ff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FF99FF\" bgcolor=\"#ff99ff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n</tr><tr>\r\n<td class=\"fw_color\" id=\"#C0C0C0\" bgcolor=\"#c0c0c0\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FF0000\" bgcolor=\"#ff0000\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FF9900\" bgcolor=\"#ff9900\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFCC66\" bgcolor=\"#ffcc66\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFFF00\" bgcolor=\"#ffff00\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#33FF33\" bgcolor=\"#33ff33\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#66CCCC\" bgcolor=\"#66cccc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#33CCFF\" bgcolor=\"#33ccff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#6666CC\" bgcolor=\"#6666cc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#CC66CC\" bgcolor=\"#cc66cc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n</tr><tr>\r\n<td class=\"fw_color\" id=\"#999999\" bgcolor=\"#999999\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#CC0000\" bgcolor=\"#cc0000\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FF6600\" bgcolor=\"#ff6600\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFCC33\" bgcolor=\"#ffcc33\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#FFCC00\" bgcolor=\"#ffcc00\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#33CC00\" bgcolor=\"#33cc00\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#00CCCC\" bgcolor=\"#00cccc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#3366FF\" bgcolor=\"#3366ff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#6633FF\" bgcolor=\"#6633ff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#CC33CC\" bgcolor=\"#cc33cc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n</tr><tr>\r\n<td class=\"fw_color\" id=\"#666666\" bgcolor=\"#666666\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#990000\" bgcolor=\"#990000\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#CC6600\" bgcolor=\"#cc6600\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#CC9933\" bgcolor=\"#cc9933\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#999900\" bgcolor=\"#999900\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#009900\" bgcolor=\"#009900\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#339999\" bgcolor=\"#339999\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#3333FF\" bgcolor=\"#3333ff\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#6600CC\" bgcolor=\"#6600cc\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#993399\" bgcolor=\"#993399\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n</tr><tr>\r\n<td class=\"fw_color\" id=\"#333333\" bgcolor=\"#333333\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#660000\" bgcolor=\"#660000\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#993300\" bgcolor=\"#993300\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#996633\" bgcolor=\"#996633\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#666600\" bgcolor=\"#666600\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#006600\" bgcolor=\"#006600\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#336666\" bgcolor=\"#336666\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#000099\" bgcolor=\"#000099\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#333399\" bgcolor=\"#333399\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#663366\" bgcolor=\"#663366\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n</tr><tr>\r\n<td class=\"fw_color\" id=\"#000000\" bgcolor=\"#000000\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#330000\" bgcolor=\"#330000\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#663300\" bgcolor=\"#663300\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#663333\" bgcolor=\"#663333\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#333300\" bgcolor=\"#333300\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#003300\" bgcolor=\"#003300\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#003333\" bgcolor=\"#003333\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#000066\" bgcolor=\"#000066\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#330099\" bgcolor=\"#330099\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n<td class=\"fw_color\" id=\"#330033\" bgcolor=\"#330033\"><img src=\""+getUrl()+"images/colorcell.gif\" alt=\"\"></td>\r\n</tr></table>\r\n</li></ul>\r\n</li>\r\n<li class=\"separator\"><img src=\""+getUrl()+"images/separator.gif\" alt=\"\" /></li>\r\n<li class=\"fw_tool\" id=\"ol\"><img src=\""+getUrl()+"images/ol.gif\" title=\"Ordered list\" alt=\"Ordered list\" /></li>\r\n<li class=\"fw_tool\" id=\"ul\"><img src=\""+getUrl()+"images/ul.gif\" title=\"List\" alt=\"List\" /></li>\r\n<li class=\"separator\"><img src=\""+getUrl()+"images/separator.gif\" alt=\"\" /></li>\r\n<li class=\"fw_tool\" id=\"b\"><img src=\""+getUrl()+"images/b.gif\" title=\"Bold\" alt=\"Bold\" /></li>\r\n<li class=\"fw_tool\" id=\"i\"><img src=\""+getUrl()+"images/i.gif\" title=\"Italic\" alt=\"Italic\" /></li>\r\n<li class=\"fw_tool\" id=\"u\"><img src=\""+getUrl()+"images/u.gif\" title=\"Underline\" alt=\"Underline\" /></li>\r\n<li class=\"fw_tool\" id=\"s\"><img src=\""+getUrl()+"images/strikethrough.gif\" title=\"Strikethrough\" alt=\"Strikethrough\" /></li>\r\n<li class=\"separator\"><img src=\""+getUrl()+"images/separator.gif\" alt=\"\" /></li>\r\n<li class=\"fw_tool\" id=\"big\"><img src=\""+getUrl()+"images/big.gif\" title=\"Big\" alt=\"Big\" /></li>\r\n<li class=\"fw_tool\" id=\"small\"><img src=\""+getUrl()+"images/small.gif\" title=\"Small\" alt=\"Small\" /></li>\r\n<li class=\"separator\"><img src=\""+getUrl()+"images/separator.gif\" alt=\"\" /></li>\r\n<li class=\"fw_tool\" id=\"sup\"><img src=\""+getUrl()+"images/superscript.gif\" title=\"Superscript\" alt=\"Superscript\" /></li>\r\n<li class=\"fw_tool\" id=\"sub\"><img src=\""+getUrl()+"images/subscript.gif\" title=\"Subscript\" alt=\"Subscript\" /></li>\r\n<li class=\"separator\"><img src=\""+getUrl()+"images/separator.gif\" alt=\"\" /></li>\r\n<li class=\"fw_tool\" id=\"link\"><img src=\""+getUrl()+"images/link.gif\" title=\"Link\" alt=\"Link\" /></li>\r\n<li class=\"separator\"><img src=\""+getUrl()+"images/separator.gif\" alt=\"\" /></li>\r\n<li class=\"fw_tool\" id=\"quote\"><img src=\""+getUrl()+"images/quote.gif\" title=\"Quote\" alt=\"Quote\" /></li>\r\n<li class=\"fw_tool\" id=\"code\"><img src=\""+getUrl()+"images/code.gif\" title=\"Code\" alt=\"Code\" /></li>\r\n</ul>\r\n</div>");
addBBcodeEvents();