<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Landing Page Framework (LPF)
	(c) MAX — http://lpf.maxsite.com.ua/
	ver. 25.2 22/02/2015
	
	Made in Ukraine | Зроблено в Україні
	
	License:  Putin huilo! | Путин хуйло!
	          Crimea this Ukraine! | Крым — это Украина!
				
	Copyright:
		MaxSite CMS: http://max-3000.com/
		CodeIgniter: http://codeigniter.com/
		Less.php: http://lessphp.gpeasy.com/
		Textile: http://txstyle.org/article/36/php-textile
		Markdown Extra: http://michelf.ca/projects/php-markdown/

*/

$_TIME_START = microtime(true); // для статистики

define("NR", "\n"); // перенос строки
define("NT", "\n\t"); // перенос + табулятор

// переменные, которые используются в шаблоне
$TITLE = '';
$META = array();
$META_LINK = array();
$DATA = array();

$VAR['autotag'] = false;
$VAR['autotag_my'] = false;
$VAR['bbcode'] = false;
$VAR['markdown'] = false;
$VAR['textile'] = false;
$VAR['autopre'] = false;
$VAR['autoremove'] = false;
$VAR['compress_text'] = false;
$VAR['remove_protocol'] = false;
$VAR['nocss'] = false;
$VAR['nojs'] = false;
$VAR['nofavicon'] = false;
$VAR['nocache'] = false;
$VAR['html_attr'] = '';
$VAR['body_attr'] = '';
$VAR['no_output_only_file'] = false;
$VAR['autoload_css_page'] = true;
$VAR['autoload_js_page'] = true;
$VAR['less_out_in_file'] = false;
$VAR['generate_static_page'] = false;
$VAR['generate_static_page_base_url'] = '';
$VAR['head_file'] = true;
$VAR['start_file'] = true;
$VAR['end_file'] = true;
$VAR['start_file_text'] = false;
$VAR['end_file_text'] = false;
$VAR['before_file'] = false;
$VAR['after_file'] = false;
$VAR['event'] = false;

// служебное
$MSO['_use_cache'] = false;
$MSO['_page_file'] = 'text.php'; // переопределяется в init.php страницы или environment/config.php
$MSO['_less_use_mini'] = true;
$MSO['_less_not_use_cache'] = false;
$MSO['_less_complier'] = 'mso_less1'; // функция less-компилятора
$MSO['_less_complier_timeout'] = 0; // задержка компилятора в секундах
$MSO['_loaded_script'] = array(); // список загруженых js-скриптов 
$MSO['_loaded_css'] = array(); // список загруженых css-файлов


 
/**
*  функция инициализации
*/
function init()
{
	global $VAR, $MSO;
	
	$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
	$base_url .= "://" . $_SERVER['HTTP_HOST'];
	$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

	define("BASEURL", $base_url); //http-адрес сайта Использовать только BASE_URL!
	
	define("BASE_DIR",BASEPATH); // аналог BASEPATH для унификации
	define("BASE_URL", BASEURL); // аналог BASEURL для унификации
	
	define("PAGES_DIR", BASE_DIR . 'pages/'); // путь к /pages/ на сервере
	define("PAGES_URL", BASE_URL . 'pages/'); // http-путь к /pages/ на сервере
	
	define("SET_DIR", BASE_DIR . 'set/'); // путь к /set/ на сервере — если используется
	define("SET_URL", BASE_URL . 'set/'); // http-путь к /set/ на сервере
	
	define("COMPONENTS_DIR", BASE_DIR . 'components/'); // путь к /components/ на сервере
	define("COMPONENTS_URL", BASE_URL . 'components/'); // http-путь к /components/
	
	define("SNIPPETS_DIR", BASE_DIR . 'snippets/'); // путь к /snippets/ на сервере
	define("SNIPPETS_URL", BASE_URL . 'snippets/'); // http-путь к /snippets/
	
	if (!defined('HOME_PAGE')) define('HOME_PAGE', 'home');
	if (!defined('PAGE_404')) define('PAGE_404', '404');
	
	$page = (isset($_GET['page'])) ? $_GET['page'] : HOME_PAGE;
	
	// обязательный файл text.php
	if ( file_exists(PAGES_DIR . $page . '/' . $MSO['_page_file']) )
	{
		define('CURRENT_PAGE', $page); // имя page
	}
	else
	{
		// файла text.php нет
		if (($pos = strpos($page, '/')) !== false) // в имени есть /
		{
			// попробуем выставить page на верхний каталог category/news -> category
			$page = substr($page, 0, $pos);
			
			// если есть text.php, то ставим эту page
			if ( file_exists(PAGES_DIR . $page . '/' . $MSO['_page_file']) )
				define('CURRENT_PAGE', $page);
			else 
				define('CURRENT_PAGE', PAGE_404);
		}
		else
		{
			define('CURRENT_PAGE', PAGE_404);
		}
	}
	
	define('CURRENT_PAGE_DIR', PAGES_DIR . CURRENT_PAGE . '/'); // путь на сервере к текущей page
	define('CURRENT_PAGE_URL', PAGES_URL . CURRENT_PAGE . '/'); // http-адрес к текущей page
	define('CURRENT_URL', BASE_URL . CURRENT_PAGE); // текущий http-адрес
	
	if ($VAR['head_file'] === true) $VAR['head_file'] = CURRENT_PAGE_DIR . 'head.php';
	if ($VAR['start_file'] === true) $VAR['start_file'] = CURRENT_PAGE_DIR . 'header.php';
	if ($VAR['end_file'] === true) $VAR['end_file'] = CURRENT_PAGE_DIR . 'footer.php';
	
	// если в адресе есть localhost, то выставляем константу
	if (stripos(BASEURL, '/localhost/') === false) 
		define('LOCALHOST', false);
	else
		define('LOCALHOST', true);
	
	
	// может есть init.php
	if ($fn = mso_fe(PAGES_DIR . CURRENT_PAGE . '/init.php')) require($fn);
}

