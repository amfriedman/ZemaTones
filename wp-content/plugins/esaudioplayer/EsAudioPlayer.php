<?php

/*

Plugin Name: EsAudioPlayer

Plugin URI: http://tempspace.net/plugins/?page_id=4

Description: This is an Extremely Simple Audio Player plugin.

Version: 1.6.1

Author: Atsushi Ueda

Author URI: http://tempspace.net/plugins/

License: GPL2

*/



define("ESP_DEBUG", 0);



//function dbg2($str){$fp=fopen("/tmp/smdebug.txt","a");fwrite($fp,$str . "\n");fclose($fp);}



function esplayer_init() {

	wp_enqueue_script('jquery');

}

add_action('init', 'esplayer_init');





$player_number = 1;

$esAudioPlayer_plugin_URL = get_option( 'siteurl' ) . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));



function esplayer_is_mobile(){//http://stackoverflow.com/questions/5122566/simple-smart-phone-detection

        $user_agent = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed

        $accept     = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed

        return false

            || (preg_match('/ipad/i',$user_agent))

            || (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent))

            || (preg_match('/android/i',$user_agent))

            || (preg_match('/opera mini/i',$user_agent))

            || (preg_match('/blackberry/i',$user_agent))

            || (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent))

            || (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent))

            || (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent))

            || ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0))

            || (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE']))

            || (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',)))

        ;

}



define("LEX_NULL", 100);

define("LEX_STRING", 101);

define("LEX_ALNUM", 102);

define("LEX_WHITE", 103);

define("LEX_MISC", 105);

define("LEX_EOL", 106);



define("LEX_PTAG_OPEN", 1000);

define("LEX_PTAG_CLOSE", 1001);

define("LEX_CANVASTAG", 1002);

define("LEX_IMGTAG_OPEN", 1003);

define("LEX_SRC", 1004);

define("LEX_GT", 1005);



$esplayer_local_token[0] = array("token"=>"<p>", "case"=>false, "code"=>LEX_PTAG_OPEN);

$esplayer_local_token[1] = array("token"=>"</p>", "case"=>false, "code"=>LEX_PTAG_CLOSE);

$esplayer_local_token[2] = array("token"=>"<canvas ", "case"=>false, "code"=>LEX_CANVASTAG);

$esplayer_local_token[3] = array("token"=>"<img", "case"=>false, "code"=>LEX_IMGTAG_OPEN);

$esplayer_local_token[4] = array("token"=>"src", "case"=>false, "code"=>LEX_SRC);

$esplayer_local_token[5] = array("token"=>">", "case"=>false, "code"=>LEX_GT);

$esplayer_local_token_idx = array();

$esplayer_max_token_length = 0;



function esplayer_simplelexer(&$str, $pos, &$ret_str)

{

	global $esplayer_local_token;

	global $esplayer_max_token_length;

	global $esplayer_local_token_idx;

	$tbuf_len = 0;

	

	if ($esplayer_max_token_length==0) {

		for ($i=0; $i<count($esplayer_local_token); $i++) {

			if (mb_strlen($esplayer_local_token[$i]["token"]) > $esplayer_max_token_length) {

				$esplayer_max_token_length = mb_strlen($esplayer_local_token[$i]["token"]);

				$esplayer_local_token_idx[mb_substr($esplayer_local_token[$i]["token"],0,1)]=1;

			}

		}

	}

	$tbuf_len = $esplayer_max_token_length*2;

	if ($tbuf_len<50) $tbuf_len=50;



	$tlen = mb_strlen($str);

	

	if ($pos >= $tlen) {

		return LEX_EOL;

	}



	$tmpstr = mb_substr($str, $pos, $tbuf_len);



	$mtr_lexer_white=" \t\n\r";

	if (!(mb_strpos($mtr_lexer_white, mb_substr($tmpstr,0,1))===FALSE)) {

		$tpos=0;

		for ($i=$pos; $i<mb_strlen($str); $i++) {

			if (mb_strpos($mtr_lexer_white, mb_substr($tmpstr,$tpos,1))===FALSE) {

				break;

			}

			$tpos ++;

			if ($tpos >= $tbuf_len-$esplayer_max_token_length) {

				$tpos = 0;

				$tmpstr = mb_substr($str,$i+1,$tbuf_len);			

			}			

		}

		$ret_str = mb_substr($str, $pos, $i-$pos);

		return LEX_WHITE;

	}



	for ($i=0; $i<count($esplayer_local_token); $i++) {

		$tok =$esplayer_local_token[$i]["token"]; 

		$rtok = mb_substr($tmpstr, 0, mb_strlen($tok));

		if (($esplayer_local_token[$i]["case"] && $rtok == $tok) || !(mb_stripos($rtok,$tok)===false)) {

			$ret_str = $rtok;

			return $esplayer_local_token[$i]["code"];

		} 

	}



	$mtr_lexer_alnum="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";

	if (!(mb_strpos($mtr_lexer_alnum, mb_substr($tmpstr,0,1))===FALSE)) {

		$tpos=0;

		for ($i=$pos; $i<mb_strlen($str); $i++) {

			if (mb_strpos($mtr_lexer_alnum, mb_substr($tmpstr,$tpos,1))===FALSE) {

				break;

			}

			$tpos++;

			if ($tpos >= $tbuf_len-$esplayer_max_token_length) {

				$tpos = 0;

				$tmpstr = mb_substr($str,$i+1,$tbuf_len);			

			}			

		}

		$ret_str = mb_substr($str, $pos, $i-$pos);

		return LEX_ALNUM;

	}



	if (!(mb_strpos("\"", mb_substr($tmpstr,0,1))===FALSE)) {

		$tpos=1;

		for ($i=$pos+1; $i<mb_strlen($str); $i++) {

			if (mb_substr($tmpstr,$tpos,1) == "\"") {

				$i++;

				break;

			}

			$tpos++;

			if ($tpos >= $tbuf_len-$esplayer_max_token_length) {

				$tpos = 0;

				$tmpstr = mb_substr($str,$i+1,$tbuf_len);			

			}

		}

		$ret_str = mb_substr($str, $pos, $i-$pos);

		return LEX_STRING;

	}

	

	$tpos=0;

	for ($i=$pos; $i<$tlen; $i++) {

		$chr = mb_substr($tmpstr,$tpos,1);

		if (!(mb_strpos($mtr_lexer_white, $chr)===FALSE)) {

			break;

		}

		if ($chr=="\"") {

			break;

		}

		if ($esplayer_local_token_idx[$chr]==1) {

			$flg = 0;

			for ($j=0; $j<count($esplayer_local_token); $j++) {

				$tok =$esplayer_local_token[$j]["token"]; 

				$rtok = mb_substr($tmpstr, $tpos, mb_strlen($tok));

				if (($esplayer_local_token[$j]["case"] && $rtok == $tok) || !(mb_stripos($rtok,$tok)===false)) {

					$flg=1;

					break;

				} 

			}

			if ($flg) break;

		}

		$tpos ++;

		if ($tpos >= $tbuf_len-$esplayer_max_token_length) {

			$tpos = 0;

			$tmpstr = mb_substr($str,$i+1,$tbuf_len);			

		}

	}

	

	$ret_str = mb_substr($str,$pos,$i-$pos);

	

	return LEX_NULL;

}







$esplayer_imgs_num = 0;

$esplayer_imgs[0] = '';

$esplayer_imgs_player_number[0] = 0;





function esplayer_lex(&$content,&$lex_pos, &$token)

{

	for (;;) {

		$ret =  esplayer_simplelexer($content, $lex_pos, $token);

		$lex_pos += mb_strlen($token);

		if ($ret == LEX_WHITE) {

			continue;

		}

		return $ret;

	}

}





function EsAudioPlayer_filter_0($raw_text) 

{

	$ret = mb_ereg_replace("\]<br />[\n]\[esplayer", "] [esplayer", $raw_text);

	$ret = mb_ereg_replace("[\n]*\[esplayer", "[esplayer", $ret);

	$ret = mb_ereg_replace("\]\[esplayer", "] [esplayer", $ret);

	return $ret;

}

add_filter('the_content',  "EsAudioPlayer_filter_0", 10) ;





$esplayer_mode = "x";



function EsAudioPlayer_read_accessibility_setting()

{

	global $esplayer_acc_text_enable;

	global $esplayer_acc_msg_download;

	global $esplayer_acc_scr_enable;

	global $esplayer_acc_scr_basic_btns;

	global $esplayer_acc_scr_msg_play_btn;

	global $esplayer_acc_scr_msg_stop_btn;

	global $esplayer_acc_scr_msg_playstop_btn;

	global $esplayer_acc_scr_msg_playpause_btn;

	global $esplayer_acc_scr_fw_enable;

	global $esplayer_acc_scr_fw_amount;

	global $esplayer_acc_scr_fw_unit;

	global $esplayer_acc_scr_fw_msg;

	global $esplayer_acc_scr_rew_enable;

	global $esplayer_acc_scr_rew_amount;

	global $esplayer_acc_scr_rew_unit;

	global $esplayer_acc_scr_rew_msg;

	global $esplayer_acc_scr_ffw_enable;

	global $esplayer_acc_scr_ffw_amount;

	global $esplayer_acc_scr_ffw_unit;

	global $esplayer_acc_scr_ffw_msg;

	global $esplayer_acc_scr_frew_enable;

	global $esplayer_acc_scr_frew_amount;

	global $esplayer_acc_scr_frew_unit;

	global $esplayer_acc_scr_frew_msg;

	global $esplayer_acc_scr_lnk_enable;

	global $esplayer_acc_scr_lnk_msg;



	$esplayer_acc_text_enable = get_option("esaudioplayer_acc_text_enable", "0");

	$esplayer_acc_msg_download = get_option("esaudioplayer_acc_msg_download", "download the audio");

	$esplayer_acc_scr_enable = get_option("esaudioplayer_acc_scr_enable", "0");

	$esplayer_acc_scr_basic_btns = get_option("esaudioplayer_acc_scr_basic_btns", "playstop");

	$esplayer_acc_scr_msg_play_btn = get_option("esaudioplayer_acc_scr_msg_play_btn", "play");

	$esplayer_acc_scr_msg_stop_btn = get_option("esaudioplayer_acc_scr_msg_stop_btn", "stop");

	$esplayer_acc_scr_msg_playstop_btn = get_option("esaudioplayer_acc_scr_msg_playstop_btn", "play or stop");

	$esplayer_acc_scr_msg_playpause_btn = get_option("esaudioplayer_acc_scr_msg_playpause_btn", "play or pause");

	$esplayer_acc_scr_fw_enable = get_option("esaudioplayer_acc_scr_fw_enable", "1");

	$esplayer_acc_scr_fw_amount = get_option("esaudioplayer_acc_scr_fw_amount", "15");

	$esplayer_acc_scr_fw_unit = get_option("esaudioplayer_acc_scr_fw_unit", "sec");

	$esplayer_acc_scr_fw_msg = get_option("esaudioplayer_acc_scr_fw_msg", "forward 15 seconds");

	$esplayer_acc_scr_rew_enable = get_option("esaudioplayer_acc_scr_rew_enable", "1");

	$esplayer_acc_scr_rew_amount = get_option("esaudioplayer_acc_scr_rew_amount", "15");

	$esplayer_acc_scr_rew_unit = get_option("esaudioplayer_acc_scr_rew_unit", "sec");

	$esplayer_acc_scr_rew_msg = get_option("esaudioplayer_acc_scr_rew_msg", "rewind 15 seconds");

	$esplayer_acc_scr_ffw_enable = get_option("esaudioplayer_acc_scr_ffw_enable", "0");

	$esplayer_acc_scr_ffw_amount = get_option("esaudioplayer_acc_scr_ffw_amount", "10");

	$esplayer_acc_scr_ffw_unit = get_option("esaudioplayer_acc_scr_ffw_unit", "pct");

	$esplayer_acc_scr_ffw_msg = get_option("esaudioplayer_acc_scr_ffw_msg", "forward 10%");

	$esplayer_acc_scr_frew_enable = get_option("esaudioplayer_acc_scr_frew_enable", "0");

	$esplayer_acc_scr_frew_amount = get_option("esaudioplayer_acc_scr_frew_amount", "10");

	$esplayer_acc_scr_frew_unit = get_option("esaudioplayer_acc_scr_frew_unit", "pct");

	$esplayer_acc_scr_frew_msg = get_option("esaudioplayer_acc_scr_frew_msg", "rewind 10%");

	$esplayer_acc_scr_lnk_enable = get_option("esaudioplayer_acc_scr_lnk_enable", "0");

	$esplayer_acc_scr_lnk_msg = get_option("esaudioplayer_acc_scr_lnk_msg", "%title%");

}

EsAudioPlayer_read_accessibility_setting();





function EsAudioPlayer_DivideDgtUnt($in, &$out_dgt, &$out_unt)

{

	for ($i=strlen($in); $i>0; $i--) {

		if (is_numeric(substr($in, $i-1, 1))) break;

	}

	$out_dgt = substr($in, 0, $i);

	$out_unt = substr($in, $i);

}



function EsAudioPlayer_CalculateSize($width, $height, $shw_rate, &$ret_w, &$ret_h)

{

	$w_d="";

	$w_u="";

	$h_d="";

	$h_u="";

	EsAudioPlayer_DivideDgtUnt($width, $w_d, $w_u);

	EsAudioPlayer_DivideDgtUnt($height, $h_d, $h_u);



	if ($shw_rate==-999) $shw_rate=get_option("esaudioplayer_shadowsize", "0.25");



	$shw_size = min($w_d, $h_d) * $shw_rate;



	$ret_w = ($w_d+$shw_size) . $w_u;

	$ret_h = ($h_d+$shw_size) . $h_u;

}







function EsAudioPlayer_shortcode($atts, $content = null) {

	global $player_number;

	global $esplayer_imgs_num, $esplayer_imgs, $esplayer_imgs_player_number;

	global $esplayer_script_var;

	global $esplayer_script_body;

	global $esplayer_mode;

	global $esplayer_acc_text_enable;

	global $esplayer_acc_msg_download;

	global $esplayer_acc_scr_enable;

	global $esplayer_acc_scr_basic_btns;

	global $esplayer_acc_scr_msg_play_btn;

	global $esplayer_acc_scr_msg_stop_btn;

	global $esplayer_acc_scr_msg_playstop_btn;

	global $esplayer_acc_scr_msg_playpause_btn;

	global $esplayer_acc_scr_fw_enable;

	global $esplayer_acc_scr_fw_amount;

	global $esplayer_acc_scr_fw_unit;

	global $esplayer_acc_scr_fw_msg;

	global $esplayer_acc_scr_rew_enable;

	global $esplayer_acc_scr_rew_amount;

	global $esplayer_acc_scr_rew_unit;

	global $esplayer_acc_scr_rew_msg;

	global $esplayer_acc_scr_ffw_enable;

	global $esplayer_acc_scr_ffw_amount;

	global $esplayer_acc_scr_ffw_unit;

	global $esplayer_acc_scr_ffw_msg;

	global $esplayer_acc_scr_frew_enable;

	global $esplayer_acc_scr_frew_amount;

	global $esplayer_acc_scr_frew_unit;

	global $esplayer_acc_scr_frew_msg;

	global $esplayer_acc_scr_lnk_enable;

	global $esplayer_acc_scr_lnk_msg;



	do_shortcode($content);

	$url = "";

	$img_id = "";

	$timetable_id="";

	$width="";

	$height="";

	$bgcolor="#ffffff" ;

	$shadow_color="";

	$shadow_size="-999";

	$corner_size="-999";

	$smartphone_size="-999";

	$vp="0";

	$border_box="";

	$border_img="0";

	$esplayer_mode="0";

	$loop="false";

	$autoplay="false";	

	$duration="";

	$acc_basic_btns="";

	$acc_fwd_btn="";

	$acc_rwd_btn="";

	$acc_ffwd_btn="";

	$acc_frwd_btn="";

	$acc_scr_enable="";

	$smartphonesize=-999;

	$title="";





	extract($atts);

	if (substr($vp,0,1)=="-") {

		$vp = substr($vp,1);

	} else {

		$vp = "-".$vp;

	}



	if ($width=="") $width=$height;

	if ($height=="") $height=$width;

	if ($height=="") {$height=27; $width=27;}

	if (esplayer_is_mobile()) {

		if ($smartphonesize==-999) {

			$smartphonesize = get_option("esaudioplayer_smartphonesize", "100");

		}

		$width *= $smartphonesize/100;

		$height *= $smartphonesize/100;

	}

	

	if (is_numeric($width)) $width = $width . "px";

	if (is_numeric($height)) $height = $height . "px";

	if (is_numeric($vp)) $vp = $vp . "px";



	$id = "esplayer_" . (string)($player_number);



	$acc_scr_enable = $esplayer_acc_scr_enable;



	if ($acc_basic_btns=="") $acc_basic_btns = $esplayer_acc_scr_basic_btns; else $acc_scr_enable="1";

	if ($acc_fwd_btn=="") $acc_fwd_btn = $esplayer_acc_scr_fw_enable;

	if ($acc_rew_btn=="") $acc_rew_btn = $esplayer_acc_scr_rew_enable;

	if ($acc_ffwd_btn=="") $acc_ffwd_btn = $esplayer_acc_scr_ffw_enable;

	if ($acc_frew_btn=="") $acc_frew_btn = $esplayer_acc_scr_frew_enable;



	$width_cv = "";

	$height_cv = "";

	EsAudioPlayer_CalculateSize($width, $height, $shadow_size, &$width_cv, &$height_cv);

	//$width_cv = "5";

	//$height_cv = "5";



	if ($img_id == "" && $timetable_id == "") {

		$esplayer_mode="simple";

		$ret = "<div style=\"display:inline;position:relative;border:solid 0px #f00;\" id=\"" . $id . "_tmpspan\"><canvas id=\"" . $id . "\" style=\"cursor:pointer;width:$width_cv; height:$height_cv;\" width=\"$width_cv\" height=\"$height_cv\"></canvas></div>";

	} else if ($timetable_id != "") {

		$esplayer_mode="slideshow";

		$ret = "<div id=\"" . $id . "_tmpspan\" style=\"width:".$width."; height:".$height."; background-color:".$bgcolor."; border:".$border_box.";\">&nbsp;</div>";

		$url = "aa.mp3";

	} else {

		$esplayer_mode="imgclick";

		$ret = "";

	}

	if ($esplayer_acc_text_enable == "1") {

		$ret .= "<div style=\"display:none;\"><a href=\"" .$url. "\">" . $esplayer_acc_msg_download . "</a></div>";

	}

	if ($acc_scr_enable == "1") {

		$js_var_a = "Array_EsAudioPlayer[".($player_number-1)."]";

		$ret .= "<div style=\"position:absolute;left:-3000px;\">";

		if ($esplayer_acc_scr_lnk_enable=="1") {

			$ret .= "<a href=\"#\" onclick=\"".$js_var_a.".func_acc_play();return -1;\">".str_replace("%title%",$title,$esplayer_acc_scr_lnk_msg)."</a>" ;

		}

		if ($acc_basic_btns == "playstop") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_msg_playstop_btn) . "' onclick=\"".$js_var_a.".func_acc_play_stop();return -1;\"/>";

		}

		if ($acc_basic_btns == "play+stop") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_msg_play_btn) . "' onclick=\"".$js_var_a.".func_acc_play();return -1;\"/>";

		}

		if ($acc_basic_btns == "playpause+stop") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_msg_playpause_btn) . "' onclick=\"".$js_var_a.".func_acc_play_pause();return -1;\"/>";

		}

		if ($acc_basic_btns == "play+stop" || $acc_basic_btns == "playpause+stop") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_msg_stop_btn) . "' onclick=\"".$js_var_a.".func_acc_stop();return -1;\"/>";

		}

		if ($acc_fwd_btn=="1") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_fw_msg) . "' onclick=\"".$js_var_a.".func_acc_seek(".$esplayer_acc_scr_fw_amount.",'".$esplayer_acc_scr_fw_unit."');return -1;\"/>";

		}

		if ($acc_rew_btn=="1") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_rew_msg) . "' onclick=\"".$js_var_a.".func_acc_seek(-".$esplayer_acc_scr_rew_amount.",'".$esplayer_acc_scr_rew_unit."');return -1;\"/>";

		}

		if ($acc_ffwd_btn=="1") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_ffw_msg) . "' onclick=\"".$js_var_a.".func_acc_seek(".$esplayer_acc_scr_ffw_amount.",'".$esplayer_acc_scr_ffw_unit."');return -1;\"/>";

		}

		if ($acc_frew_btn=="1") {

			$ret .= "<input type='button' title='" . str_replace("%title%",$title,$esplayer_acc_scr_frew_msg) . "' onclick=\"".$js_var_a.".func_acc_seek(-".$esplayer_acc_scr_frew_amount.",'".$esplayer_acc_scr_frew_unit."');return -1;\"/>";

		}



		$ret .= "</div>";



	}



	$title_utf8="";

	$artist_utf8="";



	$js_var='esplayervar' . (string)($player_number);	

	$esplayer_script_var .= "var " . $js_var . ";\n";



	if ($esplayer_mode=="simple") {

		//$esplayer_script = $esplayer_script . "ReplaceContainingCanvasPtag2div('".$id."_tmpspan');\n";

	}



	$ret .= "<input type=\"hidden\" id=\"".$js_var."\" value=\""

		. $esplayer_mode

		. '|' 

		. $id 

		. '|' 

		. ($esplayer_mode=="slideshow"?$timetable_id:$url) 

		. '|' 

		. $width

		. '|' 

		. $height

		. '|' 

		. $vp

		. '|'

		. $shadow_size

		. '|' 

		. $shadow_color

		. '|'

		. $corner_size

		. '|'

		. $smartphone_size

		. '|' 

		. $border_img 

		. '|' 

		. $loop

		. '|'

		. $autoplay

		. '|'

		. $duration 

		. '|' 

		. $img_id

		. '|' 

		. $artist_utf8 

		. '|' 

		. $title_utf8 

		. "\">\n";



	$player_number ++;

	return $ret;

}



