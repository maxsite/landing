<?php 

// сжатие css/style.css

/*
require_once(ENGINE_DIR . 'additions/compres-css.php');
mso_compres_css_file(BASEPATH . '/css/style.css');
*/

function mso_compres_css_file($fn)
{

	if (file_exists($fn))
	{
		$out = file_get_contents($fn);

		$length1 = strlen($out);

		$out = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $out);

		$out = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $out);

		$out = str_replace('; ', ';', $out);
		$out = str_replace(';}', '}', $out);
		$out = str_replace(': ', ':', $out);
		$out = str_replace('{ ', '{', $out);
		$out = str_replace(' }', '}', $out);
		$out = str_replace(' {', '{', $out);
		$out = str_replace(', ', ',', $out);
		$out = str_replace(' > ', '>', $out);
		$out = str_replace('} ', '}', $out);
		$out = str_replace(' (', '(', $out);

		$out = str_replace('0.1em', '.1em', $out);
		$out = str_replace('0.2em', '.2em', $out);
		$out = str_replace('0.3em', '.3em', $out);
		$out = str_replace('0.4em', '.4em', $out);
		$out = str_replace('0.5em', '.5em', $out);
		$out = str_replace('0.6em', '.6em', $out);
		$out = str_replace('0.7em', '.7em', $out);
		$out = str_replace('0.8em', '.8em', $out);
		$out = str_replace('0.9em', '.9em', $out);
		$out = str_replace('1.0em', '1em', $out);

		$out = str_replace(' 0%', ' 0', $out);

		$out = str_replace('#000000', '#000', $out);
		$out = str_replace('#111111', '#111', $out);
		$out = str_replace('#222222', '#222', $out);
		$out = str_replace('#333333', '#333', $out);
		$out = str_replace('#444444', '#444', $out);
		$out = str_replace('#555555', '#555', $out);
		$out = str_replace('#666666', '#666', $out);
		$out = str_replace('#777777', '#777', $out);
		$out = str_replace('#888888', '#888', $out);
		$out = str_replace('#999999', '#999', $out);
		$out = str_replace('#aaaaaa', '#aaa', $out);
		$out = str_replace('#bbbbbb', '#bbb', $out);
		$out = str_replace('#cccccc', '#ccc', $out);
		$out = str_replace('#dddddd', '#ddd', $out);
		$out = str_replace('#eeeeee', '#eee', $out);
		$out = str_replace('#ffffff', '#fff', $out);
		$out = str_replace('#AAAAAA', '#aaa', $out);
		$out = str_replace('#BBBBBB', '#bbb', $out);
		$out = str_replace('#CCCCCC', '#ccc', $out);
		$out = str_replace('#DDDDDD', '#ddd', $out);
		$out = str_replace('#EEEEEE', '#eee', $out);
		$out = str_replace('#FFFFFF', '#fff', $out);

		$out = str_replace('#ff0000', '#f00', $out);
		$out = str_replace('#ffff00', '#ff0', $out);

		$out = str_replace(' !important', '!important', $out);
		$out = str_replace(' + ', '+', $out);

		$out = str_replace('0.1', '.1', $out);
		$out = str_replace('0.2', '.2', $out);
		$out = str_replace('0.3', '.3', $out);
		$out = str_replace('0.4', '.4', $out);
		$out = str_replace('0.5', '.5', $out);
		$out = str_replace('0.6', '.6', $out);
		$out = str_replace('0.7', '.7', $out);
		$out = str_replace('0.8', '.8', $out);
		$out = str_replace('0.9', '.9', $out);

		$out = str_replace('font-weight:normal', 'font-weight:400', $out);
		$out = str_replace('font-weight:bold', 'font-weight:700', $out);

		$length2 = strlen($out);
		
		// если размер уменьшился, то обновляем файл
		if ($length1 > $length2)
		{
			$tf = filemtime($fn); // время создания файла
			
			$fp = fopen($fn, "w");
			fwrite($fp,  $out);
			fclose($fp);

			@touch($fn, $tf); // поменяем время создания файла назад
		}

	}
}