/**
*  функция для отладки
*  используется для отладки с помощью прерывания
*  
*  @param $var — данные для вывода 
*  @param $html — выводить html-спецсмволы
*  @param $echo — выводить по echo
*  
*  @return string
*/
function pr($var, $html = false, $echo = true)
{
	if (!$echo) 
		ob_start();
	else 
		echo '<pre>';
		
	if (is_bool($var))
	{
		if ($var) 
			echo 'TRUE';
		else 
			echo 'FALSE';
	}
	else
	{
		if ( is_scalar($var) )
		{
			if (!$html) 
				echo $var;
			else
			{
				$var = str_replace('<br />', "<br>", $var);
				$var = str_replace('<br>', "<br>\n", $var);
				$var = str_replace('</p>', "</p>\n", $var);
				$var = str_replace('<ul>', "\n<ul>", $var);
				$var = str_replace('<li>', "\n<li>", $var);
				$var = htmlspecialchars($var);
				$var = wordwrap($var, 300);
				echo $var;
			}
		}
		else 
			print_r ($var);
	}
	
	if (!$echo)
	{
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	else 
		echo '</pre>';
}

/**
*  функция, аналогичная pr, только завершающаяся die() 
*  используется для отладки с помощью прерывания
*  
*  @param $var — данные для вывода 
*  @param $html — выводить html-спецсмволы
*  @param $echo — выводить по echo
*/
function _pr($var, $html = false, $echo = true)
{
	pr($var, $html, $echo);
	die();
}

/**
*  Функция возвращает полный путь к файлу
*  если файла нет, то возвращается false
*  
*  @param $file - имя файла
*  
*  @return string or false
*  
*  if ($fn = mso_fe(ENGINE_DIR . 'my.php')) require($fn);  
*/
function mso_fe($file)
{
	if (file_exists($file)) 
		return $file;
	else 
		return false;
}

/**
*  Функция подключает файл
*  
*  @param $file имя файла
*  @param $dir можно явно указать каталог
*  @param $return_content возвращать ли содержимое файла
*  
*  @return string
*  
*  если $dir = TRUE, то этот параметр не учитывается
*  
*  @example  mso_fr('ads.html'); // в текущей странице
*  @example  mso_fr('home/text.php', PAGES_DIR); // из home
*  @example  mso_fr(PAGES_DIR . '404/text.php', TRUE); // произвольный путь
*  @example  $text = mso_fr('menu.php', '', true); // содержимое файла
*/
function mso_fr($file, $dir = '', $return_content = false)
{
	if (!$dir) $dir = CURRENT_PAGE_DIR;
	if ($dir === TRUE) $dir = '';
	
	$content = '';
	
	if (file_exists($dir . $file)) 
	{
		if ($return_content)
		{
			ob_start();
			require($dir . $file);
			$content = ob_get_contents();
			ob_end_clean();
		}
		else 
			require($dir . $file);
	}
	
	return $content;
}

/**
*  компилятор LESS в CSS
*  на выходе css-подключение, либо содержимое css-файла (переключается через $css_url)
*  если первый параметр — массив, то остальные игнорируются. В массиве ключи — опции
*  
*  @param $less_file - входной less-файл (полный путь на сервере)
*  @param $css_file - выходной css-файл (полный путь на сервере)
*  @param $css_url - полный http-адрес css-файла. Если $css_url = '', то отдается содержимое css-файла
*  @param $use_cache - разрешить использование кэширования LESS-файла (определяется по времени файлов)
*  @param $use_mini - использовать сжатие css-кода
*  @param $use_mini_n - если включено сжатие, то удалять переносы строк
*  
*/
function mso_lessc($less_file = '', $css_file = '', $css_url = '', $use_cache = false, $use_mini = true, $use_mini_n = true)
{
	global $MSO, $VAR;
	
	// если false, то ничего не делаем и сразу выходим
	if ($MSO['_less_complier'] === false) return;
	
	if ($VAR['remove_protocol']) $css_url = mso_remove_protocol($css_url);

	// если входящего style.less ($less_file) нет, то подключаем style.css (указан в $css_url)
	// но есть $css_file
	// и выходим 
	if (!file_exists($less_file))
	{
		if (file_exists($css_file))
			// return NR . '<link rel="stylesheet" href="' . $css_url . '">';
			return mso_load_css($css_url);
		else
			return '';
	}
	
	$MSO['_less_file'] = $less_file; // текущий less-файл
	$MSO['_less_file_dir'] = dirname($less_file) . '/'; // каталог less файла
	
	if ($MSO['_less_not_use_cache'] === true) $use_cache = false; // если установлен флаг, то отключаем кеши
	
	if (is_array($less_file)) // все параметры в массиве
	{
		$options = $less_file; // для красоты кода и чтобы не путаться
		
		$less_file = isset($options['less_file']) ? $options['less_file'] : '';
		$css_file = isset($options['css_file']) ? $options['css_file'] : '';
		$css_url = isset($options['css_url']) ? $options['css_url'] : '';
		$use_cache = isset($options['use_cache']) ? $options['use_cache'] : false;
		$use_mini = isset($options['use_mini']) ? $options['use_mini'] : true;
		$use_mini_n = isset($options['use_mini_n']) ? $options['use_mini_n'] : false;
	}
	
	if (!$less_file or !$css_file) return ''; // не указаны файлы
	
	if ($use_cache) // проверка кэша
	{
		if (file_exists($less_file) and file_exists($css_file))
		{
			$flag_compiling = false; // флаг == true — требуется компиляция 
			$t_css = filemtime($css_file); // время css-файла
			
			// смотрим все файлы каталога
			require_once('helpers/file_helper.php'); // хелпер для работы с файлами
			
			$all_files_in_dirs = get_filenames(dirname($less_file), true);

			foreach ($all_files_in_dirs as $file)
			{
				if (substr(strrchr($file, '.'), 1) !== 'less') continue; // проверка расширения файла
				
				if (filemtime($file) > $t_css) // файл старше css — нужна компиляция
				{
					$flag_compiling = true; // нужна компиляция
					break;
				}
			}
			
			if (!$flag_compiling) // можно отдать из кеша
			{
				if ($css_url) 
					// в виде имени файла
					// return NR . '<link rel="stylesheet" href="' . $css_url . '">';
					return mso_load_css($css_url);
				else
					// в виде содержимого
					return file_get_contents($css_file);
			}
		}
	}

	if (file_exists($less_file)) 
		$fc_all = file_get_contents($less_file);
	else 
		return ''; // нет файла, выходим

	// проверка на разрешение записывать css-файл
	if (file_exists($css_file) and !is_writable($css_file)) 
		return 'LESS: результирующий css-файл не имеет разрешений на запись.'; 
	
	if ($fc_all)
	{
		// возможно есть php-файл для своих функций
		// строится как исходный + .php
		// пример http://leafo.net/lessphp/docs/#custom_functions
		if (file_exists($less_file . '.php')) require_once($less_file . '.php');
		
		// в коде могут быть специальные команды 
		// универсальная конструкция: @MSO_IMPORT_ALL(каталог);
		$fc_all = preg_replace_callback('!(@MSO_IMPORT_ALL\()(.*?)(\);)!is', '_mso_less_import_all_callback', $fc_all);
		
		// в тексте исходного файла $fc_all может быть php-код
		ob_start();
		eval( '?>' . $fc_all . '<?php ');
		$fc_all = ob_get_contents();
		ob_end_clean();		
		
		if ($VAR['less_out_in_file']) // можно сохранить в указанный файл 
		{
			$fp1 = fopen(BASE_DIR . $VAR['less_out_in_file'], "w");
			fwrite($fp1, $fc_all);
			fclose($fp1);
		}
		
		$out = '';
		
		// pr($fc_all);
		
		// функции компилятора вынесены отдельно
		// $MSO['_less_complier'] = 'mso_less1';
		// $MSO['_less_complier'] = 'custom'; // собственная компиляция, например через http://winless.org/ 
	
		if (function_exists($MSO['_less_complier'])) // есть такая функция
		{
			$function_less = $MSO['_less_complier'];
			$out = $function_less($fc_all, $less_file, $use_mini);
		}
		else
		{
			// функции нет — своя компиляция
			if ($css_url) 
			{
				if ($MSO['_less_complier_timeout']) sleep($MSO['_less_complier_timeout']);
				
				return mso_load_css($css_url); // в виде имени файла
			}
			else
			{
				return '';
			}
		}

		// сжатие кода
		// $MSO['_less_use_mini'] для отладки можно выставлять в false, тогда сжатия не будет
		if ($use_mini and $MSO['_less_use_mini'] === true)
		{
			if ($use_mini_n)
			{
				$out = str_replace("\t", ' ', $out);
				$out = str_replace(array("\r\n", "\r", "\n", '  ', '    '), '', $out);
			}
			
			$out = str_replace("\n\t", '', $out);
			$out = str_replace("\n}", '}', $out);
			$out = str_replace('; ', ';', $out);
			$out = str_replace(';}', '}', $out);
			$out = str_replace(': ', ':', $out);
			$out = str_replace('{ ', '{', $out);
			$out = str_replace(' }', '}', $out);
			$out = str_replace(' {', '{', $out);
			$out = str_replace(', ', ',', $out);
			$out = str_replace(' > ', '>', $out);		
			$out = str_replace('} ', '}', $out);
			$out = str_replace('  ', ' ', $out);
		}
		
		$fp = fopen($css_file, "w");
		fwrite($fp, $out);
		fclose($fp);
		
		if ($css_url) 
			return mso_load_css($css_url); // в виде имени файла
		else
			return $out; // в виде содержимого
	}
}

 
/**
*  колбак функция для @MSO_IMPORT_ALL(каталог);
*/
function _mso_less_import_all_callback($matches)
{
	global $MSO;
	
	$dir = trim($matches[2]);

	$files = mso_get_path_files($MSO['_less_file_dir'] . $dir . '/', $dir . '/', true, array('less'));

	$m = '';
	foreach($files as $file)
	{
		// $m .= '@import \'' . $file . '\';' . NR; // старый вариант через встроенный @import
		
		$m .= '// ================== ' . $file . ' ================== ' . NR . NR;
		$m .= file_get_contents($MSO['_less_file_dir'] . $file) . NR;
	}
	
	return $m;
}

/**
*  служебная функция, срабатывающая при ошибке компиляции LESS
*/
function _mso_less_exception($message, $text)
{
	// пробуем оформить более внятный вывод ошибки с исходным кодом
	
	$out = '<pre style="color: red;">lessphp fatal error: ' . $message . '</pre>';
	
	$text = NR . htmlspecialchars($text);
	$text = str_replace("\n", "<li style='margin:0 0 0 30px'>", $text);
	
	$out .= '<ol style="height: 500px; overflow: scroll; background: #eee; font-family: monospace; ">' . $text . '</ol>';
	
	return $out;
}

/**
*  Старый LESS-компилятор автоматом перекидывается на mso_less2
*/
function mso_less1($fc_all, $less_file, $use_mini)
{
	return mso_less2($fc_all, $less_file, $use_mini);
}

/**
*  LESS-компилятор от http://lessphp.gpeasy.com - поновее, но мало тестировался
*  
*  @param $fc_all входящий текст less-файл
*  @param $less_file имя less-файла
*  @param $use_mini использовать сжатие
*  
*  @return string
*/
function mso_less2($fc_all, $less_file, $use_mini)
{
	global $MSO;
	
	require_once('less/lessc.inc.php');
		
	$options = array(
		'compress' => true, 
		'relativeUrls' => false,
	);
		
	$compiler = new Less_Parser($options);
	
	$compiler->SetImportDirs(array( dirname($less_file) => dirname($less_file) ));
	
	try
	{
		$compiler->parse($fc_all);
		$out = $compiler->getCss();
		
		// код уже сжат, рассжимаем, если нужно 
		if (!($use_mini and $MSO['_less_use_mini'] === true))
		{
			$out = str_replace('}', "\n}\n", $out);
			$out = str_replace('{', " {\n", $out);
			$out = str_replace(';', ";\n", $out);
		}
	}
	catch (Exception $ex)
	{
		$out = _mso_less_exception($ex->getMessage(), $fc_all);
		die($out);
	}
	
	return $out;
}

/**
*  функция возвращает массив $path_url-файлов по указанному $path - каталог на сервере
*  
*  @param $full_path - нужно ли возвращать полный адрес (true) или только имя файла (false)
*  @param $exts - массив требуемых расширений. По-умолчанию - картинки 
*  
*  @return array
*/
function mso_get_path_files($path = '', $path_url = '', $full_path = true, $exts = array('jpg', 'jpeg', 'png', 'gif', 'ico'))
{
	// если не указаны пути, то отдаём пустой массив
	if (!$path) return array();
	if (!is_dir($path)) return array(); // это не каталог

	require_once(ENGINE_DIR . 'helpers/directory_helper.php'); // хелпер для работы с каталогами
	
	$files = directory_map($path, true); // получаем все файлы в каталоге
	if (!$files) return array();// если файлов нет, то выходим

	$all_files = array(); // результирующий массив с нашими файлами
	
	// функция directory_map возвращает не только файлы, но и подкаталоги
	// нам нужно оставить только файлы. Делаем это в цикле
	foreach ($files as $file)
	{
		if (@is_dir($path . $file)) continue; // это каталог
		
		$ext = substr(strrchr($file, '.'), 1);// расширение файла
		
		// расширение подходит?
		if (in_array($ext, $exts))
		{
			if (strpos($file, '_') === 0) continue; // исключаем файлы, начинающиеся с _
			if (strpos($file, '-') === 0) continue; // исключаем файлы, начинающиеся с -
			
			// добавим файл в массив сразу с полным адресом
			if ($full_path)
				$all_files[] = $path_url . $file;
			else
				$all_files[] = $file;
		}
	}
	
	natsort($all_files); // отсортируем список для красоты
	
	return $all_files;
}

/**
*  возвращает подкаталоги в указаном каталоге.
*  можно указать исключения из каталогов в $exclude 
*  
*  в $need_file можно указать обязательный файл в подкаталоге
*  если $need_file = true то обязательный php-файл в подкаталоге должен совпадать с именем подкаталога например для /menu/ это menu.php
*  
*  @param $path 
*  @param $exclude 
*  @param $need_file 
*  
*  @return array
*/
function mso_get_dirs($path, $exclude = array(), $need_file = false)
{
	require_once(ENGINE_DIR . 'helpers/directory_helper.php'); // хелпер для работы с каталогами
	
	if ($all_dirs = directory_map($path, true))
	{
		$dirs = array();
		foreach ($all_dirs as $d)
		{
			// нас интересуют только каталоги
			if (is_dir($path . $d) and !in_array($d, $exclude))
			{
				if (strpos($d, '_') === 0) continue; // исключаем файлы, начинающиеся с _
				if (strpos($d, '-') === 0) continue; // исключаем файлы, начинающиеся с -
				
				// если указан обязательный файл, то проверяем его существование
				if($need_file === true and !file_exists($path . $d . '/' . $d . '.php')) continue;
				if($need_file !== true and $need_file and !file_exists($path . $d . '/' . $need_file)) continue;
				
				$dirs[] = $d;
			}
		}
		
		natcasesort($dirs);
		
		return $dirs;
	}
	else
	{
		return array();
	}
}

/**
*  формирует <script> из указанного адреса
*  если $nodouble = true, то исключается дублирование подключаемого url 
*  
*  @param $url url-адрес
*  @param $nodouble запретить дублирование 
*  @param $attr добавляет атрибут 
*  
*  @return string
*/
function mso_load_script($url = '', $nodouble = false, $attr = '')
{
	global $MSO, $VAR;
	
	if ($VAR['remove_protocol']) $url = mso_remove_protocol($url);
	
	if ($nodouble and in_array($url, $MSO['_loaded_script'])) return ''; // уже была загрузка
	
	$MSO['_loaded_script'][] = $url; // добавляем в список загруженных
	$MSO['_loaded_script'] = array_unique($MSO['_loaded_script']);
	
	$attr = ($attr) ? ' ' . $attr : '';
	
	return NR . '<script' . $attr . ' src="' . $url . '"></script>';
}

/**
*  формирует link rel="stylesheet" из указанного url-адреса 
*  
*  @param $url адрес
*  @param $nodouble запретить дублирование
*  
*  @return string
*/
function mso_load_css($url = '', $nodouble = true)
{
	global $MSO, $VAR;
	
	if ($VAR['remove_protocol']) $url = mso_remove_protocol($url);
	
	if ($nodouble and in_array($url, $MSO['_loaded_css'])) return ''; // уже есть загрузка
	
	$MSO['_loaded_css'][] = $url; // добавляем в список загруженных
	$MSO['_loaded_css'] = array_unique($MSO['_loaded_css']);
	
	return NR . '<link rel="stylesheet" href="' . $url . '">';
}

/**
*  autoload файлов в подкаталоге /autoload/ ($auto_dir) заданного $dir
*  $in_base = true — поиск относительно BASE_DIR
*  $in_page = true, в текущей page
*  результат объединяется
*  
*  @param $dir 
*  @param $in_base 
*  @param $in_page 
*  @param $auto_dir 
*  
*  @return string
*/
function mso_autoload($dir = 'js', $in_base = true, $in_page = true, $auto_dir = '/autoload/')
{
	global $VAR;
	
	$a1 = $a2 = array();
	
	if ($in_base)
		$a1 = mso_get_path_files(BASE_DIR . $dir . $auto_dir, BASE_URL . $dir . $auto_dir, true, array('js', 'css'));
	
	if ($in_page)
		$a2 = mso_get_path_files(CURRENT_PAGE_DIR . $dir . $auto_dir, CURRENT_PAGE_URL . $dir . $auto_dir, true, array('js', 'css'));

	$autoload = array_merge($a1, $a2);
	
	$ret = '';
	
	if ($autoload)
	{
		foreach($autoload as $fn)
		{
			if ($VAR['remove_protocol']) $fn = mso_remove_protocol($fn);

			$ext = substr(strrchr($fn, '.'), 1); // расширение файла
			
			if ($ext == 'js') 
			{
				// .async и .defer — если в имени файла есть это вхождение
				// то прописываем эти атрибуты к <script> 
				if (strpos($fn, '.async') !== false) $attr = ' async';
				elseif (strpos($fn, '.defer') !== false) $attr = ' defer';
				else $attr = '';
				
				$ret .= NR . '<script src="' . $fn . '"' . $attr . '></script>';
			}
			elseif ($ext == 'css') 
					$ret .= mso_load_css($fn);
		}
	}
	
	return $ret;
}

/**
*  вывод статистики страницы
*  
*  @return string
*/
function mso_stat_out()
{
	global $_TIME_START, $VAR, $MSO;
	
	$time = number_format( microtime(true) - $_TIME_START , 6) . 'sec';
	$memory	 = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2) . 'Mb';
	
	$out = $time . ' | ' . $memory;
	
	if ($MSO['_use_cache']) $out .= ' | Cache';
	if ($VAR['bbcode']) $out .= ' | BBCode';
	if ($VAR['markdown']) $out .= ' | Markdown';
	if ($VAR['textile']) $out .= ' | Textile';
	if ($VAR['nocss']) $out .= ' | NoCSS';
	if ($VAR['nojs']) $out .= ' | NoJS';
	if ($VAR['autotag']) $out .= ' | AutoTag';
	if ($VAR['autotag_my']) $out .= ' | AutoTag (' . $VAR['autotag_my'] . ')';
	if ($VAR['autopre']) $out .= ' | AutoPRE';
	if ($VAR['compress_text']) $out .= ' | Compress text';
	if ($VAR['remove_protocol']) $out .= ' | Remove protocol';
	
	$out = NR . '<!-- (c) Landing Page Framework http://lpf.maxsite.com.ua/ | ' . $out . ' | Путин хуйло! | Крым — это Украина! -->' . NR;
	
	echo $out . '</body></html>';
}