add_shortcode('esplayer', 'EsAudioPlayer_shortcode',11);

add_filter('widget_text', 'do_shortcode');







include 'EsAudioPlayer_tt.php';





/*  <head>sectionに、player Javascriptを追加   */

add_action( 'wp_head', 'EsAudioPlayer_title_filter' );



function EsAudioPlayer_title_filter( $title ) {

	global $esAudioPlayer_plugin_URL;

	global $esplayer_mode;



	echo "<script type=\"text/javascript\">\n".

		"var esplayer_isAdmin=false; \n".

		"var esAudioPlayer_plugin_URL = '" . $esAudioPlayer_plugin_URL . "';\n".

		"var esp_tt_data_encoded='';\nvar esp_tt_data; \n".

		"var esplayer_basecolor_play = '".get_option("esaudioplayer_basecolor_play", "#dbdbdb")."';\n".

		"var esplayer_symbolcolor_play = '".get_option("esaudioplayer_symbolcolor_play", "#44cc00")."';\n".

		"var esplayer_basecolor_stop = '".get_option("esaudioplayer_basecolor_stop", "#dbdbdb")."';\n".

		"var esplayer_symbolcolor_stop = '".get_option("esaudioplayer_symbolcolor_stop", "#ff1505")."';\n".

		"var esplayer_basecolor_pause = '".get_option("esaudioplayer_basecolor_pause", "#dbdbdb")."';\n".

		"var esplayer_symbolcolor_pause = '".get_option("esaudioplayer_symbolcolor_pause", "#ff7d24")."';\n".

		"var esplayer_color_slider_line = '".get_option("esaudioplayer_slidercolor_line", "#999999")."';\n".

		"var esplayer_color_slider_knob = '".get_option("esaudioplayer_slidercolor_knob", "#292929")."';\n".

		"var esplayer_shadowsize = " .get_option("esaudioplayer_shadowsize", "0.25")  .";\n".

		"var esplayer_shadowcolor = '".get_option("esaudioplayer_shadowcolor", "#a9a9a9") ."';\n".

		"var esplayer_cornersize = " .get_option("esaudioplayer_cornersize", "18")  .";\n".

		"var esplayer_smartphonesize = " .get_option("esaudioplayer_smartphonesize", "100")  .";\n".

		"</script>\n";

	echo  "<!--[if lt IE 9]><script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/excanvas.js\"></script><![endif]-->\n";

	echo  "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/jquery.base64.min.js\"></script>\n";

	echo  "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/print_r.js\"></script>\n";

	echo  "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/binaryajax.js\"></script>\n";

	echo  "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/soundmanager2-jsmin.js\"></script>\n";

	echo  "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/esplayer_tes_min.js\"></script>\n";	

	echo  "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/esplayer_tt.js\"></script>\n";	

} 





