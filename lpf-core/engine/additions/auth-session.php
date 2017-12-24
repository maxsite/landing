<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(с) Landing Page Framework (LPF), http://lpf.maxsite.com.ua/
	(c) MAX, http://maxsite.org/


Авторизация (общая) через php-сессии для любой страницы сайта
-------------------------------------------------------------

Всё в каталоге страницы. Можно вынести в общий lpf-content/config/functions.php

1. 	Файл auth/auth-options.php содержит опции

	$OPTIONS = array(
		'username' => 'login', // логин
		'password' => 'password', // пароль
	);
	
	или, если пользователей несколько
	
	$OPTIONS = array(
		'users' => array(
			'username1' => 'password1', // логин и пароль
			'username2' => 'password2', // логин и пароль
			'username3' => 'password3', // логин и пароль
			),
		
	);


2. Файл functions.php код:
	
	require_once(ENGINE_DIR . 'additions/auth-session.php');
	$auth_options = mso_load_options(CURRENT_PAGE_DIR . '/auth/auth-options.php');
	mso_auth_init($auth_options); // инициализация авторизации


3. В index.php страницы в начале (в параметре ссылка на логин)

	if (!mso_check_auth()) return;
	
	... текст доступный только после авторизации ...
		
	Ссылка на <a href="?logout">ВЫХОД</a>	

	
* Дополнительно в файле auth/auth-login-form.php можно разместить свою форму логина (см. mso_auth_form).


* В файле auth/auth-login-text.php можно разместить текст входа
	
	<p><a href="?login" class="button">Login</a></p>
	
* Или в опции login_text_out
	
	'login_text_out' => '<a href="?login">Вход</a>'
	
* Или указать при вызове mso_check_auth();
	
	if (!mso_check_auth('<a href="?login">Вход</a>')) return;

* В опциях можно указать текст ошибки
	
	'text_error' => 'Ошибка!',
	
* Можно указать текст ошибки в файле auth/auth-login-text-error.php 
  Он имеет приоритет перед опцией text_error

*/

# инициализация
function mso_auth_init($OPTIONS = true)
{
	global $MSO;
	
	// дефолтные опции
	$def_options = array(
		'username' => '', // логин
		'password' => '', // пароль
		
		'users' => false, // можно указать массивом array('user1'=>'pas1', 'user2'=>'pas2')
		
		'login_link' => 'login', // ссылка для залогирования http://сайт/page?login
		'logout_link' => 'logout', // ссылка для разлогирования http://сайт/page?logout
		
		'login_text' => CURRENT_PAGE_DIR . 'auth/auth-login-text.php', // файл текста логина
		'login_form' => CURRENT_PAGE_DIR . 'auth/auth-login-form.php', // файл формы
		
		'text_error' => 'Неверный логин/пароль', // текст при ошибке
		'login_text_out' => '<a href="?login">Вход</a>', // текст входа, если не указано иное
		
		// файл ошибки - имеет приоритет перед text_error
		'login_text_error' => CURRENT_PAGE_DIR . 'auth/auth-login-text-error.php', 
		
		// файлы просто подключаются при разных событиях
		'file_post' => CURRENT_PAGE_DIR . 'auth/auth-r-post.php', // POST
		'file_login_ok' => CURRENT_PAGE_DIR . 'auth/auth-r-login.php', //удачный вход
		'file_logout' => CURRENT_PAGE_DIR . 'auth/auth-r-logout.php', // выход
	);
	
	// если $OPTIONS === true, то загружаем из auth/auth-options.php текущей page
	if ($OPTIONS === true)
	{
		$OPTIONS = mso_load_options(CURRENT_PAGE_DIR . 'auth/auth-options.php');
	}

	// объединяем с переданными
	$OPTIONS = array_merge($def_options, $OPTIONS);
	
	// если не заданы логин/пароль, то всё рубим
	if (!$OPTIONS['username'] or !$OPTIONS['password'])
	{
		if (!is_array($OPTIONS['users']) and !$OPTIONS['users'])	
			die('Not specified username and password to login (mso_auth)');
	}
	
	$MSO['auth_login'] = ''; // результат функиции (что делать дальше)
	$MSO['auth_login_options'] = $OPTIONS; // все опции
	
	// все редиректы на эту же страницу без ?-get
	$url_redirect = mso_current_url(false, true, true);
	
	// вход
	if (mso_url_request(false, $OPTIONS['login_link']))
	{
		// если есть post, то проверяем данные
		if ($_POST 	
			and isset($_POST['flogin_user'])
			and isset($_POST['flogin_password'])
			and isset($_POST['flogin_submit']) 
			)
		{
			if (file_exists($OPTIONS['file_post'])) require($OPTIONS['file_post']);
			
			$u = $p = '';
			
			// если есть $OPTIONS['users'], то смотрим только его
			if (is_array($OPTIONS['users']) and $OPTIONS['users'])
			{
				// смотрим массив $OPTIONS['users']
				if (isset($OPTIONS['users'][$_POST['flogin_user']]))
				{
					// есть такой юзер в массиве
					$u = $_POST['flogin_user']; // юзер 
					$p = $OPTIONS['users'][$_POST['flogin_user']]; // его пароль
				}
			}
			else
			{
				// смотрим $OPTIONS['username'] и $OPTIONS['password'
				$u = $OPTIONS['username'];
				$p = $OPTIONS['password'];
			}
			
			if (!$u or !$p)
			{
				$MSO['auth_login'] = 'error_login_show_form';
			}
			else
			{
				// сравниваем логин и пароль
				if ( strcmp($_POST['flogin_user'], $u) == 0
					 and strcmp($_POST['flogin_password'], $p) == 0 )
				{
					// равно
					if (!isset($_SESSION)) session_start();
					
					$_SESSION['username'] = $u;
					$_SESSION['password'] = $p;
					
					if (file_exists($OPTIONS['file_login_ok'])) require($OPTIONS['file_login_ok']);
					
					// все ок!
					header('Location:' . $url_redirect);
				}
				else
				{
					// не равно
					$MSO['auth_login'] = 'error_login_show_form';
				}
			}
		}
		else
		{
			// нет post 
			// если уже есть залогиненость, то редиректим
			if (mso_is_auth($OPTIONS)) header('Location:' . $url_redirect);
			
			$MSO['auth_login'] = 'show_form';
		}
	}
	elseif (mso_url_request(false, $OPTIONS['logout_link'])) // ссылка на выход
	{
		if (!isset($_SESSION)) session_start();
		
		if (file_exists($OPTIONS['file_logout'])) require($OPTIONS['file_logout']);
		
		if (isset($_SESSION['username'])) unset($_SESSION['username']);
		if (isset($_SESSION['password'])) unset($_SESSION['password']);
		
		header('Location:' . $url_redirect);
	}
	
	// если нет авторизации, то выводим сслыку на ВХОД
	if (strpos($_SERVER['REQUEST_URI'], '?') === FALSE) 
	{
		if (!mso_is_auth($OPTIONS)) $MSO['auth_login'] = 'text_login';
	}
	
	if (mso_is_auth($OPTIONS))
		return $OPTIONS;
	else
		return false;
}