/**
*  выводит meta страницы из $META и $META_LINK
*  
*  @return string
*/
function mso_meta()
{
	global $META, $META_LINK, $VAR;
	
	$out = '';
	
	foreach($META as $name => $content)
	{
		$out .= NR . '<meta name="' . $name . '" content="' . $content . '">';
	}
	
	if ($META_LINK)
	{
		foreach($META_LINK as $elem)
		{
			$le = '';
			
			foreach($elem as $key => $val)
			{
				$le .= $key . '="' . $val . '" ';
			}
			
			$out .= NR . '<link ' . trim($le) . '>';
		}
	}
	
	if ($VAR['remove_protocol']) $out = mso_remove_protocol($out);
	
	echo trim($out);
}

/**
*  вывод текста
*  
*  @return string
*/
function mso_output_text()
{
	global $VAR, $MSO;
	

	if ($fn = mso_fe(CURRENT_PAGE_DIR . $MSO['_page_file']))
	{
		// имя кеша строится по фиксированному шаблону
		$cache_file = CURRENT_PAGE . '_' . $MSO['_page_file'];
		
		if ( isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] and (strpos($_SERVER['REQUEST_URI'], '?') !== FALSE) )
		{
			$cache_file .= '-' . md5($_SERVER['REQUEST_URI']);
		}
		
		$cache_file = str_replace(array('.', '/', '\\', '?'), '-', $cache_file);
		$cache_file = BASE_DIR . 'cache/' . $cache_file . '.html';
		
		// если есть кеш, то отдаем из него
		if (!$VAR['nocache'] and mso_fe($cache_file))
		{
			$t_cache = filemtime($cache_file); // время файла кеша
			
			if (filemtime($fn) < $t_cache) // кеш старше
			{
				// отдаем из кеша
				echo file_get_contents($cache_file);
				
				$MSO['_use_cache'] = true; // для статистики
				
				return ''; 
			}
		}
		
		ob_start();
		
		// файл перед page.php
		if ($VAR['start_file_text'] and $fd = mso_fe($VAR['start_file_text'])) require($fd);
		
		// файл page.php
		require($fn);
		
		// файл после page.php
		if ($VAR['end_file_text'] and $fd = mso_fe($VAR['end_file_text'])) require($fd);
		
		$out = ob_get_contents();

		ob_end_clean();
		
		$out = mso_word_processing($out);
		$out .= mso_lazy();
		
		// pr($out, 1);
		
		// результат запишем в кеш
		if (!$VAR['nocache'])
		{
			// для 404 кешируем только без REQUEST_URI
			if (!(CURRENT_PAGE == PAGE_404 and mso_url_request())) file_put_contents($cache_file, $out);
		}
		
		echo $out;
		
	}
}