// 設定メニューの追加

add_action('admin_menu', 'esaudioplayer_plugin_menu');

function esaudioplayer_plugin_menu()

{

	/*  設定画面の追加  */

	add_submenu_page('options-general.php', 'EsAudioPlayer Configuration', 'EsAudioPlayer', 'manage_options', 'esaudioplayer-submenu-handle', 'esaudioplayer_magic_function'); 

}



$esaudioplayer_col_ar[0] = '#esaudioplayer_basecolor_play';

$esaudioplayer_col_ar[1] = '#esaudioplayer_symbolcolor_play';

$esaudioplayer_col_ar[2] = '#esaudioplayer_basecolor_stop';

$esaudioplayer_col_ar[3] = '#esaudioplayer_symbolcolor_stop';

$esaudioplayer_col_ar[4] = '#esaudioplayer_basecolor_pause';

$esaudioplayer_col_ar[5] = '#esaudioplayer_symbolcolor_pause';

$esaudioplayer_col_ar[6] = '#esaudioplayer_slidercolor_line';

$esaudioplayer_col_ar[7] = '#esaudioplayer_slidercolor_knob';

$esaudioplayer_col_ar[8] = '#esaudioplayer_shadowcolor';





function esaudioplayer_farbtastic_prepare($ar)

{

	$scr = "";

	for ($i=0; $i<count($ar); $i++) {

		$id = 'colorpicker'.$i;

		echo "<div id=\"" . $id . "\" style=\"position:absolute;\"></div>";

		$scr .= "			jQuery('#".$id."').farbtastic('".$ar[$i]."').hide();\n";

		$scr .= "			SetPosition('".$ar[$i]."','#".$id."');\n";

		$scr .= "			jQuery('".$ar[$i]."').focus(function(){jQuery('#".$id."').show();});\n";

		$scr .= "			jQuery('".$ar[$i]."').blur(function(){jQuery('#".$id."').hide();});\n";

	}

	echo 	"	<script type=\"text/javascript\">\n		jQuery(document).ready(function(){\n" . $scr . "		});\n".

		"		function SetPosition(el, cl)\n".

		"		{\n".

		"			var left = 0;\n".

		"			var top = 0;\n".

		"			left = jQuery(el).offset().left + jQuery(el).width()*1.2;\n".

		"			top = jQuery(el).offset().top -jQuery(cl).height()/2;\n".

		"			var height = jQuery(el).height();\n".

		"			if (!isNaN(parseInt(jQuery(el).css('padding-top')))) height += parseInt(jQuery(el).css('padding-top'));\n".

		"			if (!isNaN(parseInt(jQuery(el).css('margin-top')))) height += parseInt(jQuery(el).css('margin-top'));\n".

		"			var y = Math.floor(top) + height;\n".

		"			var x = Math.floor(left);\n".

		"			jQuery(cl).css('top',y+\"px\");\n".

		"			jQuery(cl).css('left',x+\"px\");\n".

		"		}\n".

		"	</script>\n";

}



