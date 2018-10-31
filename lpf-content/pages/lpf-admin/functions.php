<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// авторизация
require_once(ENGINE_DIR . 'additions/auth-session.php');
$auth_options = mso_load_options(PAGES_DIR . CURRENT_PAGE_ROOT . '/auth/auth-options.php');
mso_auth_init($auth_options); // инициализация авторизации


/**
*  проверка авторизации 
*  if (!_auth()) return 'STOP';
*/
function _auth()
{
	require_once(ENGINE_DIR . 'additions/auth-session.php');
	
	$auth_options = mso_load_options(PAGES_DIR . CURRENT_PAGE_ROOT . '/auth/auth-options.php');
	
	return mso_is_auth($auth_options);
}

/**
*  список файлов для редактора текстовых файлов — включая и подкаталоги
*  на выходе данные для <select>
*  $r = new RecursiveDirectoryIterator($directory);
*  $files = _getFiles($r, 0, $directory);
*/
function _getFiles($rdi, $depth=0, $dir, $allow_ext = array('php', 'txt', 'css', 'less', 'js', 'html', 'htm', 'ini', 'sass', 'scss')) 
{
	$out = array();
	
	if (!is_object($rdi)) return $out;

	for ($rdi->rewind(); $rdi->valid(); $rdi->next()) 
	{
		if ($rdi->isDot()) continue;

		if ($rdi->isDir() || $rdi->isFile()) 
		{
			$cur = $rdi->current();
			$cur = str_replace('\\', '/', $cur);
			$cur = str_replace(str_replace('\\', '/', BASE_DIR), '', $cur);
			
			// pr($cur);
			// $cur = str_replace(BASE_DIR, '', $cur);
			// $cur = str_replace($dir, '', $cur);
			
			if ($rdi->isDir()) 
			{
				if ($depth == 0) 
				{
					$out[] = '<optgroup class="bg-blue100 mar10-t" label="' . $cur . '"></optgroup>';
				}
			}
			
			if ($rdi->isFile())
			{
				$file_ext = strtolower(str_replace('.', '', strrchr($cur, '.')));
				
				if (in_array($file_ext, $allow_ext)) 
				{
					if (is_writable($rdi->getPathname())) $out[] = $cur;
				}
			}
			
			if ($rdi->hasChildren())
			{
				$out1 = _getFiles($rdi->getChildren(), 1 + $depth, $dir, $allow_ext);
				$out = array_merge($out, $out1); 
			}
		}
	}
	
	return $out;
}

/**
* замена на английские буквы транслитерацией  
*/
function _slug($slug, $slash_del = true)
{
	$repl = array(
	"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
	"Е"=>"e", "Ё"=>"jo", "Ж"=>"zh",
	"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
	"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
	"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"h",
	"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
	"Ы"=>"y", "Ь"=>"",   "Э"=>"e",  "Ю"=>"ju", "Я"=>"ja",

	"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
	"е"=>"e", "ё"=>"jo", "ж"=>"zh",
	"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
	"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
	"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"h",
	"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
	"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"ju",  "я"=>"ja",

	# украина
	"Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
	"Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",
	
	# беларусь
	"Ў"=>"u", "ў"=>"u", "'"=>"",
	
	# румынский
	"ă"=>'a', "î"=>'i', "ş"=>'sh', "ţ"=>'ts', "â"=>'a',
	
	"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
	"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",

	"?"=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",

	"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
	"&"=>"", "="=>"", "№"=>"", "\\"=>"", "#"=>"",
	"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>"", "”"=>"", "“"=>"",
	"'"=>"",

	"’"=>"",
	"—"=>"-", // mdash (длинное тире)
	"–"=>"-", // ndash (короткое тире)
	"™"=>"tm", // tm (торговая марка)
	"©"=>"c", // (c) (копирайт)
	"®"=>"r", // (R) (зарегистрированная марка)
	"…"=>"", // (многоточие)
	"“"=>"",
	"”"=>"",
	"„"=>"",
	
	" "=>"-",
	);
	
	if ($slash_del) $slug = str_replace('/', '', $slug);
	$slug = strtr(trim($slug), $repl);
	$slug = htmlentities($slug); // если есть что-то из юникода
	$slug = strtr(trim($slug), $repl);
	$slug = strtolower($slug);
	
	return $slug;
}

/**
*  возвращает одномерный массив подкаталогов $dir
*/
function _getSubDirs($dir) 
{
	$r = new RecursiveDirectoryIterator($dir);
	return _RgetSubDirs($r, $dir); 
}

/**
*  Рекурсивная к _getSubDirs()
*/
function _RgetSubDirs($rdi, $dir, $depth=0, $all = array()) {

     if (!is_object($rdi)) return;
         
     for ($rdi->rewind(); $rdi->valid(); $rdi->next()) 
	 {
         if ($rdi->isDot()) continue;
         
         if ($rdi->isDir()) 
		 {
			$cur = $rdi->current();
			$cur = _ss($cur);
			$cur = str_replace($dir, '', $cur);

			// echo $cur . '<br>';
			
			$all[] = $cur;
            
            if ($rdi->hasChildren())
                $all = _RgetSubDirs($rdi->getChildren(), $dir, 1 + $depth, $all);
         }
     }
	 
	 return $all;
 }

// удаляет все файлы и полдкаталоги в каталоге
function _delete_files($path, $del_dir = FALSE, $level = 0)
{
	$path = rtrim($path, DIRECTORY_SEPARATOR);

	if (!$current_dir = @opendir($path))
	{
		return FALSE;
	}

	while (FALSE !== ($filename = @readdir($current_dir)))
	{
		if ($filename != "." and $filename != "..")
		{
			if (is_dir($path.DIRECTORY_SEPARATOR.$filename))
			{
				// Ignore empty folders
				if (substr($filename, 0, 1) != '.')
				{
					_delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
				}
			}
			else
			{
				@unlink($path.DIRECTORY_SEPARATOR.$filename);
			}
		}
	}
	@closedir($current_dir);

	if ($del_dir == TRUE AND $level > 0)
	{
		return @rmdir($path);
	}

	return TRUE;
}

// очистим кэш
function _clear_cache()
{
	_delete_files(CACHE_DIR, FALSE, 0);
}


// замена win-слэшей на /
function _ss($t)
{
	return str_replace('\\', '/', $t);
}


# end of file