# обработка результата mso_auth_init()
function mso_check_auth($text_login = false) 
{
	global $MSO;
	
	$OPTIONS = $MSO['auth_login_options'];
	
	if (mso_is_auth($OPTIONS)) return true;
	
	if ($MSO['auth_login'] == 'text_login')
	{
		if ($text_login === false)
		{
			if ($OPTIONS['login_text'] and file_exists($OPTIONS['login_text'])) 
				require($OPTIONS['login_text']);
			else
			{
				if ($OPTIONS['login_text_out']) 
					echo $OPTIONS['login_text_out'];
				else
					echo '<a href="?login">Вход</a>';
			}
		}
		else
			if ($text_login) 
				echo $text_login;
			else
				echo $OPTIONS['login_text_out'];
		
		return false;
	}
	elseif ($MSO['auth_login'] == 'show_form')
	{
		if (file_exists($OPTIONS['login_form'])) 
			require($OPTIONS['login_form']);
		else
			mso_auth_form();
		
		return false;
	}
	elseif ($MSO['auth_login'] == 'error_login_show_form')
	{
		if (file_exists($OPTIONS['login_text_error']))
			require($OPTIONS['login_text_error']);
		else
			echo $OPTIONS['text_error'];
			
		if (file_exists($OPTIONS['login_form'])) 
			require($OPTIONS['login_form']);
		else
			mso_auth_form();
		
		return false;
	}
	
	return false;
}


# проверка залогированности
# возвращает true — если логин и пароль верный
function mso_is_auth($OPTIONS = true)
{
	// если $OPTIONS === true, то загружаем из auth/auth-options.php текущей page
	if ($OPTIONS === true)
	{
		$OPTIONS = mso_load_options(CURRENT_PAGE_DIR . 'auth/auth-options.php');
	}
	
	if (!isset($_SESSION)) session_start();
	
	
	if (isset($_SESSION['username']) and isset($_SESSION['password']))
	{
		$u = $p = '';

		if (is_array($OPTIONS['users']) and $OPTIONS['users'])
		{
			if (isset($OPTIONS['users'][$_SESSION['username']]))
			{
				$u = $_SESSION['username']; // юзер 
				$p = $OPTIONS['users'][$_SESSION['username']]; // его пароль
			}			
		}
		else
		{
			$u = $OPTIONS['username'];
			$p = $OPTIONS['password'];
		}

		if (!$u or !$p) return false;
		
		if (strcmp($_SESSION['username'], $u) === 0
			and	strcmp($_SESSION['password'], $p) === 0
		)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

# Стандартная форма если нет файла формы
function mso_auth_form() 
{
	echo '
<form method="post">
	<p><label>Логин<br><input type="text" value="" name="flogin_user"></label></p>
	<p><label>Пароль<br><input type="password" value="" name="flogin_password"></label></p>
	<p><button type="submit" name="flogin_submit">Вход</button></p>
</form>
';
}

# end of file