/* setting screen */

function esaudioplayer_magic_function()

{

	global $esplayer_acc_text_enable;

	global $esplayer_acc_msg_download;

	global $esplayer_acc_scr_enable;

	global $esplayer_acc_scr_basic_btns;

	global $esplayer_acc_scr_msg_play_btn;

	global $esplayer_acc_scr_msg_stop_btn;

	global $esplayer_acc_scr_msg_playstop_btn;

	global $esplayer_acc_scr_msg_playpause_btn;

	global $esplayer_acc_scr_fw_enable;

	global $esplayer_acc_scr_fw_amount;

	global $esplayer_acc_scr_fw_unit;

	global $esplayer_acc_scr_fw_msg;

	global $esplayer_acc_scr_rew_enable;

	global $esplayer_acc_scr_rew_amount;

	global $esplayer_acc_scr_rew_unit;

	global $esplayer_acc_scr_rew_msg;

	global $esplayer_acc_scr_ffw_enable;

	global $esplayer_acc_scr_ffw_amount;

	global $esplayer_acc_scr_ffw_unit;

	global $esplayer_acc_scr_ffw_msg;

	global $esplayer_acc_scr_frew_enable;

	global $esplayer_acc_scr_frew_amount;

	global $esplayer_acc_scr_frew_unit;

	global $esplayer_acc_scr_frew_msg;

	global $esplayer_acc_scr_lnk_enable;

	global $esplayer_acc_scr_lnk_msg;



	/*  Save Changeボタン押下でコールされた場合、E_POSTに格納された設定情報を保?E */

	if ( isset($_POST['updateEsAudioPlayerSetting'] ) ) {

		echo '<div id="message" class="updated fade"><p><strong>Options saved.</strong></p></div>';

		update_option('esaudioplayer_basecolor_play', $_POST['esaudioplayer_basecolor_play']);

		update_option('esaudioplayer_symbolcolor_play', $_POST['esaudioplayer_symbolcolor_play']);

		update_option('esaudioplayer_basecolor_stop', $_POST['esaudioplayer_basecolor_stop']);

		update_option('esaudioplayer_symbolcolor_stop', $_POST['esaudioplayer_symbolcolor_stop']);

		update_option('esaudioplayer_basecolor_pause', $_POST['esaudioplayer_basecolor_pause']);

		update_option('esaudioplayer_symbolcolor_pause', $_POST['esaudioplayer_symbolcolor_pause']);

		update_option('esaudioplayer_slidercolor_line', $_POST['esaudioplayer_slidercolor_line']);

		update_option('esaudioplayer_slidercolor_knob', $_POST['esaudioplayer_slidercolor_knob']);

		update_option('esaudioplayer_shadowcolor', $_POST['esaudioplayer_shadowcolor']);

		update_option('esaudioplayer_shadowsize', $_POST['esaudioplayer_shadowsize']);

		update_option('esaudioplayer_cornersize', $_POST['esaudioplayer_cornersize']);

		update_option('esaudioplayer_smartphonesize', $_POST['esaudioplayer_smartphonesize']);



		update_option('esaudioplayer_acc_text_enable', $_POST['esaudioplayer_acc_text_enable']);

		update_option('esaudioplayer_acc_text_enable', $_POST['esaudioplayer_acc_text_enable']);

		update_option('esaudioplayer_acc_msg_download', $_POST['esaudioplayer_acc_msg_download']);

		update_option('esaudioplayer_acc_scr_enable', $_POST['esaudioplayer_acc_scr_enable']);

		update_option('esaudioplayer_acc_scr_basic_btns', $_POST['esaudioplayer_acc_scr_basic_btns']);

		update_option('esaudioplayer_acc_scr_msg_play_btn', $_POST['esaudioplayer_acc_scr_msg_play_btn']);

		update_option('esaudioplayer_acc_scr_msg_stop_btn', $_POST['esaudioplayer_acc_scr_msg_stop_btn']);

		update_option('esaudioplayer_acc_scr_msg_playstop_btn', $_POST['esaudioplayer_acc_scr_msg_playstop_btn']);

		update_option('esaudioplayer_acc_scr_msg_playpause_btn', $_POST['esaudioplayer_acc_scr_msg_playpause_btn']);



		update_option('esaudioplayer_acc_scr_fw_enable', isset($_POST['esaudioplayer_acc_scr_fw_enable'])?"1":"0");

		update_option('esaudioplayer_acc_scr_fw_amount', $_POST['esaudioplayer_acc_scr_fw_amount']);

		update_option('esaudioplayer_acc_scr_fw_unit', $_POST['esaudioplayer_acc_scr_fw_unit']);

		update_option('esaudioplayer_acc_scr_fw_msg', $_POST['esaudioplayer_acc_scr_fw_msg']);

		update_option('esaudioplayer_acc_scr_rew_enable', isset($_POST['esaudioplayer_acc_scr_rew_enable'])?"1":"0");

		update_option('esaudioplayer_acc_scr_rew_amount', $_POST['esaudioplayer_acc_scr_rew_amount']);

		update_option('esaudioplayer_acc_scr_rew_unit', $_POST['esaudioplayer_acc_scr_rew_unit']);

		update_option('esaudioplayer_acc_scr_rew_msg', $_POST['esaudioplayer_acc_scr_rew_msg']);

		update_option('esaudioplayer_acc_scr_ffw_enable', isset($_POST['esaudioplayer_acc_scr_ffw_enable'])?"1":"0");

		update_option('esaudioplayer_acc_scr_ffw_amount', $_POST['esaudioplayer_acc_scr_ffw_amount']);

		update_option('esaudioplayer_acc_scr_ffw_unit', $_POST['esaudioplayer_acc_scr_ffw_unit']);

		update_option('esaudioplayer_acc_scr_ffw_msg', $_POST['esaudioplayer_acc_scr_ffw_msg']);

		update_option('esaudioplayer_acc_scr_frew_enable', isset($_POST['esaudioplayer_acc_scr_frew_enable'])?"1":"0");

		update_option('esaudioplayer_acc_scr_frew_amount', $_POST['esaudioplayer_acc_scr_frew_amount']);

		update_option('esaudioplayer_acc_scr_frew_unit', $_POST['esaudioplayer_acc_scr_frew_unit']);

		update_option('esaudioplayer_acc_scr_frew_msg', $_POST['esaudioplayer_acc_scr_frew_msg']);

		update_option('esaudioplayer_acc_scr_lnk_enable', isset($_POST['esaudioplayer_acc_scr_lnk_enable'])?"1":"0");

		update_option('esaudioplayer_acc_scr_lnk_msg', $_POST['esaudioplayer_acc_scr_lnk_msg']);

	}



	global $esaudioplayer_col_ar;

	esaudioplayer_farbtastic_prepare($esaudioplayer_col_ar);



	$plugin = plugin_basename('EsAudioPlayer'); $plugin = dirname(__FILE__);

	?>



	<div id="colorpicker1" style="position:absolute"></div>

	<div id="colorpicker2" style="position:absolute"></div>

	<div id="colorpicker3" style="position:absolute"></div>

	<div id="colorpicker4" style="position:absolute"></div>

	<div id="colorpicker5" style="position:absolute"></div>

	<div id="colorpicker6" style="position:absolute"></div>

	<div id="colorpicker7" style="position:absolute"></div>

	<div id="colorpicker8" style="position:absolute"></div>

	<div id="colorpicker9" style="position:absolute"></div>



	<div class="wrap">

		<h2>EsAudioPlayer configuration</h2>



		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">



		<?php

		wp_nonce_field('update-options');  

		$basecolor_play = get_option("esaudioplayer_basecolor_play", "#dbdbdb"); 

		$symbolcolor_play = get_option("esaudioplayer_symbolcolor_play", "#44cc00"); 

		$basecolor_stop = get_option("esaudioplayer_basecolor_stop", "#dbdbdb"); 

		$symbolcolor_stop = get_option("esaudioplayer_symbolcolor_stop", "#ff1505"); 

		$basecolor_pause = get_option("esaudioplayer_basecolor_pause", "#dbdbdb"); 

		$symbolcolor_pause = get_option("esaudioplayer_symbolcolor_pause", "#ff7d24"); 

		$slidercolor_line = get_option("esaudioplayer_slidercolor_line", "#999999"); 

		$slidercolor_knob = get_option("esaudioplayer_slidercolor_knob", "#292929"); 

		$shadowcolor = get_option("esaudioplayer_shadowcolor", "#a9a9a9"); 

		$shadowsize = get_option("esaudioplayer_shadowsize", "0.25"); 

		$cornersize = get_option("esaudioplayer_cornersize", "18"); 

		$smartphonesize = get_option("esaudioplayer_smartphonesize", "100"); 



		EsAudioPlayer_read_accessibility_setting(); 

		$acc_text_enable = $esplayer_acc_text_enable; 

		$acc_msg_download = $esplayer_acc_msg_download; 

		$acc_scr_enable = $esplayer_acc_scr_enable; 

		$acc_scr_basic_btns = $esplayer_acc_scr_basic_btns; 

		$acc_scr_msg_play_btn = $esplayer_acc_scr_msg_play_btn; 

		$acc_scr_msg_stop_btn = $esplayer_acc_scr_msg_stop_btn; 

		$acc_scr_msg_playstop_btn = $esplayer_acc_scr_msg_playstop_btn; 

		$acc_scr_msg_playpause_btn = $esplayer_acc_scr_msg_playpause_btn;

		$acc_scr_fw_enable = $esplayer_acc_scr_fw_enable;

		$acc_scr_fw_amount = $esplayer_acc_scr_fw_amount;

		$acc_scr_fw_unit = $esplayer_acc_scr_fw_unit;

		$acc_scr_fw_msg = $esplayer_acc_scr_fw_msg;

		$acc_scr_rew_enable = $esplayer_acc_scr_rew_enable;

		$acc_scr_rew_amount = $esplayer_acc_scr_rew_amount;

		$acc_scr_rew_unit = $esplayer_acc_scr_rew_unit;

		$acc_scr_rew_msg = $esplayer_acc_scr_rew_msg;

		$acc_scr_ffw_enable = $esplayer_acc_scr_ffw_enable;

		$acc_scr_ffw_amount = $esplayer_acc_scr_ffw_amount;

		$acc_scr_ffw_unit = $esplayer_acc_scr_ffw_unit;

		$acc_scr_ffw_msg = $esplayer_acc_scr_ffw_msg;

		$acc_scr_frew_enable = $esplayer_acc_scr_frew_enable;

		$acc_scr_frew_amount = $esplayer_acc_scr_frew_amount;

		$acc_scr_frew_unit = $esplayer_acc_scr_frew_unit;

		$acc_scr_frew_msg = $esplayer_acc_scr_frew_msg;

		$acc_scr_lnk_enable = $esplayer_acc_scr_lnk_enable;

		$acc_scr_lnk_msg = $esplayer_acc_scr_lnk_msg;

 		?>



		<h3>Color Settings</h3>



		<table class="form-table">

		<tr>

		<th scope="row" style="text-align:right;">Base Color (Play)</th>

		<td> <input type="text" id="esaudioplayer_basecolor_play" name="esaudioplayer_basecolor_play" value="<?php echo $basecolor_play; ?>" /></td>

		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Symbol Color (Play)</th>

		<td> <input type="text" id="esaudioplayer_symbolcolor_play" name="esaudioplayer_symbolcolor_play" value="<?php echo $symbolcolor_play; ?>" /></td>



		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Base Color (Stop)</th>

		<td> <input type="text" id="esaudioplayer_basecolor_stop" name="esaudioplayer_basecolor_stop" value="<?php echo $basecolor_stop; ?>" /></td>

		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Symbol Color (Stop)</th>

		<td> <input type="text" id="esaudioplayer_symbolcolor_stop" name="esaudioplayer_symbolcolor_stop" value="<?php echo $symbolcolor_stop; ?>" /></td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Base Color (Pause)</th>

		<td> <input type="text" id="esaudioplayer_basecolor_pause" name="esaudioplayer_basecolor_pause" value="<?php echo $basecolor_pause; ?>" /></td>

		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Symbol Color (Pause)</th>

		<td> <input type="text" id="esaudioplayer_symbolcolor_pause" name="esaudioplayer_symbolcolor_pause" value="<?php echo $symbolcolor_pause; ?>" /></td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Slider Color (line)</th>

		<td> <input type="text" id="esaudioplayer_slidercolor_line" name="esaudioplayer_slidercolor_line" value="<?php echo $slidercolor_line; ?>" /></td>

		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Slider Color (knob)</th>

		<td> <input type="text" id="esaudioplayer_slidercolor_knob" name="esaudioplayer_slidercolor_knob" value="<?php echo $slidercolor_knob; ?>" /></td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Shadow Size</th>

		<td><input type="text" id="esaudioplayer_shadowsize" name="esaudioplayer_shadowsize" value="<?php echo $shadowsize; ?>" /></td>

		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Shadow Color</th>

		<td><input type="text" id="esaudioplayer_shadowcolor" name="esaudioplayer_shadowcolor" value="<?php echo $shadowcolor; ?>" /></td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Corner radius (% of shorter side)</th>

		<td><input type="text" id="esaudioplayer_cornersize" name="esaudioplayer_cornersize" value="<?php echo $cornersize; ?>" /></td>

		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Smartphone size (% of normal size)</th>

		<td><input type="text" id="esaudioplayer_smartphonesize" name="esaudioplayer_smartphonesize" value="<?php echo $smartphonesize; ?>" /></td>

		</tr>

		</table>



<br/>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Preview)<br>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<div id="esplayer_1_tmpspan" style="display:inline;"><canvas id="esplayer_1" style="cursor:pointer;" width="60" height="60"></canvas></div>

