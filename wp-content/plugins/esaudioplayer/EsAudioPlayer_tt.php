<?php



$esp_tt_data = array();



function EsAudioPlayer_filter_tt($raw_text) 

{

	$n = 0;

	global $esp_tt_data;

	$cur_pos = 0;

	$ret = "";

	for ($i=0; $i<9999; $i++) {

		$pos1 = mb_stripos($raw_text, "[esplayer_timetable]", $cur_pos);

		if ($pos1 === false) break;

		$pos2 = mb_stripos($raw_text, "[/esplayer_timetable]", $cur_pos);

		if ($pos2 === false) break;

		$ret = $ret . mb_substr($raw_text, $cur_pos, $pos1-$cur_pos);

		$content = mb_substr($raw_text, $pos1+20, $pos2-$pos1-20);

		$dat0 = EsAudioPlayer_filter_tt_sub($content);

		$esp_tt_data[count($esp_tt_data)]=$dat0;

		$cur_pos = $pos2 + 21;

	}

	$ret = $ret . mb_substr($raw_text, $cur_pos);

	return $ret;

}



add_filter('the_content',  "EsAudioPlayer_filter_tt", 9) ;



function EsAudioPlayer_filter_tt_sub($content)

{

	$dat->tnum = 0;

	//echo $content;

	$cur_pos = 0;

	$lex_pos = 0;

	$token="";



	$data_num = -1;

	$default_duration = 500;



	for ( $i=0 ; $i<9999 ; $i++ ) {

		$idt_code = esplayer_lex($content, $lex_pos, $token);

		if ($idt_code == LEX_EOL) {

			break;

		}

		$idt=$token;

		if ($idt != 'end') {

			$eq_code = esplayer_lex($content, $lex_pos, $token);

			$eq = $token;

			$str_code = esplayer_lex($content, $lex_pos, $token);

			$str=str_replace('"','',$token);

		}

		//$sc_code = esplayer_lex($content, $lex_pos, $token);

		//$sc = $token;

		//echo '['.$idt.'] - ['.$eq.'] - ['.$str.'] <br>';



		if ($idt=='default_img') {

			$data_num=0;

			$dat->time[$data_num] = 0;

			$dat->img[$data_num] = $str;

			$dat->width[$data_num] = 0;

			$dat->height[$data_num] = 0;

			$dat->duration[$data_num] = 100;

		}

		if ($idt=='default_duration') {

			$default_duration = $str;

		}

		if ($idt=='id') {

			$dat->id = $str;

		}

		if ($idt=='url') {

			$dat->url = $str;

		}

		if ($idt=='time') {

			$data_num ++;

			$dat->time[$data_num] = EsAudioPlayer_filter_tt_get_time($str);

			$dat->img[$data_num] = "";

			$dat->width[$data_num] = 0;

			$dat->height[$data_num] = 0;

			$dat->duration[$data_num] = $default_duration;

			$dat->misc[$data_num] = "";

		}

		if ($idt=='img') {

			$dat->img[$data_num] = $str;

		}

		if ($idt=='width') {

			$dat->width[$data_num] = $str;

		}

		if ($idt=='height') {

			$dat->height[$data_num] = $str;

		}

		if ($idt=='duration') {

			$dat->duration[$data_num] = $str;

		}

		if ($idt=='end') {

			$dat->misc[$data_num] = 'end';

		}

	}

	$data_num++;



	return $dat;

}



function EsAudioPlayer_filter_tt_get_time($str)

{

	$cln_pos = stripos($str,":");

	$com_pos = stripos($str,".");



	if ($cln_pos === false || $com_pos === false) {

		echo 'time format error: '.$str.'<br>';

		return -1;

	}



	$min = substr($str,0,$cln_pos);

	$sec = substr($str,$cln_pos+1, $com_pos-$cln_pos-1);

	$ff = substr($str, $com_pos);



	return ($min*60 + $sec + $ff)*1000;

}







?>

