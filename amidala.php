<?
/*
* Copyright (c) 2004-2007, Amidala Project
* All rights reserved.
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
*     * Redistributions of source code must retain the above copyright
*       notice, this list of conditions and the following disclaimer.
*     * Redistributions in binary form must reproduce the above copyright
*       notice, this list of conditions and the following disclaimer in the
*       documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

$time_begin=mtime();
#<php>
$ver_serial='2007020800';
ini_set('display_errors', '0');
#error_reporting(E_ALL & ~E_NOTICE);
set_magic_quotes_runtime(0);
define('ERR','Error');
define('WARN','Warning');
define('INFO','Information');
session_start();
if(get_magic_quotes_gpc()==1){
	foreach($_POST as $key => $val){
		$_POST[$key]=stripslashes($_POST[$key]);
	}
}

if(local()){
	set_time_limit(0);
}else{
	set_time_limit(180);
}

function local(){
	return (ip()=='127.0.0.1')?true:false;
}

function autoslash($s){
	if(get_magic_quotes_gpc()==1){
		return stripslashes($s);
	}else{
		return $s;
	}
}

if(!pcre()){
	addmsg(ERR,'PCRE Library Not Found!');
}

if(!bc()){
	addmsg(WARN,'BC Math Library Not Found! Calculator will operate with low precision.');
}
if(!mb()){
	addmsg(WARN,'MBString Library Not Found! Some function will be limited.');
	$_POST['mbstring']='off';
}

$cancel=0;

function mtime(){
	$t=explode(' ',microtime());
	return $t[1]+$t[0];
}

function ip(){
return empty($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['REMOTE_ADDR']:$_SERVER['HTTP_X_FORWARDED_FOR'];
}

function pcre(){
return function_exists('preg_replace')?TRUE:FALSE;
}

function bc(){
return function_exists('bcadd')?TRUE:FALSE;
}

function mb(){
return function_exists('mb_internal_encoding')?TRUE:FALSE;
}

function mbs(){
return (mb() && $_POST['mbstring']=='on')?TRUE:FALSE;
}

function counter($s){
	$arr[0]=$s;
	for($i=0;$i<count($_POST['ssep_de']);$i++){
		$ct=count($arr);
		for($j=0;$j<$ct;$j++){
			$tmp=explod($_POST['ssep_de'][$i],$arr[$j]);
			$arr[$j]=$tmp[0];
			for($k=1;$k<count($tmp);$k++){
				$arr[]=$tmp[$k];
			}
		}
	}
	return count($arr);
}

function strwidth($s){
return mbs()?mb_strwidth($s):strlen($s);
}

function strleng($s){
return mbs()?mb_strlen($s):strlen($s);
}

function substri($a,$b,$c=NULL){
	if($c===NULL){
		return mbs()?mb_substr($a,$b):substr($a,$b);
	}else{
		return mbs()?mb_substr($a,$b,$c):substr($a,$b,$c);
	}
}

function addmsg($t,$s,$f=0){
	global $msg,$cancel;
	$msg[]='<a>'.$t.':</a><span> '.$s.'</span>';
	if($f==1){
		$cancel=1;
	}
}

function cancel(){
global $cancel;
return ($cancel==1)?TRUE:FALSE;
}

function bbs2html_dc($s){
if(strtolower($_POST['ccharset'])!='big5'){
	addmsg(ERR,'Input charset must be Big5');
	return $s;
}
$flag=0;
$clr=array('000','B00','0B0','BB0','00B','B0B','0BB','BBB');
$color='BBB';
$bg='000';
$hl=0;
$ul=0;
$sh=0;
$rv=0;
$dec='';
if($ul==0 && $sh==0){$dec=' none';}
$style='color: #'.$color.'; text-decoration:'.$dec.'; background-color: #'.$bg.';';
$r='<html><head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<style type="text/css">
a {
font-family: monospace;
white-space: pre;
line-height: 1em;
}
.dc {
width: 0.5em;
position: absolute;
overflow: hidden;
text-align: left;
}
</style>
</head>
<body style="background: #000000;"><table align="center"><tr><td><pre><a style="'.$style.'">';
for($i=0;$i<strlen($s);$i++){
	if(substr($s,$i,2)==chr(27)."["){
		$tmp='';
		$j=$i+2;
		while(substr($s,$j,1)!="m"){
			$tmp.=substr($s,$j,1);
			$j++;
		}
if($flag==1){
	$h.=substr($s,$j+1,1);
	$bak=$style;
	$i++;
}
		$l=strlen($tmp);
		if($l==0){
			$color='BBB';
			$bg='000';
			$hl=0;
			$ul=0;
			$sh=0;
			$rv=0;
			$dec='';
			if($ul==0 && $sh==0){$dec=' none';}
			$style='color: #'.$color.'; text-decoration:'.$dec.'; background-color: #'.$bg.';';
			$r.=(chr(0).'</a><a style="'.$style.'">');
		}else{
			$t=explode(';',$tmp);
			for($k=0;$k<count($t);$k++){
				if($t[$k]==0 || strlen($t[$k])==0){
					$color='BBB';
					$bg='000';
					$hl=0;
					$ul=0;
					$sh=0;
					$rv=0;
				}elseif($t[$k]==1){
					$hl=1;
				}elseif($t[$k]==4){
					$ul=1;
				}elseif($t[$k]==5){
					$sh=1;
				}elseif($t[$k]==7){
					$rv=1;
				}else{
					if(substr($t[$k],0,1)=='3'){
						if(ereg("[0-7]",substr($t[$k],1,1))){
							$color=$clr[substr($t[$k],1,1)];
						}else{
							$color='BBB';
						}
					}elseif(substr($t[$k],0,1)=='4'){
						if(ereg("[0-7]",substr($t[$k],1,1))){
							$bg=$clr[substr($t[$k],1,1)];
						}else{
							$bg='000';
						}
					}
				}
			}
			if($rv==1){$p=$color; $color=$bg; $bg=$p;}
			if($hl==1){$color=str_replace("B","F",$color); $color=str_replace("0","5",$color);}
			$dec='';
			if($ul==1){$dec.=' underline';}
			if($sh==1){$dec.=' blink';}
			if($ul==0 && $sh==0){$dec=' none';}
			$style='color: #'.$color.'; text-decoration:'.$dec.'; background-color: #'.$bg.';';
			if($hl==1){$color=str_replace("F","B",$color); $color=str_replace("5","0",$color);}
			if($rv==1){$p=$color; $color=$bg; $bg=$p;}
if($flag==1){
	$r.=chr(0).'</a><a class="dc" style="'.$bak.'">'.$h.'</a><a style="'.$style.'">'.$h;
	$flag=0;
}else{
			$r.=(chr(0).'</a><a style="'.$style.'">');
}
		}
		$i=$i+$l+2;
	}elseif(preg_match("/[\xA1-\xF9][\x40-\x7E\xA1-\xFE]/",substr($s,$i,2))){
		$r.=substr($s,$i,2);
		$i++;
	}elseif(preg_match("/[\xA1-\xF9]\x1B/",substr($s,$i,2))){
		$h=substr($s,$i,1);
		$flag=1;
	}else{
		$r.=htmlspecialchars(substr($s,$i,1));
	}
}
	$r.='</a></pre></td></tr></table></body></html>';
	return $r;
}

function bbs2html($s){
$flag=0;
$clr=array('000','B00','0B0','BB0','00B','B0B','0BB','BBB');
$color='BBB';
$bg='000';
$hl=0;
$ul=0;
$sh=0;
$rv=0;
$dec='';
if($ul==0 && $sh==0){$dec=' none';}
$style='color: #'.$color.'; text-decoration:'.$dec.'; background-color: #'.$bg.';';
$r='<html><head><meta http-equiv=content-type content="text/html; charset='.$_POST['charset'].'">
<style type="text/css">
a {
font-family: monospace;
white-space: pre;
line-height: 1em;
}
</style>
</head>
<body style="background: #000000;"><table align="center"><tr><td><pre><a style="'.$style.'">';
for($i=0;$i<strlen($s);$i++){
	if(substr($s,$i,2)==chr(27)."["){
		$tmp='';
		$j=$i+2;
		while(substr($s,$j,1)!="m"){
			$tmp.=substr($s,$j,1);
			$j++;
		}
if(eregi("^big5$",$_POST['ccharset']) && $flag==1){
	$r.=substr($s,$j+1,1);
	$i++;
	$flag=0;
}
		$l=strlen($tmp);
		if($l==0){
			$color='BBB';
			$bg='000';
			$hl=0;
			$ul=0;
			$sh=0;
			$rv=0;
			$dec='';
			if($ul==0 && $sh==0){$dec=' none';}
			$style='color: #'.$color.'; text-decoration:'.$dec.'; background-color: #'.$bg.';';
			$r.=(chr(0).'</a><a style="'.$style.'">');
		}else{
			$t=explode(';',$tmp);
			for($k=0;$k<count($t);$k++){
				if($t[$k]==0 || strlen($t[$k])==0){
					$color='BBB';
					$bg='000';
					$hl=0;
					$ul=0;
					$sh=0;
					$rv=0;
				}elseif($t[$k]==1){
					$hl=1;
				}elseif($t[$k]==4){
					$ul=1;
				}elseif($t[$k]==5){
					$sh=1;
				}elseif($t[$k]==7){
					$rv=1;
				}else{
					if(substr($t[$k],0,1)=='3'){
						if(ereg("[0-7]",substr($t[$k],1,1))){
							$color=$clr[substr($t[$k],1,1)];
						}else{
							$color='BBB';
						}
					}elseif(substr($t[$k],0,1)=='4'){
						if(ereg("[0-7]",substr($t[$k],1,1))){
							$bg=$clr[substr($t[$k],1,1)];
						}else{
							$bg='000';
						}
					}
				}
			}
			if($rv==1){$p=$color; $color=$bg; $bg=$p;}
			if($hl==1){$color=str_replace("B","F",$color); $color=str_replace("0","5",$color);}
			$dec='';
			if($ul==1){$dec.=' underline';}
			if($sh==1){$dec.=' blink';}
			if($ul==0 && $sh==0){$dec=' none';}
			$style='color: #'.$color.'; text-decoration:'.$dec.'; background-color: #'.$bg.';';
			if($hl==1){$color=str_replace("F","B",$color); $color=str_replace("5","0",$color);}
			if($rv==1){$p=$color; $color=$bg; $bg=$p;}
			$r.=(chr(0).'</a><a style="'.$style.'">');
		}
		$i=$i+$l+2;
	}elseif(eregi("^big5$",$_POST['ccharset']) && preg_match("/[\xA1-\xF9][\x40-\x7E\xA1-\xFE]/",substr($s,$i,2))){
		$r.=substr($s,$i,2);
		$i++;
	}elseif(eregi("^big5$",$_POST['ccharset']) && preg_match("/[\xA1-\xF9]\x1B/",substr($s,$i,2))){
		$r.=substr($s,$i,1);
		$flag=1;
	}else{
		$r.=htmlspecialchars(substr($s,$i,1));
	}
}
	$r.='</a></pre></td></tr></table></body></html>';
	return $r;
}

function accumulation($s,$f){
	$a=s2a($s);
	if($f==0){
		for($i=1;$i<count($a);$i++){
			$a[$i]+=$a[$i-1];
		}
	}else{
		for($i=count($a)-1;$i>=0;$i--){
			$a[$i]-=$a[$i-1];
		}
	}
	return a2s($a);
}

function network($s){
$a=s2a($s);
$ipa=explode(".",$a[0]);
$ipb=explode(".",$a[1]);
$bina='';
$binb='';
if(count($ipa)!=4 || count($ipb)!=4){
addmsg(ERR,'Incorrect IP format');
return $s;
}
	for($i=0;$i<4;$i++){
if($ipa[$i]>255 || $ipb[$i]>255 || $ipa[$i]<0 || $ipb[$i]<0){
addmsg(ERR,'Incorrect IP format');
return $s;
}
		$temp=decbin($ipa[$i]);
		$bina.=str_repeat('0',8-strlen($temp)).$temp;
		$temp=decbin($ipb[$i]);
		$binb.=str_repeat('0',8-strlen($temp)).$temp;
	}
	$lala='';
	$lala2='';
	for($i=0;$i<32;$i++){
		if(substr($bina,$i,1)==substr($binb,$i,1)){
			$lala.=substr($bina,$i,1);
			$lala2.='1';
		}else{
			break;
		}
	}
	$lala.=str_repeat('0',32-strlen($lala));
	$lala2.=str_repeat('0',32-strlen($lala2));
	for($i=0;$i<4;$i++){
		$p[$i]=substr($lala2,$i*8,8);
		$p[$i]=bindec($p[$i]);
		$q[$i]=substr($lala,$i*8,8);
		$q[$i]=bindec($q[$i]);
	}
	if(ereg("^1111",$lala)){
		$class='E';
	}elseif(ereg("^111",$lala)){
		$class='D';
	}elseif(ereg("^11",$lala)){
		$class='C';
	}elseif(ereg("^1",$lala)){
		$class='B';
	}else{
		$class='A';
	}
	addmsg(INFO,'Class: '.$class);
	addmsg(INFO,'Network: '.implode('.',$q));
	addmsg(INFO,'Mask: '.implode('.',$p));
	return $s;
}

function s2a($s,$k=0){
	if(count($_POST['ssep_de'])<$k){
		addmsg(ERR,'SubSeparator not enough');
		$r[0]=$s;
		return $r;
	}
	$r=explod($_POST['ssep_de'][$k],$s);
	return $r;
}
function a2s($s,$k=0){
	$r=implode($_POST['ssep_de'][$k],$s);
	return $r;
}

function s2m($s){
	$r=s2a($s,1);
	for($i=0;$i<count($r);$i++){
		$r[$i]=s2a($r[$i]);
	}
	return $r;
}

function mfix($s){
	$k=0;
	for($i=0;$i<count($s);$i++){
		if(count($s[$i])>$k){
			$k=count($s[$i]);
		}
	}
	$pad=ent_de($_POST['mfix_pad']);
	for($i=0;$i<count($s);$i++){
		for($j=0;$j<$k;$j++){
			if(!isset($s[$i][$j])){
				$s[$i][$j]=$pad;
			}
		}
	}
	return $s;
}

function m2s($s){
	for($i=0;$i<count($s);$i++){
		$s[$i]=a2s($s[$i]);
	}	
	$r=a2s($s,1);
	return $r;
}

function crc16($string){ 
 	$crc = 0xFFFF;
	for($x=0;$x<strlen($string);$x++){
		$crc=$crc ^ ord($string[$x]);
			for($y=0;$y<8;$y++) {
				if(($crc & 0x0001)==0x0001){
					$crc=(($crc >> 1) ^ 0xA001);
				}else{$crc=$crc >> 1;}
			}
	}
	return $crc;
}

function totable($s){
	$l=s2m($s);
	for($i=0;$i<count($l);$i++){
		for($j=0;$j<count($l[$i]);$j++){
			if(empty($len[$j]) || strwidth($l[$i][$j])>$len[$j]){
				$len[$j]=strwidth($l[$i][$j]);
			}
		}
	}
	for($i=0;$i<count($len);$i++){
		if($len[$i]%2==1){
			$len[$i]+=1;
		}
	}
if($_POST['ttb_mono']=="on"){
	rsort($len);
	for($i=0;$i<count($len);$i++){
		$len[$i]=$len[0];
	}
}
	for($i=0;$i<count($l);$i++){
		for($j=0;$j<count($len);$j++){
			if(!isset($l[$i][$j])){
				$l[$i][$j]=str_repeat(' ',$len[$j]);
			}else{
if($_POST['ttb_align']=="left"){
				$l[$i][$j].=str_repeat(' ',$len[$j]-strwidth($l[$i][$j]));
}elseif($_POST['ttb_align']=="center"){
				$l[$i][$j]=str_repeat(' ',floor(($len[$j]-strwidth($l[$i][$j]))/2)).$l[$i][$j].str_repeat(' ',ceil(($len[$j]-strwidth($l[$i][$j]))/2));
}elseif($_POST['ttb_align']=="right"){
				$l[$i][$j]=str_repeat(' ',$len[$j]-strwidth($l[$i][$j])).$l[$i][$j];
}
			}
			$l[$i][$j]=htmlspecialchars($l[$i][$j]);
		}
	}
	for($i=0;$i<count($l);$i++){
if($_POST['ttb_ibrd']=="on"){
		$l[$i]=implode('&#9474;',$l[$i]);
}else{
		$l[$i]=implode('  ',$l[$i]);
}
if($_POST['ttb_brd']=="on"){
$l[$i]="&#9474;".$l[$i]."&#9474;";
}else{
$l[$i]=preg_replace('/ +$/','',$l[$i]);
}
	}
	for($i=0;$i<count($len);$i++){
		$len[$i]=str_repeat('&#9472;',$len[$i]/2);
	}
	$im=implode('&#9532;',$len);
if($_POST['ttb_brd']=="on"){
	if($_POST['ttb_ibrd']=="on"){
		$im="&#9500;".$im."&#9508;";
		$him="&#9484;".implode('&#9516;',$len)."&#9488;";
		$fim="&#9492;".implode('&#9524;',$len)."&#9496;";
		$s=$him."\n".implode("\n".$im."\n",$l)."\n".$fim;
	}else{
		$him="&#9484;".implode('&#9472;',$len)."&#9488;";
		$fim="&#9492;".implode('&#9472;',$len)."&#9496;";
		$s=$him."\n".implode("\n",$l)."\n".$fim;
	}
}else{
	if($_POST['ttb_ibrd']=="on"){
		$s=implode("\n".$im."\n",$l);
	}else{
		$s=implode("\n",$l);
	}
}
	return $s;
}

function uniq($s,$m){
	$a=s2a($s);
	if($m==1){
		$a=array_reverse($a);
	}
	$a=array_unique($a);
	if($m==1){
		$a=array_reverse($a);
	}
	return a2s($a);
}

function sqr_reflect($s){
	$m=s2m($s);
	$m=mfix($m);
	$q=count($m);
	$r=count($m[0]);
	for($i=0;$i<$q;$i++){
		for($j=0;$j<$r;$j++){
			$i2=($_POST['ref_hor']=='on'?$q-$i-1:$i);
			$j2=($_POST['ref_ver']=='on'?$r-$j-1:$j);
			$m2[$i][$j]=$m[$i2][$j2];
		}
	}
	return m2s($m2);
}

function correct($s){
	$k=s2a($s);
	for($i=0;$i<count($k);$i++){
		for($j=0;$j<strleng($k[$i]);$j++){
			$arr[$i][$j]=substri($k[$i],$j,1);
		}
	}
	$max=0;
	$min=count($arr[0]);
	for($i=0;$i<count($arr);$i++){
		if(count($arr[$i])>$max){
			$max=count($arr[$i]);
		}
		if(count($arr[$i])<$min){
			$min=count($arr[$i]);
		}
	}
	for($j=0;$j<$max;$j++){
		$width[$j]=0;
		for($i=0;$i<count($arr);$i++){
			if($j<count($arr[$i]) && strwidth($arr[$i][$j])>$width[$j]){
				$width[$j]=strwidth($arr[$i][$j]);
			}
		}
	}
	for($i=0;$i<$min;$i++){
		$flag=FALSE;
		for($j=1;$j<count($arr);$j++){
			if($arr[$j][$i]!=$arr[0][$i]){
				$flag=TRUE;
			}
		}
		if($flag){
			$res[$i]='^';
		}else{
			$res[$i]=' ';
		}
	}
	for(;$i<$max;$i++){
		$res[$i]='X';
	}
	for($i=0;$i<count($arr);$i++){
		for($j=0;$j<count($arr[$i]);$j++){
			$arr[$i][$j].=str_repeat(' ',$width[$j]-strwidth($arr[$i][$j]));
		}
	}
	for($i=0;$i<$max;$i++){
		$res[$i].=str_repeat(' ',$width[$i]-strwidth($res[$i]));
	}
	$k=array();
	for($i=0;$i<count($arr);$i++){
		$k[$i]=implode('',$arr[$i]);
	}
	$k[]=implode('',$res);
	return a2s($k);
}

function str_rev($s){
	if(mbs()){
		$r='';
		for($i=0;$i<mb_strlen($s);$i++){
			$r=mb_substr($s,$i,1).$r;
		}
		return $r;
	}else{
		return strrev($s);
	}
}

function case_rev($s){
	$r='';
	$a="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$b="abcdefghijklmnopqrstuvwxyz";
	for($i=0;$i<strleng($s);$i++){
		$e=substri($s,$i,1);
		if(ereg("[A-Z]",$e)){
			$r.=substr($b,(ord($e)-ord('A')),1);
		}elseif(ereg("[a-z]",$e)){
			$r.=substr($a,(ord($e)-ord('a')),1);
		}else{
			$r.=$e;
		}
	}
	return $r;
}

function rotate($s,$rot,$nrot){
	$r='';
	$a="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$b="abcdefghijklmnopqrstuvwxyz";
	$n="0123456789";
	for($i=0;$i<strleng($s);$i++){
		$e=substri($s,$i,1);
		if(ereg("[A-Z]",$e)){
			$r.=substr($a,(ord($e)-ord('A')+$rot)%26,1);
		}elseif(ereg("[a-z]",$e)){
			$r.=substr($b,(ord($e)-ord('a')+$rot)%26,1);
		}elseif(ereg("[0-9]",$e)){
			$r.=substr($n,($e+$nrot)%10,1);
		}else{
			$r.=$e;
		}
	}
	return $r;
}

function matrix_multiply($s){
$a=s2a($s,2);
if(count($a)<2){
		addmsg(ERR,'Matrix not enough');
		return $s;
}
for($i=0;$i<count($a);$i++){
	$a[$i]=s2m($a[$i]);
	$a[$i]=mfix($a[$i]);
	if($i>0 && count($a[$i-1][0])!=count($a[$i])){
		addmsg(ERR,'Columns != Rows');
		return $s;
	}
}
$ma=$a[0];
for($n=1;$n<count($a);$n++){
$mb=$a[$n];
	for($i=0;$i<count($ma);$i++){
		for($j=0;$j<count($mb[0]);$j++){
			$r[$i][$j]=0;
			for($k=0;$k<count($mb);$k++){
				$r[$i][$j]+=$ma[$i][$k]*$mb[$k][$j];
			}
		}
	}
$ma=$r;
$r=array();
}
return m2s($ma);
}

function matrix_inverse($s){
	$inv=determinant($s);
	$m=s2m($s);
	$m=mfix($m);
	if(count($m)==2){
		$r[0][0]=$m[1][1];
		$r[1][0]=$m[0][1]*(-1);
		$r[0][1]=$m[1][0]*(-1);
		$r[1][1]=$m[0][0];
		return "(1/".$inv.")*\n".m2s($r);
	}
	for($i=0;$i<count($m);$i++){
		for($j=0;$j<count($m);$j++){
			$p=0;
			$q=0;
			for($k=0;$k<count($m);$k++){
				if($k!=$i){
					for($l=0;$l<count($m);$l++){
						if($l!=$j){
							$t[$p][$q]=$m[$k][$l];
							$q++;
						}
					}
					$p++;
					$q=0;
				}
			}
			$r[$i][$j]=determinant_core($t);
			if(($i%2==1) xor ($j%2==1)){
				$r[$i][$j]*=(-1);
			}
		}
	}
	if($inv==0){
		addmsg(INFO,'det()=0 -> No Solution.');
	}
	return "(1/".$inv.")*\n".matrix_transpose(m2s($r));
}

function determinant($s){
	$m=s2m($s);
	$m=mfix($m);
	$k=count($m);
	$l=count($m[0]);
	if($k!=$l){
		addmsg(ERR,'Incoherent columns and rows.');
	}
	return determinant_core($m);
}

function determinant_core($m){
	$r=0;
	$k=count($m);
	if($k==2){
		return $m[0][0]*$m[1][1]-$m[1][0]*$m[0][1];
	}
	$f=1;
	for($w=0;$w<$k;$w++){
		$mul=$m[$w][0];
		$m2=array();
		$i1=0;
		for($i=0;$i<$k;$i++){
			if($i!=$w){
				$m2[$i1]=array();
				$i2=0;
				for($j=1;$j<$k;$j++){
					$m2[$i1][$i2]=$m[$i][$j];
					$i2++;
				}
				$i1++;
			}
		}
		$r+=$mul*$f*determinant_core($m2);
		$f*=-1;
	}
	return $r;
}

function matrix_rotate($s,$ac){
	$m=s2m($s);
	$m=mfix($m);
	$t=count($m);
	$k=count($m[0]);
	for($i=0;$i<$k;$i++){
		for($j=0;$j<$t;$j++){
			if($ac==0){
				$r[$i][$j]=$m[$j][$k-$i-1];
			}else{
				$r[$i][$j]=$m[$t-$j-1][$i];
			}
		}
	}
	return m2s($r);
}

function matrix_transpose($s){
	$m=s2m($s);
	$m=mfix($m);
	for($i=0;$i<count($m);$i++){
		for($j=0;$j<count($m[0]);$j++){
			$r[$j][$i]=$m[$i][$j];
		}
	}
	return m2s($r);
}

function tran($p,$s,$f){
	$a=explod("\n",$s);
	for($i=0;$i<count($a);$i++){
		if(ereg("^[[:space:][:punct:]CDEFGAB#bdxXMimsuagdj+/0-9\-]*$",$a[$i])){
			$r[$i]=transpose($p,$a[$i],$f);
		}else{
			$r[$i]=$a[$i];
		}
	}	
	return implode("\n",$r);
}

function transpose($p,$s,$f){
	$t[0]=array("C"=>0,"#C"=>1,"bD"=>1,"D"=>2,"#D"=>3,"bE"=>3,"E"=>4,"F"=>5,"#F"=>6,"bG"=>6,"G"=>7,"#G"=>8,"bA"=>8,"A"=>9,"#A"=>10,"bB"=>10,"B"=>11);
	$t[1]=array("C","#C","D","#D","E","F","#F","G","#G","A","#A","B");
	$t[2]=array("C","bD","D","bE","E","F","bG","G","bA","A","bB","B");
	$r='';
	$s=preg_replace("/([CDEFGAB])([#b])/","\\2\\1",$s);
	for($i=0;$i<strlen($s);$i++){
		$e=substr($s,$i,1);
		if($e=="#" && ereg("[CDEFGAB]",substr($s,$i+1,1))){
			$e.=substr($s,++$i,1);
			$r.=$t[$f][($t[0][$e]+$p)%12];
		}elseif($e=="b" && ereg("[CDEFGAB]",substr($s,$i+1,1))){
			$e.=substr($s,++$i,1);
			$r.=$t[$f][($t[0][$e]+$p)%12];
		}elseif(ereg("[CDEFGAB]",$e)){
			$r.=$t[$f][($t[0][$e]+$p)%12];
		}else{
			$r.=$e;
		}
	}
	return preg_replace("/([#b])([CDEFGAB])/","\\2\\1",$r);
}

function bitorder_en($o,$s){
	$r='';
	for($i=0;$i<8;$i++){
		$k[$i]=substr($o,$i,1)-1;
	}
	$s=bin_en($s);
	for($i=0;$i<strlen($s)/8;$i++){
		$e=substr($s,$i*8,8);
		for($j=0;$j<8;$j++){
			$r.=substr($e,$k[$j],1);
		}
	}
	return bin_de($r);
}

function bitorder_de($o,$s){
	$r='';
	for($i=0;$i<8;$i++){
		$k[substr($o,$i,1)-1]=$i;
	}
	$s=bin_en($s);
	for($i=0;$i<strlen($s)/8;$i++){
		$e=substr($s,$i*8,8);
		for($j=0;$j<8;$j++){
			$r.=substr($e,$k[$j],1);
		}
	}
	return bin_de($r);
}

function bit_rev($s){
	$r='';
	for($i=0;$i<strlen($s);$i++){
		$r.= (~ substr($s,$i,1));
	}
	return $r;
}

function pcre_valid(&$s){
	if(empty($s)){
		addmsg(ERR,'Empty PCRExpression');
		return FALSE;
	}
	$del=substr($s,0,1);
	$l=strlen($s);
	$flag=true;
	for($i=1;$i<$l;$i++){
		$e=substr($s,$i,1);
		if($e=='\\'){
			$i++;
			continue;
		}elseif($e==$del){
			$flag=false;
			break;
		}
	}
	if($flag){
		addmsg(ERR,'No delimiter in PCRE');
		return false;
	}
	if($i+1==$l){
		return true;
	}
	$m=substr($s,$i+1);
	if(ereg('[^iAmsexEU]',$m)){
		addmsg(WARN,'Unknown modifier \''.$m.'\'');
		return FALSE;
	}
	if(ereg('e',$m)){
		addmsg(WARN,'Modifier \'e\' is disabled');
		return FALSE;
	}	
	return TRUE;
}

function pcre_rep($s){
	global $patterns, $replacements;
	$p=$patterns;
	$r=$replacements;
	for($i=0;$i<count($p);$i++){
		if(!pcre_valid($p[$i])){
			continue;
		}
		if($_POST['casei']=='on'){
			$p[$i].='i';
		}
		$s=preg_replace($p[$i], $r[$i], $s);
	}
	return $s;
}

function base_conv($s,$flag=0,$from=NULL,$symbol1=NULL,$to=NULL,$symbol2=NULL){
	if(!bc()){
		addmsg(ERR,'Sorry, Numeric Base need BCMath.');
		return $s;
	}
	$pt1=$pt2='.';
	$sign1=$sign2='-';
	if($from===NULL){
		$from=$_POST['base_from'];
		$to=$_POST['base_to'];
		$symbol1=$_POST['num_base_symbol1'];
		$symbol2=$_POST['num_base_symbol2'];
		$pt1=$_POST['base_point1'];
		$pt2=$_POST['base_point2'];
		$sign1=$_POST['base_sign1'];
		$sign2=$_POST['base_sign2'];
	}
	if($flag==1){
		$tmp=$from;
		$from=$to;
		$to=$tmp;
		$tmp=$symbol1;
		$symbol1=$symbol2;
		$symbol2=$tmp;
		$tmp=$pt1;
		$pt1=$pt2;
		$pt2=$tmp;	
		$tmp=$sign1;
		$sign1=$sign2;
		$sign2=$tmp;	
	}
	$symlen1=strleng($symbol1);
	$symlen2=strleng($symbol2);
	if($from>$symlen1 || $to>$symlen2){
		addmsg(ERR,'Symbol not enough!');
		return $s;
	}
	for($i=0;$i<$symlen1;$i++){
		$de_table[substri($symbol1,$i,1).'']=$i;
	}
	if(substri($s,0,1)==$sign1){
		$s=substri($s,1);
		$neg=true;
	}else{
		$neg=false;
	}
	if(in_array($pt1,explod('',$s))){
		list($x,$y)=explod($pt1,$s);
		$ptr=true;
	}else{
		$ptr=false;
		$x=$s;
	}
	$val=0;
	for($i=0;$i<strleng($x);$i++){
		$val=bcmul($val,$from);
		$e=substri($x,$i,1);
		if(!isset($de_table[$e])){
			addmsg(WARN,'Unknown symbol.');
			$de_table[$e]=0;
		}
		$val=bcadd($val,$de_table[$e],0);
	}
	$ret='';
	if($to==10){
		for($i=0;$i<strlen($val);$i++){
			$ret.=substri($symbol2,substr($val,$i,1),1);
		}
	}else{
		while(!empty($val)){
			$m=bcmod($val,$to);
			$ret=substri($symbol2,$m,1).$ret;
			$val=bcsub($val,$m,0);
			$val=bcdiv($val,$to,0);
		}
	}
	if($ret==''){
		$ret='0';
	}
	if($neg){
		$ret=$sign2.$ret;
	}
	if($ptr){
		$pval=0;
		$y=str_rev($y);
		for($i=0;$i<strleng($y);$i++){
			$e=substri($y,$i,1);
			if(!isset($de_table[$e])){
				addmsg(WARN,'Unknown symbol.');
				$de_table[$e]=0;
			}
			$pval=bcadd($pval,$de_table[$e]);
			$pval=bcdiv($pval,$from);
		}
		$pret=array();
		if($to==10){
			for($i=2;$i<strlen($pval);$i++){
				$pret[]=substr($pval,$i,1);
			}
		}else{
			$mod=1;
			while(!empty($pval) && count($pret)<=$_POST['scale'] && bccomp($mod,'0')==1){
				$mod=bcdiv($mod,$to);
				list($r,$pval)=base_divmod($pval,$mod);
				$pret[]=$r;
			}
		}
		for($i=count($pret);$i>=0;$i--){
			if($pret[$i]!=0){
				break;
			}
		}
		$pret2='';
		for($j=0;$j<=$i;$j++){
			$pret2.=substri($symbol2,$pret[$j],1);
		}
		if(count($pret)>0){
			$ret.=$pt2.$pret2;	
		}
	}
	return $ret;
}

function base_divmod($n,$mod){
	$i=0;
	$comp=bccomp($n,$mod);
	while(($comp==0 || $comp==1) && bccomp($mod,'0')==1){
		$n=bcsub($n,$mod);
		$i++;
		$comp=bccomp($n,$mod);
	}
	return array($i,$n);
}

function gen_rep($s){
	global $patterns, $replacements;
	$p=$patterns;
	$r=$replacements;
	for($i=0;$i<count($p);$i++){
		$s=streplace($p[$i], $r[$i], $s);
	}
	return $s;
}

function gen_rep_de($s){
	global $patterns, $replacements;
	$p=$patterns;
	$r=$replacements;
	$c=count($p);
	for($i=0;$i<count($p);$i++){
		$s=streplace($r[$c-$i-1], $p[$c-$i-1], $s);
	}
	return $s;
}

function pcre_mat($s){
	global $patterns,$patternclip;
	$p=$patterns;
	$ret=array();
	for($i=0;$i<count($p);$i++){
		if(!pcre_valid($p[$i])){
			continue;
		}
		preg_match_all($p[$i],$s,$res);
		$ret=array_merge($ret,$res[$patternclip[$i]]);
	}
	addmsg(INFO,count($ret).' record(s) found.');
	return implode("\n",$ret);
}

function key_xor($a,$b){
	$r='';
	for($i=0;$i<strlen($b);$i++){
		$r.=substr($a,$i % strlen($a),1) ^ substr($b,$i,1);
	}
	return $r;
}

function base_en($s){
	if(pow(2,$_POST['base_bit'])>strleng($_POST['base_symbol'])){
		addmsg(ERR,'Symbol not enough');
		return $s;
	}
	$r='';
	$s=bin_en($s);
	$s.=str_repeat('0',($_POST['base_bit']-(strlen($s)%$_POST['base_bit']))%$_POST['base_bit']);
	$len=strlen($s)/$_POST['base_bit'];
	for($i=0;$i<$len;$i++){
		$r.=substri($_POST['base_symbol'],base_conv(substr($s,$i*$_POST['base_bit'],$_POST['base_bit']),0,2,'01',10,'0123456789'),1);
	}
	$padn=cac_func('lcmc','8,'.$_POST['base_bit'],1)/$_POST['base_bit'];
	$r.=str_repeat($_POST['base_pad'],($padn-(strleng($r)%$padn))%$padn);
	return $r;
}

function base_de($s){
	if(pow(2,$_POST['base_bit'])>strleng($_POST['base_symbol'])){
		addmsg(ERR,'Symbol not enough');
		return $s;
	}
	for($i=0;$i<strleng($_POST['base_symbol']);$i++){
		$e=substri($_POST['base_symbol'],$i,1);
		$list[$i]=$e;
		$t=base_conv($i,0,10,'0123456789',2,'01');
		$table[$e]=str_repeat('0',$_POST['base_bit']-strlen($t)).$t;
	}
	$so='';
	$len=strleng($s);
	for($i=0;$i<$len;$i++){
		$e=substri($s,$i,1);
		if(in_array($e,$list)){
			$so.=$table[$e];
		}
	}
	$len=strlen($so);
	$len-=$len%8;
	$so=substr($so,0,$len);
	return bin_de($so);
}

function chewing($s){
	$table=array('1'=>'12549;', 'q'=>'12550;', 'a'=>'12551;', 'z'=>'12552;', '2'=>'12553;', 'w'=>'12554;', 's'=>'12555;', 'x'=>'12556;', 'e'=>'12557;', 'd'=>'12558;', 'c'=>'12559;', 'r'=>'12560;', 'f'=>'12561;', 'v'=>'12562;', '5'=>'12563;', 't'=>'12564;', 'g'=>'12565;', 'b'=>'12566;', 'y'=>'12567;', 'h'=>'12568;', 'n'=>'12569;', 'u'=>'12583;', 'j'=>'12584;', 'm'=>'12585;', '8'=>'12570;', 'i'=>'12571;', 'k'=>'12572;', ','=>'12573;', '9'=>'12574;', 'o'=>'12575;', 'l'=>'12576;', '.'=>'12577;', '0'=>'12578;', 'p'=>'12579;', ';'=>'12580;', '/'=>'12581;', '-'=>'12582;', ' '=>' ', '6'=>'714; ', '3'=>'711; ', '4'=>'715; ', '7'=>'729; ');
	$r='';
	$rett=array();
	$ret=array();
	for($i=0;$i<strleng($s);$i++){
		$e=substri($s,$i,1);
		if($_POST['chewing_sort']=='on'){
			$r.=$table[$e];
		}else{
			if(isset($table[$e])){
				$rett[]=TRUE;
				$ret[]=$table[$e];
			}else{
				$rett[]=FALSE;
				$ret[]=$e;
			}
		}
	}
	if($_POST['chewing_sort']!='on'){
		for($i=0;$i<count($rett);$i++){
			if($rett[$i]){
				$ret[$i]=preg_replace('/^(\\d+);( ?)$/','&#\\1;\\2',$ret[$i]);
			}
		}
		return implode('',$ret);
	}
	$k=explod(" ",$r);
	for($i=0;$i<count($k);$i++){
		$k[$i]=substr($k[$i],0,strlen($k[$i])-1);
		$m=explode(";",$k[$i]);
		for($j=0;$j<count($m);$j++){
			if($m[$j]>=12549 && $m[$j]<=12569){
				$m[$j]-=10000;
			}
			if($m[$j]>=12570 && $m[$j]<=12582){
				$m[$j]+=50000;
			}
			if($m[$j]==714 || $m[$j]==711 || $m[$j]==715 || $m[$j]==729){
				$m[$j]+=99000;
			}
		}
		sort($m);
		for($j=0;$j<count($m);$j++){
			if($m[$j]>=2549 && $m[$j]<=2569){
				$m[$j]+=10000;
			}
			if($m[$j]>=62570 && $m[$j]<=62582){
				$m[$j]-=50000;
			}
			if($m[$j]==99714 || $m[$j]==99711 || $m[$j]==99715 || $m[$j]==99729){
				$m[$j]-=99000;
			}
		}
		$k[$i]=implode(";",$m);
		$k[$i]=str_replace(";",";&#",$k[$i]);
		if(ereg("[0-9]{3,5}",$k[$i])){
			$k[$i]="&#".$k[$i].';';
		}
	}
	$r=implode(' ',$k);
	return $r;
}

function bin_en($s){
	$t='';
	for($i=0;$i<strlen($s);$i++){
		$e=decbin(ord(substr($s, $i,1)));
		$t.=str_repeat('0', 8-strlen($e));
		$t.=$e;
	}
	return $t;
}

function bin_de($s){
	$s=preg_replace("/[^01]/",'',$s);
	$t='';
	$s=str_repeat('0', (8-strlen($s)%8)%8).$s;
	for($i=0;$i<(strlen($s)/8);$i++){
		$t.=chr(bindec(substr($s, $i*8, 8)));
	}
	return $t;
}

if(mb()){
	if(!function_exists('mb_explode')){
		function mb_explode($a,$s){
			$ret=array();
			$c=0;
			$ret[$c]=$s;
			while(($e=mb_strpos($ret[$c],$a))!==FALSE){
				$ret[$c+1]=mb_substr($ret[$c],$e+mb_strlen($a));
				$ret[$c]=mb_substr($ret[$c],0,$e);
				$c++;
			}
			return $ret;
		}
	}
	if(!function_exists('mb_stripos')){
		function mb_stripos($a,$b){
			$a=mb_strtolower($a);
			$b=mb_strtolower($b);
			$c=mb_strpos($a,$b);
			return $c;
		}
	}
	function mb_iexplode($a,$s){
		$ret=array();
		$c=0;
		$ret[$c]=$s;
		while(($e=mb_stripos($ret[$c],$a))!==FALSE){
			$ret[$c+1]=mb_substr($ret[$c],$e+mb_strlen($a));
			$ret[$c]=mb_substr($ret[$c],0,$e);
			$c++;
		}
		return $ret;
	}
}

if(!function_exists('stripos')){
	function stripos($h,$n){
		return strpos(strtolower($h),strtolower($n));
	}
}

if(!function_exists('iexplode')){
	function iexplode($a,$s){
		$ret=array();
		$c=0;
		$ret[$c]=$s;
		while(($e=stripos($ret[$c],$a))!==FALSE){
			$ret[$c+1]=substr($ret[$c],$e+strlen($a));
			$ret[$c]=substr($ret[$c],0,$e);
			$c++;
		}
		return $ret;
	}
}

function streplace($a,$b,$c){
	return implode($b,explod($a,$c));
}

function explod($a,$s){
	if(mbs()){
		if($a==''){
			$ret=array();
			for($i=0;$i<mb_strlen($s);$i++){
				$ret[]=mb_substr($s,$i,1);
			}
			return $ret;
		}
		if($_POST['casei']=='on'){
			return mb_iexplode($a,$s);
		}else{
			return mb_explode($a,$s);
		}
	}else{
		if($a==''){
			$ret=array();
			for($i=0;$i<strlen($s);$i++){
				$ret[]=substr($s,$i,1);
			}
			return $ret;
		}
		if($_POST['casei']=='on'){
			return iexplode($a,$s);
		}else{
			return explode($a,$s);
		}
	}
}

function strimwidth($s){
	if(mbs()){
		return mb_strimwidth($s,0,$_POST['stmwthl'],$_POST['stmwtha']);
	}
	return substr($s,0,$_POST['stmwthl']-strlen($_POST['stmwtha'])).$_POST['stmwtha'];
}

function array_values_recursive($a,&$arr){
	for($i=0;$i<count($a);$i++){
		if(is_array($a[$i])){
			array_values_recursive($a[$i],$arr);
		}else{
			$arr[]=$a[$i];
		}
	}
}

function super_explode($s,$lv=NULL){
	if($lv===NULL){
		$lv=count($_POST['ssep_de'])-1;
	}
	if($lv==-1){
		return $s;
	}
	$a=explod($_POST['ssep_de'][$lv],$s);
	for($i=0;$i<count($a);$i++){
		$a[$i]=super_explode($a[$i],$lv-1);
	}
	return $a;
}

function statistics($s){
	$a=super_explode($s);
	$arr=array();
	array_values_recursive($a,$arr);
	if($_POST['casei']=='on'){
		for($i=0;$i<count($arr);$i++){
			$arr[$i]=mb()?mb_strtolower($arr[$i]):strtolower($arr[$i]);
		}
	}
	$ret=array_count_values($arr);
	$r=array();
	foreach($ret as $key => $val){
		$r[]=ent_en($key)."\t".$val;
	}
	return implode("\n",$r);
}

function cac_pre($s){
	global $func,$pi;
	$pi='3.141592653589793238462643383279502884197169399375105820974944592307816406286208998628034825342117067982148086513282306647093844609550582231725359408128';
	$func=array('radians','degrees','root','abs','avedev','count','analyze','average','stdevp','stdev','round','floor','ceil','sqrt','log','sum','pow','exp','mod','sin','cos','tan','cot','sec','csc','ln','c');
	$func_s=array('radians','degrees','analyze','round','floor','ceil','sqrt','log','exp','sin','cos','tan','cot','sec','csc','ln');
	if($s==''){return 0;}
	$m=$_POST['calculator'];
	if($m==''){$m='x';}
	$m=strtolower($m);
	$m=str_replace('x','('.$s.')',$m);
	$m=str_replace('(('.$s.'))','('.$s.')',$m);
	$m=preg_replace('/(\\d)\\s+(-?)(\\d)/','\\1,\\2\\3',$m);
	$m=preg_replace('/(\\d)\\s+(-?)(\\d)/','\\1,\\2\\3',$m);	//Dont delete!
	$m=preg_replace('/\\(\\s+(-?)(\\d)/','(\\1\\2',$m);
	$m=preg_replace('/(\\d)\\s+\\)/','\\1)',$m);
	$m=preg_replace('/\\s/','',$m);
	$m=preg_replace('/(\\d)\\.(\\D)/','\\1.0\\2',$m);
	$m=preg_replace('/pi/i','('.$pi.')',$m);
	if(ereg('[<>]',$m)){
		addmsg(ERR,'Wrong input');
		return $s;
	}
	$f=0;
	for($i=0;$i<strlen($m);$i++){
		if(substr($m,$i,1)=='('){
			$f++;
		}elseif(substr($m,$i,1)==')'){
			$f--;
		}
		if($f<0){
			addmsg(ERR,'Wrong parentheses order');
			return $s;
		}
	}
	if($f>0){
	addmsg(ERR,'Lone parenthesis');
		return $s;
	}
	$f=0;
	$p=0;
	$t='';
	$temp=array();
	for($i=0;$i<strlen($m);$i++){
		$e=substr($m,$i,1);
		if($e=='|'){
			if($f==0){
				$t.='{';
				$f=1;
			}else{
				$t.='}';
				$f=0;
			}
		}else{
			$t.=$e;
			if($e=='('){
				array_push($temp,$f);
				$f=0;
			}elseif($e==')'){
				if($f==1){
					addmsg(ERR,'Wrong |');
					break;
				}
				$f=array_pop($temp);
			}
		}
	}
	$m=$t;
	$m=preg_replace('/(-?\d+(\.\d)?\d*(e\+\d|e-\d|e)?\d*)/','<\1>',$m);
	$m=preg_replace('/([)}!>])<-/','\1-<',$m);
	$m=preg_replace('/([*\/])-/','\1<-1>*',$m);
	$m=preg_replace('/([>)}!])([<({])/','\1*\2',$m);
	while(preg_match('/(\+-|--|-\+|\+\+)/',$m)){
		$m=str_replace('+-','-',$m);
		$m=str_replace('-+','-',$m);
		$m=str_replace('++','+',$m);
		$m=str_replace('--','+',$m);
	}
	if(preg_match('/[+\-*\/\^%,]{2,}/',$m)){
		addmsg(ERR,'Wrong operator');
	}
	for($i=0;$i<count($func);$i++){
		$m=preg_replace('/([!)>}])'.$func[$i].'/','\1*'.$func[$i],$m);
	}
	for($i=0;$i<count($func_s);$i++){
		$m=preg_replace('/'.$func_s[$i].'(<[^>]*>)/',$func_s[$i].'(\1)',$m);
	}
	$t=$m;
	for($i=0;$i<count($func);$i++){
		$t=str_replace($func[$i].'(','',$t);
	}
	$t=preg_replace('/<[^>]*>/i','',$t);
	$t=preg_replace('/[,()+\^\-*\/!{}\[\]%]/','',$t);
	if(strlen($t)>0){
		addmsg(ERR,'Invalid input',1);
	}
	$m=calculator('('.$m.')');
	$m=substr($m,1,strlen($m)-2);
	$m=preg_replace('/\.0+$/','',$m);
	$m=preg_replace('/\.(.*[^0])0+$/','.\1',$m);
	return $m;
}
function calculator($s){
	global $func;
	$z=$s;
	$count=0;
	$flist=implode('|',$func);
while(!ereg('^<[^>]*>$',$s)){
	if(ereg('\[<[^>]*>\]',$s)){
		#gauss(floor);
		$s=preg_replace('/\[(<[^>]*>)\]/e','cac_func("floor","\1")',$s);
		continue;
	}
	if(ereg('\[<[^>]*>(,<[^>]*>)+\]',$s)){
		#lcm;
		$s=preg_replace('/\[(<[^>]*>(,<[^>]*>)+)\]/e','cac_func("lcm","\1")',$s);
		continue;
	}
	if(ereg('\{<[^>]*>\}',$s)){
		#abs;
		$s=preg_replace('/\{(<[^>]*>)\}/e','cac_func("abs","\1")',$s);
		continue;
	}
	if(ereg('<[^>]*>!',$s)){
		#factorial;
		$s=preg_replace('/(<[^>]*>)!/e','cac_func("fac","\1")',$s);
		continue;
	}
	if(ereg('<[^>]*>\^<[^>]*>',$s)){
		#pow;
		$s=preg_replace('/(<[^>]*>)\^(<[^>]*>)/e','cac_func("pow","\1,\2")',$s);	
		continue;
	}
	if(ereg('<[^>]*>\*<[^>]*>',$s)){
		#multiply;
		$s=preg_replace('/(<[^>]*>)\*(<[^>]*>)/e','cac_func("multiply","\1,\2")',$s);	
		continue;
	}
	if(ereg('<[^>]*>/<[^>]*>',$s)){
		#divide;
		$s=preg_replace('/(<[^>]*>)\/(<[^>]*>)/e','cac_func("divide","\1,\2")',$s);	
		continue;
	}
	if(ereg('<[^>]*>%<[^>]*>',$s)){
		#mod;
		$s=preg_replace('/(<[^>]*>)%(<[^>]*>)/e','cac_func("mod","\1,\2")',$s);	
		continue;
	}
	if(ereg('<[^>]*>\+<[^>]*>',$s)){
		#plus;
		$s=preg_replace('/(<[^>]*>)\+(<[^>]*>)/e','cac_func("plus","\1,\2")',$s);	
		continue;
	}
	if(ereg('<[^>]*>-<[^>]*>',$s)){
		#minus;
		$s=preg_replace('/(<[^>]*>)-(<[^>]*>)/e','cac_func("minus","\1,\2")',$s);	
		continue;
	}
	if(preg_match('/('.$flist.')\((<[^>]*>(,<[^>]*>)*)\)/',$s)){
		#func;
		for($i=0;$i<count($func);$i++){
			$s=preg_replace('/'.$func[$i].'\((<[^>]*>(,<[^>]*>)*)\)/e','cac_func("'.$func[$i].'","\1")',$s);
		}
		continue;
	}
	if(ereg('\(<[^>]*>(,<[^>]*>)+\)',$s)){
		#gcd;
		$s=preg_replace('/\((<[^>]*>(,<[^>]*>)+)\)/e','cac_func("gcd","\1")',$s);
		continue;
	}
	$s=preg_replace('/\((<[^>]*>)\)/','\1',$s);
	if(cancel()){
		return '<0>';
	}
	if($s==$z){
		$count++;
	}else{
		$z=$s;
		$count=0;
	}
	if($count>3){
		break;
	}
}
	return $s;
}

function cac_func($f,$s,$tqwe=0){
	global $pi;
	$tma='Too many arguments';
	$tfa='Too few arguments';
	$a=explode(',',$s);
	$c=count($a);
	if($tqwe==0){
		for($i=0;$i<$c;$i++){
			$a[$i]=substr($a[$i],1,strlen($a[$i])-2);
		}
	}
	if($f=='count'){
		$r=$c;
	}elseif($f=='gcdr'){
		if($a[1]==0){
			$r=$a[0];
		}else{
			$r=cac_func('gcdr',$a[1].','.(bc()?bcmod($a[0],$a[1]):$a[0]%$a[1]),1);
		}
	}elseif($f=='root'){
		$r=pow($a[0],1/$a[1]);
	}elseif($f=='gcd'){
		$q=$a[0];
		for($i=1;$i<$c;$i++){
			$q=cac_func('gcdr',$q.','.$a[$i],1);
		}
		$r=$q;
	}elseif($f=='lcmc'){
		$gcd=cac_func('gcdr',$s,1);
		$r=bc()?bcmul(bcdiv($a[0],$gcd),$a[1]):$a[0]/$gcd*$a[1];
	}elseif($f=='lcm'){
		$r=$a[0];
		for($i=1;$i<$c;$i++){
			$r=cac_func('lcmc',$r.','.$a[$i],1);
		}
	}elseif($f=='plus'){
		$r=bc()?bcadd($a[0],$a[1]):$a[0]+$a[1];
	}elseif($f=='minus'){
		$r=bc()?bcsub($a[0],$a[1]):$a[0]-$a[1];
	}elseif($f=='multiply'){
		$r=bc()?bcmul($a[0],$a[1]):$a[0]*$a[1];
	}elseif($f=='divide'){
		if($a[1]==0){
			addmsg(ERR,'Divided by zero');
		}
		$r=bc()?bcdiv($a[0],$a[1]):$a[0]/$a[1];
	}elseif($f=='sum'){
		$r=0;
		for($i=0;$i<$c;$i++){
			$r=bc()?bcadd($r,$a[$i]):$r+$a[$i];
		}
	}elseif($f=='average'){
		$r=cac_func('sum',implode(',',$a),1);
		$r=bc()?bcdiv($r,$c):$r/$c;
	}elseif($f=='avedev'){
		$ave=cac_func('average',implode(',',$a),1);
		$r='0';
		if(bc()){
			for($i=0;$i<$c;$i++){
				$tmp=bcsub($a[$i],$ave);
				if(substr($tmp,0,1)=='-'){
					$tmp=substr($tmp,1);
				}
				$r=bcadd($r,$tmp);
				$r=bcdiv($r,$c);
			}
		}else{
			for($i=0;$i<$c;$i++){
				$tmp=$a[$i]+$ave;
				if($tmp<0){
					$tmp*=-1;
				}
				$r+=$tmp;
				$r/=$c;
			}	
		}
	}elseif($f=='stdev'){
		$ave=cac_func('average',implode(',',$a),1);
		$r=0;
		for($i=0;$i<$c;$i++){
			$r=bc()?bcadd($r,bcpow(bcsub($a[$i],$ave),'2')):$r+pow($a[$i]-$ave,2);
		}
		$r=bc()?bcsqrt(bcdiv($r,$c-1)):sqrt($r/($c-1));
	}elseif($f=='stdevp'){
		$ave=cac_func('average',implode(',',$a),1);
		$r=0;
		for($i=0;$i<$c;$i++){
			$r=bc()?bcadd($r,bcpow(bcsub($a[$i],$ave),'2')):$r+pow($a[$i]-$ave,2);
		}
		$r=bc()?bcsqrt(bcdiv($r,$c)):sqrt($r/$c);
	}elseif($f=='min'){
		if($a[0]>$a[1]){
			$r=$a[1];
		}else{
			$r=$a[0];
		}
	}elseif($f=='max'){
		if($a[0]>$a[1]){
			$r=$a[0];
		}else{
			$r=$a[1];
		}
	}elseif($f=='fac'){
		if($c>1){
			addmsg(WARN,$tma);
		}
		if(floor($a[0])!=$a[0]){
			addmsg(ERR,'Argument should be an integer');
		}
		$i=1;
		for($j=1;$j<=$a[0];$j++){
			if(bc()){
				$i=bcmul($i,$j);
			}else{
				$i*=$j;
			}
		}
		$r=$i;
	}elseif($f=='c'){
		if($c>2){
			addmsg(WARN,$tma);
		}
		if($c<2){
			addmsg(WARN,$tfa);
		}
		$r=cac_func('fac',$a[0],1)/cac_func('fac',$a[1],1)/cac_func('fac',cac_func('minus',$a[0].','.$a[1],1),1);
		
	}elseif($f=='abs'){
		for($i=0;$i<$c;$i++){
			if(substr($a[$i],0,1)=='-'){
				$a[$i]=substr($a[$i],1);
			}
		}
		$r=implode(',',$a);
	}elseif($f=='ceil'){
		for($i=0;$i<$c;$i++){
			$a[$i]=ceil($a[$i]);
		}
		$r=implode(',',$a);
	}elseif($f=='floor'){
		for($i=0;$i<$c;$i++){
			$a[$i]=floor($a[$i]);
		}
		$r=implode(',',$a);
	}elseif($f=='round'){
		if($c>2){
			addmsg(WARN,$tma);
			$r=0;
		}
		if($c==1){
			$a[1]=0;
		}
		$r=round($a[0],$a[1]);	
	}elseif($f=='radians'){
		$r=bc()?bcdiv(bcmul($a[0],$pi),180):($a[0]*$pi)/180;
	}elseif($f=='degrees'){
		$r=bc()?bcdiv(bcmul($a[0],180),$pi):($a[0]*180)/$pi;
	}elseif($f=='log'){
		if($c>2){
			addmsg(WARN,$tma);
			$r=0;
		}
		if($c==1){
			$a[1]='10';
		}
		$r=log($a[0],$a[1]);
	}elseif($f=='ln'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=log($a[0]);
	}elseif($f=='pow'){
		if($c>2){
			addmsg(WARN,$tma);
			$r=0;
		}
		if($c<2){
			addmsg(WARN,$tma);
			$r=0;
		}
		if(strpos($a[1],'.')===false){
			$r=bc()?bcpow($a[0],$a[1]):pow($a[0],$a[1]);
		}else{
			$r=pow($a[0],$a[1]);
		}
	}elseif($f=='sqrt'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=bc()?bcsqrt($a[0]):sqrt($a[0]);
	}elseif($f=='exp'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=exp($a[0]);
	}elseif($f=='mod'){
		if($c>2){
			addmsg(WARN,$tma);
			$r=0;
		}
		if($c<2){
			addmsg(WARN,$tfa);
			$r=0;
		}
		$r=bc()?bcmod($a[0],$a[1]):$a[0] % $a[1];
	}elseif($f=='sin'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=sin($a[0]);
	}elseif($f=='cos'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=cos($a[0]);
	}elseif($f=='tan'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=tan($a[0]);
	}elseif($f=='cot'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=cot($a[0]);
	}elseif($f=='sec'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=sec($a[0]);
	}elseif($f=='csc'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=csc($a[0]);
	}elseif($f=='analyze'){
		if($c>1){
			addmsg(WARN,$tma);
		}
		$r=$a[0];
		$t=$r;
		$b=array();
		$e=array();
		$z=0;
		$i=2;
		$m='';
if(bc()){
		if(substr($r,0,1)=='-'){
			$m.='&nbsp;-1';
			$t=bcmul($t,'-1');
		}
		while($t!='1'){
			if(bcmod($t,$i)=='0'){
				$z=count($b);
				$t=bcdiv($t,$i);
				$b[$z]=$i;
				$e[$z]=1;
			}
			while(bcmod($t,$i)=='0'){
				$e[$z]++;
				$t=bcdiv($t,$i);
			}
			$i=bcadd($i,1);
		}
}else{
		if(substr($r,0,1)=='-'){
			$m.='&nbsp;-1';
			$t*=-1;
		}
		while($t!=1){
			$bak=$t;
			if($t%$i==0){
				$z=count($b);
				$t/=$i;
				$b[$z]=$i;
				$e[$z]=1;
			}
			while($t%$i==0){
				$e[$z]++;
				$t=$t/$i;
			}
			$i++;
			if($bak==$t){
				addmsg(ERR,'The number might be too large.');
				break;
			}
		}
}
		for($i=0;$i<=$z;$i++){
			$b[$i]=preg_replace('/\.0+$/','',$b[$i]);
			$m.='&nbsp;&nbsp;'.$b[$i].($e[$i]>1?('<sup>'.$e[$i].'</sup>'):'');
		}
		addmsg(INFO,$r.'&nbsp;='.$m);
	}
	if($tqwe==0){
		$r='<'.$r.'>';
	}
	return $r;
}

function hex_en($s){
	$t='';
	for($i=0;$i<strlen($s);$i++){
		$e=dechex(ord(substr($s, $i,1)));
		$t.=str_repeat('0', 2-strlen($e));
		$t.=$e;
	}
	return $t;
}

function hex_de($s){
	$s=preg_replace("/[^0-9A-Fa-f]/",'',$s);
	$t='';
	$s=str_repeat('0', (2-strlen($s)%2)%2).$s;
	$n=strlen($s)/2;
	for($i=0;$i<$n;$i++){
		$t.=chr(hexdec(substr($s, $i*2, 2)));
	}
	return $t;
}

function ASCIIFilter($s){
	if(strlen($_POST['ssep_de'][0])==1){
	$q=$_POST['ssep_de'][0];
	}else{
	$q=' ';
	addmsg(ERR,'Length of SubSeparator != 1, use SPACE');
	}
	if(substr($s,0,1)==$q){
		$r='SP';
	}elseif(ord(substr($s,0,1))>127){
		$r=strtoupper(hex_en(substr($s,0,1)));
	}else{
		$r=substr($s,0,1);
	}
	for($i=1;$i<strlen($s);$i++){
		if(substr($s,$i,1)==$q){
			$r.=' SP';
		}elseif(ord(substr($s,$i,1))>127){
			$r.=' '.strtoupper(hex_en(substr($s,$i,1)));
		}else{
			$r.=' '.substr($s,$i,1);
		}
	}
	return $r;
}

function sqr($s,$codec=0){
	if($codec==0){
		$func='sqr_en_part';
	}else{
		$func='sqr_de_part';	
	}
	$l=strleng($s);
	if($_POST['sqr_cl']=='auto'){
		$r=$c=ceil(sqrt($l));
		addmsg(INFO,'Rows/Colums: '.$r);
	}else{
		if(empty($_POST['sqr_r']) || empty($_POST['sqr_c'])){
			addmsg(ERR,'Incorrect Rows/Columns');
			return $s;
		}
		$r=$_POST['sqr_r'];
		$c=$_POST['sqr_c'];
	}
	$con=$_POST['sqr_r']*$_POST['sqr_c'];
	if($_POST['sqr_cl']=='man' && $l>$con){
		$ret='';
		$left=$l%$con;
		$l-=$left;
		$l/=$con;
		for($i=0;$i<$l;$i++){
			$ret.=$func(substri($s,$i*$con,$con),$r,$c);
		}
		$ret.=$func(substri($s,$i*$con),$r,$c);
		return $ret;
	}else{
		return $func($s,$r,$c);
	}
}

function sqr_en_part($s,$r,$c){
	$l=strleng($s);
	$p=0;
	for($i=0;$i<$r;$i++){
		for($j=0;$j<$c;$j++){
			if($p<$l){
				$a[$i][$j]=substri($s,$p,1);
			}else{
				$a[$i][$j]='';
			}
			$p++;
		}
	}
	$ret='';
	for($j=0;$j<$c;$j++){
		for($i=0;$i<$r;$i++){
			$ret.=$a[$i][$j];
		}
	}
	return $ret;
}

function sqr_de_part($s,$r,$c){
	$l=strleng($s);
	$p=0;
	for($j=0;$j<$c;$j++){
		for($i=0;$i<$r;$i++){
			if(($i*$c)+$j<$l && $p<$l){
				$a[$i][$j]=substri($s,$p,1);
				$p++;
			}else{
				$a[$i][$j]='';
			}
		}
	}
	$ret='';
	for($i=0;$i<$r;$i++){
		for($j=0;$j<$c;$j++){
			$ret.=$a[$i][$j];
		}
	}
	return $ret;
}

function ASCIIFilter_de($s){
	if(strlen($_POST['ssep_de'][0])==1){
	$q=$_POST['ssep_de'][0];
	}else{
	$q=' ';
	addmsg(ERR,'Length of SubSeparator != 1, use SPACE');
	}
	$r='';
	$a=explode($q,$s);
	for($i=0;$i<count($a);$i++){
		if($a[$i]=='SP'){
			$r.=' ';
		}elseif(preg_match("/^[a-f0-9]{2}$/i",$a[$i])){
			$r.=hex_de($a[$i]);
		}else{
			$r.=$a[$i];
		}
	}
	return $r;
}

function dec_en($s){
	$t='';
	for($i=0;$i<strlen($s);$i++){
		$e=ord(substr($s, $i,1));
		$t.=str_repeat('0', 3-strlen($e));
		$t.=$e;
	}
	return $t;
}

function dec_de($s){
	$s=preg_replace("/[^0-9]/",'',$s);
	$t='';
	$s=str_repeat('0', (3-strlen($s)%3)%3).$s;
	for($i=0;$i<(strlen($s)/3);$i++){
		$t.=chr(substr($s, $i*3, 3));
	}
	return $t;
}

function oct_en($s){
	$t='';
	for($i=0;$i<strlen($s);$i++){
		$e=decoct(ord(substr($s, $i,1)));
		$t.=str_repeat('0', 3-strlen($e));
		$t.=$e;
	}
	return $t;
}

function oct_de($s){
	$s=preg_replace("/[^0-7]/",'',$s);
	$t='';
	$s=str_repeat('0', (3-strlen($s)%3)%3).$s;
	for($i=0;$i<(strlen($s)/3);$i++){
		$t.=chr(octdec(substr($s, $i*3, 3)));
	}
	return $t;
}

function preg_explode($a,$s){
	if(!pcre_valid($a)){
		return array($s);
	}
	$ret=array();
	$c=0;
	preg_match_all($a,$s,$arr);
	$ret[0]=$s;
	for($i=0;$i<count($arr[0]);$i++){
		$p=strpos($ret[$c],$arr[0][$i]);
		$ret[$c+1]=$arr[0][$i];
		$ret[$c+2]=substr($ret[$c],$p+strlen($arr[0][$i]));
		$ret[$c]=substr($ret[$c],0,$p);
		$c+=2;
	}
	return $ret;
}

function str_mutate($s){
	$ret='';
	$tmp='';
	$s.=' ';
	for($i=0;$i<strleng($s);$i++){
		$e=substri($s,$i,1);
		if(preg_match('/[^a-z]/is',$e) || strlen($e)>1){
			$a=$c='';
			if(strlen($tmp)>=$_POST['mut_l']+$_POST['mut_r']){
				if($_POST['mut_l']>0){
				$a=substr($tmp,0,$_POST['mut_l']);
				}
				if($_POST['mut_r']>0){
				$c=substr($tmp,$_POST['mut_r']*-1);
				}
				$tmp=substr($tmp,$_POST['mut_l'],strlen($tmp)-$_POST['mut_l']-$_POST['mut_r']);
			}
			if($_POST['mut_fit']=='on'){
				$tmp=str_mut_fit($tmp);
			}else{
				$tmp=str_shuffle($tmp);
			}
			$ret.=$a.$tmp.$c.$e;
			$tmp='';
		}else{
			$tmp.=$e;
		}
	}
	$ret=substr($ret,0,strlen($ret)-1);
	return $ret;
}
function str_mut_fit($s){
	$list='bdfghjklpqty';
	$ret='';
	$tmp='';
	for($i=0;$i<strlen($s);$i++){
		$e=substr($s,$i,1);
		if(strpos($list,$e)!==FALSE){
			$ret.=str_shuffle($tmp).$e;
			$tmp='';
		}else{
			$tmp.=$e;
		}
	}
	$ret.=str_shuffle($tmp);
	return $ret;
}

function ent_en($s){
	$r='';
	for($i=0;$i<strleng($s);$i++){
		$e=substri($s,$i,1);
		switch($e){
			case "\\": $r.='\\\\'; break;
			case "\t": $r.='\\t'; break;
			case "\r": $r.='\\r'; break;
			case "\n": $r.='\\n'; break;
			case "\f": $r.='\\f'; break;
			case "\0": $r.='\\0'; break;
			default: $r.=$e; break;
		
		}
	}
	return $r;
}

function ent_de($s){
	$r='';
	for($i=0;$i<strleng($s);$i++){
		if(substri($s,$i,1)!="\\"){
			$r.=substri($s,$i,1);
		}else{
			switch(substri($s,$i+1,1)){
				case "\\": $r.="\\"; break;
				case "t": $r.="\t"; break;
				case "r": $r.="\r"; break;
				case "n": $r.="\n"; break;
				case "f": $r.="\f"; break;
				case "0": $r.="\0"; break;
				default: $r.=("\\".substri($s,$i+1,1)); break;
			}
			$i++;
		}
	}
	return $r;
}

function en($method, $s){
	switch($method){
		case 'snd': $s=soundex($s); break;
		case 'bin': $s=bin_en($s); break;
		case 'dec': $s=dec_en($s); break;
		case 'oct': $s=oct_en($s); break;
		case 'hex': $s=hex_en($s); break;
		case 'rot': $s=rotate($s,$_POST['rot'],$_POST['nrot']); break;
		case 'url': $s=($_POST['url_raw']=='on')?rawurlencode($s):urlencode($s); break;
		case 'raw': break;
		case 'stmwth': $s=strimwidth($s); break;
		case 'rpt': $s=str_repeat($s,$_POST['rpt']); break;
		case 'rev': $s=str_rev($s); break;
		case 'crv': $s=case_rev($s); break;
		case 'nbase': $s=base_conv($s,0); break;
		case 'base': $s=base_en($s); break;
		case 'rep': $s=gen_rep($s); break;
		case 'pcr': $s=pcre_rep($s); break;
		case 'pcm': $s=pcre_mat($s); break;
		case 'spe': $s=htmlspecialchars($s); break;
		case 'hen': $s=((mbs())?mb_convert_encoding($s,'HTML-ENTITIES'):htmlentities($s)); break;
		case 'md5': $s=md5($s); break;
		case 'sha1': $s=sha1($s); break;
		case 'crc16': $s=sprintf("%x",crc32($s)); break;
		case 'crc32': $s=sprintf("%x",crc32($s)); break;
		case 'srt': $k=s2a($s); sort($k); $s=a2s($k); break;
		case 'stu': $s=mbs()?mb_strtoupper($s):strtoupper($s); break;
		case 'bbs': $s=bbs2html($s); break;
		case 'bbd': $s=bbs2html_dc($s); break;
		case 'unq': $s=uniq($s,0); break;
		case 'mut': $s=str_mutate($s); break;
		case 'ttb': $s=totable($s);break;
		case 'acc': $s=accumulation($s,0); break;
		case 'stl': $s=mbs()?mb_strtolower($s):strtolower($s); break;
		case 'ucw': $s=ucwords($s); break;
		case 'ctr': $s=counter($s); break;
		case 'swd': $s=strwidth($s); break;
		case 'cor': $s=correct($s); break;
		case 'det': $s=determinant($s); break;
		case 'uue': $s=convert_uuencode($s); break;
		case 'msk': $s=network($s); break;
		case 'ref': $s=sqr_reflect($s); break;
		case 'che': $s=chewing($s); break;
		case 'rf'; $s=sqr($s,0); break;
		case 'cac': $s=cac_pre($s); break;
		case 'mmtp': $s=matrix_multiply($s); break;
		case 'mro': $s=matrix_rotate($s,0); break;
		case 'miv': $s=matrix_inverse($s); break;
		case 'mtr': $s=matrix_transpose($s); break;
		case 'ascii': $s=ASCIIFilter($s); break;
		case 'key': $s=key_xor($_POST['key'],$s); break;
		case 'bre': $s=bit_rev($s); break;
		case 'bod': $s=bitorder_en($_POST['order'],$s); break;
		case 'tra': $s=tran($_POST['transpose'],$s,2); break;
		case 'sta': $s=statistics($s); break;
		default: addmsg(WARN,'Undefined Method: '.$method);
	}
	return $s;
}

function de($method, $s){
	switch($method){
		case 'snd': break;
		case 'bin': $s=bin_de($s); break;
		case 'dec': $s=dec_de($s); break;
		case 'oct': $s=oct_de($s); break;
		case 'hex': $s=hex_de($s); break;
		case 'rot': $s=rotate($s,26-$_POST['rot'],10-$_POST['nrot']); break;
		case 'url': $s=($_POST['url_raw']=='on')?rawurldecode($s):urldecode($s); break;
		case 'ur2': $s=urldecode($s); break;
		case 'raw': break;
		case 'stmwth': break;
		case 'rev': $s=str_rev($s); break;
		case 'spe': $s=html_entity_decode($s); break;
		case 'hen': break;
		case 'nbase': $s=base_conv($s,1); break;
		case 'base': $s=base_de($s); break;
		case 'md5': addmsg(INFO,'<a href="http://www.md5lookup.com/?category=main&page=search" target="_blank">http://www.md5lookup.com</a>'); break;
		case 'stu': break;
		case 'crv': $s=case_rev($s); break;
		case 'stl': break;
		case 'ucw': break;
		case 'bbs': break;
		case 'srt': $k=s2a($s); rsort($k); $s=a2s($k); break;
		case 'bbd': break;
		case 'rpt': break;
		case 'unq': $s=uniq($s,1); break;
		case 'rf'; $s=sqr($s,1); break;
		case 'pcr': break;
		case 'acc': $s=accumulation($s,1); break;
		case 'uue': $s=convert_uudecode($s); break;
		case 'det': break;
		case 'ttb': break;
		case 'mut': break;
		case 'sha1': break;
		case 'msk': break;
		case 'ref': $s=sqr_reflect($s); break;
		case 'crc16': break;
		case 'crc32': break;
		case 'sta': break;
		case 'rep': $s=gen_rep_de($s); break;
		case 'cor': break;
		case 'pcm': break;
		case 'ascii': $s=ASCIIFilter_de($s); break;
		case 'ave': break;
		case 'miv': break;
		case 'mmtp': break;
		case 'mro': $s=matrix_rotate($s,1); break;
		case 'mtr': $s=matrix_transpose($s); break;
		case 'cac': break;
		case 'ctr': break;
		case 'swd': break;
		case 'che': break;
		case 'key': $s=key_xor($_POST['key'],$s); break;
		case 'bre': $s=bit_rev($s); break;
		case 'bod': $s=bitorder_de($_POST['order'],$s); break;
		case 'tra': $s=tran(12-$_POST['transpose'],$s,1); break;
		default: addmsg(ERR,'Undefined Method: '.$method);
	}
	return $s;
}
function proc($s){
if($_POST['processs']=='en'){
	$bat=explode(",",$_POST['batch2']);
	for($i=0;$i<count($bat);$i++){
		list($a,$m)=explode("-",$bat[$i]);
		if(trim($a)=="e"){
			$s=en(trim($m),$s);
		}
		if(trim($a)=="d"){
			$s=de(trim($m),$s);
		}
	}
}
if($_POST['processs']=='de'){
	$bat=explode(",",$_POST['batch2']);
	for($i=count($bat)-1;$i>=0;$i--){
		list($a,$m)=explode("-",$bat[$i]);
		if(trim($a)=="e"){
			$s=de(trim($m),$s);
		}
		if(trim($a)=="d"){
			$s=en(trim($m),$s);
		}
	}
}
return $s;
}

function in_opt_range($n,$lv,$x){
	if(empty($GLOBALS['opt_oper'][$lv])){
		return TRUE;
	}
	if($_POST['plus_1']=='on'){
		$n++;
		$x++;
	}
	$arr=explode(',',$GLOBALS['opt_oper'][$lv]);
	$ret=false;
	for($i=0;$i<count($arr);$i++){
		$flag=FALSE;
		$arr[$i]=preg_replace('/[^0-9\\-?!*$%+]/','',$arr[$i]);
		while(substr($arr[$i],0,1)=='!'){
			$flag=!$flag;
			$arr[$i]=substr($arr[$i],1);
		}
		$arr[$i]=str_replace('!','',$arr[$i]);
		if(strlen($arr[$i])==0){
			$ret=TRUE;
		}elseif(preg_match('/^\\d+$/',$arr[$i])){
			if($n==$arr[$i]){
				$ret=TRUE;
			}
		}elseif(preg_match('/^\\d+-$/',$arr[$i])){
			$no=preg_replace('/^(\\d+)-$/','\\1',$arr[$i]);
			if($n>=$no){
				$ret=true;
			}else{
				$ret=false;
			}
		}elseif(preg_match('/^-\\d+$/',$arr[$i])){
			$no=preg_replace('/^-(\\d+)$/','\\1',$arr[$i]);
			if($n<=$no){
				$ret=true;
			}else{
				$ret=false;
			}
		}elseif(preg_match('/^\\d+-\\d+$/',$arr[$i])){
			$n1=preg_replace('/^(\\d+)-(\\d+)$/','\\1',$arr[$i]);
			$n2=preg_replace('/^(\\d+)-(\\d+)$/','\\2',$arr[$i]);
			if($n>=$n1 && $n<=$n2){
				$ret=true;
			}else{
				$ret=false;
			}
		}elseif(preg_match('/^\\+\\d+%\\d+$/',$arr[$i])){
			$n1=preg_replace('/^\\+(\\d+)%(\\d)+$/','\\1',$arr[$i]);
			$n2=preg_replace('/^\\+(\\d+)%(\\d)+$/','\\2',$arr[$i]);
			if(($n+$n1)%$n2==0){
				$ret=true;
			}else{
				$ret=false;
			}
		}elseif($arr[$i]=='$'){
			if($n+1==$x){
				$ret=true;
			}else{
				$ret=false;
			}
		}elseif(preg_match('/[*?]/',$arr[$i])){
			$pat='';
			for($i=0;$i<strlen($arr[$i]);$i++){
				$e=substr($arr[$i],$i,1);
				if($e=='*'){
					$pat.='\\d*';
				}elseif($e=='?'){
					$pat.='\\d';
				}else{
					$pat.=$e;
				}
			}
			if(preg_match('/^'.$pat.'$/',$n)){
				$ret=true;
			}else{
				$ret=false;
			}
		}
		if($flag){
			if($ret){
				$ret=false;
			}else{
				$ret=true;
			}
		}
	}
	return $ret;
}

function pro($s,$lv){
	if($lv==count($GLOBALS['sep_array'])){
		return proc($s);
	}
	if($_POST['sep_pcre']=='on'){
		$a=preg_explode($GLOBALS['sep_array'][$lv],$s);
		$step=2;
	}else{
		$a=explod($GLOBALS['sep_array'][$lv],$s);
		$step=1;
	}
	$ct=0;
	$x=count($a);
	for($i=0;$i<$x;$i+=$step){
		if(in_opt_range($ct,$lv,$x)){
			$a[$i]=pro($a[$i],$lv+1);
		}
		$ct++;
	}
	if($_POST['sep_pcre']=='on'){
		$s=implode('',$a);
	}else{
		$s=implode($GLOBALS['sep_array'][$lv],$a);
	}
	return $s;
}

function radio($name,$val,$des){
	echo '<input type="radio" id="'.$name.'_'.$val.'" name="'.$name.'" value="'.$val.'" ';
	if($_POST[$name]==$val){
		echo 'checked="checked" ';
	}
	echo '/><label for="'.$name.'_'.$val.'">'.$des.'</label>';
}

function chkbx($tag,$des,$enable=TRUE){
	echo '<input type="checkbox" id="'.$tag.'" name="'.$tag.'" ';
	if($_POST[$tag]=='on'){
		echo 'checked="checked" ';
	}
	if($enable!==TRUE){
		echo 'disabled="disabled" ';
	}
	echo '/><label for="'.$tag.'">'.$des.'</label>';
}
#</function>
if($_GET['appendix']=='source'){
	header('Content-Type: text/plain');
	$r=file_get_contents($_SERVER['SCRIPT_FILENAME']);
	$r=str_replace("\r\n","\n",$r);
	die($r);
}elseif($_GET['appendix']=='connector'){
	htmlhead();
?>
<script type="text/javascript">
function init(){
smitharray=new Object;
count=0;
document.getElementById('bt').value='new Amidala['+count+']';
}
function newsmith(){
smitharray[count]=window.open('<?echo $_SERVER['PHP_SELF']?>?smith='+count);
count++;
document.getElementById('bt').value='new Smith['+count+']';
}
function go(){
var alist=document.getElementById('order').value.split(',');
var nextptr=new Object;
for(i=0;i<alist.length-1;i++){
	id=parseInt(alist[i+1]);
	if(parseInt(alist[i])==parseInt(document.getElementById('trigger').value) && typeof(smitharray[id])!='undefined' && !smitharray[id].closed){
		smitharray[id].document.getElementById('form').submit();
	}
}
}
</script>
<body onload="init();" style="background:#abf;">
<input type="button" onClick="newsmith()" id="bt" /><br />
Order:<input type="text" size="30" id="order" />
<input type="button" onClick="go()" id="trigger" style="display:none;" />
</body>
</html>
<?
die();
}
#<init>
if($_POST['action']=='yes'){
	if(mbs()){
		if(!mb_internal_encoding($_POST['ccharset'])){
			addmsg(WARN,'Failed setting MBString encoding. Maybe the charset you are using is not support.');
			$_POST['mbstring']='off';
		}
	}
	$_POST['in_sess_slot']=intval($_POST['in_sess_slot'])%8;
	$_POST['out_sess_slot']=intval($_POST['out_sess_slot'])%8;
	if($_POST['input']=='file' && $_FILES['fin']['tmp_name']!="none" && $_FILES['fin']['tmp_name']!="" && $_FILES['fin']['size']>0){
		$s=file_get_contents($_FILES['fin']['tmp_name']);
		unlink($_FILES['fin']['tmp_name']);
		$oridata='Please Re-Upload';
	}elseif($_POST['input']=='session' && isset($_SESSION['data'])){
		$s=$_SESSION['data'][$_POST['in_sess_slot']];
		if($_POST['ibk']=='on'){
			$backup=$s;
		}else{
			$backup='';
		}
		$oridata=$s;		
	}else{
		$_POST['input']='text';
		$s=$_POST['text'];
		if($_POST['ibk']=='on'){
			$backup=$s;
		}else{
			$backup='';
		}
		$oridata=$s;
	}
	$_POST['base_bit']=abs(intval($_POST['base_bit']));
	$_POST['base_pad']=substri($_POST['base_pad'],0,1);
	$_POST['base_from']=abs(intval($_POST['base_from']));
	if($_POST['base_from']<2){
		$_POST['base_from']=2;
		addmsg(ERR,'Numeric base should be >=2.');
	}
	$_POST['base_to']=abs(intval($_POST['base_to']));
	if($_POST['base_to']<2){
		$_POST['base_to']=2;
		addmsg(ERR,'Numeric base should be >=2.');
	}
	$_POST['rot']=$_POST['rot']%26;
	$_POST['nrot']=$_POST['nrot']%10;
	$method=$_REQUEST['method'];
	$dir=$_POST['dir'];
	$_POST['trows']=abs($_POST['trows']+0);
	$_POST['tcols']=abs($_POST['tcols']+0);
	$_POST['text_size']=abs($_POST['text_size']+0);
	$_POST['transpose']=$_POST['transpose']%12;
	$_POST['rpt']+=0;
	$_POST['stmwthl']+=0;
	$_POST['scale']=abs(intval($_POST['scale']));
	if(local() || $_POST['scale']<=50){
		if(bc()){
			bcscale($_POST['scale']);
		}
		ini_set('precision',$_POST['scale']);
	}else{
		$_POST['scale']=50;
		addmsg(WARN,'Remote user\'s precision is limited to 50.');
	}
	$_POST['base_point1']=substri($_POST['base_point1'],0,1);
	$_POST['base_point2']=substri($_POST['base_point2'],0,1);
	$_POST['base_sign1']=substri($_POST['base_sign1'],0,1);
	$_POST['base_sign2']=substri($_POST['base_sign2'],0,1);
	$_POST['sepr']=str_replace("\r\n","\n",$_POST['sepr']);
	$_POST['ssep']=str_replace("\r\n","\n",$_POST['ssep']);
	$tmp=explod("\n",$_POST['ssep']);
	$_POST['ssep_arg']=$_POST['ssep_de']=array();
	for($i=0;$i<count($tmp);$i++){
		list($_POST['ssep_de'][$i],$_POST['ssep_arg'][$i])=explod("\n",$tmp[$i]);
		$_POST['ssep_de'][$i]=ent_de($_POST['ssep_de'][$i]);
	}
	$_POST['ssep_de']=array_reverse($_POST['ssep_de']);
	$_POST['sqr_r']=intval($_POST['sqr_r']);
	$_POST['sqr_c']=intval($_POST['sqr_c']);
	$_POST['mut_l']=abs(intval($_POST['mut_l']));
	$_POST['mut_r']=abs(intval($_POST['mut_r']));
	$GLOBALS['sep_array']=explod("\n",$_POST['sepr']);
	if($_POST['sep']=="on"){
	for($i=0;$i<count($GLOBALS['sep_array']);$i++){
		$tmp=explode("\t",$GLOBALS['sep_array'][$i]);
		$GLOBALS['sep_array'][$i]=ent_de($tmp[0]);
		$GLOBALS['opt_oper'][$i]=$tmp[1];
	}
	}
	$tmp=$GLOBALS['sep_array'];
	sort($tmp);
	$flag=false;
	for($i=1;$i<count($tmp);$i++){
		if($tmp[$i]==$tmp[$i-1]){
			$flag=true;
		}
	}
	if($flag){
		addmsg(WARN,'Duplicated Separator');
	}
	if(trim($_POST['batch'])==''){
		$_POST['batch2']=($_POST['mode']=="en"?'e':'d').'-'.$method;
		$_POST['processs']='en';
	}elseif(!ereg("^(e|d)-[A-Za-z0-9]+(, *(e|d)-[A-Za-z0-9]+)*$",$_POST['batch'])){
		addmsg(ERR,'Incorrect Batch Format');
		$_POST['batch2']='e-raw';
		$_POST['processs']=$_POST['process'];
	}else{
		$_POST['batch2']=$_POST['batch'];
		$_POST['processs']=$_POST['process'];
	}
}else{
#Default setting;
	if($_GET['action']=='clear'){
		unset($_SESSION);
		if(isset($_COOKIE[session_name()])){
			setcookie(session_name(), '', time()-42000, '/');
		}
		unset($_COOKIE[session_name()]);
		session_destroy();
	}
	$_POST['passtonext']=$_POST['plus_1']=$_POST['jmpmsg']=$_POST['sess_txt_also']=$_POST['scr']='on';
	$_POST['mfix_pad']='';
	$_POST['curtab']='gen';
	$_POST['input']='text';
	$_POST['casei']='off';
	$_POST['rot']=13;
	$_POST['nrot']=5;
	$_POST['num_base_symbol1']=$_POST['num_base_symbol2']='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$_POST['base_point1']=$_POST['base_point2']='.';
	$_POST['base_sign1']=$_POST['base_sign2']='-';
	$_POST['base_bit']=6;
	$_POST['base_symbol']='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	$_POST['base_pad']='=';
	$_POST['scale']=50;
	$_POST['base_from']=10;
	$_POST['base_to']=16;
	if(isset($_REQUEST['method'])){
		$method=$_REQUEST['method'];
	}else{
		$method='raw';
	}
	if(isset($_GET['charset'])){
		$_POST['charset']=$_GET['charset'];
	}elseif(!empty($_SERVER['HTTP_ACCEPT_CHARSET'])){
		$tmp=explode(',',$_SERVER['HTTP_ACCEPT_CHARSET']);
		$_POST['charset']=$tmp[0];
	}else{
		$_POST['charset']='utf-8';
	}
	$_POST['out']='text';
	$dir="LTR";
	$_POST['process']=$_POST['mode']='en';
	$_POST['stmwthl']=12;
	$_POST['stmwtha']='..';
	$_POST['trows']=15;
	$_POST['tcols']=35;
	$_POST['transpose']=0;
	$_POST['ssep']="\\n\\n\n\\n\n\\t";
	$_POST['chewing_sort']=$_POST['ttb_brd']=$_POST['ttb_ibrd']=$_POST['ibk']=$_POST['ref_ver']=$_POST['ref_hor']=$_POST['url_raw']='on';
	$_POST['ttb_align']="left";
	$_POST['calculator']='x';
	$backup=$_POST['sqr_r']=$_POST['sqr_c']='';
	$_POST['sqr_cl']='auto';
	$_POST['rpt']=$_POST['mut_l']=$_POST['mut_r']='1';
	if(mb()){
		$_POST['mbstring']='on';
	}else{
		$_POST['mbstring']='off';
	}
	$_POST['text_size']='12';
}
$pattern=str_replace("\r\n","\n",$_POST['pattern']);
$patterns=explod("\n",$pattern);
for($i=0;$i<count($patterns);$i++){
	list($patterns[$i],$patternclip[$i])=explod("\t",$patterns[$i]);
	$patternclip[$i]=abs(intval($patternclip[$i]));
	$patterns[$i]=ent_de($patterns[$i]);
}
$replacement=str_replace("\r\n","\n",$_POST['replacement']);
$replacements=explod("\n",$replacement);
for($i=0;$i<count($replacements);$i++){
	$replacements[$i]=ent_de($replacements[$i]);
}
if(!isset($_POST['order'])){
	$_POST['order']="12345678";
}else{
	$n=0;
	$m=1;
	for($i=0;$i<8;$i++){
		$n+=substr($_POST['order'],$i,1);
		$m*=substr($_POST['order'],$i,1);
	}
	if($n!=36 || $m!=40320 || !ereg("[1-8]{8}",$_POST['order'])){
		$_POST['order']="12345678";
	}
}
#</init>
if($_POST['scr']=='on'){
	$s=str_replace("\r\n","\n",$s);
}
if($_POST['sep']=='on'){
	$s=pro($s,0);
}else{
	$s=proc($s);
}
if($_POST['out']=='file'){
	set_time_limit(600);
	header("Content-Type: application/force-download");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Disposition: attachment; filename=untitled");
	die($s);
}elseif($_POST['out']=='blank'){
	die($s);
}elseif($_POST['out']=='session'){
	if(local() || strlen($s)<=102400){
		$_SESSION['data'][$_POST['out_sess_slot']]=$s;
		if($_POST['sess_txt_also']!='on'){
			$s='';
		}
	}else{
		addmsg(WARN,'Remote user\'s quota of each session slot is 100KB.');
	}
}
$tabs=array(
array('gen','General'),
array('conf','Configuration'),
array('arg','Arguments'),
array('txt','Textarea'),
array('char','Charset'),
array('cli','Information'),
array('msg','Message')
);
#</php>
function htmlhead(){
global $ver_serial;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $_POST['charset'];?>" />
<title>Amidala<?
if(isset($_REQUEST['smith'])){
echo '['.intval($_REQUEST['smith']).']';
}
?> - ver. <?echo $ver_serial;
if(ip()=='127.0.0.1'){
	echo ' <Local Connection>';
}?> </title>
<?
}
htmlhead();
?>
<script type="text/javascript">
var s;
function doKeyDown(event){
	if (event.ctrlKey && event.keyCode == 13){
		getobj('form').submit();
		return false;
	}
} 
function init(){
szobj='rep';
szctl(5);
szobj='sepr';
szctl(5);
szobj='ssep';
szctl(5);
szobj='text';
szctl(5);
assis(0);
textconfig(0);
showtab('<?echo ($_POST['jmpmsg']=='on' && count($msg)>0)?'msg':$_POST['curtab'];?>');
<?if(count($msg)==0){?>
getobj('msgt').style.display='none';
<?}?>
getobj('text').focus();<?
if($_POST['action']=='yes' && isset($_REQUEST['smith']) && $_POST['passtonext']){
echo 'opener.document.getElementById(\'trigger\').value='.$_REQUEST['smith'].';';
echo 'opener.document.getElementById(\'trigger\').click();';
}
?>}

function getobj(t){
	return document.getElementById(t);
}

function textconfig(f){
	var t,text=getobj('text');
	t=parseFloat(getobj('text_cols').value);
	if(t<=0){
		t=35;
	}
	text_width=t;
	text.style.width=t*20+"px";
	getobj('text_cols').value=t;
	t=parseFloat(getobj('text_rows').value);
	if(t<=0){
		t=15;
	}
	text_height=t;
	text.style.height=t*20+"px";
	getobj('text_rows').value=t;
	if(f==1){return;}
	t=parseFloat(getobj('text_size').value);
	if(t<=0){
		t=12;
	}
	text.style.fontSize=t+"pt";
	getobj('text_size').value=t;
	text.dir=getobj('text_dir').value;
}

function ssep(s){
	getobj('ssep').value=s;
}

function assis(s){
	var msg = new Object;
//[Begin of Help]
	msg['raw']='Color Definition:\n<a class="ow">One Way Function</a>\n<a class="ed">Encode = Decode</a>';
	msg['srt']='Use 1 SubSeparator\nEncode: ascend sort\nDecode: descend sort';
	msg['tra']='Unit: semitone\nEncode: raise tone (prefers to b)\nDecode: fall tone (prefers to #)';
	msg['ttb']='Skip HtmlSpecialChars: Auto On\nUse SubSeparator\n\nGeneral Usage:\nSubSeparator: \\t';
	msg['che']='Skip HtmlSpecialChars: Auto On';
	msg['ascii']='Use 1 SubSeparator\n\nGeneral Usage:\nSubSeparator: SPACE';
	msg['cor']='Use 1 SubSeparator\n\nGeneral Usage:\nSubSeparator: \\n';
	msg['acc']='Use 1 SubSeparator';
	msg['ave']='Use 1 SubSeparator';
	msg['unq']='Use 1 SubSeparator\nEncode: Keep the first sample\nDecode: Keep the last sample';
	msg['sdv']='Use 1 SubSeparator';
	msg['ssdv']='Use 1 SubSeparator';
	msg['mtr']='Use 2 SubSeparator';
	msg['mro']='Use 2 SubSeparator';
	msg['miv']='Use 2 SubSeparator';
	msg['mmtp']='Use 3 SubSeparator';
	msg['det']='Use 2 SubSeparator';
	msg['bbd']='For Big5 only';
	msg['cac']='Use \'x\' to represent original data.';
//[End of Help]
	if(typeof(msg[getobj('method').value])=="undefined"){
		getobj('help').innerHTML="Nothing to tell you.";
	}else{
		getobj('help').innerHTML=msg[getobj('method').value].replace(/\n/g,"<br />");
	}
	if(s==1){
		switch(getobj('method').value){
			case 'ttb':getobj('ssp').checked=true; break;
			case 'che':getobj('ssp').checked=true; break;
			default:getobj('ssp').checked=<?echo $_POST['ssp']=='on'?'true':'false';?>; break;
		}
	}
}
//<Begin of EnableTabinTextarea> Source:http://www.webdeveloper.com/forum/showthread.php?s=&threadid=32317
function setSelectionRange(input, selectionStart, selectionEnd) {
  if (input.setSelectionRange) {
    input.focus();
    input.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (input.createTextRange) {
    var range = input.createTextRange();
    range.collapse(true);
    range.moveEnd('character', selectionEnd);
    range.moveStart('character', selectionStart);
    range.select();
  }
}

function replaceSelection (input, replaceString) {
	if (input.setSelectionRange) {
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
    
		if (selectionStart != selectionEnd){ 
			setSelectionRange(input, selectionStart, selectionStart + 	replaceString.length);
		}else{
			setSelectionRange(input, selectionStart + replaceString.length, selectionStart + replaceString.length);
		}

	}else if (document.selection) {
		var range = document.selection.createRange();

		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			range.text = replaceString;

			 if (!isCollapsed)  {
				range.moveStart('character', -replaceString.length);
				range.select();
			}
		}
	}
}


// We are going to catch the TAB key so that we can use it, Hooray!
function catchTab(item,e){
	if(navigator.userAgent.match("Gecko")){
		c=e.which;
	}else{
		c=e.keyCode;
	}
	if(c==9){
		replaceSelection(item,String.fromCharCode(9));
		setTimeout("getobj('"+item.id+"').focus();",0);	
		return false;
	}
		    
}
//<End of EnableTabinTextarea>
function showtab(t){
	var tabs = new Array();
	var i;
<?
for($i=0;$i<count($tabs);$i++){
	echo 'tabs['.$i."]='".$tabs[$i][0]."';\n";
}
?>
	for(i=0;i<tabs.length;i++){
		getobj(tabs[i]).style.display='none';
		getobj(tabs[i]+'t').style.backgroundColor='#ccf';
		getobj(tabs[i]+'t').style.color='#777';
	}
	getobj(t+'t').style.backgroundColor='#ddf';
	getobj(t+'t').style.color='#000';
	getobj(t).style.display='block';
	getobj('curtab').value=t;
}

function szctl(n){
	if(n==1){
		szctl(2);
		szctl(4);
	}else if(n==3){
		szctl(2);
		szctl(6);
	}else if(n==7){
		szctl(4);
		szctl(8);
	}else if(n==9){
		szctl(6);
		szctl(8);
	}else if(n==2){
		if(szobj=='text'){
getobj('text_rows').value=text_height+5;
textconfig(1);
		}else if(szobj=='sepr'){
getobj('sepr').rows+=5;
		}else if(szobj=='ssep'){
getobj('ssep').rows+=5;
		}else if(szobj=='rep'){
getobj('pattern').rows+=5;
getobj('replacement').rows+=5;
		}
	}else if(n==4){
		if(szobj=='text'){
if(text_width>5){
getobj('text_cols').value=text_width-5;
}
textconfig(1);
		}else if(szobj=='sepr'){
if(getobj('sepr').cols>20)getobj('sepr').cols-=20;
		}else if(szobj=='ssep'){
if(getobj('ssep').cols>20)getobj('ssep').cols-=20;
		}else if(szobj=='rep'){
if(getobj('pattern').cols>20)getobj('pattern').cols-=20;
getobj('replacement').cols=getobj('pattern').cols;
		}
	}else if(n==6){
		if(szobj=='text'){
getobj('text_cols').value=text_width+5;
textconfig(1);
		}else if(szobj=='sepr'){
getobj('sepr').cols+=20;
		}else if(szobj=='ssep'){
getobj('ssep').cols+=20;
		}else if(szobj=='rep'){
getobj('pattern').cols+=20;
getobj('replacement').cols+=20;	
		}
	}else if(n==8){
		if(szobj=='text'){
if(text_height>5){
getobj('text_rows').value=text_height-5;
}
textconfig(1);
		}else if(szobj=='sepr'){
if(getobj('sepr').rows>5)getobj('sepr').rows-=5;
		}else if(szobj=='ssep'){
if(getobj('ssep').rows>5)getobj('ssep').rows-=5;
		}else if(szobj=='rep'){
if(getobj('pattern').rows>5)getobj('pattern').rows-=5;
getobj('replacement').rows=getobj('pattern').rows;
		}
	}else if(n==5){
		if(szobj=='text'){
getobj('text_rows').value=<?echo $_POST['trows'];?>;
getobj('text_cols').value=<?echo $_POST['tcols'];?>;
textconfig(1);
		}else if(szobj=='sepr'){
getobj('sepr').cols=20;
getobj('sepr').rows=6;
		}else if(szobj=='ssep'){
getobj('ssep').cols=20;
getobj('ssep').rows=6;
		}else if(szobj=='rep'){
getobj('pattern').rows=1;
getobj('replacement').rows=1;
getobj('pattern').cols=70;
getobj('replacement').cols=70;
		}
	}
}
</script>
<style type="text/css">
	body{background-color:#abf;}
	#msg a{
		color:#00f;
		font-size:10pt;
	}
	#msg span{font-size:10pt;}
	a.link{text-decoration:none;color:#808080;}
	a:hover.link{text-decoration:underline;position:relative;top:1px;left:1px;}
	.ed {color:#00ff00;}
	.ow {color:#ff0000;}
	.rw {color:#555555;}
	td {vertical-align:top;}
	.tabc{background-color:#ddf; display:none; padding:10px; height:13em; overflow:auto;}
	.tab{color:#777; font-weight:bold; padding-left:0.7em; padding-right:0.7em; margin-right:5px; cursor:pointer;}
	.block{float:left; margin-right:50px;}
	.pt{cursor:pointer;}
</style>
</head>
<body onload="init();" onkeydown="doKeyDown(event)"><a name="top"></a>
<form method="post" action="<?echo $_SERVER['PHP_SELF'];?>" name="form" id="form" enctype="multipart/form-data">
<?
if(isset($_REQUEST['smith'])){
echo '<input type="hidden" name="smith" value="'.intval($_REQUEST['smith']).'" />';
}
?>
<input type="hidden" name="action" value="yes" />
<input type="hidden" name="curtab" id="curtab" value="<?echo $_POST['curtab'];?>" />
<table><tr>
<td style="width:12em;">
<fieldset><legend>Assistance</legend>
<div id="help" style="font-size:10pt; background-color:#ddf; color:#333; height:10em;"></div>
</fieldset>
<input type="button" onclick="if(getobj('ccharset').value=='undefined'){alert('Please fill out your current charset!'); getobj('ccharset').value=''; getobj('ccharset').focus();}else{getobj('form').submit();}" value="Submit" /> <input type="button" onclick="bkbk=getobj('text').value; getobj('text').value=getobj('backup').value; getobj('backup').value=bkbk; if(this.value=='Undo'){this.value='Redo'}else{this.value='Undo'}" value="Undo"> <input type="button" onClick="if(confirm('Sure to clear ?')){location.href='<?echo $_SERVER['PHP_SELF'];?>?action=clear'}" value="Clear" />
<fieldset><legend>Size Controller</legend>
<div style="text-align:center;">
<table>
<tr><td><a class="pt" onclick="szctl(7);">&#8598;</a></td><td><a class="pt" onclick="szctl(8);">&uarr;</a></td><td><a class="pt" onclick="szctl(9);">&#8599;</a></td></tr>
<tr><td><a class="pt" onclick="szctl(4);">&larr;</a></td><td><a class="pt" onclick="szctl(5);">&#9678;</a></td><td><a class="pt" onclick="szctl(6);">&rarr;</a></td></tr>
<tr><td><a class="pt" onclick="szctl(1);">&#8601;</a></td><td><a class="pt" onclick="szctl(2);">&darr;</a></td><td><a class="pt" onclick="szctl(3);">&#8600;</a></td></tr>
</table>
</div>
</fieldset>
</td>
<td>
<textarea onkeydown="return catchTab(this,event);" onfocus="szobj='text';" id="text" name="text" rows="15" cols="85"><?
if(cancel()){
echo $oridata;
}else{
echo ($_POST['ssp']=="on")?$s:htmlspecialchars($s);
}
?></textarea></td></tr></table>
<textarea id="backup" style="display:none;"><?echo htmlspecialchars($backup);?></textarea>
<div>
<?
for($i=0;$i<count($tabs);$i++){
?>
<span class="tab" id="<?echo $tabs[$i][0];?>t" onclick="showtab('<?echo $tabs[$i][0];?>')"><?echo $tabs[$i][1];?></span>
<?
}
if(!isset($_REQUEST['smith'])){?><span class="tab" style="color:#777; background:#ccf;" onclick="document.location.href='<?echo $_SERVER['PHP_SELF'];?>?appendix=connector';">Connector</span><?}?>
</div>
<div id="gen" class="tabc"><span class="block">
<table>
<tr><td>Input: </td><td>
<input type="radio" name="input" onclick="getobj('help').innerHTML='Auto turn on \'Skip &amp;lt;CR&amp;gt;\''; getobj('scr').checked=true; getobj('fin').disabled=true;" id="intext" value="text" <?echo $_POST['input']=='text'?'checked="checked"':'';?>/><label for="intext">Text Area</label>
<input type="radio" onclick="getobj('help').innerHTML='Auto turn off \'Skip &amp;lt;CR&amp;gt;\''; getobj('scr').checked=false; getobj('fin').disabled=false;" name="input" id="infile" value="file" <?echo $_POST['input']=='file'?'checked="checked"':'';?>/><label for="infile">File</label><input type="file" id="fin" name="fin" <?echo $_POST['input']=='file'?'':'disabled="disabled"';?>/>
<?if(isset($_SESSION['data'])){?><input type="radio" name="input" onclick="getobj('help').innerHTML='Auto turn off \'Skip &amp;lt;CR&amp;gt;\''; getobj('scr').checked=false; getobj('fin').disabled=true;" id="insession" value="session" <?echo $_POST['input']=='session'?'checked="checked"':'';?>/><label for="insession">Session</label><select name="in_sess_slot"><?
for($i=0;$i<count($_SESSION['data']);$i++){
echo '<option value="'.$i.'"'.($i==$_POST['in_sess_slot']?' selected="selected"':'').'>Slot '.$i.'</option>';
}
?></select><?}?></td></tr>
<tr><td>Output: </td><td>
<input type="radio" name="out" onclick="getobj('form').target='_self'" id="ta" value="text" <?echo ($_POST['out']=="text")?'checked="checked" ':'';?>/><label for="ta">Text Area</label>
<input type="radio" name="out" id="fd" onclick="getobj('form').target='_self'" value="file" <?echo ($_POST['out']=="file")?'checked="checked" ':'';?>/><label for="fd">File Download</label>
<input type="radio" name="out" onclick="getobj('form').target='_blank'" id="bf" value="blank" <?echo ($_POST['out']=="blank")?'checked="checked" ':'';?>/><label for="bf">Blank Frame</label>
<input type="radio" name="out" onclick="getobj('form').target='_self'" id="sess" value="session" <?echo ($_POST['out']=='session')?'checked="checked" ':'';?>/><label for="sess">Session</label>
<select name="out_sess_slot"><?
for($i=0;$i<count($_SESSION['data']);$i++){
	echo '<option value="'.$i.'"'.($i==$_POST['out_sess_slot']?' selected="selected"':'').'>Slot '.$i.'</option>';
}
if($i<8){ echo '<option value="'.$i.'">Slot '.$i.' (New)</option>'; }
?></select>
</td></tr>
<tr><td>Method: </td><td>
<select name="method" id="method" onchange="assis(1);">
<?
$methods_table=array(
array('raw','RAW (Output input)','rw'),
array('acc','Accumulation','no'),
array('srt','Sort','no'),
array('unq','Unique','no'),
array('tra','Transpose','no'),
array('msk','Network','ow'),
array('ttb','To Table','ow'),
array('cac','Calculator','ow'),
array('nbase','Numeric Base','no'),
array('det','Determinant Value','ow'),
array('mmtp','Matrix Multiplication','ow'),
array('mtr','Matrix/Square Transpose','ed'),
array('miv','Matrix Inverse','ow'),
array('mro','Square Rotate','no'),
array('ref','SquareReflect','ed'),
array('cor','Correct','ow'),
array('ascii','ASCIIFilter','no'),
array('sta','Statistics','ow'),
array('snd','Soundex','ow'),
array('che','Chewing','ow'),
array('rep','Replace','no'),
array('pcr','PCRE Replace','ow'),
array('pcm','PCRE Match','ow'),
array('spe','HtmlSpecialChars','ow'),
array('hen','HtmlEntity','ow'),
array('rpt','Repeat','ow'),
array('rev','Reverse','ed'),
array('crv','Case Reverse','ed'),
array('stu','StringToUpper','ow'),
array('stl','StringToLower','ow'),
array('ucw','UppercaseTheFirstCharacter','ow'),
array('ctr','Counter','ow'),
array('swd','StringWidth','ow'),
array('stmwth','StringTrimWidth','ow'),
array('bod','BitOrder','no'),
array('bre','BitReverse (not)','ed'),
array('key','Key (xor)','ed'),
array('rot','StringRotate','no'),
array('mut','StringMutate','ow'),
array('rf','Square','no'),
array('md5','MD5','no'),
array('sha1','SHA-1','ow'),
array('crc16','CRC16','ow'),
array('crc32','CRC32','ow'),
array('url','URL','no'),
array('uue','UUEncode','no'),
array('base','Base','no'),
array('bin','Bin','no'),
array('oct','Oct','no'),
array('dec','Dec','no'),
array('hex','Hex','no'),
array('bbd','BBS -> HTML (Double Color)','ow'),
array('bbs','BBS -> HTML','ow'),
);
for($i=0;$i<count($methods_table);$i++){
	echo '<option ';
	if($methods_table[$i][2]!='no'){
		echo 'class="'.$methods_table[$i][2].'" ';
	}
	echo 'value="'.$methods_table[$i][0].'" ';
	if($method==$methods_table[$i][0]){
		echo 'selected="selected" ';
	}
	echo '>';
	echo htmlspecialchars($methods_table[$i][1]);
	echo '</option>'."\n";
}
?>
</select> <input type="button" value="Add to Batch" onClick="var i; i=((getobj('mode_en').checked==true)?'e':'d'); if(getobj('batch').value==''){getobj('batch').value=i+'-'+getobj('method').value;}else{getobj('batch').value=getobj('batch').value+', '+i+'-'+getobj('method').value}" /></td></tr>
<tr><td></td><td><?radio('mode','en','Encode');?> <?radio('mode','de','Decode');?></td></tr>
<tr><td>Batch: </td><td><input type="text" size="70" name="batch" id="batch" value="<?echo $_POST['batch'];?>" /><br /><?radio('process','en','Forward');?> <?radio('process','de','Backward');?> <input type="button" value="Clear" onclick="getobj('batch').value='';" /></td></tr>
</table></span><span class="block"><?chkbx('sep','Separator');?><br />
<span style="padding-left:10px;">
<?chkbx('sep_pcre','PCRE');?><br />
<textarea name="sepr" onkeydown="return catchTab(this,event);" onfocus="szobj='sepr'" id="sepr"><?echo $_POST['sepr'];?></textarea><br /><?chkbx('plus_1','Plus 1');?>
</span></span>
</div>
<div id="conf" class="tabc"><span class="block">
<?chkbx('jmpmsg','Auto Jump to Message');?><br />
<?chkbx('mbstring','Enable MBString',mb());?><br />
<?chkbx('casei','Case-Insensitive');?><br />
<?chkbx('sess_txt_also','Copy Session output to Textarea');?><br />
<?chkbx('scr','Skip &lt;CR&gt;');?><br />
<?chkbx('ssp','Skip HtmlSpecialChars');?><br />
<?chkbx('passtonext','Pass to Next');?><br />
<?chkbx('ibk','Enable Undo');?><br />
Precision: <input type="text" name="scale" size="2" value="<?echo $_POST['scale'];?>" /><br />
Square Padding: <input type="text" size="10" name="mfix_pad" value="<?echo $_POST['mfix_pad'];?>" />
</span>
<span class="block">
SubSeparator:<br /><textarea onfocus="szobj='ssep'" name="ssep" id="ssep" onkeydown="return catchTab(this,event);"><?echo $_POST['ssep'];?></textarea>
</span></div>
<div id="arg" class="tabc"><span class="block">
<table>
<tr><td>Match/Replace:</td><td><input type="button" value="Clear" onClick="getobj('pattern').value=''; getobj('replacement').value='';" /></td></tr>
<tr><td>Pattern:</td><td><textarea onkeydown="return catchTab(this,event);" onfocus="szobj='rep'" name="pattern" id="pattern"><?echo htmlspecialchars($pattern);?></textarea></td></tr>
<tr><td>Replacement:</td><td><textarea onfocus="szobj='rep'" name="replacement" id="replacement"><?echo htmlspecialchars($replacement);?></textarea></td></tr>
<tr><td>Calculator:</td><td><input type="text" name="calculator" value="<?echo $_POST['calculator']?>" /></td></tr>
<tr><td>Key:</td><td><input type="text" name="key" value="<?echo $_POST['key']?>" /></td></tr>
<tr><td>BitOrder:</td><td><input type="text" name="order" maxlength="8" size="6" value="<?echo $_POST['order'];?>" /></td></tr>
<tr><td>Transpose:</td><td><select name="transpose">
<?
for($i=0;$i<12;$i++){
echo '<option value="'.$i.'"'.(($_POST['transpose']==$i)?' selected="selected"':'').'>'.$i.'</option>'."\n";
}
?>
</select></td></tr>
<tr><td>Rotate:</td><td>Letter<select name="rot">
<?
for($i=0;$i<26;$i++){
echo '<option value="'.$i.'"'.(($_POST['rot']==$i)?' selected="selected"':'').'>'.$i.'</option>'."\n";
}
?></select> Number<select name="nrot">
<?
for($i=0;$i<10;$i++){
echo '<option value="'.$i.'"'.(($_POST['nrot']==$i)?' selected="selected"':'').'>'.$i.'</option>'."\n";
}
?></select></td></tr>
<tr><td>StringTrimWidth</td><td>Width:<input type="text" name="stmwthl" size="2" value="<?echo $_POST['stmwthl'];?>" /> Append:<input type="text" name="stmwtha" size="5" value="<?echo $_POST['stmwtha'];?>" /></td></tr>
<tr><td>Base:</td><td>Bits<input type="text" name="base_bit" size="2" value="<?echo $_POST['base_bit'];?>" /> Pad<input type="text" name="base_pad" size="2" value="<?echo $_POST['base_pad'];?>" /><br />
Symbol<input type="text" name="base_symbol" size="70" value="<?echo $_POST['base_symbol'];?>" /></td></tr>
<tr><td>Numeric Base:</td><td>From<input type="text" name="base_from" size="2" value="<?echo $_POST['base_from'];?>" /> To<input type="text" name="base_to" size="2" value="<?echo $_POST['base_to'];?>" /></td></tr>
<tr><td>From Symbols</td><td><input type="text" name="num_base_symbol1" size="80" value="<?echo $_POST['num_base_symbol1'];?>" /><br />
Sign<input type="text" name="base_sign1" size="2" value="<?echo $_POST['base_sign1'];?>" /> Point<input type="text" name="base_point1" size="2" value="<?echo $_POST['base_point1'];?>" /></td></tr>
<tr><td>To Symbols</td><td><input type="text" name="num_base_symbol2" size="80" value="<?echo $_POST['num_base_symbol2'];?>" /><br />
Sign<input type="text" name="base_sign2" size="2" value="<?echo $_POST['base_sign2'];?>" /> Point<input type="text" name="base_point2" size="2" value="<?echo $_POST['base_point2'];?>" /></td></tr></td></tr>
</table></span>
<span class="block">
<table>
<tr><td>Square:</td><td><?radio('sqr_cl','auto','Auto');?> <?radio('sqr_cl','man','Manual');?> Rows:<input type="text" name="sqr_r" size="2" value="<?echo $_POST['sqr_r'];?>" /> Columns:<input type="text" name="sqr_c" size="2" value="<?echo $_POST['sqr_c'];?>" /></td></tr>
<tr><td>SquareReflect</td><td><?chkbx('ref_ver','Vertical');?> <?chkbx('ref_hor','Horizonal');?></td></tr>
<tr><td>StringMutate:</td><td><?chkbx('mut_fit','ShapeFit');?> Keep Left:<input type="text" name="mut_l" size="2" value="<?echo $_POST['mut_l'];?>" /> Right:<input type="text" name="mut_r" size="2" value="<?echo $_POST['mut_r'];?>" /></td></tr>
<tr><td>URL:</td><td><?chkbx('url_raw','RFC1738');?></td></tr>
<tr><td>Chewing:</td><td><?chkbx('chewing_sort','Sort');?></td></tr>
<tr><td>Repeat:</td><td><input type="text" name="rpt" size="2" value="<?echo $_POST['rpt'];?>" /></td></tr>
<tr><td rowspan="3">To Table:</td><td>Border: <?chkbx('ttb_brd','Outer');?> <?chkbx('ttb_ibrd','Inner');?></td></tr>
<tr><td><?chkbx('ttb_mono','MonoWidth');?></td></tr>
<tr><td>Align:<?radio('ttb_align','left','Left');?> <?radio('ttb_align','center','Center');?> <?radio('ttb_align','right','Right');?></td></tr>
</table>
</span>
</div>
<div id="txt" class="tabc">
<table>
<tr><td>Size:</td><td>Width: <input type="text" size="2" name="tcols" id="text_cols" value="<?echo $_POST['tcols'];?>" /> Height: <input type="text" size="2" name="trows" id="text_rows" value="<?echo $_POST['trows']?>" /></td></tr>
<tr><td>Directionality:</td><td><select name="dir" id="text_dir"><option value="LTR"<?echo ($dir=="LTR")?' selected="selected"':'';?>>Left to Right</option><option value="RTL"<?echo ($dir=="RTL")?' selected="selected"':'';?>>Right to Left</option></select></td></tr>
<tr><td>Font:</td><td>Size: <input type="text" size="2" name="text_size" id="text_size" value="<?echo $_POST['text_size'];?>" /></td></tr>
</table>
<input type="button" value="Apply" onclick="textconfig(0);" />
</div>
<div id="char" class="tabc">
Current : <script type="text/javascript">
	document.write('<input type="text" name="ccharset" id="ccharset" value="'+((typeof(document.charset)=="undefined")?document.characterSet:document.charset)+'" />');
</script><br />
Next : <input type="text" name="charset" id="charset" value="<?echo $_POST['charset'];?>" /><select onChange="getobj('charset').value=this.value;">
<option value="<?echo $_POST['charset'];?>" selected="selected">Default(<?echo $_POST['charset'];?>)</option>
<option value="UTF-8">Unicode(UTF-8)</option>
<option value="big5">Chinese Traditional(big5)</option>
<option value="gb2312">Chinese Simplified(gb2312)</option>
<option value="GB18030">Chinese Simplified(GB18030)</option>
<option value="hz-gb-2312">Chinese Simplified(hz-gb-2312)</option>
<option value="euc-kr">Korean(euc-kr)</option>
<option value="ks_c_5601-1987">Korean(ks_c_5601-1987)</option>
<option value="euc-jp">Japanese(euc-jp)</option>
<option value="shift-jis">Japanese(shift-jis)</option>
<option value="koi8-r">Slavic(koi8-r)</option>
<option value="koi8-r">Slavic(koi8-u)</option>
<option value="iso-8859-5">Slavic(iso-8859-5)</option>
<option value="windows-1251">Slavic(windows-1251)</option>
<option value="cp866">Slavic(cp866)</option>
<option value="windows-1258">Vietnamese(windows-1258)</option>
<option value="windows-874">Thai(windows-874)</option>
<option value="iso-8859-1">Western European(iso-8859-1)</option>
<option value="windows-1252">Western European(windows-1252)</option>
<option value="iso-8859-2">Central European(iso-8859-2)</option>
<option value="windows-1250">Central European(windows-1250)</option>
<option value="ibm852">Central European(ibm852)</option>
<option value="iso-8859-4">Baltic Sea(iso-8859-4)</option>
<option value="windows-1257">Baltic Sea(windows-1257)</option>
<option value="iso-8859-7">Greek(iso-8859-7)</option>
<option value="windows-1253">Greek(windows-1253)</option>
<option value="iso-8859-9">Turkish(iso-8859-9)</option>
<option value="windows-1254">Turkish(windows-1254)</option>
</select>
</div>
<div id="cli" class="tabc">
<table>
<tr><td>IP</td><td><?echo ip();?></td></tr>
<tr><td>FQDN</td><td><?echo gethostbyaddr(ip());?></td></tr>
<tr><td>User Agent</td><td><?echo $_SERVER['HTTP_USER_AGENT'];?></td></tr>
<tr><td>Accept Language</td><td><?echo $_SERVER["HTTP_ACCEPT_LANGUAGE"];?></td></tr>
<tr><td>Accept Charset</td><td><?echo empty($_SERVER["HTTP_ACCEPT_CHARSET"])?'Not Provided':$_SERVER["HTTP_ACCEPT_CHARSET"];?></td></tr>
<tr><td>Request Duration</td><td><?echo mtime()-$time_begin;?></td></tr>
</table>
</div>
<div id="msg" class="tabc">
<?
if(count($msg)>0){
echo implode('<br />',$msg);
}else{
echo 'No message.';
}
?>
</div>
<p>
<span style="text-align:left; float:left;"><a class="link" href="mailto:buganini@gmail.com">Contact me</a></span>
<span style="text-align:right; float:right;"><a class="link" style="font-size:9pt;" href="<?echo $_SERVER['PHP_SELF'];?>?appendix=source">ver. <?echo $ver_serial;?></a></span>
</p>
</form>
</body>
</html>
