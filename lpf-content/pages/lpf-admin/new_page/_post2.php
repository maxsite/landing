<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(PAGES_DIR . CURRENT_PAGE_ROOT . '/functions.php');

if ($post = mso_check_post('check_page'))
{
	if (!_auth()) return 'STOP';
	
	$check_page = _slug($post['check_page'], false); // слэш можно использовать для подкаталогов
	
	if (is_dir(PAGES_DIR . $check_page))
	{
		echo 'Page «' . $check_page . '» already exists';
	}
	else
	{
		echo 'OK';
	}
	
	return 'STOP';
}


if ($post = mso_check_post('form_create_page'))
{
	if (!_auth()) return;
	
	// pr($post['form_create_page']);
	
	// преобразуем входящий массив в обычный от формы
	// могут быть поля add_file
	// и new_page — новый каталог — если его нет, то вообще выходим с ошибкой
	// add_dir — может содержать подкаталоги css js fonts 
	// каждый элемент проверяем на верность — если что-то недопустимое, то игнорируем
	$in = array();
	
	foreach($post['form_create_page'] as $e)
	{
		if ($e['value'] and $e['name'])
		{
			if ($e['name'] == 'add_file' and in_array($e['value'], array(
					'index.php', 
					'variables.php', 
					'functions.php', 
					'head.php', 
					'header.php', 
					'footer.php', 
					'init.php', 
					'_server.php', 
					'_post.php', 
					'_post2.php'
					))) $in[$e['name']][] = $e['value'];
					
			if ($e['name'] == 'add_dir' and in_array($e['value'], array(
					'css', 
					'js'
					))) $in[$e['name']][] = $e['value'];
			
			if ($e['name'] == 'new_page') $in[$e['name']] = _slug($e['value'], false);
		}
	}
	
	// pr($in);
	
	// проверки
	if (!isset($in['new_page']))
	{
		echo 'ERROR: new_page (1)';
		return 'STOP';
	}
	
	if (!($in['new_page']))
	{
		echo 'ERROR: new_page (2)';
		return 'STOP';
	}
	
	if (is_dir(PAGES_DIR . $in['new_page']))
	{
		echo 'Page already exists (3)';
		return 'STOP';
	}
	
	// создаем каталог
	@mkdir(PAGES_DIR . $in['new_page'], 0777);
	
	// и сразу проверяем - если запрещено создание, то выходим с ошибкой
	if (!is_dir(PAGES_DIR . $in['new_page']))
	{
		echo 'Failed to create folder (4)';
		return 'STOP';
	}
	
	$new_page_dir = PAGES_DIR . $in['new_page'] . '/';
	
	// подкаталоги
	if (isset($in['add_dir']))
	{
		foreach($in['add_dir'] as $dir)
		{
			@mkdir(PAGES_DIR . $in['new_page'] . '/' . $dir, 0777);
			
			if ($dir == 'js')
			{
				@mkdir($new_page_dir . $dir . '/autoload', 0777);
				@mkdir($new_page_dir . $dir . '/lazy', 0777);
			}
			
			if ($dir == 'css')
			{
				@copy(CURRENT_PAGE_DIR . '/blanks/style.css.txt', $new_page_dir . 'css/style.css');
			}
		}
	}
	
	// файлы хрянятся в подкаталоге blanks с расширением .txt 
	// просто их копируем в каталог страницы
	if (isset($in['add_file']))
	{
		foreach($in['add_file'] as $file)
		{
			@copy(CURRENT_PAGE_DIR . '/blanks/' . $file . '.txt', $new_page_dir . $file);
		}
	}
	
	// возвращаем адрес новой страницы
	echo '<a class="t-green" href="' . BASE_URL . $in['new_page'] . '" target="_blank">OK: page «' . $in['new_page'] . '» created!</a>';
	
	
	return 'STOP';
}

# end of file