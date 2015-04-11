<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	autotag из MaxSite CMS
	
	Делает замену всех абзацев и авторасстановку тэгов.
	
	Подключение в variables.php:
		$VAR['autotag_my'] = 'maxsite';
	
*/

function autotag_maxsite($pee)
{
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee);
	
	# если html-код в [html_r] код [/html_r]
	# в отличие от [html] — отдаёт полностью исходный html без обработок 
	$pee = str_replace('<p>[html_r]</p>', '[html_r]', $pee);
	$pee = str_replace('<p>[/html_r]</p>', '[/html_r]', $pee);
	$pee = preg_replace_callback('!\[html_r\](.*?)\[\/html_r\]!is', '_clean_html_r_do', $pee );
	
	# если html в [html] код [/html]
	$pee = str_replace('<p>[html]</p>', '[html]', $pee);
	$pee = str_replace('<p>[/html]</p>', '[/html]', $pee);
	$pee = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', '_clean_html_do', $pee );

	# преформатированный текст
	$pee = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', 'mso_clean_pre_do', $pee );
	
	$pee = str_replace('<br />', '<br>', $pee);
	$pee = str_replace('<br/>', '<br>', $pee);
	
	$pee = str_replace("\n\t", "\n", $pee); 
	
	# ставим абзацы
	$pee = preg_replace('!(.*)\n!', "\n<p>$1</p>", $pee);
	
	# исправим абзацы ошибочные
	$pee = str_replace("<p></p>", "", $pee); 
	$pee = str_replace("<p> </p>", "", $pee); 
	$pee = str_replace("<p><p>", "<p>", $pee); 
	$pee = str_replace("</p></p>", "</p>", $pee); 
	$pee = str_replace("</script></p>", "</script>", $pee); 
	$pee = str_replace("<p>	<div", "<div", $pee); 
	$pee = preg_replace('!<p>(\s)+<!', "<p><", $pee); # <p>   <li
	
	# блочные тэги
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|center|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|code|select|form|map|area|blockquote|address|math|style|input|embed|h1|h2|h3|h4|h5|h6|hr|p|hgroup|section|header|footer|article|aside|nav|main)';
	
	# здесь не нужно ставить <p> и </p>
	$pee = preg_replace('!<p>(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $pee); # <p><tag></p>
	$pee = preg_replace('!<p>(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p><tag> 
	$pee = preg_replace('!<p>(</' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p></tag> 
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $pee); # <tag></p>
	$pee = preg_replace('!(</' . $allblocks . '>)</p>!', "$1", $pee); # </tag></p>
	$pee = preg_replace('!(</' . $allblocks . '>) </p>!', "$1", $pee); # </tag></p>
	
	$pee = preg_replace('!<p>&nbsp;&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p>&nbsp;&nbsp;<tag> 
	$pee = preg_replace('!<p>&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p>&nbsp;<tag> 
	
	# специфичные ошибки
	$pee = str_replace("<blockquote>\n<p>", "<blockquote>", $pee); 
	$pee = preg_replace('!<li>(.*)</p>\n!', "<li>$1</li>\n", $pee); # <li>...</p>
	$pee = str_replace("</li>\n\n<li>", "</li>\n<li>", $pee);
	$pee = str_replace("<p>	<li", "<li", $pee);
	$pee = str_replace("<p></a></p>", "</a>", $pee);
	
	$pee = preg_replace('!<p><a id="(.*)"></a></p>\n!', "<a id=\"$1\"></a>\n", $pee);
	$pee = preg_replace('!<p><a class="(.*)"></p>\n!', "<a class=\"$1\">\n", $pee);
	
	# еще раз подчистка
	$pee = preg_replace('!<p><br(.*)></p>!', "<br$1>", $pee);
	$pee = preg_replace('!<p><br></p>!', "<br>", $pee);
	$pee = str_replace("--></p>", "-->", $pee);
	
	$pee = str_replace("\n\n\n", "\n", $pee); 
	$pee = str_replace("\n\n", "\n", $pee); 

	# завершим [html]
	$pee = str_replace('<p>[html_base64]', '[html_base64]', $pee);
	$pee = str_replace('[/html_base64]</p>', '[/html_base64]', $pee);
	$pee = str_replace('[/html_base64] </p>', '[/html_base64]', $pee);
	
	$pee = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', '_clean_html_posle', $pee );
	
	# [br]
	$pee = str_replace('[br]', '<br style="clear:both">', $pee);
	$pee = str_replace('[br none]', '<br>', $pee);
	$pee = str_replace('[br left]', '<br style="clear:left">', $pee);
	$pee = str_replace('[br right]', '<br style="clear:right">', $pee);
	
	$pee = str_replace('<p><br></p>', '<br>', $pee);
	$pee = preg_replace('!<p><br(.*)></p>!', "<br$1>", $pee);

	# принудительный пробел
	$pee = str_replace('[nbsp]', '&nbsp;', $pee);
	
	# спецзамены
	$pee = str_replace('[BASEURL]', BASE_URL , $pee); // адрес сайта
	$pee = str_replace('[BASE_URL]', BASE_URL , $pee); // адрес сайта
	
	// pr($pee,0); 
	
	return trim($pee);
}

/**
*  предподготовка html в тексте между [html] ... [/html]
*  конвертируем все символы в реальный html
*  после этого кодируем его в одну строчку base64 [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function _clean_html_do($matches)
{
	$arr1 = array('&amp;', '&lt;', '&gt;', '<br />', '<br>', '&nbsp;');
	$arr2 = array('&',     '<',    '>',    "\n",     "\n",   ' ');
	
	$m = trim( str_replace($arr1, $arr2, $matches[1]) );
	$m = '[html_base64]' . base64_encode($m) . '[/html_base64]';

	return $m;
}

/**
*  аналогично _clean_html_do, только без замен — [html_r] ... [/html_r]
*  
*  @param $matches 
*  
*  @return string
*/
function _clean_html_r_do($matches)
{
	return '[html_base64]' . base64_encode($matches[1]) . '[/html_base64]';
}

/**
*  декодирование из [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function _clean_html_posle($matches)
{
	return base64_decode($matches[1]);
}

# end of file