<div id="esplayer_2_tmpspan" style="display:inline;"><canvas id="esplayer_2" style="cursor:pointer;" width="60" height="60"></canvas></div>

<div id="esplayer_3_tmpspan" style="display:inline;"><canvas id="esplayer_3" style="cursor:pointer;" width="120" height="60"></canvas></div>

<div id="esplayer_4_tmpspan" style="display:inline;"><canvas id="esplayer_4" style="cursor:pointer;" width="120" height="60"></canvas></div>

<script type="text/javascript">



var esplayer_basecolor_play;

var esplayer_symbolcolor_play;

var esplayer_basecolor_stop;

var esplayer_symbolcolor_stop;

var esplayer_basecolor_pause;

var esplayer_symbolcolor_pause;

var esplayer_color_slider_line;

var esplayer_color_slider_knob;

var esplayer_shadowsize;

var esplayer_shadowcolor;

var esplayer_cornersize;

var esplayer_smartphonesize;



function esplayer_reflect_setting()

{

	esplayer_basecolor_play = jQuery('#esaudioplayer_basecolor_play').val();

	esplayer_symbolcolor_play = jQuery('#esaudioplayer_symbolcolor_play').val();

	esplayer_basecolor_stop = jQuery('#esaudioplayer_basecolor_stop').val();

	esplayer_symbolcolor_stop = jQuery('#esaudioplayer_symbolcolor_stop').val();

	esplayer_basecolor_pause = jQuery('#esaudioplayer_basecolor_pause').val();

	esplayer_symbolcolor_pause = jQuery('#esaudioplayer_symbolcolor_pause').val();

	esplayer_color_slider_line = jQuery('#esaudioplayer_slidercolor_line').val();

	esplayer_color_slider_knob = jQuery('#esaudioplayer_slidercolor_knob').val();

	esplayer_shadowsize = jQuery('#esaudioplayer_shadowsize').val();

	esplayer_shadowcolor = jQuery('#esaudioplayer_shadowcolor').val();

	esplayer_cornersize = jQuery('#esaudioplayer_cornersize').val();

	esplayer_smartphonesize = jQuery('#esaudioplayer_smartphonesize').val();

}

