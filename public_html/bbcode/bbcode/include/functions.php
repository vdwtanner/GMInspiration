<?php
/*
Copyright (c) 2008 http://ramui.com. All right reserved.
This product is protected by copyright and distributed under licenses restricting copying, distribution. Permission is granted to the public to download and use this script provided that this Notice and any statement of authorship are reproduced in every page on all copies of the script.
*/
function fw_strip_slashes($str)
{
        if (get_magic_quotes_gpc()){return stripslashes($str);}
        return $str;
}
function xss_cleaner($str)
{
		$str = str_replace( array('&','<','>',"'",'"',')','('), array('&amp;','&lt;','&gt;','&apos;','&#x22;','&#x29;','&#x28;'), $str );
		return str_ireplace( '%3Cscript', '', $str );
}
class bbcode
{
private $autolink;
private $nofollow;
function __construct($nofollow)
{
		$this->nofollow=$nofollow;
}
public function format_text($text,$autolink)
{
		$this->autolink=$autolink;
		$text=str_replace("\r","",$text);
		$text=str_replace("\n[code]","[code]",str_replace("[/code]\n","[/code]",$text));
		$text=str_replace("\n[ul]","[ul]",str_replace("[/ul]\n","[/ul]",$text));
		$text=str_replace("\n[ol]","[ol]",str_replace("[/ol]\n","[/ol]",$text));
		$text=str_replace("\n[li]","[li]",str_replace("[/li]\n","[/li]",$text));
        $this->escape_code($text);
		return $text;
}
private function escape_code(&$content)
{
		if(empty($content)){return;}
        $start=strpos($content,'[code]');
        $end=strpos($content,'[/code]');
        if(($start===false)||($end===false)||($start > $end)){$this->url($content);return;}
        $firstpart=substr($content,0,$start);
        $this->url($firstpart);
        $code=substr($content,$start+6,($end-$start-6));
		$code=$this->format_code($code);
        $lastpart=substr($content,$end +7);
		$this->escape_code($lastpart);
        $content=$firstpart.$code.$lastpart;
}
private function url(&$content)
{
		while(strpos($content," [url]\n")!==false){$content=str_replace(" [url]\n","\n[url]",$content);}
		while(strpos($content,"[url]\n")!==false){$content=str_replace("[url]\n","\n[url]",$content);}
		while(strpos($content,"[url] ")!==false){$content=str_replace("[url] ","[url]",$content);}
		if($this->autolink){$content=$this->convert2_link($content);}
		$content=preg_replace("/\[url\](.+)\[\/url\]/eiUs", "\$this->geturl('\\1')", $content);
		$content=preg_replace('/\[url\=(.+)\](.+)\[\/url\]/iUs', '<a target="_blank" '.(($this->nofollow)? 'rel="nofollow" ':'').'href="$1">$2</a>', $content);
		$content=$this->convert2_html($content);
		$content=nl2br($content);
}
private function geturl($link)
{
		if(strpos($link,']')!==false){
			$x=str_replace('[','<',str_replace(']','>',$link));
			$x=strip_tags($x);
			return '<a target="_blank" '.(($this->nofollow)? 'rel="nofollow" ':'').'href="'.$x.'">'.$link.'</a>';
		}
		else{return '<a target="_blank" '.(($this->nofollow)? 'rel="nofollow" ':'').'href="'.$link.'">'.$link.'</a>';}
}
private function convert2_link($x)
{
		$exp='/(?<!\=|\[url\]|\[img\]|\&gt;|\"|\&quot;|\'|\"|\>)((http|https)\:\/\/[a-z0-9\-\.]+\.[a-z0-9\/\-\?\&\=\.]{2,80}+([^\[\"\>\'\&quot;\&gt;\s]*))/i';
		$x=preg_replace($exp, '[url]${0}[/url]', $x);
		return $x;
}
private function convert2_html($text)
{
		if(empty($text)){return;}
		$search=array('[b]','[/b]','[u]','[/u]','[i]','[/i]','[s]','[/s]','[sup]','[/sup]','[sub]','[/sub]');
		$replace=array('<strong>','</strong>','<u>','</u>','<i>','</i>','<s>','</s>','<sup>','</sup>','<sub>','</sub>');
		$s=array('[ul]','[/ul]','[ol]','[/ol]','[li]','[/li]','[quote]','[/quote]','[big]','[/big]','[small]','[/small]');
		$r=array('<ul>','</ul>','<ol>','</ol>','<li>','</li>','<blockquote><p>','</p></blockquote>','<big>','</big>','<small>','</small>');
		$search=array_merge($search,$s);
		$replace=array_merge($replace,$r);
		$text=str_replace($search,$replace,$text);
		$text=preg_replace('/\[color\=([0-9a-fA-F#]{4,7})\](.+)\[\/color\]/iUs', '<span style="color:$1">$2</span>', $text);
		return $text;
}
private function format_code($code)
{
		if(empty($code)){return;}
        $line_count=substr_count($code,"\n");
        $height=40+($line_count-1)*20;
        if($height<70){$height=70;}
        if($height>300){$height=300;}
        $code='<div class="fw_code_heading">Code</div><pre class="fw_code" style="padding:5px; overflow:auto; height:'.$height.'px;">'.$code.'</pre>';
        return $code;
}
}
?>