/**
*  обработка текста
*  если $var = false, то используется $VAR
*  
*  @param $out входящий текст
*  @param $var опци
*  
*  @return string
*/
function mso_word_processing($out, $var = false)
{
	global $VAR;
	
	if ($var === false) $var = $VAR;
	
	if ($a = $var['autotag_my']) 
	{
		// например $VAR['autotag_my'] = 'simple';
		// файл: pages/autotag/simple.php
		// функция должна называться как autotag_ПАРСЕР: autotag_simple($text);
		
		// или в ENGINE_DIR/autotag/simple.php
		// функция: autotag_simple();
		
		$fu = 'autotag_' . $a;
		
		// парсер может быть в каталоге страницы autotag/
		if ($fn = mso_fe(CURRENT_PAGE_DIR . 'autotag/' . $a . '.php'))
		{
			require_once($fn);
			if (function_exists($fu)) $out = $fu($out);
		}
		// парсер может быть в каталоге ENGINE_DIR
		elseif ($fn = mso_fe(ENGINE_DIR . 'autotag/' . $a . '.php'))
		{
			require_once($fn);
			if (function_exists($fu)) $out = $fu($out);
		}
	}
	
	if ($var['autoremove']) $out = mso_autoremove($out);
	if ($var['autotag']) $out = mso_autotag($out);
	if ($var['autopre']) $out = mso_autopre($out);

	if ($var['bbcode'] and mso_fe(ENGINE_DIR . 'bbcode/index.php')) 
	{
		require_once(ENGINE_DIR . 'bbcode/index.php'); // bb-код
		$out = bbcode_custom($out);
	}
	
	if ($var['markdown'] and mso_fe(ENGINE_DIR . 'markdown/markdown.php')) 
	{
		require_once(ENGINE_DIR . 'markdown/markdown.php'); // markdown-код
		$out = Markdown($out);
	}
	
	if ($var['textile'] and mso_fe(ENGINE_DIR . 'textile/textile.php')) 
	{
		require_once(ENGINE_DIR . 'textile/textile.php'); // textile-код
		
		$parser = new Textile('html5');
		$out = $parser->textileThis($out);
	}
	
	if ($var['remove_protocol']) $out = mso_remove_protocol($out);
	if ($var['compress_text']) $out = mso_compress_text($out);
		
	// удалить спецкод, если остался в тексте
	$out = str_replace(array('[html]', '[/html]', '[html_r]', '[/html_r]'), '', $out);
	
	return $out;
}

/**
*  авторасстановка тэгов
*  
*  @param $pee входной текст 
*  
*  @return string
*/
function mso_autotag($pee)
{
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee);
	
	# если html-код в [html_r] код [/html_r]
	# в отличие от [html] — отдаёт полностью исходный html без обработок 
	$pee = str_replace('<p>[html_r]</p>', '[html_r]', $pee);
	$pee = str_replace('<p>[/html_r]</p>', '[/html_r]', $pee);
	$pee = preg_replace_callback('!\[html_r\](.*?)\[\/html_r\]!is', 'mso_clean_html_r_do', $pee );
	
	# если html в [html] код [/html]
	$pee = str_replace('<p>[html]</p>', '[html]', $pee);
	$pee = str_replace('<p>[/html]</p>', '[/html]', $pee);
	$pee = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', 'mso_clean_html_do', $pee );

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
	
	$pee = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $pee );
	
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
function mso_clean_html_do($matches)
{
	$arr1 = array('&amp;', '&lt;', '&gt;', '<br />', '<br>', '&nbsp;');
	$arr2 = array('&',     '<',    '>',    "\n",     "\n",   ' ');
	
	$m = trim( str_replace($arr1, $arr2, $matches[1]) );
	$m = '[html_base64]' . base64_encode($m) . '[/html_base64]';

	return $m;
}

/**
*  аналогично mso_clean_html_do, только без замен — [html_r] ... [/html_r]
*  
*  @param $matches 
*  
*  @return string
*/
function mso_clean_html_r_do($matches)
{
	return '[html_base64]' . base64_encode($matches[1]) . '[/html_base64]';
}

/**
*  pre, которое загоняется в [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function mso_clean_pre_do($matches)
{
	$text = trim($matches[2]);

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br>", "\n", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "\n", $text);
	
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('&lt;pre', '<pre', $text);
	$text = str_replace('&lt;/pre', '</pre', $text);
	$text = str_replace('pre&gt;', 'pre>', $text);

	$text = $matches[1] . "\n" . '[html_base64]' . base64_encode($text) . '[/html_base64]'. $matches[3];

	return $text;
}

/**
*  декодирование из [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function mso_clean_html_posle($matches)
{
	return base64_decode($matches[1]);
}

# 
/**
*  script, который загоняется в [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function mso_clean_html_script($matches)
{
	$text = trim($matches[2]);
	$text = $matches[1] . '[html_base64]' . base64_encode($text) . '[/html_base64]'. $matches[3];
	
	return $text;
}

/**
*  функция автоматической обработки содержимого <pre> в html-спецсимволы 
*  
*  @param $pee текст
*  
*  @return текст
*/
function mso_autopre($pee)
{
	$pee = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', 'mso_clean_pre_do', $pee);
	$pee = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $pee );
	
	return $pee;
}

/**
*  удаляет из текста блок [remove] ... [/remove]
*  
*  @param $pee текст
*  
*  @return string
*/
function mso_autoremove($pee)
{
	$pee = preg_replace('!\[remove\](.*?)\[\/remove\]!is', '', $pee);
	
	return $pee;
}