esplayer_reflect_setting();



var esplayervar1;

jQuery(document).ready(function() {

esplayervar1 = new EsAudioPlayer("simple", "esplayer_1", "http://tempspace.net/hu7/wp-content/uploads/mus/a_nys_2fwksong01.mp3", "25px", "25px", "-0px", 1, "#888",-999,-999, "0", false, "1:18", "", "", ""); });

var esplayervar2;

jQuery(document).ready(function() {

esplayervar2 = new EsAudioPlayer("simple", "esplayer_2", "http://tempspace.net/hu7/wp-content/uploads/mus/a_nys_2fwksong01.mp3", "25px", "25px", "-0px", 1, "#888",-999,-999, "0", false, "1:18", "", "", ""); });

var esplayervar3;

jQuery(document).ready(function() {

esplayervar3 = new EsAudioPlayer("simple", "esplayer_3", "http://tempspace.net/hu7/wp-content/uploads/mus/a_nys_2fwksong01.mp3", "90px", "25px", "-0px", 1, "#888",-999,-999, "0", false, "1:18", "", "", ""); });

var esplayervar4;

jQuery(document).ready(function() {

esplayervar4 = new EsAudioPlayer("simple", "esplayer_4", "http://tempspace.net/hu7/wp-content/uploads/mus/a_nys_2fwksong01.mp3", "90px", "25px", "-0px", 1, "#888",-999,-999, "0", false, "1:18", "", "", ""); });



