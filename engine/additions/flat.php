<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
	flat-опции
	
	Каждая опция в отдельном файле, имя которому определяется ключом
	
	require_once(ENGINE_DIR . 'additions/flat.php');

	$f = new Flat(SET_DIR . 'flat'); // каталог с данными
	
	or 
	
	$f = new Flat(SET_DIR . 'flat', 'salt123'); // каталог с данными и соль
	
	$f->add('ключ', 'данные'); // добавление опции
	
	$data = $f->get('ключ'); // получение опции
	
	$f->delete('ключ'); // удаление опции
	
*/

class Flat 
{
	protected $dir = false; // каталог с файлами
	protected $salt = '';   // дополнительная соль к ключу

	
	function __construct($dir, $salt = '')
	{
		if (!is_dir($dir) ) @mkdir($dir, 0777); // нет каталога, пробуем создать
		
		// нет каталога или он не для записи
		if (!is_dir($dir) or !is_writable($dir)) return false; 
		
		$this->dir = $dir . '/';
		$this->salt = $salt;
	}
	
	
	# добавляем данные 
	function add($key, $value)
	{
		if (!$this->dir) return false;
		
		// файл формируется из ключа
		$file = $this->dir . strrev(md5($key . $this->salt));
		
		if (!$fp = @fopen($file, 'wb')) return false; 

		flock($fp, LOCK_EX);
		fwrite($fp, serialize($value));
		flock($fp, LOCK_UN);
		fclose($fp);

		return true;
	}
	
	
	# получаем данные по ключу
	function get($key, $def_value = false)
	{
		if (!$this->dir) return false;
		
		$file = $this->dir . strrev(md5($key . $this->salt));
		
		if (file_exists($file))
		{
			$data = @unserialize(file_get_contents($file));
			
			return $data;
		}
		else
		{
			return $def_value;
		}
	}
	
	
	# удаление
	function delete($key)
	{
		if (!$this->dir) return false;

		$file = $this->dir . strrev(md5($key . $this->salt));

		if (file_exists($file))
		{
			@unlink($file);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
}

# end file