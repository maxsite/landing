<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	Различные функции для работы с $DATA страниц
	
	require_once(ENGINE_DIR . 'additions/pages-data.php');
	
*/

class PD 
{
	protected $pages; // полученные данные страниц
	
	// для mso_pages_data()
	protected $include; // включать только
	protected $exclude; // исключить
	protected $dir; // основной каталог страниц
	protected $url; // основной http-адрес
	protected $cache_time = 3600; // время кеша

	
	# по конструктору сразу получаем данные
	function __construct($dir = false, $url = false, $include = array(), $exclude = array())
	{
		$this->pages =  $this->load_pages($dir, $url, $include, $exclude);
	}
	
	# вернуть данные 
	function get_pages()
	{
		return $this->pages;
	}
	
	# добавить данные 
	function set_pages($pages)
	{
		$this->pages = $pages;
	}
	
	# добавить данные 
	function set_cache_time($cache_time)
	{
		$this->cache_time = $cache_time;
	}
	
	# соединить данные 
	function merge_pages($pages)
	{
		$this->pages = array_merge($this->pages, $pages);
	}
	
	# получить pages
	function load_pages($dir = false, $url = false, $include = array(), $exclude = array())
	{
		return mso_pages_data($include, $exclude, $dir, $url, $this->cache_time);
	}
	
	# поиск вхождения $find (строка через запятую) в $DATA[$key]
	# на выходе массив найденных страниц
	function find_key($find, $key)
	{
		if ($find === false) return false;
		
		$out = array();
		
		foreach($this->pages as $p) 
		{
			if (isset($p[$key]))
			{
				$ts = mso_explode($p[$key]);
			
				if (in_array($find, $ts) )
				{
					$out[$p['page']] = $p;
				}
			}
		}
		
		$this->pages = $out;
	}


	# получить данные $DATA ключа $key из всех pages
	# значение => количество
	function get_key($key)
	{
		$all = array();
		
		foreach($this->pages as $p) 
		{
			if (isset($p[$key]))
			{
				$ts = mso_explode($p[$key]);
				$all = array_merge ($all, $ts);
			}
		}
		
		natsort($all);
		
		$this->pages = array_count_values($all);
	}

	# найти все записи, у которых $key = true
	# $DATA['home'] = true;
	# data_find_key_bool('home');
	function find_key_bool($key)
	{
		$all = array();
		
		foreach($this->pages as $p) 
		{
			if (isset($p[$key]) and $p[$key])
			{
				$all[$p['page']] = $p;
			}
		}
		
		$this->pages = $all;
	}

	# пользовательская к mso_pages_data_sort_by_date()
	protected function _cmp_sort_by_date($a, $b) 
	{
		if ($a['date'] == $b['date']) return 0;
		
		return ($a['date'] > $b['date']) ? -1 : 1;
	}


	# отсортировать по полю date — новые выше
	function sort_by_date()
	{
		$pages = $this->pages;
		
		uasort($pages, array("PD", "_cmp_sort_by_date"));
		
		$this->pages = $pages;
	}

}

# end of file