function esplayer_preview_update()

{

	esplayer_reflect_setting();	

	esplayervar2.func_acc_play();esplayervar4.func_acc_play();

	esplayervar1.getSetting(true);

	esplayervar2.getSetting(true);

	esplayervar3.getSetting(true);

	esplayervar4.getSetting(true);

}

setTimeout('setInterval("esplayer_preview_update()",100);',1000);

</script>





		<h3>Accessibility Settings</h3>



		<h4>For Text-based Browsers</h4>



		<table class="form-table">

		<tr>

		<th scope="row" style="text-align:right;">Status</th>

		<td>

		<input type="radio" name="esaudioplayer_acc_text_enable" value="1" <?php echo $acc_text_enable=="1"?"checked ":""; ?>/>Enabled<br/>

		<input type="radio" name="esaudioplayer_acc_text_enable" value="0" <?php echo $acc_text_enable=="0"?"checked ":""; ?>/>Disabled

		</td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Download link speech</th>

		<td><input type="text" id="esaudioplayer_acc_msg_download" name="esaudioplayer_acc_msg_download" value="<?php echo $acc_msg_download; ?>" /></td>

		</tr>		</table>









		<h4>For Screen Readers</h4>		



		<table class="form-table">

		<tr>

		<th scope="row" style="text-align:right;">Status</th>

		<td>

		<input type="radio" name="esaudioplayer_acc_scr_enable" value="1" <?php echo $acc_scr_enable=="1"?"checked":""; ?>>Enabled<br/>

		<input type="radio" name="esaudioplayer_acc_scr_enable" value="0" <?php echo $acc_scr_enable=="0"?"checked":""; ?>>Disabled

		</td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Basic buttons</th>

		<td>

		<input type="radio" name="esaudioplayer_acc_scr_basic_btns" value="playstop" <?php echo $acc_scr_basic_btns=="playstop"?"checked":""; ?>>[Play/Stop]<br/>

		<input type="radio" name="esaudioplayer_acc_scr_basic_btns" value="play+stop" <?php echo $acc_scr_basic_btns=="play+stop"?"checked":""; ?>>[Play] + [Stop]<br/>

		<input type="radio" name="esaudioplayer_acc_scr_basic_btns" value="playpause+stop" <?php echo $acc_scr_basic_btns=="playpause+stop"?"checked":""; ?>>[Play/Pause] + [Stop]<br/>

		</td>

		</tr>

		<tr>

		<th scope="row" style="text-align:right;">Play button speech</th>

		<td><input type="text" name="esaudioplayer_acc_scr_msg_play_btn" value="<?php echo $acc_scr_msg_play_btn; ?>" /></td>

		</tr>		<tr>

		<th scope="row" style="text-align:right;">Stop button speech</th>

		<td><input type="text" name="esaudioplayer_acc_scr_msg_stop_btn" value="<?php echo $acc_scr_msg_stop_btn; ?>" /></td>

		</tr>		<tr>

		<th scope="row" style="text-align:right;">Play/Stop button speech</th>

		<td><input type="text" name="esaudioplayer_acc_scr_msg_playstop_btn" value="<?php echo $acc_scr_msg_playstop_btn; ?>" /></td>

		</tr>		<tr>

		<th scope="row" style="text-align:right;">Play/Pause button speech</th>

		<td><input type="text" name="esaudioplayer_acc_scr_msg_playpause_btn" value="<?php echo $acc_scr_msg_playpause_btn; ?>" /></td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Forward Button</th>

		<td><input type="checkbox" name="esaudioplayer_acc_scr_fw_enable" value="1" <?php echo $acc_scr_fw_enable=="1"?"checked":""; ?> />Enable<br/>

		Amount <input type="text" name="esaudioplayer_acc_scr_fw_amount" value="<?php echo $acc_scr_fw_amount; ?>" />

		<input type="radio" name="esaudioplayer_acc_scr_fw_unit" value="sec" <?php echo $acc_scr_fw_unit=="sec"?"checked":""; ?>>sec.

		<input type="radio" name="esaudioplayer_acc_scr_fw_unit" value="pct" <?php echo $acc_scr_fw_unit=="pct"?"checked":""; ?>>%<br/>

		Speech <input type="text" name="esaudioplayer_acc_scr_fw_msg" value="<?php echo $acc_scr_fw_msg; ?>" />

		</td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Rewind Button</th>

		<td><input type="checkbox" name="esaudioplayer_acc_scr_rew_enable" value="1" <?php echo $acc_scr_rew_enable=="1"?"checked":""; ?> />Enable<br/>

		Amount <input type="text" name="esaudioplayer_acc_scr_rew_amount" value="<?php echo $acc_scr_rew_amount; ?>" />

		<input type="radio" name="esaudioplayer_acc_scr_rew_unit" value="sec" <?php echo $acc_scr_rew_unit=="sec"?"checked":""; ?>>sec.

		<input type="radio" name="esaudioplayer_acc_scr_rew_unit" value="pct" <?php echo $acc_scr_rew_unit=="pct"?"checked":""; ?>>%<br/>

		Speech <input type="text" name="esaudioplayer_acc_scr_rew_msg" value="<?php echo $acc_scr_rew_msg; ?>" />

		</td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Fast Forward Button</th>

		<td><input type="checkbox" name="esaudioplayer_acc_scr_ffw_enable" value="1" <?php echo $acc_scr_ffw_enable=="1"?"checked":""; ?> />Enable<br/>

		Amount <input type="text" name="esaudioplayer_acc_scr_ffw_amount" value="<?php echo $acc_scr_ffw_amount; ?>" />

		<input type="radio" name="esaudioplayer_acc_scr_ffw_unit" value="sec" <?php echo $acc_scr_ffw_unit=="sec"?"checked":""; ?>>sec.

		<input type="radio" name="esaudioplayer_acc_scr_ffw_unit" value="pct" <?php echo $acc_scr_ffw_unit=="pct"?"checked":""; ?>>%<br/>

		Speech <input type="text" name="esaudioplayer_acc_scr_ffw_msg" value="<?php echo $acc_scr_ffw_msg; ?>" />

		</td>

		</tr>



		<tr>

		<th scope="row" style="text-align:right;">Fast Rewind Button</th>

		<td><input type="checkbox" name="esaudioplayer_acc_scr_frew_enable" value="1" <?php echo $acc_scr_frew_enable=="1"?"checked":""; ?> />Enable<br/>

		Amount <input type="text" name="esaudioplayer_acc_scr_frew_amount" value="<?php echo $acc_scr_frew_amount; ?>" />

		<input type="radio" name="esaudioplayer_acc_scr_frew_unit" value="sec" <?php echo $acc_scr_frew_unit=="sec"?"checked":""; ?>>sec.

		<input type="radio" name="esaudioplayer_acc_scr_frew_unit" value="pct" <?php echo $acc_scr_frew_unit=="pct"?"checked":""; ?>>%<br/>

		Speech <input type="text" name="esaudioplayer_acc_scr_frew_msg" value="<?php echo $acc_scr_frew_msg; ?>" />

		</td>

		</tr>