/**
*  заменяет в тексте все вхождения http:// и https:// на // 
*  
*  @param $text текст
*  
*  @return string
*/
function mso_remove_protocol($text)
{
	# защищенный текст
	$text = preg_replace_callback('!\[html_r\](.*?)\[\/html_r\]!is', 'mso_clean_html_r_do', $text);
	$text = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', 'mso_clean_html_do', $text);
	$text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', 'mso_clean_pre_do', $text);
	$text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', 'mso_clean_pre_do', $text);
	$text = preg_replace_callback('!(<script.*?>)(.*?)(</script>)!is', 'mso_clean_html_script', $text);
	
	$text = str_replace('https://', '//', $text);
	$text = str_replace('http://', '//', $text);
	
	$text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $text);
		
	return $text;
}

/**
*  сжатие HTML-текста путём удаления лишних пробелов
*  
*  @param $text 
*  
*  @return string
*/
function mso_compress_text($text)
{
	# защищенный текст
	$text = preg_replace_callback('!\[html_r\](.*?)\[\/html_r\]!is', 'mso_clean_html_r_do', $text);
	$text = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', 'mso_clean_html_do', $text);
	$text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', 'mso_clean_pre_do', $text);
	$text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', 'mso_clean_pre_do', $text);
	$text = preg_replace_callback('!(<script.*?>)(.*?)(</script>)!is', 'mso_clean_html_script', $text);
	
	$text = str_replace(array("\r\n", "\r"), "\n", $text);
	$text = str_replace("\t", ' ', $text);
	$text = str_replace("\n   ", "\n", $text);
	$text = str_replace("\n  ", "\n", $text);
	$text = str_replace("\n ", "\n", $text);
	$text = str_replace("\n", '', $text);
	$text = str_replace('   ', ' ', $text);
	$text = str_replace('  ', ' ', $text);
	
	// специфичные замены
	$text = str_replace('<!---->', '', $text);
	$text = str_replace('>    <', '><', $text);
	$text = str_replace('>   <', '><', $text);
	$text = str_replace('>  <', '><', $text);
	
	$text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $text);
	
	return $text;
}

/**
*  вывод/подключение компонента (каталог /components/)
*  имя файла совпадает с каталогом: menu/menu.php
*  в файле компонента будет доступна переменная $OPTIONS
*  
*  @param $component 
*  
*  @return string
*/
function mso_component($component, $OPTIONS = array())
{
	if ($fn = mso_fe(BASE_DIR . 'components/' . $component . '/' . $component . '.php')) require($fn);
}

/**
*  возвращает массив опций, заданный в файле
*  опции задаются как массив переменной $OPTIONS
*  
*  @param $file подключаемый файл
*  
*  @return array
*  
*  $auth_option = mso_load_options(SET_DIR . 'auth.php'); // загрузка
*  mso_component('auth', $auth_option); // использование
*/
function mso_load_options($file)
{
	if ($fn = mso_fe($file))
	{
		require($fn);
		
		if (isset($OPTIONS)) 
			return $OPTIONS;
		else
			return array();
	}
	else 
		return array();
}

/**
*  текущий URL
*  если $delete_request = true то отсекаем строку после ?
*  
*  @param $explode 
*  @param $absolute 
*  @param $delete_request 
*  
*  @return string
*/
function mso_current_url($explode = false, $absolute = false, $delete_request = false)
{
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
	$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	if ($delete_request) // отделим по «?»
	{
		$url = explode('?', $url);
		$url = $url[0];
	}
	
	if ($absolute) return $url;
	
	$url = str_replace(BASE_URL, "", $url);
	$url = trim( str_replace('/', ' ', $url) );
	$url = str_replace(' ', '/', $url);
	
	if ($explode) $url = explode('/', $url);
	
	return $url;
}

/**
*  значение указаного сегмента (относительно главной страницы)
*  
*  @param $segment номер сегмента
*  
*  @return string
*  
*  Пример: http://сайт/news/2014/10/22
*  mso_segment(1) -> news
*  mso_segment(2) -> 2014
*  mso_segment(3) -> 10
*  mso_segment(4) -> 22
*/
function mso_segment($segment)
{
	$url = mso_current_url(true, false, true);
	
	// есть ли сегмент?
	if (isset($url[$segment - 1])) 
		$s = urldecode($url[$segment - 1]); // есть, декодируем
	else 
		$s = false; // нет сегмента
	
	return $s;
}

/**
*  Парсинг входящего URL в массив
*  
*  @param $keys_only == false, то возвращается полный массив ключ=значение
*  @param $keys_only == true, то возвращается только массив ключей
*  @param $key_present В $key_present можно задать наличие ключа: если есть, то функция вернет true, иначе false при этом, если $key_present_return_array = true, то возвращается значение ключа $key_present
*  @param $key_present_return_array 
*  
*  @return array
*  
*  Пример: http://сайт/test?mytext&color=red
*  Если $keys_only == false, то возвращается полный массив ключ=значение
*  Array
*     [mytext] => 
*     [color] => red
*  Если $keys_only == true, то возвращается только массив ключей:
*  Array
*    [0] => mytext
*    [1] => color
*/
function mso_url_request($keys_only = true, $key_present = false, $key_present_return_array = false)
{
	if ( isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] and (strpos($_SERVER['REQUEST_URI'], '?') !== FALSE) )
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
		$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = str_replace(BASE_URL, "", $url);
		$url = explode('?', $url);
		
		if ($url[1])
		{
			$s = str_replace('&amp;', '&', $url[1]);
			
			// !!!! УТОЧНИТь, может не нужно запрещать??? !!!
			//$s = str_replace('/', '-', $s); // запрещаем использовать / в адресе
			
			// запрещаем использовать символы в адресе
			$s = str_replace('.', '_', $s); 
			$s = str_replace('~', '-', $s);
			$s = str_replace('\\', '-', $s);
			
			$s = explode('&', $s);
			
			$uri_get_array = array();
			
			foreach ($s as $val)
			{
				parse_str($val, $arr);
				foreach ($arr as $key1 => $val1)
				{
					$uri_get_array[$key1] = $val1;
				}
			}

			if ($keys_only) $uri_get_array = array_keys($uri_get_array);
			
			if ($key_present !== false)
			{
				if ($key_present_return_array)
					return $uri_get_array[$key_present];
				else
					return isset($uri_get_array[$key_present]);
			}
			else 
				return $uri_get_array;
			
		}
		else 
			return false;
	}
	else 
		return false;
}

/**
*  получение адреса подключаемого файла, основанного на GET-запросе
*  
*  @param $dir каталог
*  @param $def_file файл по-умолчанию
*  @param $ext расширение файлов
*  
*  @return string
*  
*  http://сайт/test?mytext
*  где mytext — это файл mytext.php в подкаталоге $dir текущей page
*  пути указываются относительно текущей page
*  if ($fn = mso_get_subpage('mytext', 'mytext/default.php')) require($fn);  
*/
function mso_get_subpage($dir, $def_file, $ext = '.php') 
{
	$default_file = CURRENT_PAGE_DIR . $def_file;
	
	if (!file_exists($default_file)) $default_file = false;
	
	if ($mytext = mso_url_request()) 
	{
		$mytext = $mytext[0];
		
		// pr(CURRENT_PAGE_DIR . $dir . '/' . $mytext . $ext);
		
		if ($fn = mso_fe(CURRENT_PAGE_DIR . $dir . '/' . $mytext . $ext)) 
			return $fn;
		else 
			return $default_file;
	}
	else 
		return $default_file;
}

/**
*  разные подключения в HEAD секции
*  
*  @return echo
*/
function mso_head()
{
	global $VAR;
	
	$baseurl = BASE_URL;
	$current_page_url = CURRENT_PAGE_URL;
	
	if ($VAR['remove_protocol'])
	{
		$baseurl = mso_remove_protocol($baseurl);
		$current_page_url = mso_remove_protocol($current_page_url);
	}
		
	if ($VAR['nofavicon'] === false)
	{
		if (mso_fe(BASE_DIR . 'images/favicon.png'))
			echo NR . '<link rel="shortcut icon" href="' . $baseurl . 'images/favicon.png" type="image/x-icon">';
	}
	
	if ($VAR['nocss'] === false)
	{
		// какой-то ещё свой вариант для less-компиляции
		if ($fn = mso_fe(BASE_DIR . 'css-less/less.php')) require($fn);
		
		echo mso_lessc(BASE_DIR . 'css-less/style.less', BASE_DIR . 'css/style.css', $baseurl . 'css/style.css', true, true, true);
		
		// autoload css-файлов из BASE_DIR
		echo mso_autoload('css', true, false, '/'); 
	}
	
	if ($VAR['nojs'] === false)
	{
		// jQuery с 22.0 загружается на общих основаниях в /autoload/
		// if (mso_fe(BASE_DIR . 'js/jquery.min.js')) echo mso_load_script($baseurl .  'js/jquery.min.js');
		
		echo mso_autoload('js', true, false); // autoload js-файлов из BASE_DIR
	}
	
	if($VAR['autoload_css_page'] === true) // разрешена автозагрузка из текущей page
	{
		// какой-то ещё свой вариант для less-компиляции
		if ($fn = mso_fe(CURRENT_PAGE_DIR . 'css-less/less.php')) require($fn);
		
		// autoload из каталога page css и css-less с компиляцией
		echo mso_lessc(CURRENT_PAGE_DIR . 'css-less/style.less', CURRENT_PAGE_DIR . 'css/style.css', $current_page_url . 'css/style.css', true, true, true);
		
		echo mso_autoload('css', false, true, '/');
	}
	
	if ($VAR['autoload_js_page'] === true) // разрешена автозагрузка из текущей page
		echo mso_autoload('js', false, true); // autoload js-файлов из CURRENT_PAGE_DIR
}

