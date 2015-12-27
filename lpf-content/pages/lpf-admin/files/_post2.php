<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(PAGES_DIR . CURRENT_PAGE_ROOT . '/functions.php');

if ($post = mso_check_post('load_files', 'dir'))
{
	if (!_auth()) return 'STOP';
	
	$_dr = $post['dir'];
	
	if (!is_dir(BASEPATH . $_dr)) die('no exist updir');
	
	// echo $_dr;
	
	$d = BASEPATH . $_dr;
	
	$files = mso_get_path_files($d, '', true, array('jpg', 'jpeg', 'png', 'gif', 'ico', 'svg', 'php', 'txt', 'css', 'less', 'js', 'html', 'htm', 'ini', 'sass', 'scss', 'zip', 'gz', 'rar', 'pdf'));
	
	
	if (!$files)
	{
		echo 'Files not found...';
	}
	else
	{
		/*
		типа сортировка по расширению
		$files = array_map('strrev', $files);
		sort($files);
		$files = array_map('strrev', $files);
		*/
		// pr($files);
		
		// форма Для удаления выбранных файлов
		// отправка идет по POST с обычной перезагрузкой, поскольку
		// нужно будет полностью обновить страницу
		echo '<form action="" method="POST"><ul class="out-list mar20-b">';
		
		$i = 1;
		foreach($files as $file)
		{
			$ext = mso_file_ext($file);
			$url = BASE_URL . $_dr . $file;
			
			$class = 'i-file-o';
			$lightbox = '';
			
			if (in_array($ext, array('jpg', 'jpeg', 'png', 'svg', 'gif'))) 
			{
				$class = 'i-file-image-o';
				$lightbox = ' data-lightbox="images" data-title="' . $file . '"';
			}
			
			elseif (in_array($ext, array('txt', 'ini'))) $class = 'i-file-text-o';
			
			elseif (in_array($ext, array('zip', 'gz', 'rar'))) $class = 'i-file-zip-o';
			
			elseif (in_array($ext, array('php', 'less', 'sass', 'scss', 'js', 'html', 'htm'))) $class = 'i-file-code-o';
			
			elseif (in_array($ext, array('pdf'))) $class = 'i-file-pdf-o';
			
			if(($i % 2) == 1) 
				$class_li = 'bg-gray100';
			else
				$class_li = '';
				
			$i++;
			
			echo '<li class="' . $class_li . ' pad10"><label><input type="checkbox" name="files[]" value="' . $file . '"> <i class="mar10-l ' . $class . '"></i> ' . $file . '</label> <a target="_blank" href="' . $url . '" class="t-gray"' .$lightbox . '>link</a></li>';
		}
		
		echo '</ul><input type="hidden" name="dir" value="' . $_dr . '"><button type="submit" name="delete_files" onClick="if(confirm(\'Delete files?\')) {return true;} else {return false;}">Delete select files</button></form>';
	}
	
	
	return 'STOP';
}

// удаление выбранных файлов
if ($post = mso_check_post('delete_files', 'files', 'dir'))
{
	if (!_auth()) return 'STOP';
	
	$_dr = $post['dir'];
	if (!is_dir(BASEPATH . $_dr)) die('no exist dir');
	
	$d = BASEPATH . $_dr;
	
	if ($post['files'])
	{
		foreach($post['files'] as $file)
		{
			if (file_exists($d . $file)) @unlink($d . $file);
		}
	}
	
	// редиректим сюда же
	header('Location: ' . mso_current_url(false, true));
	return 'STOP';
}

if ($post = mso_check_post('create_subdir', 'subdir', 'dir', 'upload_dir'))
{
	if (!_auth()) return 'STOP';
	
	// в $post['dir'] каталог относительно BASEPATH
	// а upload_dir нужен только для нового редиректа
	
	$_dr = $post['dir'];
	if (!is_dir(BASEPATH . $_dr)) die('no exist dir');
	
	$d = BASEPATH . $_dr;
	
	// вичищаем введенный каталог
	$subdir = _slug($post['subdir']);
	$subdir = str_replace('.', '', $subdir); 
	
	$new_redirect = false;
	
	if ($subdir)
	{
		// новый каталог только если его уже нет
		if (!is_dir(BASEPATH . $_dr . $subdir)) @mkdir(BASEPATH . $_dr . $subdir, 0777);

		// и тут же проверяем и ставим новый адрес для редиректа
		if (is_dir(BASEPATH . $_dr . $subdir)) 
		{
			$new_redirect = mso_current_url(false, true, true) . '?dir=' . base64_encode(str_replace($post['upload_dir'], '', $_dr . $subdir));
		}
	}
	
	if ($new_redirect)
		header('Location: ' .$new_redirect);
	else
		header('Location: ' . mso_current_url(false, true));
	
	return 'STOP';
}

# end of file