<!--

		<tr>

		<th scope="row" style="text-align:right;">Embed link to be listed by screen readers</th>

		<td><input type="checkbox" name="esaudioplayer_acc_scr_lnk_enable" value="1" <?php echo $acc_scr_lnk_enable=="1"?"checked":""; ?> />Enable<br/>

		Speech <input type="text" name="esaudioplayer_acc_scr_lnk_msg" value="<?php echo $acc_scr_lnk_msg; ?>" />

		</td>

		</tr>

-->



		</table>





		<input type="hidden" name="action" value="update" />

		<input type="hidden" name="page_options" value="esaudioplayer_basecolor_play,esaudioplayer_symbolcolor_play,esaudioplayer_basecolor_stop,esaudioplayer_symbolcolor_stop" />

		<p class="submit">

			<input type="submit" name="updateEsAudioPlayerSetting" class="button-primary" value="<?php _e('Save  Changes')?>" onclick="" />

		</p>

		</form>





	</div>

	<?php 

	if ( isset($_POST['updateEsAudioPlayerSetting'] ) ) {

		//echo '<script type="text/javascript">alert("Options Saved.");</script>';

	}

}



function EsAudioPlayer_admin_head()

{

	global $esAudioPlayer_plugin_URL;

	echo  "<!--[if lt IE 9]><script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/excanvas.js\"></script><![endif]-->\n";

	echo "<link rel='stylesheet' href='". $esAudioPlayer_plugin_URL . "/mattfarina-farbtastic/farbtastic.css' type='text/css' media='all' />\n";

	echo "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/mattfarina-farbtastic/farbtastic.min.js\"></script>\n";

	echo "<script type=\"text/javascript\">var esplayer_isAdmin = true;</script>\n";

	echo "<script type=\"text/javascript\" src=\"" . $esAudioPlayer_plugin_URL . "/esplayer_tes_min.js\"></script>\n";

}

add_action( 'admin_head', 'EsAudioPlayer_admin_head'); 





/*  <footer>sectionに、player Javascriptを追加   */

add_action( 'wp_footer', 'EsAudioPlayer_footer_filter' );



function EsAudioPlayer_footer_filter( $title ) 

{

	global $esp_tt_data;

	$esp_tt_data_encoded = base64_encode ( json_encode($esp_tt_data) );

	echo "<script type=\"text/javascript\">\n";

	echo "esp_tt_data_encoded = \"" . $esp_tt_data_encoded . "\";\n";

	echo "</script>\n";

}



/*

memo

0. EsAudioPlayer_title_filter makes code of including scripts and declaration of variables, and a script of deleting p-tags enclosing canvas tags.

1. EsAudioPlayer_filter_tt (priority 9) reads time tables.

2. EsAudioPlayer_filter_0 (priority 10) deletes white spaces in the series of shortcords.

3. EsAudioPlayer_shortcode (priority 12) makes code of declaration of class instances of players.

4. (deleted)EsAudioPlayer_filter_pdel (priority 15) replaces <p></p> tags encloseing canvas tags to <div></div> so that IE (explorercanvas.js) can display canvases.

5. (deleted)EsAudioPlayer_filter (priority 15) makes markups for image-click-mode.

6. EsAudioPlayer_filter_2 (priority 99) makes code of declaration of class instances of players at the end of the article.

7. EsAudioPlayer_footer_filter makes code of obtaining mime64-encoded time-table JSON data

*/

?>