/**
*  вывод js-скриптов и статистики в конце страницы
*  
*  @param $to  = 'любой текст', то он добавляется в общий массив
*  @param $to  = null, то происходит вывод всех данных по echo
*  
*  @return echo
*/
function mso_lazy($to = null)
{
	global $VAR;
	
	static $to_out = '';
	
	$out = '';
	
	if (is_null($to))
	{
		if ($VAR['nojs'] === false)
		{
			$out .= mso_autoload('js', true, false, '/lazy/'); // autoload js-файлов из BASE_DIR
			// pr($out, 1);
		}
		
		if ($VAR['autoload_js_page'] === true) // разрешена автозагрузка из текущей page
			$out .= mso_autoload('js', false, true, '/lazy/'); // autoload js-файлов из CURRENT_PAGE_DIR
		
		if ($VAR['nojs'] === false and mso_fe(BASE_DIR . 'js/my.js')) 
			$out .= mso_load_script(BASE_URL . 'js/my.js');
		
		$out .= $to_out; // вывод остального
		
		
		return $out;
	}
	else
	{
		if ($to and is_string($to)) $to_out .= NR . $to;
	}
}

/**
*  функция для A/B-тестирования — возвращает случайным образом один элемент
*  
*  @return string
*  
*  в environment/config.php: define("HOME_PAGE", mso_ab(array('home1', 'home2')));
*  где home1 и home2 — страницы в /pages/
*  или 
*  разный текст в пределах одной страницы. В init.php 
*  $MSO['_page_file'] =  mso_ab(array('text.php', 'text2.php'));  
*/
function mso_ab($items = array())
{
	return $items[array_rand($items)];
}

/**
*  запись данных в кеш
*  кеш дефолтный /cache/
*  
*  @param $key ключ
*  @param $data данные
*  
*  if (!$mydata = mso_get_cache('mydata', 600))
*  {
*    $mydata = 'текст';
*    mso_add_cache('mydata', $mydata);
*  }
*  echo $mydata;
*/
function mso_add_cache($key, $data)
{
	// ключ кеша шифруется по адресу сайта
	$file = BASE_DIR . 'cache/' . strrev(md5($key . BASE_URL));
	
	$data = serialize($data); // все даные через серилизацию
	$fp = fopen($file, "w");
	fwrite($fp, $data);
	fclose($fp);
}

/**
*  получение данных из кеша
*  работает по времени создания файла
*  
*  @param $key ключ
*  @param $time время в секундах (по-умолчанию 1 час)
*  @param $time если $time == false, то кеш отдается без учета времени
*  @param $r значение если нет кеша
*  
*  @return любой тип
*  
*  mso_get_cache('component_menu', 600);
*/
function mso_get_cache($key, $time = 3600, $r = false)
{
	// ключ кеша шифруется по адресу сайта
	$file = BASE_DIR . 'cache/' . strrev(md5($key . BASE_URL));
	
	if (!file_exists($file)) return $r; // нет кеша
	
	// время создания файла + жизнь кеша > текущего времени
	if ($time === false or (filemtime($file) + $time > time()))  // рабочий кеш
	{
		$data = file_get_contents($file);
		$data = @unserialize($data);
		return $data;
	}
	else
	{
		return $r; // недействительный кеш
	}
}

/**
*  функция возвращает текст Lorem Ipsum 
*  
*  @param $count количество слов
*  @param $color если указан $color, то текст обрамляется в <span> с color: $color
*  
*  @return string
*/
function mso_lorem($count = 50, $color = false)
{
	$LoremText = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vivamus vitae risus vitae lorem iaculis placerat. Aliquam sit amet felis. Etiam congue. Donec risus risus, pretium ac, tincidunt eu, tempor eu, quam. Morbi blandit mollis magna. Suspendisse eu tortor. Donec vitae felis nec ligula blandit rhoncus. Ut a pede ac neque mattis facilisis. Nulla nunc ipsum, sodales vitae, hendrerit non, imperdiet ac, ante. Morbi sit amet mi. Ut magna. Curabitur id est. Nulla velit. Sed consectetuer sodales justo. Aliquam dictum gravida libero. Sed eu turpis. Nunc id lorem. Aenean consequat tempor mi. Phasellus in neque. Nunc fermentum convallis ligula. Suspendisse in nulla. Nunc eu ipsum tincidunt risus pellentesque fringilla. Integer iaculis pharetra eros. Nam ut sapien quis arcu ullamcorper cursus. Vestibulum tempor nisi rhoncus eros. Sed iaculis ultricies tellus. Cras pellentesque erat eu urna. Cras malesuada. Quisque congue ultricies neque. Nullam a nisl. Sed convallis turpis a ante. Morbi eu justo sed tortor euismod porttitor. Aenean ut lacus. Maecenas nibh eros, dapibus at, pellentesque in, auctor a, enim. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam congue pede a ipsum. Sed libero quam, sodales eget, venenatis non, cursus vel, velit. In vulputate. In vehicula. Aenean quam mauris, vehicula non, suscipit at, venenatis sed, arcu. Etiam ornare fermentum felis. Donec ligula metus, placerat quis, blandit at, congue molestie, ante. Donec viverra nibh et dolor. Sed elementum, nunc ac gravida pulvinar, libero ligula vestibulum urna, eget luctus eros ipsum ut velit. Vestibulum at diam. Suspendisse hendrerit. Sed facilisis libero pretium nisl. Morbi eget urna ut mi egestas aliquet. Donec interdum, urna eget semper ultrices, nibh sapien laoreet massa, at laoreet nulla metus sit amet nunc. In augue. Etiam sit amet sapien. Aliquam nulla mi, tincidunt a, ullamcorper pharetra, mollis eu, purus. Suspendisse auctor nunc a dolor. Donec elit diam, fringilla nec, cursus a, dapibus ut, justo. Maecenas rhoncus lacinia mi. Sed tempus leo in risus. Quisque vitae est. Integer eu mi vel justo lacinia posuere. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec pretium auctor mauris. Cras at risus. Vestibulum ligula purus, venenatis varius, tincidunt aliquam, volutpat ut, felis. In nulla. Suspendisse magna. Fusce ac tortor. Morbi semper hendrerit purus. Donec scelerisque erat quis magna. Vivamus interdum metus at tellus. Nam molestie suscipit arcu. Sed sed leo non sapien lobortis gravida. Mauris ultricies imperdiet lacus. Maecenas semper sapien in mauris. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc euismod odio eget lectus. Vestibulum nonummy pharetra eros. Donec semper venenatis sapien. Phasellus scelerisque lectus quis tortor. Quisque turpis. Etiam rutrum metus eget nisi. Morbi varius ligula id elit. Ut augue. Nulla arcu est, rhoncus non, eleifend ut, imperdiet vel, magna. Sed pretium pulvinar augue. Sed sit amet nulla eget lacus viverra sollicitudin. Nulla facilisi. Proin sed ipsum vel lacus faucibus dignissim. Nulla purus. Nullam sapien elit, elementum eget, consequat vitae, vehicula cursus, pede. Sed quis leo. Praesent tincidunt convallis ligula. Sed purus eros, malesuada eget, posuere a, convallis suscipit, tellus. Proin tincidunt. Suspendisse leo. Suspendisse risus nisi, hendrerit in, ullamcorper id, porta in, pede. Maecenas lectus mi, congue vitae, ullamcorper vitae, bibendum sit amet, dui. Ut volutpat, nibh scelerisque malesuada bibendum, ipsum felis elementum lacus, nec pretium libero neque ut elit. Duis enim. Fusce arcu nulla, sodales eget, rhoncus sed, fermentum a, erat. Donec vitae mi. Duis sed nunc a justo egestas tincidunt. Morbi elit. Morbi venenatis fermentum erat. Cras purus orci, imperdiet a, sodales vel, aliquet at, quam. Etiam erat diam, ornare a, nonummy ut, accumsan non, felis. Fusce dignissim. Ut in ligula vitae risus varius viverra. Aenean elit diam, dapibus et, imperdiet in, suscipit at, felis. Curabitur vitae nunc ac mauris tincidunt posuere. Morbi id tortor. Nam sagittis. Sed dolor. Nulla imperdiet magna et lectus. Vivamus sapien diam, condimentum at, ultricies nec, vestibulum sit amet, pede. Nunc non orci vel magna lacinia sodales. In ac nunc vel mauris pharetra pharetra.";
	
	// перетусуем предложения
	$ar = explode('.', $LoremText);
	shuffle($ar);
	$LoremText = implode('.', $ar);
	$words = explode(' ', $LoremText);
	
	if (count($words) > $count) 
		$text = implode(' ', array_slice($words, 0, $count));
	else 
		$text = $LoremText;
	
	$text = trim($text);
	
	if ($color) $text = '<span style="color: ' . $color . '">' . $text . '</span>';
	
	return $text;
}

