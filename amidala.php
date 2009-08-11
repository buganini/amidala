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
#<php>
#Designed by gmobug, from CNMC.HSNU
$ver_serial="2006092100";

if(empty($_REQUEST['debug'])){
ini_set('display_errors', '0');
}else{
error_reporting(E_ALL);
}

define('ERR','Error');
define('WARN','Warning');
define('INFO','Information');

set_time_limit(180);

if(!function_exists('preg_replace')){
	addmsg('Critical Error','PCRE Library Not Found!');
}

if(!bc()){
	addmsg(WARN,'BC Math Library Not Found! Calculator will operate with low precision.');
}else{
	bcscale(150);
}

if(!function_exists('file_get_contents')){
	function file_get_contents($a){
		return implode('',file($a));
	}
}
#<function>
$cancel=0;

function bc(){
if(function_exists('bcadd')){
return TRUE;
}else{
return FALSE;
}
}

function addmsg($t,$s,$f=0){
	global $msg,$cancel;
	$msg[]='<a style="color:#00f">'.$t.':</a> '.$s;
	if($f==1){
		$cancel=1;
	}
}

function cancel(){
	global $cancel;
	if($cancel==1){
		return TRUE;
	}else{
		return FALSE;
	}
}

function bbs2html_dc($s){
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
$r='<html><head><meta http-equiv="Content-Type" content="text/html; charset='.$_POST['charset'].'">
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
if(eregi("^big5$",$_POST['ccharset']) && $flag==1){
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
if(eregi("^big5$",$_POST['ccharset']) && $flag==1){
	$r.=chr(0).'</a><a class="dc" style="'.$bak.'">'.$h.'</a><a style="'.$style.'">'.$h;
	$flag=0;
}else{
			$r.=(chr(0).'</a><a style="'.$style.'">');
}
		}
		$i=$i+$l+2;
	}elseif(eregi("^big5$",$_POST['ccharset']) && preg_match("/[\xA1-\xF9][\x40-\x7E\xA1-\xFE]/",substr($s,$i,2))){
		$r.=substr($s,$i,2);
		$i++;
	}elseif(eregi("^big5$",$_POST['ccharset']) && preg_match("/[\xA1-\xF9]\x1B/",substr($s,$i,2))){
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

function s2a($s,$k=1){
	if(count($_POST['ssep_de'])<$k){
		addmsg(ERR,'SubSeparator not enough');
		$r[0]=$s;
		return $r;
	}
	if($_POST['ssepupcre']=="on"){
		$r=preg_split($_POST['ssep_de'][count($_POST['ssep_de'])-$k],$s);
	}else{
		$r=explode($_POST['ssep_de'][count($_POST['ssep_de'])-$k],$s);
	}
	return $r;
}
function a2s($s,$k=1){
	$r=implode($_POST['ssep_de'][count($_POST['ssep_de'])-$k],$s);
	return $r;
}

function s2m($s,$cl=" \t\n\r\0\x0b"){
	$s=trim($s,$cl);
	$r=s2a($s,2);
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
	for($i=0;$i<count($s);$i++){
		for($j=0;$j<$k;$j++){
			if(empty($s[$i][$j]) && $s[$i][$j]!='0'){
				$s[$i][$j]='';
			}
		}
	}
	return $s;
}

function m2s($s){
	for($i=0;$i<count($s);$i++){
		$s[$i]=a2s($s[$i]);
	}	
	$r=a2s($s,2);
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
			if(empty($len[$j]) || strlen($l[$i][$j])>$len[$j]){
				$len[$j]=strlen($l[$i][$j]);
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
			if(empty($l[$i][$j]) && $l[$i][$j]!='0'){
				$l[$i][$j]=str_repeat(' ',$len[$j]);
			}else{
if($_POST['ttb_align']=="left"){
				$l[$i][$j].=str_repeat(' ',$len[$j]-strlen($l[$i][$j]));
}elseif($_POST['ttb_align']=="center"){
				$l[$i][$j]=str_repeat(' ',floor(($len[$j]-strlen($l[$i][$j]))/2)).$l[$i][$j].str_repeat(' ',ceil(($len[$j]-strlen($l[$i][$j]))/2));
}elseif($_POST['ttb_align']=="right"){
				$l[$i][$j]=str_repeat(' ',$len[$j]-strlen($l[$i][$j])).$l[$i][$j];
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
	$bak=$k;
	$m=0;
	for($i=0;$i<count($k);$i++){
		$tmp[$i]=strlen($k[$i]);
		if(strlen($k[$i])>strlen($k[$m])){
			$m=$i;
		}
	}
	$tmp2=$k[0];
	$k[0]=$k[$m];
	$k[$m]=$tmp2;
	if(!same($tmp)){
		addmsg('Notice','Incoherent Length');
	}
	$r='';
	for($i=0;$i<strlen($k[0]);$i++){
		for($j=0;$j<count($k);$j++){
			if($i<strlen($k[$j])){
			$tmp1[$j]=substr($k[$j],$i,1);			
			}else{
			$tmp1[$j]='';
			}
		}
		if(same($tmp1)){
			$r.=' ';
		}else{
			$r.='X';
		}
	}
	$bak[count($bak)]=$r;
	return a2s($bak);
}
function same($s){
	for($i=1;$i<count($s);$i++){
		if($s[$i]!=$s[0]){
			return FALSE;
		}
	}
	return TRUE;
}
function case_rev($s){
	$r='';
	$a="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$b="abcdefghijklmnopqrstuvwxyz";
	for($i=0;$i<strlen($s);$i++){
		$e=substr($s,$i,1);
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
	for($i=0;$i<strlen($s);$i++){
		$e=substr($s,$i,1);
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
$a=s2a($s,3);
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
	$m=s2m($s," \n\r\0\x0b");
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
	$m=s2m($s," \n\r\0\x0b");
	$m=mfix($m);
	for($i=0;$i<count($m);$i++){
		for($j=0;$j<count($m[0]);$j++){
			$r[$j][$i]=$m[$i][$j];
		}
	}
	return m2s($r);
}

function tran($p,$s,$f){
	$a=explode("\n",$s);
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

function pcre_rep($s){
	global $pattern, $replacement;
	if($_POST['pattern']!=''){
		$p=explode("\n",$pattern);
		$r=explode("\n",$replacement);
		for($i=0;$i<count($p);$i++){
			$m=preg_replace('/.*?([a-z]*)$/i','\1',ent_de($p[$i]));
			if(ereg('[^iAmsexEU]',$m)){
				$m=preg_replace('[iAmsexEU]','',$m);
				addmsg(WARN,'Unknown modifier \''.$m.'\'');
				continue;
			}
			if(ereg('e',$m)){
				addmsg(WARN,'Modifier \'e\' is disabled');
				continue;
			}
			$s=preg_replace(ent_de($p[$i]), ent_de($r[$i]), $s);
		}
	}
	return $s;
}

function gen_rep($s){
	global $pattern, $replacement;
	if($_POST['pattern']!=''){
		$p=explode("\n",$pattern);
		$r=explode("\n",$replacement);
		for($i=0;$i<count($p);$i++){
			$s=str_replace(ent_de($p[$i]), ent_de($r[$i]), $s);
		}
	}
	return $s;
}

function gen_rep_de($s){
	global $pattern, $replacement;
	if($_POST['pattern']!=''){
		$p=explode("\n",$pattern);
		$r=explode("\n",$replacement);
		for($i=0;$i<count($p);$i++){
			$s=str_replace(ent_de($r[$i]), ent_de($p[$i]), $s);
		}
	}
	return $s;
}

function pcre_mat($s){
	global $pattern;
	if($_POST['pattern']!=''){
		preg_match_all($pattern,$s,$res);
	}
	addmsg(INFO,count($res[0]).' record(s) found.');
	return implode("\n",$res[0]);
}

function key_xor($a,$b){
	$r='';
	for($i=0;$i<strlen($b);$i++){
		$r.=substr($a,$i % strlen($a),1) ^ substr($b,$i,1);
	}
	return $r;
}
function dna_en($s){
	$table=array("00"=>'A',"01"=>'T',"10"=>'G',"11"=>'C');
	$r='';
	$s=bin_en($s);
	for($i=0;$i<(strlen($s)/2);$i++){
		$r.=$table[substr($s,$i*2,2)];
	}
	return $r;
}

function dna_de($s){
	$table=array('A'=>"00",'T'=>"01",'G'=>"10",'C'=>"11");
	$r='';
	$s=strtoupper($s);
	$s=preg_replace("/[^ATCG]/",'',$s);
	for($i=0;$i<strlen($s);$i++){
		$r.=$table[substr($s,$i,1)];
	}
	return bin_de($r);
}

function chewing_de($s){
	$table=array('1'=>"&#12549;", 'q'=>"&#12550;", 'a'=>"&#12551;", 'z'=>"&#12552;", '2'=>"&#12553;", 'w'=>"&#12554;", 's'=>"&#12555;", 'x'=>"&#12556;", 'e'=>"&#12557;", 'd'=>"&#12558;", 'c'=>"&#12559;", 'r'=>"&#12560;", 'f'=>"&#12561;", 'v'=>"&#12562;", '5'=>"&#12563;", 't'=>"&#12564;", 'g'=>"&#12565;", 'b'=>"&#12566;", 'y'=>"&#12567;", 'h'=>"&#12568;", 'n'=>"&#12569;", 'u'=>"&#12583;", 'j'=>"&#12584;", 'm'=>"&#12585;", '8'=>"&#12570;", 'i'=>"&#12571;", 'k'=>"&#12572;", ','=>"&#12573;", '9'=>"&#12574;", 'o'=>"&#12575;", 'l'=>"&#12576;", '.'=>"&#12577;", '0'=>"&#12578;", 'p'=>"&#12579;", ';'=>"&#12580;", '/'=>"&#12581;", '-'=>"&#12582;", ' '=>" ", '6'=>"&#714; ", '3'=>"&#711; ", '4'=>"&#715; ", '7'=>"&#729; ");
	$r='';
	for($i=0;$i<strlen($s);$i++){
		$r.=$table[substr($s,$i,1)];
	}
	return $r;
}

function chewing_sort_de($s){
	$table=array('1'=>"2549;", 'q'=>"2550;", 'a'=>"2551;", 'z'=>"2552;", '2'=>"2553;", 'w'=>"2554;", 's'=>"2555;", 'x'=>"2556;", 'e'=>"2557;", 'd'=>"2558;", 'c'=>"2559;", 'r'=>"2560;", 'f'=>"2561;", 'v'=>"2562;", '5'=>"2563;", 't'=>"2564;", 'g'=>"2565;", 'b'=>"2566;", 'y'=>"2567;", 'h'=>"2568;", 'n'=>"2569;", 'u'=>"12583;", 'j'=>"12584;", 'm'=>"12585;", '8'=>"62570;", 'i'=>"62571;", 'k'=>"62572;", ','=>"62573;", '9'=>"62574;", 'o'=>"62575;", 'l'=>"62576;", '.'=>"62577;", '0'=>"62578;", 'p'=>"62579;", ';'=>"62580;", '/'=>"62581;", '-'=>"62582;", ' '=>" ", '6'=>"99714; ", '3'=>"99711; ", '4'=>"99715; ", '7'=>"99729; ");
	$r='';
	for($i=0;$i<strlen($s);$i++){
		$r.=$table[substr($s,$i,1)];
	}
	$k=explode(" ",$r);
	for($i=0;$i<count($k);$i++){
		$k[$i]=preg_replace("/;$/",'',$k[$i]);
		$m=explode(";",$k[$i]);
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

function cac_pre($s){
	global $func;
	$func=array('analyze','average','stdevp','stdev','power','round','floor','ceil','sqrt','log','sum','pow','exp','mod','sin','cos','tan','cot','sec','csc','ln','c');
	$func_s=array('analyze','power','round','floor','ceil','sqrt','log','exp','sin','cos','tan','cot','sec','csc','ln');
	if($s==''){$s='0';}
	$m=$_POST['caculator'];
	if($m==''){$m='x';}
	$m=strtolower($m);
	$m=str_replace('x','('.$s.')',$m);
	$m=strtolower($m);
	$m=preg_replace("/[\r\n ]/",'',$m);
	$m=preg_replace('/pi/i','(3.141592653589793238462643383279502884197169399375105820974944592307816406286208998628034825342117067982148086513282306647093844609550582231725359408128)',$m);
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
#echo $m.'<br />';
	$t=$m;
	for($i=0;$i<count($func);$i++){
		$t=str_replace($func[$i].'(','',$t);
	}
	$t=preg_replace('/<[^>]*>/i','',$t);
	$t=preg_replace('/[,()+\^\-*\/!{}\[\]]/','',$t);
	if(strlen($t)>0){
		addmsg(ERR,'Invalid input',1);
	}
	$m=caculator('('.$m.')');
	$m=substr($m,1,strlen($m)-2);
	$m=preg_replace('/\.0+$/','',$m);
	$m=preg_replace('/\.(.*[^0])0+$/','.\1',$m);
	return $m;
}
function caculator($s){
	global $func;
	$z=$s;
	$count=0;
	$flist=implode('|',$func);
while(!ereg('^<[^>]*>$',$s)){
#echo $s.'<br />';
	while(ereg('\[<[^>]*>\]',$s)){
		#gauss(floor);
		$s=preg_replace('/\[(<[^>]*>)\]/e','cac_func("floor","\1")',$s);
	}
	while(ereg('\[<[^>]*>(,<[^>]*>)+\]',$s)){
		#lcm;
		$s=preg_replace('/\[(<[^>]*>(,<[^>]*>)+)\]/e','cac_func("lcm","\1")',$s);
	}
	while(ereg('\{<[^>]*>\}',$s)){
		#abs;
		$s=preg_replace('/\{(<[^>]*>)\}/e','cac_func("abs","\1")',$s);
	}
	while(ereg('<[^>]*>!',$s)){
		#factorial;
		$s=preg_replace('/(<[^>]*>)!/e','cac_func("fac","\1")',$s);
	}
	while(ereg('<[^>]*>\^<[^>]*>',$s)){
		#pow;
		$s=preg_replace('/(<[^>]*>)\^(<[^>]*>)/e','cac_func("power","\1,\2")',$s);	
	}
	while(ereg('<[^>]*>\*<[^>]*>',$s)){
		#multiply;
		$s=preg_replace('/(<[^>]*>)\*(<[^>]*>)/e','cac_func("multiply","\1,\2")',$s);	
	}
	while(ereg('<[^>]*>/<[^>]*>',$s)){
		#divide;
		$s=preg_replace('/(<[^>]*>)\/(<[^>]*>)/e','cac_func("divide","\1,\2")',$s);	
	}
	while(ereg('<[^>]*>%<[^>]*>',$s)){
		#mod;
		$s=preg_replace('/(<[^>]*>)%(<[^>]*>)/e','cac_func("mod","\1,\2")',$s);	
	}
	while(ereg('<[^>]*>\+<[^>]*>',$s)){
		#plus;
		$s=preg_replace('/(<[^>]*>)\+(<[^>]*>)/e','cac_func("plus","\1,\2")',$s);	
	}
	while(ereg('<[^>]*>-<[^>]*>',$s)){
		#minus;
		$s=preg_replace('/(<[^>]*>)-(<[^>]*>)/e','cac_func("minus","\1,\2")',$s);	
	}
	while(preg_match('/('.$flist.')\((<[^>]*>(,<[^>]*>)*)\)/',$s)){
		#func;
		for($i=0;$i<count($func);$i++){
			$s=preg_replace('/'.$func[$i].'\((<[^>]*>(,<[^>]*>)*)\)/e','cac_func("'.$func[$i].'","\1")',$s);
		}
	}
	while(ereg('\(<[^>]*>(,<[^>]*>)+\)',$s)){
		#gcd;
		$s=preg_replace('/\((<[^>]*>(,<[^>]*>)+)\)/e','cac_func("gcd","\1")',$s);
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
	if($count>5){
		break;
	}
}
	return $s;
}

function cac_func($f,$s,$tqwe=0){
#echo $f.':'.$s.'<br />';
	$tma='Too many arguments';
	$tfa='Too few arguments';
	$a=explode(',',$s);
	$c=count($a);
	if($tqwe==0){
		for($i=0;$i<$c;$i++){
			$a[$i]=substr($a[$i],1,strlen($a[$i])-2);
		}
	}
	if($f=='gcdr'){
		if($a[1]==0){
			$r=$a[0];
		}else{
			$r=cac_func('gcdr',$a[1].','.(bc()?bcmod($a[0],$a[1]):$a[0]%$a[1]),1);
		}
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
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		if($a[0]<0){
			$r=$a[0]*(-1);
		}else{
			$r=$a[0];
		}
	}elseif($f=='ceil'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=ceil($a[0]);
	}elseif($f=='floor'){
		if($c>1){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=floor($a[0]);
	}elseif($f=='round'){
		if($c>2){
			addmsg(WARN,$tma);
			$r=0;
		}
		if($c==1){
			$a[1]=0;
		}
		$r=round($a[0],$a[1]);
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
		$r=pow($a[0],$a[1]);
	}elseif($f=='power'){
		if($c>2){
			addmsg(WARN,$tma);
			$r=0;
		}
		if($c<2){
			addmsg(WARN,$tma);
			$r=0;
		}
		$r=bc()?bcpow($a[0],$a[1]):pow($a[0],$a[1]);
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
	for($i=0;$i<(strlen($s)/2);$i++){
		$t.=chr(hexdec(substr($s, $i*2, 2)));
	}
	return $t;
}

function ASCIIFilter($s){
	if(strlen($_POST['ssep_de'][count($_POST['ssep_de'])-1])==1){
	$q=$_POST['ssep_de'][count($_POST['ssep_de'])-1];
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

function sqr_en($s){
	$l=strlen($s);
	if($_POST['sqr_cl']=='auto'){
		$r=$c=ceil(sqrt($l));
		addmsg(INFO,'Rows: '.$r);
		addmsg(INFO,'Colums: '.$c);
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
			$ret.=sqr_en_part(substr($s,$i*$con,$con),$r,$c);
		}
		$ret.=sqr_en_part(substr($s,$i*$con),$r,$c);
		return $ret;
	}else{
		return sqr_en_part($s,$r,$c);
	}
}

function sqr_de($s){
	$l=strlen($s);
	if($_POST['sqr_cl']=='auto'){
		$r=$c=ceil(sqrt($l));
		addmsg(INFO,'Rows: '.$r);
		addmsg(INFO,'Colums: '.$c);
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
			$ret.=sqr_de_part(substr($s,$i*$con,$con),$r,$c);
		}
		$ret.=sqr_de_part(substr($s,$i*$con),$r,$c);
		return $ret;
	}else{
		return sqr_de_part($s,$r,$c);
	}
}

function sqr_en_part($s,$r,$c){
	$l=strlen($s);
	$p=0;
	for($i=0;$i<$r;$i++){
		for($j=0;$j<$c;$j++){
			if($p<$l){
				$a[$i][$j]=substr($s,$p,1);
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
	$l=strlen($s);
	$p=0;
	for($j=0;$j<$c;$j++){
		for($i=0;$i<$r;$i++){
			if(($i*$c)+$j<$l && $p<$l){
				$a[$i][$j]=substr($s,$p,1);
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
	if(strlen($_POST['ssep_de'][count($_POST['ssep_de'])-1])==1){
	$q=$_POST['ssep_de'][count($_POST['ssep_de'])-1];
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

function str_mutate($s){
	$ret='';
	$tmp='';
	for($i=0;$i<strlen($s);$i++){
		$e=substr($s,$i,1);
		if(preg_match('/^[^a-z]$/is',$e)){
			$ret.=str_mutate_core($tmp).$e;
			$tmp='';
		}else{
			$tmp.=$e;
		}
	}
	$ret.=str_mutate_core($tmp);
	return $ret;
}

function str_mutate_core($in){
	if($_POST['mut_l']+$_POST['mut_r']+1>=strlen($in)){
		return $in;
	}
	$lkeep=substr($in,0,$_POST['mut_l']);
	$rkeep=substr($in,-1*$_POST['mut_r']);
	$s=substr($in,$_POST['mut_l'],strlen($in)-$_POST['mut_l']-$_POST['mut_r']);
	$n=strlen($s);
	$flag=true;
	for($i=0;$i<$n;$i++){
		$arr[$i]=$i;
	}
	while($flag){
		shuffle($arr);
		$flag=true;
		for($i=0;$i<$n;$i++){
			if($arr[$i]!=$i){
				$flag=false;
			}
		}
	}
	$ret='';
	for($i=0;$i<$n;$i++){
		$ret.=substr($s,$arr[$i],1);
	}
	return $lkeep.$ret.$rkeep;
}

function ie(){
	if(ereg("MSIE",getenv('HTTP_USER_AGENT'))){
		return TRUE;
	}else{
		return FALSE;
	}
}

function ent_de($s){
	$r='';
	for($i=0;$i<strlen($s);$i++){
		if(substr($s,$i,1)!="\\"){
			$r.=substr($s,$i,1);
		}else{
			switch(substr($s,$i+1,1)){
				case "\\": $r.="\\"; break;
				case "t": $r.="\t"; break;
				case "r": $r.="\r"; break;
				case "n": $r.="\n"; break;
				case "f": $r.="\f"; break;
				case "0": $r.="\0"; break;
				default: $r.=("\\".substr($s,$i+1,1)); break;
			}
			$i++;
		}
	}
	return $r;
}

function en($method, $s){
	switch($method){
		case 'bin': $s=bin_en($s); break;
		case 'dec': $s=dec_en($s); break;
		case 'oct': $s=oct_en($s); break;
		case 'hex': $s=hex_en($s); break;
		case 'b64': $s=base64_encode($s); break;
		case 'rot': $s=rotate($s,$_POST['rot'],$_POST['nrot']); break;
		case 'url': $s=urlencode($s); break;
		case 'ur2': $s=str_replace("+","%20",urlencode($s)); break;
		case 'raw': break;
		case 'rpt': $s=str_repeat($s,$_POST['rpt']); break;
		case 'rev': $s=strrev($s); break;
		case 'crv': $s=case_rev($s); break;
		case 'rep': $s=gen_rep($s); break;
		case 'pcr': $s=pcre_rep($s); break;
		case 'pcm': $s=pcre_mat($s); break;
		case 'spe': $s=htmlspecialchars($s); break;
		case 'hen': $s=htmlentities($s); break;
		case 'md5': $s=md5($s); break;
		case 'sha1': $s=sha1($s); break;
		case 'crc16': $s=sprintf("%x",crc32($s)); break;
		case 'crc32': $s=sprintf("%x",crc32($s)); break;
		case 'srt': $k=s2a($s); sort($k); $s=a2s($k); break;
		case 'stu': $s=strtoupper($s); break;
		case 'bbs': $s=bbs2html($s); break;
		case 'bbd': $s=bbs2html_dc($s); break;
		case 'mut': $s=str_mutate($s); break;
		case 'ttb': $s=totable($s);break;
		case 'stl': $s=strtolower($s); break;
		case 'ucw': $s=ucwords($s); break;
		case 'sln': $s=strlen($s); break;
		case 'cor': $s=correct($s); break;
		case 'det': $s=determinant($s); break;
		case 'msk': $s=network($s); break;
		case 'ref': $s=sqr_reflect($s); break;
		case 'che': $s=chewing_de($s); break;
		case 'chs': $s=chewing_sort_de($s); break;
		case 'rf'; $s=sqr_en($s); break;
		case 'cac': $s=cac_pre($s); break;
		case 'mmtp': $s=matrix_multiply($s); break;
		case 'mro': $s=matrix_rotate($s,0); break;
		case 'miv': $s=matrix_inverse($s); break;
		case 'mtr': $s=matrix_transpose($s); break;
		case 'ascii': $s=ASCIIFilter($s); break;
		case 'dna': $s=dna_en($s); break;
		case 'key': $s=key_xor($_POST['key'],$s); break;
		case 'bre': $s=bit_rev($s); break;
		case 'bod': $s=bitorder_en($_POST['order'],$s); break;
		case 'tra': $s=tran($_POST['transpose'],$s,2); break;
		default: addmsg(WARN,'Undefined Method: '.$method);
	}
	return $s;
}

function de($method, $s){
	switch($method){
		case 'bin': $s=bin_de($s); break;
		case 'dec': $s=dec_de($s); break;
		case 'oct': $s=oct_de($s); break;
		case 'hex': $s=hex_de($s); break;
		case 'b64': $s=base64_decode($s); break;
		case 'rot': $s=rotate($s,26-$_POST['rot'],10-$_POST['nrot']); break;
		case 'url': $s=urldecode($s); break;
		case 'ur2': $s=urldecode($s); break;
		case 'raw': break;
		case 'rev': $s=strrev($s); break;
		case 'spe': $s=html_entity_decode(html_entity_decode($s)); break;
		case 'hen': $s=html_entity_decode($s); break;
		case 'md5': addmsg(INFO,'<a href="http://www.md5lookup.com/?category=main&page=search" target="_blank">http://www.md5lookup.com</a>'); break;
		case 'stu': break;
		case 'crv': $s=case_rev($s); break;
		case 'stl': break;
		case 'ucw': break;
		case 'bbs': break;
		case 'srt': $k=s2a($s); rsort($k); $s=a2s($k); break;
		case 'bbd': break;
		case 'rpt': break;
		case 'rf'; $s=sqr_de($s); break;
		case 'pcr': break;
		case 'det': break;
		case 'ttb': break;
		case 'mut': break;
		case 'sha1': break;
		case 'msk': break;
		case 'ref': $s=sqr_reflect($s); break;
		case 'crc16': break;
		case 'crc32': break;
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
		case 'sln': break;
		case 'che': break;
		case 'chs': break;
		case 'dna': $s=dna_de($s); break;
		case 'key': $s=key_xor($_POST['key'],$s); break;
		case 'bre': $s=bit_rev($s); break;
		case 'bod': $s=bitorder_de($_POST['order'],$s); break;
		case 'tra': $s=tran(12-$_POST['transpose'],$s,1); break;
		default: addmsg(WARN,'Undefined Method: '.$method);
	}
	return $s;
}
function proc($s){
global $process;
if($process=="ob"){
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
if($process=="re"){
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

function pro($s,$sp){
	if(count($sp)>1){
		$a=explode($sp[0],$s);
		for($i=1;$i<count($sp);$i++){
			$tmp[$i-1]=$sp[$i];
		}
		for($i=0;$i<count($a);$i++){
			$a[$i]=pro($a[$i],$tmp);
		}
		$s=implode($sp[0],$a);
	}elseif(count($sp)==1){
		$a=explode($sp[0],$s);
		for($i=0;$i<count($a);$i++){
			$a[$i]=proc($a[$i]);
		}
		$s=implode($sp[0],$a);
	}else{
		$a=explode($sp[0],$s);
		for($i=0;$i<count($a);$i++){
			$a[$i]=proc($a[$i]);
		}
		$s=implode($sp[0],$a);
	}
	return $s;
}
#</function>
if($_GET['appendix']=="source"){
	header("Content-Type: text/plain");
	$r=file_get_contents($_SERVER['SCRIPT_FILENAME']);
	$r=str_replace("\r\n","\n",$r);
	die($r);
}
if($_GET['appendix']=="note"){
$r='<pre>
Big5 Range: [\xA1-\xF9][\x40-\x7E\xA1-\xFE]
</pre>';
	die($r);
}
#<init>
if(isset($_POST['action'])){
	if($_POST['input']=='file' && $_FILES['fin']['tmp_name']!="none" && $_FILES['fin']['tmp_name']!="" && $_FILES['fin']['size']>0){
		$s=file_get_contents($_FILES['fin']['tmp_name']);
		unlink($_FILES['fin']['tmp_name']);
		$oridata='Please Re-Upload';
	}else{
		$_POST['input']='text';
		$s=stripslashes($_POST['text']);
		if($_POST['ibk']=='on'){
			$backup=$s;
		}
		$oridata=$s;
	}
	$_POST['rot']=$_POST['rot']%26;
	$_POST['nrot']=$_POST['nrot']%10;
	$method=$_POST['method'];
	$dir=$_POST['dir'];
	$process=$_POST['process'];
	$_POST['trows']=$_POST['trows']*1;
	$_POST['tcols']=$_POST['tcols']*1;
	$_POST['transpose']=$_POST['transpose']%12;
	$_POST['rpt']=$_POST['rpt']*1;
	$_POST['sepr']=str_replace("\r\n","\n",stripslashes($_POST['sepr']));
	$_POST['ssep']=str_replace("\r\n","\n",stripslashes($_POST['ssep']));
	$_POST['caculator']=stripslashes($_POST['caculator']);
	$_POST['ssep_de']=explode("\n",$_POST['ssep']);
	$_POST['sqr_r']=intval($_POST['sqr_r']);
	$_POST['sqr_c']=intval($_POST['sqr_c']);
	$_POST['mut_l']=intval($_POST['mut_l']);
	$_POST['mut_r']=intval($_POST['mut_r']);
for($i=0;$i<count($_POST['ssep_de']);$i++){
	$_POST['ssep_de'][$i]=ent_de($_POST['ssep_de'][$i]);
if($_POST['ssep_de'][$i]==''){
addmsg(WARN,'Null SubSeparator');
}
}
	$sep_array=explode("\n",$_POST['sepr']);
	if($_POST['sep']=="on"){
	for($i=0;$i<count($sep_array);$i++){
		$sep_array[$i]=ent_de($sep_array[$i]);
		if($sep_array[$i]==''){
			$_POST['sep']="off";
			addmsg(ERR,'Null Separator');
		}
	}
	}
	$tmp=$sep_array;
	sort($tmp);
	for($i=1;$i<count($tmp);$i++){
		if($tmp[$i]==$tmp[$i-1]){
			addmsg(WARN,'Iterant Separator');
		}
	}
	if($_POST['batch']==''){
		$_POST['batch2']=($_POST['mode']=="en"?'e':'d').'-'.$method;
	}elseif(!ereg("^(e|d)-[A-Za-z0-9]+(, *(e|d)-[A-Za-z0-9]+)*$",$_POST['batch'])){
		addmsg(ERR,'Incorrect Batch Format');
		$_POST['batch2']='e-raw';
	}else{
		$_POST['batch2']=$_POST['batch'];
	}
}else{
	$_POST['input']='text';
	$_POST['rot']=13;
	$_POST['nrot']=5;
	$method="raw";
	$_POST['scr']="on";
	$_POST['charset']="big5";
	$dir="LTR";
	$process="ob";
	$_POST['trows']=17;
	$_POST['tcols']=85;
	$_POST['transpose']=0;
	$_POST['out']="text";
	$_POST['rpt']=1;
	$_POST['ssep']="\\n\\n\n\\n\n\\t";
	$_POST['ttb_brd']="on";
	$_POST['ttb_ibrd']="on";
	$_POST['ttb_align']="left";
	$_POST['ibk']='on';
	$_POST['caculator']='x';
	$_POST['sqr_r']='';
	$_POST['sqr_c']='';
	$_POST['sqr_cl']='auto';
	$_POST['ref_ver']='on';
	$_POST['ref_hor']='on';
	$_POST['mut_l']='1';
	$_POST['mut_r']='1';
}
$pattern=str_replace("\r\n","\n",stripslashes($_POST['pattern']));
$replacement=str_replace("\r\n","\n",stripslashes($_POST['replacement']));
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
if($_POST['scr']=="on"){
	$s=str_replace("\r\n","\n",$s);
}
if($_POST['sep']=="on"){
	$s=pro($s,$sep_array);
}else{
	$s=proc($s);
}
if($_POST['out']=="file"){
	set_time_limit(600);
	header("Content-Type: application/force-download");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Disposition: attachment; filename=untitled");
	die($s);
}elseif($_POST['out']=="blank"){
	die($s);
}
#</php>
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $_POST['charset'];?>" />
<style type="text/css">
	a.link{text-decoration:none;color:#808080;}
	a:hover.link{text-decoration:underline;position:relative;top:1px;left:1px;}
	.ed {color:#00ff00;}
	.ow {color:#ff0000;}
	.nd {text-decoration:none; color:#000;}
	a:visited.nd {color:#000;}
	.sub {position:relative; left: +1em;}
	td {vertical-align:top;}
</style>
<title>Bug Converter - ver. <?echo $ver_serial;?></title>
<script type="text/javascript">
var s;
function ssep(s){
	document.all.ssep.value=s;
}
function assis(s){
	var msg = new Object;
//[Begin of Help]
	msg['raw']='Color Definition:\n<a class="ow">One Way Function</a>\n<a class="ed">Encode = Decode</a>';
	msg['srt']='Use 1 SubSeparator\nEncode: ascend sort\nDecode: descend sort';
	msg['tra']='Unit: semitone\nEncode: raise tone (prefers to b)\nDecode: fall tone (prefers to #)';
	msg['ttb']='Skip HtmlSpecialChars: Auto On\nUse SubSeparator\n\nGeneral Usage:\nSubSeparator: \\t';
	msg['che']='Skip HtmlSpecialChars: Auto On';
	msg['chs']='Skip HtmlSpecialChars: Auto On';
	msg['ascii']='Use 1 SubSeparator\n\nGeneral Usage:\nSubSeparator: SPACE';
	msg['cor']='Use 1 SubSeparator\n\nGeneral Usage:\nSubSeparator: \\n';
	msg['ave']='Use 1 SubSeparator';
	msg['sdv']='Use 1 SubSeparator';
	msg['ssdv']='Use 1 SubSeparator';
	msg['mtr']='Use 2 SubSeparator';
	msg['mro']='Use 2 SubSeparator';
	msg['miv']='Use 2 SubSeparator';
	msg['mmtp']='Use 3 SubSeparator';
	msg['det']='Use 2 SubSeparator';
	msg['cac']='Use \'x\' to represent original data.';
//[End of Help]
	if(typeof(msg[document.all.method.value])=="undefined"){
		document.getElementById('help').innerHTML="Nothing to tell you.";
	}else{
		document.getElementById('help').innerHTML=msg[document.all.method.value].replace(/\n/g,"<br />");
	}
	if(s==1){
		switch(document.all.method.value){
			case 'ttb':document.all.ssp.checked=true; break;
			case 'che':document.all.ssp.checked=true; break;
			case 'chs':document.all.ssp.checked=true; break;
			default:document.all.ssp.checked=false; break;
		}
	}
}
function showlinks(){
var link='<a href="http://mathml.twbbs.org">Bug\'s MathML Board</a>\n<a href="http://www.google.com">Google</a>\n<a href="http://babelfish.altavista.com">Babel Fish Translation</a>\n<a href="http://www.naturalvoices.att.com/demos/">AT&amp;T TTS Engine</a>\n<a href="http://www.wikipedia.org/">Wikipedia</a>\n<a href="http://programmingebooks.tk/">Programming eBooks</a>\n<a href="http://www.twbsd.org">twbsd.org</a>\n<a href="http://fanqiang.chinaunix.net/">ChinaUNIX</a>';
document.getElementById('help').innerHTML=(link.replace(/\n/g,"<br />")).replace(/<a/g,'<a target="_blank"');
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
		setTimeout("document.getElementById('"+item.id+"').focus();",0);	
		return false;
	}
		    
}
//<End of EnableTabinTextarea>
var flag=0;
function collapse(){
document.getElementById('message').style.display='none';
document.getElementById('message_exp').style.display='block';
}
function expand(){
document.getElementById('message').style.display='block';
document.getElementById('message_exp').style.display='none';
}
</script>
</head>
<body onload="assis(0);" onUnload="if(flag==0)alert('May the force be with you!');"><a name="top"></a>
<?
if(count($msg)>0){
echo '<div style="font-size:10pt;"><fieldset><legend>Message</legend><div id="message_exp" style="display:none;">[<a href="#" onClick="expand()">Expand</a>]</div><div id="message">[<a href="#" onClick="collapse()">Collapse</a>]<br />'.implode('<br />',$msg).'</div></fieldset></div>';
}
?>
<form method="post" action="<?echo $_SERVER['PHP_SELF'];?>" name="form" enctype="multipart/form-data">
<input type="hidden" name="action" value="y" />
<?
if(isset($_REQUEST['debug'])){
echo '<input type="hidden" name="debug" value="1" />';
}
?>
<table><tr><td>
<textarea dir="<?echo $dir;?>" onkeydown="return catchTab(this,event);" id="text" name="text" rows="<?echo $_POST['trows'];?>" cols="<?echo $_POST['tcols'];?>"><?
if(cancel()){
echo $oridata;
}else{
echo ($_POST['ssp']=="on")?$s:htmlspecialchars($s);
}
?></textarea></td>
<td>
<fieldset><legend>Assistance</legend>
<div id="help" style="font-size:10pt; color:#333;<?echo ie()?'':' overflow:auto;';?> height:8em;"></div>
</fieldset>
<fieldset><legend>Miscellaneous</legend>
<input type="checkbox" id="scr" name="scr"<?echo ($_POST['scr']=="on")?' checked="checked"':'';?> /><label for="scr">Skip &lt;CR&gt;</label><br />
<input type="checkbox" id="ssp" name="ssp"<?echo ($_POST['ssp']=="on")?' checked="checked"':'';?> /><label for="ssp">Skip HtmlSpecialChars</label><br />
<input type="checkbox" id="ibk" name="ibk"<?echo ($_POST['ibk']=="on")?' checked="checked"':'';?> /><label for="ibk">Enable Undo</label><br />
<!--
<input type="checkbox" onclick="if(this.checked==true){document.getElementById('cpb').style.display='block';}else{document.getElementById('cpb').style.display='none';}" id="cpo" name="cpo"<?echo ($_POST['cpo']=="on")?' checked="checked"':'';?> /><label for="cpo">ClipBoard</label><br />
<input type="checkbox" onclick="if(this.checked==true && document.all.sep.checked==true){document.all.sep.click();}" name="blk"<?echo ($_POST['blk']=="on")?' checked="checked"':'';?> /><a onclick="document.all.blk.click();">Block</a><input type="text" size="10" name="blkpat" value="<?echo $_POST['blkpat'];?>" /><br />
<input type="checkbox" onclick="if(this.checked==true){document.all.blk.checked=false; document.getElementById('sepa').style.display='block';}else{document.getElementById('sepa').style.display='none';}" name="sep"<?echo ($_POST['sep']=="on")?' checked="checked"':'';?> /><a onclick="document.all.sep.click();">Separator</a><br />
<div id="sepa" class="sub" style="display:<?echo $_POST['sep']=="on"?'block':'none';?>;"><input type="checkbox" name="upcre"<?echo ($_POST['upcre']=="on")?' checked="checked"':'';?> /><a onclick="document.all.upcre.click();">Use PCRE</a>&nbsp;<a href="javascript:void(null);" class="nd" onclick="if(document.all.sepr.cols>20)document.all.sepr.cols-=20;">&#8592;</a><a href="javascript:void(null);" class="nd" onclick="if(document.all.sepr.rows>5)document.all.sepr.rows-=5;">&#8593;</a><a href="javascript:void(null);" class="nd" onclick="document.all.sepr.cols=20; document.all.sepr.rows=3;">&#9678;</a><a href="javascript:void(null);" class="nd" onclick="document.all.sepr.rows+=5;">&#8595;</a><a href="javascript:void(null);" class="nd" onclick="document.all.sepr.cols+=20;">&#8594;</a><br /><textarea name="sepr" cols="20" rows="3"><?echo $_POST['sepr'];?></textarea></div>
-->
<!--BOD-->
<input type="checkbox" onclick="if(this.checked==true){document.getElementById('sepa').style.display='block';}else{document.getElementById('sepa').style.display='none';}" name="sep"<?echo ($_POST['sep']=="on")?' checked="checked"':'';?> /><a onclick="document.all.sep.click();">Separator</a><br />
<div id="sepa" class="sub" style="display:<?echo $_POST['sep']=="on"?'block':'none';?>;"><a href="javascript:void(null);" class="nd" onclick="if(document.all.sepr.cols>20)document.all.sepr.cols-=20;">&#8592;</a><a href="javascript:void(null);" class="nd" onclick="if(document.all.sepr.rows>5)document.all.sepr.rows-=5;">&#8593;</a><a href="javascript:void(null);" class="nd" onclick="document.all.sepr.cols=20; document.all.sepr.rows=3;">&#9678;</a><a href="javascript:void(null);" class="nd" onclick="document.all.sepr.rows+=5;">&#8595;</a><a href="javascript:void(null);" class="nd" onclick="document.all.sepr.cols+=20;">&#8594;</a><br /><textarea name="sepr" cols="20" rows="3"><?echo $_POST['sepr'];?></textarea></div>
<!--EOD-->
</fieldset>
<span style="float:left;"><a href="#" class="link" style="font-weight:bold; color:#333" onclick="flag=1; document.all.form.submit();">[Submit]</a></span><?if(isset($backup)){?><span style="float:left;"><a href="#" class="link" style="font-weight:bold; color:#333" onclick="if(confirm('Sure to undo ?')){document.getElementById('text').value=document.getElementById('backup').value;}">[Undo]</a></span><? }?><span style="float: right;"><a href="#" class="link" onClick="if(confirm('Sure to clear ?')){flag=1; location.href='<?echo $_SERVER['PHP_SELF'];?>'}" style="font-weight:bold; color: #333;">[Clear]</a></span>
</td></tr></table>
<!--
<fieldset id="cpb" style="display: <?#echo $_POST['cpo']=="on"?'block':'none';?>;"><legend>ClipBoard</legend>
<textarea name="clip" rows="5" cols="90"><?#echo htmlspecialchars(file_get_contents($_SERVER['SCRIPT_FILENAME'].".clip"));?></textarea>
</fieldset>
-->
<?
if(isset($backup)){
echo '<textarea rows="5" cols="90" id="backup" style="display:none;">'.$backup.'</textarea>';
}
?>
<fieldset><legend>I/O</legend>
<table>
<tr><td>Input: </td><td><input type="radio" name="input" onclick="document.all.fin.disabled=true;" id="intext" value="text" <?echo $_POST['input']=='text'?'checked="checked"':'';?>/><label for="intext">Text Area</label><input type="radio" onclick="document.all.fin.disabled=false;" name="input" id="infile" value="file" <?echo $_POST['input']=='file'?'checked="checked"':'';?>/><label for="infile">File</label><input type="file" name="fin" <?echo $_POST['input']=='file'?'':'disabled="disabled"';?>/></td></tr>
<tr><td>Output: </td><td><input type="radio" name="out" onclick="document.all.form.target='_self'" id="ta" value="text" <?echo ($_POST['out']=="text")?'checked="checked" ':'';?>/><label for="ta">Text Area</label> <input type="radio" name="out" id="fd" onclick="document.all.form.target='_self'" value="file" <?echo ($_POST['out']=="file")?'checked="checked" ':'';?>/><label for="fd">File Download</label> <input type="radio" name="out" onclick="document.all.form.target='_blank'" id="bf" value="blank" <?echo ($_POST['out']=="blank")?'checked="checked" ':'';?>/><label for="bf">Blank Frame</label></td></tr>
</table>
</fieldset>
<fieldset><legend>Convert</legend>
<table>
<tr><td>Method: </td><td>
<select name="method" onchange="assis(1);">
<option style="color:#555555;" value="raw"<?echo ($method=="raw")?' selected="selected"':'';?>>RAW (Output input)</option>
<option value="srt"<?echo ($method=="srt")?' selected="selected"':'';?>>Sort</option>
<option value="tra"<?echo ($method=="tra")?' selected="selected"':'';?>>Transpose</option>
<option class="ow" value="msk"<?echo ($method=="msk")?' selected="selected"':'';?>>Network</option>
<option class="ow" value="ttb"<?echo ($method=="ttb")?' selected="selected"':'';?>>To Table</option>
<option class="ow" value="cac"<?echo ($method=="cac")?' selected="selected"':'';?>>Calculator</option>
<option class="ow" value="det"<?echo ($method=="det")?' selected="selected"':'';?>>Determinant Value</option>
<option class="ow" value="mmtp"<?echo ($method=="mmtp")?' selected="selected"':'';?>>Matrix Multiplication</option>
<option class="ow" value="miv"<?echo ($method=="miv")?' selected="selected"':'';?>>Matrix Inverse</option>
<option value="mro"<?echo ($method=="mro")?' selected="selected"':'';?>>Matrix Rotate</option>
<option class="ed" value="mtr"<?echo ($method=="mtr")?' selected="selected"':'';?>>Matrix Transpose</option>
<option class="ed" value="ref"<?echo ($method=="ref")?' selected="selected"':'';?>>SquareReflect</option>
<option class="ow" value="cor"<?echo ($method=="cor")?' selected="selected"':'';?>>Correct</option>
<option value="ascii"<?echo ($method=="ascii")?' selected="selected"':'';?>>ASCIIFilter</option>
<option class="ow" value="che"<?echo ($method=="che")?' selected="selected"':'';?>>Chewing</option>
<option class="ow" value="chs"<?echo ($method=="chs")?' selected="selected"':'';?>>Chewing (Sort)</option>
<option value="rep"<?echo ($method=="rep")?' selected="selected"':'';?>>Replace</option>
<option class="ow" value="pcr"<?echo ($method=="pcr")?' selected="selected"':'';?>>PCRE Replace</option>
<option class="ow" value="pcm"<?echo ($method=="pcm")?' selected="selected"':'';?>>PCRE Match</option>
<option value="spe"<?echo ($method=="spe")?' selected="selected"':'';?>>HtmlSpecialChars</option>
<option value="hen"<?echo ($method=="hen")?' selected="selected"':'';?>>HtmlEntity</option>
<option class="ow" value="rpt"<?echo ($method=="rpt")?' selected="selected"':'';?>>Repeat</option>
<option class="ed" value="rev"<?echo ($method=="rev")?' selected="selected"':'';?>>Reverse</option>
<option class="ed" value="crv"<?echo ($method=="crv")?' selected="selected"':'';?>>Case Reverse</option>
<option class="ow" value="stu"<?echo ($method=="stu")?' selected="selected"':'';?>>StringToUpper</option>
<option class="ow" value="stl"<?echo ($method=="stl")?' selected="selected"':'';?>>StringToLower</option>
<option class="ow" value="ucw"<?echo ($method=="ucw")?' selected="selected"':'';?>>UppercaseTheFirstCharacter</option>
<option class="ow" value="sln"<?echo ($method=="sln")?' selected="selected"':'';?>>StringLength</option>
<option value="bod"<?echo ($method=="bod")?' selected="selected"':'';?>>BitOrder</option>
<option class="ed" value="bre"<?echo ($method=="bre")?' selected="selected"':'';?>>BitReverse (not)</option>
<option class="ed" value="key"<?echo ($method=="key")?' selected="selected"':'';?>>Key (xor)</option>
<option value="rot"<?echo ($method=="rot")?' selected="selected"':'';?>>StringRotate</option>
<option class="ow" value="mut"<?echo ($method=="mut")?' selected="selected"':'';?>>StringMutate</option>
<option value="rf"<?echo ($method=="rf")?' selected="selected"':'';?>>Square</option>
<option value="md5"<?echo ($method=="md5")?' selected="selected"':'';?>>MD5</option>
<option class="ow" value="sha1"<?echo ($method=="sha1")?' selected="selected"':'';?>>SHA-1</option>
<option class="ow" value="crc16"<?echo ($method=="crc16")?' selected="selected"':'';?>>CRC16</option>
<option class="ow" value="crc32"<?echo ($method=="crc32")?' selected="selected"':'';?>>CRC32</option>
<option value="url"<?echo ($method=="url")?' selected="selected"':'';?>>URL</option>
<option value="ur2"<?echo ($method=="ur2")?' selected="selected"':'';?>>URL-2 (Space to %20)</option>
<option value="b64"<?echo ($method=="b64")?' selected="selected"':'';?>>Base64</option>
<option value="dna"<?echo ($method=="dna")?' selected="selected"':'';?>>DNA</option>
<option value="bin"<?echo ($method=="bin")?' selected="selected"':'';?>>Bin</option>
<option value="oct"<?echo ($method=="oct")?' selected="selected"':'';?>>Oct</option>
<option value="dec"<?echo ($method=="dec")?' selected="selected"':'';?>>Dec</option>
<option value="hex"<?echo ($method=="hex")?' selected="selected"':'';?>>Hex</option>
<option class="ow" value="bbd"<?echo ($method=="bbd")?' selected="selected"':'';?>>BBS -> HTML (Double Color)</option>
<option class="ow" value="bbs"<?echo ($method=="bbs")?' selected="selected"':'';?>>BBS -> HTML</option>
</select> <input type="button" value="Add to Batch" onClick="var i; i=((document.all.mode[0].checked==true)?'e':'d'); if(document.all.batch.value==''){document.all.batch.value=i+'-'+document.all.method.value;}else{document.all.batch.value=document.all.batch.value+', '+i+'-'+document.all.method.value}" /></td></tr>
<tr><td></td><td><input type="radio" name="mode" id="encode" value="en" <?echo ($_POST['mode']!="de")?'checked="checked" ':'';?>/><label for="encode">Encode</label> <input type="radio" name="mode" id="decode" value="de" <?echo ($_POST['mode']=="de")?'checked="checked" ':'';?>/><label for="decode">Decode</label></td></tr>
<tr><td>Batch: </td><td><input type="text" size="70" name="batch" value="<?echo $_POST['batch'];?>" /><br /><input type="radio" name="process" id="forward" value="ob" <?echo ($process=="ob")?'checked="checked" ':'';?> /><label for="forward">Forward</label> <input type="radio" name="process" id="backward" value="re" <?echo ($process=="re")?'checked="checked" ':'';?> /><label for="backward">Backward</label> <input type="button" value="Clear" onclick="document.all.batch.value='';" /></td></tr>
</table>
</fieldset>
<fieldset><legend>Setting</legend>
<table>
<tr><td>Match/Replace:</td><td><span id="mrc"><a href="javascript:void(null);" class="nd" onclick="if(document.all.pattern.cols>20)document.all.pattern.cols-=20; if(document.all.replacement.cols>20)document.all.replacement.cols-=20;">&#8592;</a><a href="javascript:void(null);" class="nd" onclick="if(document.all.pattern.rows>5)document.all.pattern.rows-=5; if(document.all.replacement.rows>5)document.all.replacement.rows-=5;">&#8593;</a><a href="javascript:void(null);" class="nd" onclick="document.all.pattern.rows=1; document.all.replacement.rows=1; document.all.pattern.cols=70; document.all.replacement.cols=70;">&#9678;</a><a href="javascript:void(null);" class="nd" onclick="document.all.pattern.rows+=5; document.all.replacement.rows+=5;">&#8595;</a><a href="javascript:void(null);" class="nd" onclick="document.all.pattern.cols+=20; document.all.replacement.cols+=20;">&#8594;</a></span>&nbsp;&nbsp;<input type="button" value="Clear" onClick="document.all.pattern.value=''; document.all.replacement.value='';" /></td></tr>
<tr><td>Pattern:</td><td><textarea name="pattern" cols="70" rows="1"><?echo htmlspecialchars($pattern);?></textarea></td></tr>
<tr><td>Replacement:</td><td><textarea name="replacement" cols="70" rows="1"><?echo htmlspecialchars($replacement);?></textarea></td></tr>
<tr><td>SubSeparator:</td><td><textarea name="ssep" cols="20" rows="3"><?echo $_POST['ssep'];?></textarea></td></tr>
<!--
<tr><td> </td><td><span class="sub"><input type="checkbox"  name="ssepupcre"<?echo ($_POST['ssepupcre']=="on")?' checked="checked"':'';?> /><a onclick="document.all.ssepupcre.click();">Use PCRE</a></span></td></tr>
-->
<tr><td>Calculator:</td><td><input type="text" name="caculator" value="<?echo $_POST['caculator']?>" /></td></tr>
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
<tr><td>Square:</td><td><input type="radio" name="sqr_cl" id="sqr_cl_auto" value="auto"<?echo ($_POST['sqr_cl']=='auto'?' checked="checked"':'');?> /><label for="sqr_cl_auto">Auto</label> <input type="radio" name="sqr_cl" id="sqr_cl_man" value="man"<?echo ($_POST['sqr_cl']=='man'?' checked="checked"':'');?> /><label for="sqr_cl_man">Manual</label> Rows:<input type="text" name="sqr_r" size="2" value="<?echo $_POST['sqr_r'];?>" /> Columns:<input type="text" name="sqr_c" size="2" value="<?echo $_POST['sqr_c'];?>" /></td></tr>
<tr><td>SquareReflect</td><td><input type="checkbox" id="ref_ver" name="ref_ver"<?echo ($_POST['ref_ver']=='on'?' checked="checked"':'');?> /><label for="ref_ver">Vertical</label><input type="checkbox" id="ref_hor" name="ref_hor"<?echo ($_POST['ref_hor']=='on'?' checked="checked"':'');?> /><label for="ref_hor">Horizonal</label></td></tr>
<tr><td>StringMutate:</td><td>Keep Left:<input type="text" name="mut_l" size="2" value="<?echo $_POST['mut_l'];?>" /> Right:<input type="text" name="mut_r" size="2" value="<?echo $_POST['mut_r'];?>" /></td></tr>
<tr><td>Repeat:</td><td><input type="text" name="rpt" size="2" value="<?echo $_POST['rpt'];?>" /></td></tr>
<tr><td rowspan="3">To Table:</td><td>Border: <input type="checkbox" id="ttb_brd" name="ttb_brd"<?echo ($_POST['ttb_brd']=="on")?' checked="checked"':'';?> /><label for="ttb_brd">Outer</label> <input type="checkbox" id="ttb_ibrd" name="ttb_ibrd"<?echo ($_POST['ttb_ibrd']=="on")?' checked="checked"':'';?> /><label for="ttb_ibrd">Inner</label></td></tr>
<tr><td><input type="checkbox" id="ttb_mono" name="ttb_mono"<?echo ($_POST['ttb_mono']=="on")?' checked="checked"':'';?> /><label for="ttb_mono">MonoWidth</label></td></tr>
<tr><td>Align:<input type="radio" name="ttb_align" id="ttb_left" value="left" <?echo ($_POST['ttb_align']=="left")?'checked="checked" ':'';?>/><label for="ttb_left">Left</label> <input type="radio" name="ttb_align" id="ttb_center" value="center" <?echo ($_POST['ttb_align']=="center")?'checked="checked" ':'';?>/><label for="ttb_center">Center</label> <input type="radio" id="ttb_right" name="ttb_align" value="right" <?echo ($_POST['ttb_align']=="right")?'checked="checked" ':'';?>/><label for="ttb_right">Right</label></td></tr>
</table>
</span>
</fieldset>
<fieldset><legend>Textarea Appearance</legend>
<table>
<tr><td>Size: </td><td>Rows: <input type="text" size="3" name="trows" value="<?echo $_POST['trows']?>" /> Cols: <input type="text" size="3" name="tcols" value="<?echo $_POST['tcols'];?>" /></td></tr>
<tr><td>Directionality: </td><td><select name="dir" onChange="document.all.text.dir=this.value"><option value="LTR"<?echo ($dir=="LTR")?' selected="selected"':'';?>>Left to Right</option><option value="RTL"<?echo ($dir=="RTL")?' selected="selected"':'';?>>Right to Left</option></select> <input type="button" value="Change" onclick="if(document.all.trows.value>0)document.all.text.rows=document.all.trows.value; if(document.all.tcols.value>0)document.all.text.cols=document.all.tcols.value;" /></td></tr>
</table>
</fieldset>
<fieldset><legend>Charset</legend>
Current : <script type="text/javascript">document.write(((typeof(document.charset)=="undefined")?document.characterSet:document.charset));</script><br />
<script type="text/javascript">
	document.write('<input type="hidden" name="ccharset" value="'+((typeof(document.charset)=="undefined")?document.characterSet:document.charset)+'" />');
</script>
Next : <input type="text" name="charset" value="<?echo $_POST['charset'];?>" /><select onChange="document.all.charset.value=this.value;">
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
</select></fieldset>
<p>
<span style="text-align:left; float:left;"><a class="link" href="#top" onclick="showlinks();">Links</a> :: <a class="link" href="<?echo $_SERVER['PHP_SELF'];?>?appendix=note" target="_blank">Note</a> :: <a class="link" href="mailto:gmobug@gmail.com">Contact me</a></span>
<span style="text-align:right; float:right;"><a class="link" style="font-size:9pt;" href="<?echo $_SERVER['PHP_SELF'];?>?appendix=source">ver. <?echo $ver_serial;?></a></span>
</p>
</form>
</body>
</html>
