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
$ver_serial="200602053";

if(!function_exists('preg_replace')){
	addmsg('Fatal Error: Please Install PCRE Library!');
}

if(!function_exists('file_get_contents')){
	function file_get_contents($a){
		return implode('',file($a));
	}
}
#<function>
function addmsg($s){
	global $msg;
	$msg[count($msg)]=$s;
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
$r='<html><head><meta http-equiv=content-type content="text/html; charset='.$_POST['charset'].'">
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

function s2a($s){
	if($_POST['ssep_de']==''){
		addmsg('Error: Null SubSeparator');
		$r[0]=$s;
		return $r;
	}
	if($_POST['ssepupcre']=="on"){
		$r=preg_split($_POST['ssep_de'],$s);
	}else{
		$r=explode($_POST['ssep_de'],$s);
	}
	return $r;
}
function a2s($s){
	$r=implode($_POST['ssep_de'],$s);
	return $r;
}

function totable($s){
	global $hsc;
	$hsc=1;
	$l=explode("\n",$s);
	for($i=0;$i<count($l);$i++){
		$l[$i]=explode("\t",$l[$i]);
	}
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
	for($i=0;$i<count($l);$i++){
		for($j=0;$j<count($len);$j++){
			if(empty($l[$i][$j])){
				$l[$i][$j]=str_repeat(' ',$len[$j]);
			}else{
				$l[$i][$j].=str_repeat(' ',$len[$j]-strlen($l[$i][$j]));
			}
		}
	}
	for($i=0;$i<count($l);$i++){
		$l[$i]=implode('&#9474;',$l[$i]);
		$l[$i]=preg_replace("/ *$/","",$l[$i]);
	}
	for($i=0;$i<count($len);$i++){
		$len[$i]=str_repeat('&#9472;',$len[$i]/2);
	}
	$im=implode('&#9532;',$len);
	$s=implode("\n".$im."\n",$l);
	return $s;
}

function totable_brd($s){
	global $hsc;
	$hsc=1;
	$l=explode("\n",$s);
	for($i=0;$i<count($l);$i++){
		$l[$i]=explode("\t",$l[$i]);
	}
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
	for($i=0;$i<count($l);$i++){
		for($j=0;$j<count($len);$j++){
			if(empty($l[$i][$j])){
				$l[$i][$j]=str_repeat(' ',$len[$j]);
			}else{
				$l[$i][$j].=str_repeat(' ',$len[$j]-strlen($l[$i][$j]));
			}
		}
	}
	for($i=0;$i<count($l);$i++){
		$l[$i]="&#9474;".implode('&#9474;',$l[$i])."&#9474;";
	}
	for($i=0;$i<count($len);$i++){
		$len[$i]=str_repeat('&#9472;',$len[$i]/2);
	}
	$im="&#9500;".implode('&#9532;',$len)."&#9508;";
	$him="&#9484;".implode('&#9516;',$len)."&#9488;";
	$fim="&#9492;".implode('&#9524;',$len)."&#9496;";
	$s=$him."\n".implode("\n".$im."\n",$l)."\n".$fim;
	return $s;
}

function stdev($s){
	$a=average($s);
	$k=s2a($s);
	$t=0;
	for($i=0;$i<count($k);$i++){
		$t+=pow($k[$i]-$a,2);
	}
	return @sqrt($t/(count($k)-1));
}

function stdevp($s){
	$a=average($s);
	$k=s2a($s);
	$t=0;
	for($i=0;$i<count($k);$i++){
		$t+=pow($k[$i]-$a,2);
	}
	return @sqrt($t/count($k));
}

function average($s){
	$a=s2a($s);
	$t=0;
	for($i=0;$i<count($a);$i++){
		$t+=$a[$i];
	}
	return $t/count($a);
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
		addmsg('Notice: Incoherent Length');
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
			$s=@preg_replace(ent_de($p[$i]), ent_de($r[$i]), $s);
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

function pcre_mat($s){
	global $pattern;
	if($_POST['pattern']!=''){
		preg_match_all($pattern,$s,$res);
	}
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
	global $hsc;
	$hsc=1;
	$table=array('1'=>"&#12549;", 'q'=>"&#12550;", 'a'=>"&#12551;", 'z'=>"&#12552;", '2'=>"&#12553;", 'w'=>"&#12554;", 's'=>"&#12555;", 'x'=>"&#12556;", 'e'=>"&#12557;", 'd'=>"&#12558;", 'c'=>"&#12559;", 'r'=>"&#12560;", 'f'=>"&#12561;", 'v'=>"&#12562;", '5'=>"&#12563;", 't'=>"&#12564;", 'g'=>"&#12565;", 'b'=>"&#12566;", 'y'=>"&#12567;", 'h'=>"&#12568;", 'n'=>"&#12569;", 'u'=>"&#12583;", 'j'=>"&#12584;", 'm'=>"&#12585;", '8'=>"&#12570;", 'i'=>"&#12571;", 'k'=>"&#12572;", ','=>"&#12573;", '9'=>"&#12574;", 'o'=>"&#12575;", 'l'=>"&#12576;", '.'=>"&#12577;", '0'=>"&#12578;", 'p'=>"&#12579;", ';'=>"&#12580;", '/'=>"&#12581;", '-'=>"&#12582;", ' '=>" ", '6'=>"&#714; ", '3'=>"&#711; ", '4'=>"&#715; ", '7'=>"&#729; ");
	$r='';
	for($i=0;$i<strlen($s);$i++){
		$r.=$table[substr($s,$i,1)];
	}
	return $r;
}

function chewing_sort_de($s){
	global $hsc;
	$hsc=1;
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
	if(strlen($_POST['ssep_de'])==1){
	$q=$_POST['ssep_de'];
	}else{
	$q=' ';
	addmsg('Error: Length of SubSeparator != 1, use SPACE');
	}
	if(substr($s,0,1)==$q){
		$r='SEP';
	}elseif(ord(substr($s,0,1))>127){
		$r=strtoupper(hex_en(substr($s,0,1)));
	}else{
		$r=substr($s,0,1);
	}
	for($i=1;$i<strlen($s);$i++){
		if(substr($s,$i,1)==$q){
			$r.=' SEP';
		}elseif(ord(substr($s,$i,1))>127){
			$r.=' '.strtoupper(hex_en(substr($s,$i,1)));
		}else{
			$r.=' '.substr($s,$i,1);
		}
	}
	return $r;
}

function ASCIIFilter_de($s){
	if(strlen($_POST['ssep_de'])==1){
	$q=$_POST['ssep_de'];
	}else{
	$q=' ';
	addmsg('Error: Length of SubSeparator != 1, use SPACE');
	}
	$r='';
	$a=explode($q,$s);
	for($i=0;$i<count($a);$i++){
		if($a[$i]=='SEP'){
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

function factorial($s){
	$s=(int) $s;
	$i=1;
	for($j=1;$j<=$s;$j++){
		$i*=$j;
	}
	return $i;
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
		case 'md5': $s=md5($s); break;
		case 'srt': $k=s2a($s); sort($k); $s=a2s($k); break;
		case 'fac': $s=factorial($s); break;
		case 'ave': $s=average($s); break;
		case 'sdv': $s=stdevp($s); break;
		case 'ssdv': $s=stdev($s); break;
		case 'stu': $s=strtoupper($s); break;
		case 'bbs': $s=bbs2html($s); break;
		case 'bbd': $s=bbs2html_dc($s); break;
		case 'ttb': $s=totable($s);break;
		case 'ttbd': $s=totable_brd($s);break;
		case 'stl': $s=strtolower($s); break;
		case 'ucw': $s=ucwords($s); break;
		case 'sln': $s=strlen($s); break;
		case 'cor': $s=correct($s); break;
		case 'che': break;
		case 'ascii': $s=ASCIIFilter($s); break;
		case 'dna': $s=dna_en($s); break;
		case 'key': $s=key_xor($_POST['key'],$s); break;
		case 'bre': $s=bit_rev($s); break;
		case 'bod': $s=bitorder_en($_POST['order'],$s); break;
		case 'tra': $s=tran($_POST['transpose'],$s,2); break;
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
		case 'md5': addmsg('Information: <a href="http://www.md5lookup.com/?category=main&page=search" target="_blank">http://www.md5lookup.com</a>'); break;
		case 'stu': break;
		case 'crv': $s=case_rev($s); break;
		case 'stl': break;
		case 'ucw': break;
		case 'bbs': break;
		case 'srt': $k=s2a($s); rsort($k); $s=a2s($k); break;
		case 'bbd': break;
		case 'rpt': break;
		case 'pcr': break;
		case 'ttb': break;
		case 'ttbd': break;
		case 'rep': break;
		case 'cor': break;
		case 'pcm': break;
		case 'ascii': $s=ASCIIFilter_de($s); break;
		case 'fac': break;
		case 'ave': break;
		case 'sdv': break;
		case 'ssdv': break;
		case 'sln': break;
		case 'che': $s=chewing_de($s); break;
		case 'chs': $s=chewing_sort_de($s); break;
		case 'dna': $s=dna_de($s); break;
		case 'key': $s=key_xor($_POST['key'],$s); break;
		case 'bre': $s=bit_rev($s); break;
		case 'bod': $s=bitorder_de($_POST['order'],$s); break;
		case 'tra': $s=tran(12-$_POST['transpose'],$s,1); break;
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
	die(file_get_contents($_SERVER['SCRIPT_FILENAME']));
}
if($_GET['appendix']=="note"){
$r='<pre>
Big5 Range: [\xA1-\xF9][\x40-\x7E\xA1-\xFE]
</pre>';
	die($r);
}
$hsc=0;
#<init>
if(isset($_POST['action'])){
	$_POST['rot']=$_POST['rot']%26;
	$_POST['nrot']=$_POST['nrot']%10;
	$method=$_POST['method'];
	$dir=$_POST['dir'];
	$process=$_POST['process'];
	$_POST['trows']=$_POST['trows']*1;
	$_POST['tcols']=$_POST['tcols']*1;
	$_POST['transpose']=$_POST['transpose']%12;
	$_POST['rpt']=$_POST['rpt']*1;
	$_POST['batch2']=$_POST['batch'];
	$f=@fopen($_SERVER['SCRIPT_FILENAME'].".clip","w+");
	@fwrite($f,stripslashes($_POST['clip']));
	@fclose($f);
	$_POST['sepr']=str_replace("\r\n","\n",stripslashes($_POST['sepr']));
	$_POST['ssep']=stripslashes($_POST['ssep']);
	$_POST['ssep_de']=ent_de($_POST['ssep']);
	$sep_array=explode("\n",$_POST['sepr']);
	if($_POST['sep']=="on"){
	for($i=0;$i<count($sep_array);$i++){
		$sep_array[$i]=ent_de($sep_array[$i]);
		if($sep_array[$i]==''){
			$_POST['sep']="off";
			addmsg('Error: Null Separator');
		}
	}
	}
}else{
	$_POST['rot']=13;
	$_POST['nrot']=5;
	$method="raw";
	$_POST['charset']="big5";
	$dir="LTR";
	$process="ob";
	$_POST['trows']=15;
	$_POST['tcols']=85;
	$_POST['transpose']=0;
	$_POST['out']="text";
	$_POST['rpt']=1;
	$_POST['ssep']=' ';
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
if($_FILES['fin']['tmp_name']!="none" && $_FILES['fin']['tmp_name']!="" && $_FILES['fin']['size']>0){
	$s=str_replace("\r\n","\n",file_get_contents($_FILES['fin']['tmp_name']));
	unlink($_FILES['fin']['tmp_name']);
}else{
	$s=str_replace("\r\n","\n",stripslashes($_POST['text']));
}
if($_POST['batch']==''){
	$_POST['batch2']=($_POST['mode']=="en"?'e':'d').'-'.$method;
}
if($_POST['sep']=="on"){
	$s=pro($s,$sep_array);
}else{
	$s=proc($s);
}
if($_POST['out']=="file"){
	set_time_limit(60);
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
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $_POST['charset'];?>">
<style type="text/css">
	a.link{text-decoration:none;}
	a:hover.link{text-decoration:underline;position:relative;top:2px;left:2px;}
	.ed {color:#00ff00;}
	.ue {color:#0000ff;}
	.ud {color:#ff0000;}
	.nd {text-decoration:none; color:#000;}
	a:visited.nd {color:#000;}
	.sub {position:relative; left: +1em;}
</style>
<title>Bug Converter - ver. <?echo $ver_serial;?></title>
</head>
<body>
<?
if(count($msg)>0){
echo '<div style="font-size:10pt;"><fieldset><legend>Message</legend>'.implode('<br />',$msg).'</fieldset></div>';
}
?>
<form method="post" action="<?echo $_SERVER['PHP_SELF'];?>" name="form" enctype="multipart/form-data">
<input type="hidden" name="action" value="y" />
<table><tr><td style="vertical-align:top;">
<textarea dir="<?echo $dir;?>" name="text" rows="<?echo $_POST['trows'];?>" cols="<?echo $_POST['tcols'];?>"><?echo ($_POST['ssp']=="on" || $hsc==1)?$s:htmlspecialchars($s);?></textarea></td>
<td style="vertical-align:top;">
<fieldset><legend>Assistance</legend>
<a class="link" href="javascript:void(null);" title="Click and Paste" onclick="document.all.text.value=document.all.text.value+'	'; document.all.text.focus();">TAB</a><br />
<a class="link" href="http://mathml.twbbs.org" target="_blank">Bug's MathML Board</a><br />
<a class="link" href="<?echo $_SERVER['PHP_SELF'];?>?appendix=note" target="_blank">Note</a><br /><br />
<a class="link" href="mailto:gmobug@gmail.com">Contact me</a>
</fieldset>
<fieldset><legend>Miscellaneous</legend>
<input type="checkbox" name="ssp"<?echo ($_POST['ssp']=="on")?' checked="checked"':'';?> /><a onclick="document.all.ssp.click();">Skip HtmlSpecialChars</a><br />
<input type="checkbox" onclick="if(this.checked==true){document.getElementById('cpb').style.display='block';}else{document.getElementById('cpb').style.display='none';}" name="cpo"<?echo ($_POST['cpo']=="on")?' checked="checked"':'';?> /><a onclick="document.all.cpo.click();">ClipBoard</a><br />
<!--
<input type="checkbox" onclick="if(this.checked==true && document.all.sep.checked==true){document.all.sep.click();}" name="blk"<?echo ($_POST['blk']=="on")?' checked="checked"':'';?> /><a onclick="document.all.blk.click();">Block</a><input type="text" size="10" name="blkpat" value="<?echo $_POST['blkpat'];?>" /><br />
<input type="checkbox" onclick="if(this.checked==true){document.all.blk.checked=false; document.getElementById('sepa').style.display='block';}else{document.getElementById('sepa').style.display='none';}" name="sep"<?echo ($_POST['sep']=="on")?' checked="checked"':'';?> /><a onclick="document.all.sep.click();">Separator</a><br />
<div id="sepa" class="sub" style="display:<?echo $_POST['sep']=="on"?'block':'none';?>;"><input type="checkbox" name="upcre"<?echo ($_POST['upcre']=="on")?' checked="checked"':'';?> /><a onclick="document.all.upcre.click();">Use PCRE</a>&nbsp;<a href="#" class="nd" onclick="if(document.all.sepr.cols>20)document.all.sepr.cols-=20;">&#8592;</a><a href="#" class="nd" onclick="if(document.all.sepr.rows>5)document.all.sepr.rows-=5;">&#8593;</a><a href="#" class="nd" onclick="document.all.sepr.cols=20; document.all.sepr.rows=3;">&#9678;</a><a href="#" class="nd" onclick="document.all.sepr.rows+=5;">&#8595;</a><a href="#" class="nd" onclick="document.all.sepr.cols+=20;">&#8594;</a><br /><textarea name="sepr" cols="20" rows="3"><?echo $_POST['sepr'];?></textarea></div>
-->
<!--BOD-->
<input type="checkbox" onclick="if(this.checked==true){document.getElementById('sepa').style.display='block';}else{document.getElementById('sepa').style.display='none';}" name="sep"<?echo ($_POST['sep']=="on")?' checked="checked"':'';?> /><a onclick="document.all.sep.click();">Separator</a><br />
<div id="sepa" class="sub" style="display:<?echo $_POST['sep']=="on"?'block':'none';?>;"><a href="#" class="nd" onclick="if(document.all.sepr.cols>20)document.all.sepr.cols-=20;">&#8592;</a><a href="#" class="nd" onclick="if(document.all.sepr.rows>5)document.all.sepr.rows-=5;">&#8593;</a><a href="#" class="nd" onclick="document.all.sepr.cols=20; document.all.sepr.rows=3;">&#9678;</a><a href="#" class="nd" onclick="document.all.sepr.rows+=5;">&#8595;</a><a href="#" class="nd" onclick="document.all.sepr.cols+=20;">&#8594;</a><br /><textarea name="sepr" cols="20" rows="3"><?echo $_POST['sepr'];?></textarea></div>
<!--EOD-->
</fieldset>
<span style="float:left;"><a href="#" class="link" style="font-weight:bold; color: #333333;" onclick="document.all.form.submit();">[Submit]</a></span><span style="float: right;"><a href="#" class="link" onClick="if(confirm('Sure to clear ?'))location.href='<?echo $_SERVER['PHP_SELF'];?>'" style="font-weight:bold; color: #333333;">[Clear]</a></span>
</td></tr></table><fieldset id="cpb" style="display: <?echo $_POST['cpo']=="on"?'block':'none';?>;"><legend>ClipBoard</legend>
<textarea name="clip" rows="5" cols="90"><?echo htmlspecialchars(@file_get_contents($_SERVER['SCRIPT_FILENAME'].".clip"));?></textarea>
</fieldset>
<table width="%100"><tr><td>
<fieldset><legend>I/O</legend>
<table>
<tr><td>Input: </td><td>From File<input type="file" name="fin"> <input type="button" value="Clear" onclick="document.all.fin.outerHTML=document.all.fin.outerHTML.replace(/value=\w/g,'');" /></td></tr>
<tr><td>Output: </td><td><input type="radio" name="out" onclick="document.all.form.target='_self'" value="text" <?echo ($_POST['out']=="text")?'checked="checked" ':'';?>/><a onClick="document.all.out[0].click();">Text Area</a> <input type="radio" name="out" onclick="document.all.form.target='_self'" value="file" <?echo ($_POST['out']=="file")?'checked="checked" ':'';?>/><a onClick="document.all.out[1].click();">File Download</a> <input type="radio" name="out" onclick="document.all.form.target='_blank'" value="blank" <?echo ($_POST['out']=="blank")?'checked="checked" ':'';?>/><a onClick="document.all.out[2].click();">Blank Frame</a></td></tr>
</table>
</fieldset></td><td>
<fieldset><legend>Textarea Appearance</legend>
<table><tr><td>Directionality: </td><td><select name="dir" onChange="document.all.text.dir=this.value"><option value="LTR"<?echo ($dir=="LTR")?' selected="selected"':'';?>>Left to Right</option><option value="RTL"<?echo ($dir=="RTL")?' selected="selected"':'';?>>Right to Left</option></select></td></tr>
<tr><td>Size: </td><td>Rows: <input type="text" size="3" name="trows" value="<?echo $_POST['trows']?>" /> Cols: <input type="text" size="3" name="tcols" value="<?echo $_POST['tcols'];?>" /> <input type="button" value="Change" onclick="if(document.all.trows.value>0)document.all.text.rows=document.all.trows.value; if(document.all.tcols.value>0)document.all.text.cols=document.all.tcols.value;" /></td></tr>
</table>
</fieldset></td></tr>
</table>
<fieldset><legend>Convert</legend>
<span style="float:left;">
<table>
<tr><td style="vertical-align:top;">Method: </td><td><select name="method">
<option style="color:#555555;" value="raw"<?echo ($method=="raw")?' selected="selected"':'';?>>RAW (Output input)</option>
<option value="srt"<?echo ($method=="srt")?' selected="selected"':'';?>>Sort</option>
<option value="tra"<?echo ($method=="tra")?' selected="selected"':'';?>>Transpose</option>
<option class="ud" value="bbd"<?echo ($method=="bbd")?' selected="selected"':'';?>>BBS -> HTML (Double Color)</option>
<option class="ud" value="bbs"<?echo ($method=="bbs")?' selected="selected"':'';?>>BBS -> HTML</option>
<option value="url"<?echo ($method=="url")?' selected="selected"':'';?>>URL</option>
<option value="ur2"<?echo ($method=="ur2")?' selected="selected"':'';?>>URL-2 (Space to %20)</option>
<option value="b64"<?echo ($method=="b64")?' selected="selected"':'';?>>Base64</option>
<option value="dna"<?echo ($method=="dna")?' selected="selected"':'';?>>DNA</option>
<option value="bin"<?echo ($method=="bin")?' selected="selected"':'';?>>Bin</option>
<option value="oct"<?echo ($method=="oct")?' selected="selected"':'';?>>Oct</option>
<option value="dec"<?echo ($method=="dec")?' selected="selected"':'';?>>Dec</option>
<option value="hex"<?echo ($method=="hex")?' selected="selected"':'';?>>Hex</option>
<option class="ud" value="ttb"<?echo ($method=="ttb")?' selected="selected"':'';?>>To Table</option>
<option class="ud" value="ttbd"<?echo ($method=="ttbd")?' selected="selected"':'';?>>To Table (Border)</option>
<option class="ud" value="cor"<?echo ($method=="cor")?' selected="selected"':'';?>>Correct</option>
<option value="ascii"<?echo ($method=="ascii")?' selected="selected"':'';?>>ASCIIFilter</option>
<option class="ue" value="che"<?echo ($method=="che")?' selected="selected"':'';?>>Chewing</option>
<option class="ue" value="chs"<?echo ($method=="chs")?' selected="selected"':'';?>>Chewing (Sort)</option>
<option class="ud" value="rep"<?echo ($method=="rep")?' selected="selected"':'';?>>Replace</option>
<option class="ud" value="pcr"<?echo ($method=="pcr")?' selected="selected"':'';?>>PCRE Replace</option>
<option class="ud" value="pcm"<?echo ($method=="pcm")?' selected="selected"':'';?>>PCRE Match</option>
<option value="spe"<?echo ($method=="spe")?' selected="selected"':'';?>>HtmlSpecialChars</option>
<option class="ud" value="rpt"<?echo ($method=="rpt")?' selected="selected"':'';?>>Repeat</option>
<option class="ed" value="rev"<?echo ($method=="rev")?' selected="selected"':'';?>>Reverse</option>
<option class="ed" value="crv"<?echo ($method=="crv")?' selected="selected"':'';?>>Case Reverse</option>
<option class="ud" value="stu"<?echo ($method=="stu")?' selected="selected"':'';?>>StringToUpper</option>
<option class="ud" value="stl"<?echo ($method=="stl")?' selected="selected"':'';?>>StringToLower</option>
<option class="ud" value="ucw"<?echo ($method=="ucw")?' selected="selected"':'';?>>UppercaseTheFirstCharacter</option>
<option class="ud" value="sln"<?echo ($method=="sln")?' selected="selected"':'';?>>StringLength</option>
<option value="bod"<?echo ($method=="bod")?' selected="selected"':'';?>>BitOrder</option>
<option class="ed" value="bre"<?echo ($method=="bre")?' selected="selected"':'';?>>BitReverse (not)</option>
<option class="ed" value="key"<?echo ($method=="key")?' selected="selected"':'';?>>Key (xor)</option>
<option value="rot"<?echo ($method=="rot")?' selected="selected"':'';?>>Rotate</option>
<option value="md5"<?echo ($method=="md5")?' selected="selected"':'';?>>MD5</option>
<option class="ud" value="fac"<?echo ($method=="fac")?' selected="selected"':'';?>>Factorial</option>
<option class="ud" value="ave"<?echo ($method=="ave")?' selected="selected"':'';?>>Arithmetic Average</option>
<option class="ud" value="sdv"<?echo ($method=="sdv")?' selected="selected"':'';?>>Standard Deviation</option>
<option class="ud" value="ssdv"<?echo ($method=="ssdv")?' selected="selected"':'';?>>Sample Standard Deviation</option>
</select> <input type="button" value="Add to Batch" onClick="var i; i=((document.all.mode[0].checked==true)?'e':'d'); if(document.all.batch.value==''){document.all.batch.value=i+'-'+document.all.method.value;}else{document.all.batch.value=document.all.batch.value+', '+i+'-'+document.all.method.value}" /></td></tr>
<tr><td></td><td><input type="radio" name="mode" value="en" <?echo ($_POST['mode']!="de")?'checked="checked" ':'';?>/><a onClick="document.all.mode[0].checked='checked';">Encode</a> <input type="radio" name="mode" value="de" <?echo ($_POST['mode']=="de")?'checked="checked" ':'';?>/><a onClick="document.all.mode[1].checked='checked';">Decode</a> <a style="font-size:9pt; color:#0000ff;">(Unsupport Encode)</a><a style="font-size:9pt; color:#ff0000;">(Unsupport Decode)</a><a style="font-size:9pt; color:#00ff00;">(Encode = Decode)</a></td></tr>
<tr><td style="vertical-align:top;">Batch: </td><td><input type="text" size="70" name="batch" value="<?echo $_POST['batch'];?>" /><br /><input type="radio" name="process" value="ob" <?echo ($process=="ob")?'checked="checked" ':'';?> /><a onClick="document.all.process[0].checked='checked';">Forward</a> <input type="radio" name="process" value="re" <?echo ($process=="re")?'checked="checked" ':'';?> /><a onClick="document.all.process[1].checked='checked';">Backward</a> <input type="button" value="Clear" onclick="document.all.batch.value='';" /></td></tr>
<tr><td>Match/Replace:</td><td><span id="mrc"><a href="#" class="nd" onclick="if(document.all.pattern.cols>20)document.all.pattern.cols-=20; if(document.all.replacement.cols>20)document.all.replacement.cols-=20;">&#8592;</a><a href="#" class="nd" onclick="if(document.all.pattern.rows>5)document.all.pattern.rows-=5; if(document.all.replacement.rows>5)document.all.replacement.rows-=5;">&#8593;</a><a href="#" class="nd" onclick="document.all.pattern.rows=1; document.all.replacement.rows=1; document.all.pattern.cols=70; document.all.replacement.cols=70;">&#9678;</a><a href="#" class="nd" onclick="document.all.pattern.rows+=5; document.all.replacement.rows+=5;">&#8595;</a><a href="#" class="nd" onclick="document.all.pattern.cols+=20; document.all.replacement.cols+=20;">&#8594;</a></span>&nbsp;&nbsp;<input type="button" value="Clear" onClick="document.all.pattern.value=''; document.all.replacement.value='';" /></td></tr>
<tr><td style="vertical-align:top;">Pattern:</td><td><textarea name="pattern" cols="70" rows="1"><?echo htmlspecialchars($pattern);?></textarea></td></tr>
<tr><td style="vertical-align:top;">Replacement:</td><td><textarea name="replacement" cols="70" rows="1"><?echo htmlspecialchars($replacement);?></textarea></td></tr></table>
</span><span style="float:left;"><table>
<tr><td>SubSeparator:</td><td><input type="text" name="ssep" value="<?echo $_POST['ssep'];?>" /></td></tr>
<!--
<tr><td> </td><td><span class="sub"><input type="checkbox"  name="ssepupcre"<?echo ($_POST['ssepupcre']=="on")?' checked="checked"':'';?> /><a onclick="document.all.ssepupcre.click();">Use PCRE</a></span></td></tr>
-->
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
<tr><td>Repeat:</td><td><input type="text" name="rpt" size="2" value="<?echo $_POST['rpt'];?>" /></td></tr></table>
</span>
</fieldset>
<fieldset><legend>Charset</legend>
Current : <script language="javascript">document.write(((typeof(document.charset)=="undefined")?document.characterSet:document.charset));</script><br />
<script language="javascript">
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
<p style="text-align: right;"><a class="link" style="color:#808080; font-size:9pt;" href="<?echo $_SERVER['PHP_SELF'];?>?appendix=source">ver. <?echo $ver_serial;?></a></p>
</form>
</body>
</html>