/**
*  преобразует php-массив в js-массив свойств («свойство: значение,») с учетом:
*  true, false, [], {}, function, чисел и строк
*  !!! null следует задавать как строчку 'null' (конфликт синтаксиса js с php)
*  
*  @param $a массив
*  @param $def массив дефолтных значений
*  @param $ignore игнорируемые ключи (не включать в результирующий)
*  @param $nr если $nr = true, то каждое свойство в отдельной строке (красота)
*  
*  @return string
*  
*  если задан массив $def, то в результат отдается только те элементы, 
*  которые отличаются от такого же элемента (по ключу-значению) $def
*/
function mso_array_php_to_js($a, $def = array(), $ignore = array('element', 'load_css'), $nr = false)
{
	$out = '';
	
	// преобразуем элементы массива в ключи
	if ($ignore) $ignore = array_fill_keys($ignore, 'putin_huilo');
	
	foreach ($a as $key => $val)
	{
		
		if (array_key_exists($key, $ignore)) continue; // игнорируем указанные
		
		if ($def and isset($def[$key]) and $val === $def[$key]) continue; // игнорируем дефолтные
		
		if ($val === false) $out .= $key . ': false';  // false
		elseif ($val === true) $out .= $key . ': true'; //true
		elseif ($val === 'null') $out .= $key . ': null'; //null
		elseif (is_numeric($val)) $out .= $key . ': ' . $val; // число
		elseif (strpos($val, '[') === 0) $out .= $key . ': ' . $val; // [ ... ] 
		elseif (strpos($val, '{') === 0) $out .= $key . ': ' . $val; // { ... } 
		elseif (strpos($val, '$(') === 0) $out .= $key . ': ' . $val; // $(... 
		elseif (strpos($val, 'function') === 0) $out .= $key . ': ' . $val; // function 
		else $out .= $key . ': \'' . $val . '\''; // строка
		
		$out .= ',';
		
		if ($nr) $out .= NR; // перенос
	}

	return $out;
}


/**
*  Формирование строчки html-кода js-скрипта (jQuery)
*  <script>$(document).ready(function(){ $('ЭЛЕМЕНТ').ФУНКЦИЯ({ОПЦИИ})})</script>
*  
*  @param $element — элемент
*  @param $function — функция
*  @param $options - опции от функции mso_array_php_to_js
*  
*  @return string
*/
function mso_jsscript($element, $function, $options, $do = '', $posle = '')
{
	return "<script>$(document).ready(function(){" . $do . "\$('" . $element . "')." . $function . "({" . $options . "})" . $posle . "})</script>";
}

/**
*  функция преобразования #-цвета в массив RGB
*  
*  @param $color цвет в виде #RRGGBB
*  
*  @return array
*/
function mso_hex2rgb($color)
{
	$color = str_replace('#', '', $color);
	
	if ($color == 'rand')
	{
		$arr = array(
			"red" => rand(1, 255),
			"green" => rand(1, 255),
			"blue" => rand(1, 255)
			);
	}
	else
	{
		$int = hexdec($color);
	
		$arr = array(
			"red" => 0xFF & ($int >> 0x10),
			"green" => 0xFF & ($int >> 0x8),
			"blue" => 0xFF & $int
			);
	}
	
	return $arr;
}

/**
*  создание заглушки holder для <IMG>
*  цвет задавать как в HTML в полном формате #RRGGBB
*  если цвет = rand то он формируется случайным образом
*  текст только английский (кодировка latin2)
*  если $text = true, то выводится размер изображения ШШхВВ
*  
*  @param $width ширина
*  @param $height высота
*  @param $text текст
*  @param $background_color цвет фона
*  @param $text_color цвет текста
*  @param $font_size размер шрифта от 1 до 5
*  
*  @return string
*  
*  <img src="<?= mso_holder() ? >" -> в формате data:image/png;base64, 
*  mso_holder(250, 80)
*  mso_holder(300, 50, 'My text', '#660000', '#FFFFFF')
*  mso_holder(600, 400, 'Slide', 'rand', 'rand')
*/
function mso_holder($width = 100, $height = 100, $text = true, $background_color = '#CCCCCC', $text_color = '#777777', $font_size = 5)
{
	$im = @imagecreate($width, $height) or die("Cannot initialize new GD image stream");
	
	$color = mso_hex2rgb($background_color);
	$bg = imagecolorallocate($im, $color['red'], $color['green'], $color['blue']);
	
	$color = mso_hex2rgb($text_color);
	$tc = imagecolorallocate($im, $color['red'], $color['green'], $color['blue']);
	
	if ($text)
	{
		if ($text === true) $text = $width . 'x' . $height;
		
		$center_x = ceil( ( imagesx($im) - ( ImageFontWidth($font_size) * mb_strlen($text) ) ) / 2 );
		$center_y = ceil( ( ( imagesy($im) - ( ImageFontHeight($font_size) ) ) / 2));
		
		imagestring($im, $font_size, $center_x, $center_y,  $text, $tc);
	}
	
	ob_start();
	imagepng($im);
	$src = 'data:image/png;base64,' . base64_encode(ob_get_contents());
	ob_end_clean();
	
	imagedestroy($im);
	
	return $src;
}

/**
*  возвращает массив структуры всех pages
*  включается title description keywords date dir url, а также массива $DATA в variables.php
*  date — время создания файла text.php (или явно заданное в $DATA['date'])
*  
*  @param $include — включить только указанные страницы
*  @param $exclude — исключить указанные страницы
*  @param $dir — основной каталог, если не указано то это PAGES_DIR
*  @param $url — основной http-путь, если не указано то это BASE_URL
*  
*  @return array
*  
*  [home] => Array (
*  		   // обязательные
*          [page] => home
*          [title] => Blog
*          [description] => Best page
*          [keywords] => 
*          [date] => 2014-10-22 12:56
*  		   [dir] => путь к странице
*  		   [url] => http-адрес страницы
*          
*          // все что задано в $DATA
*          [menu_name] => home
*          [menu_class] => icon star
*          [menu_order] => 23
*          [cat] => news, blog
*          [tag] => first, second
*   )
*/
function mso_pages_data($include = array(), $exclude = array(), $dir = false, $url = false)
{
	static $cache = array();
	
	// кеш хранится как массив с ключам = входящим параметрам
	$cache_key = md5(serialize($include) . serialize($exclude) . serialize($dir) .serialize($url));
	
	// уже получали данные, отдаем результат
	if (isset($cache[$cache_key])) return $cache[$cache_key];
	else
	{
		// возможно есть данные в файловом кеше
		if ($out = mso_get_cache('pages_data' . $cache_key, 3600))
		{
			$cache[$cache_key] = $out; // статичный кеш
			return $out;
		}
	}

	$out = array();
	
	// путь на сервере
	if ($dir === false) $dir = PAGES_DIR;
	
	// url-путь 
	if ($url === false) $url = BASE_URL;
	
	$pages = mso_get_dirs($dir, $exclude, 'variables.php');

	if ($pages)
	{
		if ($include) $pages = array_intersect($include, $pages);
	
		// для вложенных страниц добавляем префикс равный отличию от PAGES_DIR
		// pages/about => about
		// pages/blog/about => blog/about
		
		$prefix = str_replace(PAGES_DIR, '', $dir);
		
		foreach ($pages as $page)
		{
			$page_k = $prefix . $page;
		
			if ($page == HOME_PAGE)
				$out[$page_k] = array('page' => '/'); // это главная
			else
				$out[$page_k] = array('page' => $page_k);
				
			// обнуляем данные
			$TITLE = '';
			$META = array();
			$DATA = array();
			
			// считываем данные
			require($dir . $page . '/variables.php');
			
			$out[$page_k]['title'] = $TITLE;
			$out[$page_k]['description'] = isset($META['description']) ? $META['description'] : '';
			$out[$page_k]['keywords'] = isset($META['keywords']) ? $META['keywords'] : '';
			
			// дата создания text.php
			$out[$page_k]['date'] = date('Y-m-d H:i', filemtime($dir . $page . '/text.php'));
			
			// путь на сервере
			$out[$page_k]['dir'] = $dir . $page;
			
			// url
			$out[$page_k]['url'] = $url . $page;
			
			if (isset($DATA)) $out[$page_k] = array_merge($out[$page_k], $DATA);
		}
	}
	
	$cache[$cache_key] = $out; // статичный кеш
	
	mso_add_cache('pages_data' . $cache_key, $out); // файловый
	
	return $out;
}

/**
*  возвращает массив опций, заданный в файле
*  опции задаются как двумерный массив переменной $DATA
*  $DATA[key1] = array();
*  $DATA[key2] = array();
*  $DATA[key3] = array();
*  
*  @param $file файл для загрузки 
*  @param $def обязательные поля в каждом элементе
*  
*  @return array
*  
*  В SET_DIR . 'cat.php':
*  $DATA['cat']['news'] = array('name' => 'Новости', 'description' => 'Новости сайта');
*  $DATA['cat']['blog'] = array('name' => 'Блог');
*  
*  Использование:
*  $cat = mso_load_data(SET_DIR . 'cat.php', array('name'=>'', 'description'=>''));
*  pr($cat);
*/
function mso_load_data($file, $def = array())
{
	$out = array();
	
	if ($fn = mso_fe($file))
	{
		require($fn);
		
		if (isset($DATA)) $out = $DATA;
			
		if ($out and $def)
		{
			foreach($out as $key=>$val)
			{
				$out[$key] = array_merge($def, $out[$key]);
			}
		}
	}
	
	return $out;
}

/**
*  Функция использует глобальный одномерный массив который используется 
*  для получения значения указанного ключа $key
*  Если в массиве ключ не определён, то используется значение $default
*  если $array = true, то возвращаем значение ключа массива $key[$default]
*  
*  
*  @param $key ключ
*  @param $default значение по-умолчанию
*  @param $array возвратить значение массива ключа
*  
*  @return string
*  
*  см. примеры к mso_set_val()
*/
function mso_get_val($key = '', $default = '', $array = false)
{
	global $MSO;
	
	// нет такого массива, создаём
	if (!isset($MSO['key_options'])) 
	{
		$MSO['key_options'] = array();
		return $default;
	}
	
	if ($array !== false and $default and isset($MSO['key_options'][$key][$default]))
		return $MSO['key_options'][$key][$default]; 
	else
		// возвращаем значение или дефаулт
		return (isset($MSO['key_options'][$key])) ? $MSO['key_options'][$key] :	$default; 
}

/**
*  Функция обратная mso_get_val() - задаёт для ключа $key значение $val 
*  если $val_val == null, присваиваем всему $key значение $val
*  если $val_val != null, $val - это ключ массива
*  
*  
*  @param $key ключ
*  @param $val значение
*  @param $val_val 
*  
*  mso_set_val('type_home', 'cache_time');
* 		[type_home]=>'cache_time'
* 
*  mso_set_val('type_home', 'cache_time', 900); 
*  mso_set_val('type_home', 'cache_limit', 7); 
* 		[type_home] => Array
* 		(
*             [cache_time] => 900
*             [cache_limit] => 7
* 		)
*/
function mso_set_val($key, $val, $val_val = null)
{
	global $MSO;
	
	// нет массива, создаём
	if (!isset($MSO['key_options'])) $MSO['key_options'] = array();

	if ($val_val !== null)
		$MSO['key_options'][$key][$val] = $val_val;
	else
		$MSO['key_options'][$key] = $val; // записали значение
}

/**
*  Функция удаляет ключ $key
*  
*  @param $key ключ
*  
*/
function mso_unset_val($key)
{
	global $MSO;
	
	if (isset($MSO['key_options'][$key])) unset($MSO['key_options'][$key]);
}

/**
*  преобразовать строку с фрагментами, разделенных запятыми, в массив
*  
*  @param $s строка
*  @param $probel пробел тоже может быть разделителем
*  @param $unique удалить дубли
*  
*  @return string
*/
function mso_explode($s, $probel = false, $unique = true)
{
	if ($probel)
	{
		$s = trim( str_replace('  ', ',', $s) );
		$s = trim( str_replace(' ', ',', $s) );
	}

	$s = trim( str_replace(',,', ',', $s) );
	$s = explode(',', trim($s));
	
	if ($unique) $s = array_unique($s);

	$out = array();
	
	foreach ($s as $key => $val)
	{
		if (trim($val)) $out[] = trim($val);
	}

	if ($unique) $out = array_unique($out);

	return $out;
}

/**
*  Система событий
*  
*  @param $event — событие
*  @param $ARGS — массив аргументов
*  
*  Событие — это файл, функция или компонент
*  
*  $EVENT['событие'] = array(
*  		'file' => 'файл'
*  	or
*  		'function' => 'функция'
*  	or
*  		'component' => 'компонент'
*   or
*  		'val' => 'ключ для mso_get_val()'
*   or
*  		'text' => 'любой текст'
*  
*  		'options' => array(опции, если есть)
*  );
*  
*  Sample:
*  
*  In variables.php 
*  		$VAR['event'] = SET_DIR . 'event.php';
*  
*  In text.php
* 		mso_event('layout/top')
*  		mso_event('next-prev')
*  		mso_event('pagination', array(другие опции))
*  
*  In SET_DIR/event.php
*  		$EVENT['layout/top'] = array('file' => SET_DIR . 'layout/top.php');
*  		$EVENT['next-prev'] = array('file' => SET_DIR . 'next-prev.php');
*		$EVENT['pagination'] = array('component' => 'jpaginate', 'options' => array(опции));
*/
function mso_event($event, $args = array())
{
	global $VAR;
	
	static $ecache = array(); // кеш для файлов $VAR['event']
	
	
	if (!$VAR['event']) return; // не задан файл event
	
	// проверим статичный кеш
	if (isset($ecache[$VAR['event']])) 
		$EVENT = $ecache[$VAR['event']];
	else
	{
		if (!file_exists($VAR['event'])) return; // нет файла
		
		require($VAR['event']); // подключили файл
				
		if (isset($EVENT)) // в нём должен быть массив $EVENT
			$ecache[$VAR['event']] = $EVENT; // сохраним в кеше
		else 
			return;
	}
	
	// теперь всегда есть $EVENT

	if (isset($EVENT[$event])) // в нём должен быть массив $EVENT['событие']
	{
		// аргументы могут быть заданы как при вызове mso_event, так и в $EVENT
		// приоритет в mso_event
		$OPTIONS = $args;
		
		if (isset($EVENT[$event]['options'])) // могут быть опции в $EVENT
			$OPTIONS = $EVENT[$event]['options'];
		
		if (isset($EVENT[$event]['file'])) // может быть задан подключаемый файл
		{
			// в файле будет доступна переменная $OPTIONS
			if ($fn = mso_fe($EVENT[$event]['file'])) require($fn); 
		}
		elseif (isset($EVENT[$event]['function'])) // может быть указана функция
		{
			$fu = $EVENT[$event]['function'];
			if (function_exists($fu)) return $fu($OPTIONS);
		}
		elseif (isset($EVENT[$event]['component'])) // может быть указан компонент
		{
			mso_component($EVENT[$event]['component'], $OPTIONS);
		}
		elseif (isset($EVENT[$event]['val'])) // может быть указан val
		{
			return mso_get_val($EVENT[$event]['val']);
		}
		elseif (isset($EVENT[$event]['text'])) // может быть указан text
		{
			return $EVENT[$event]['text'];
		}
		
	}
}

/**
*  Возвращает snippet из каталога /snippets/
*  Каждый snippet в отдельном php-файле
*  Файлы можно размещать в кодкаталогах
*  
*  $snippets — имя сниппета
*  $return_content = true — возвращать контент
*  $return_content = false — только подключить через require
*  
*  Sample:
*  	В /snippets/header.php
*  		<?= mso_snippet('header') ?>
*  	
*  	В /snippets/home/top.php
*  		echo mso_snippet('home/top');
*  
*  @return string
*/
function mso_snippet($snippet = '', $return_content = true) {
	
	if (!$snippet) return;
	
	return mso_fr($snippet . '.php', SNIPPETS_DIR, $return_content);
}

#end of file