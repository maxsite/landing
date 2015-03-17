<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
	http-авторизация / форма логина в браузере
	
	Подключение в environment/config.php
	или в init.php страницы
	
	require_once(ENGINE_DIR . 'additions/auth.php');
	
*/


# авторизация: вход-выход
# На странице (произвольной page) авторизации
#	
#	В SET_DIR/auth-options.php
#
#		$OPTIONS['username'] = '1';
#		$OPTIONS['password'] = '2';
#		$OPTIONS['session'] = 'firstauthenticate'; // ключ сессии
#
#   	mso_auth(mso_load_options(SET_DIR . 'auth-options.php'));
#
#   	// можно вывысти пункты меню
#   	if (mso_get_val('auth')) 
#   			echo '<p><a href="?logout">выход</a></p>';
#   		else
#   			echo '<p><a href="?login">вход</a></p>';
#
#	Использование на любой другой странице
#		if (mso_is_auth(mso_load_options(SET_DIR . 'auth-options.php'))) 
#		{ ... есть авторизация ... }
#
function mso_auth($OPTIONS)
{
	// дефолтные опции
	$def_options = array(
		'username' => 'admin',
		'password' => 'admin',
		'session' => 'firstauthenticate', // ключ сессии
		'logout_link' => 'logout', // разлогиневание http://сайт/page?logout
		'login_link' => 'login', // разлогиневание http://сайт/page?login
	);

	// объединяем с переданными
	$OPTIONS = array_merge($def_options, $OPTIONS);
	
	$url_redirect = (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	
	// вход
	if (mso_url_request(false, $OPTIONS['login_link']))
	{
		if (!isset($_SESSION)) session_start();
		
		mso_auth_dialog($OPTIONS);
		
		if (mso_is_auth($OPTIONS)) 
		{
			$_SESSION[$OPTIONS['session']] = 1;
			mso_set_val('auth', true);
			if ($url_redirect) header('Location:' . $url_redirect);
		}
	}
	
	// выход
	if (mso_url_request(false, $OPTIONS['logout_link'])) 
	{
		if (!isset($_SESSION)) session_start();
		if (isset($_SESSION[$OPTIONS['session']])) unset($_SESSION[$OPTIONS['session']]);
		
		mso_set_val('auth', false);
		
		if ($url_redirect) header('Location:' . $url_redirect);
	}
	
	mso_set_val('auth', mso_is_auth($OPTIONS)); // сохраняем реальное значение авторизации
}


# форма логина в браузере
function mso_auth_dialog($OPTIONS) 
{
	if (!isset($_SERVER['PHP_AUTH_USER']))
	{
		header('WWW-Authenticate: Basic realm=""');
		header('HTTP/1.0 401 Unauthorized');
		// echo 'Cancel';
		exit;
	}
	else
	{
		// введены данные, проверяем
		if ( 
			isset($_SERVER['PHP_AUTH_USER'])
			and isset($_SERVER['PHP_AUTH_PW']) 
			and strcmp($_SERVER['PHP_AUTH_USER'], $OPTIONS['username']) == 0 
			and strcmp($_SERVER['PHP_AUTH_PW'], $OPTIONS['password']) == 0 
		)
		{
			if (!isset($_SESSION)) session_start();
			$_SESSION[$OPTIONS['session']] = 1; // ставим признак 
			mso_set_val('auth', true); // запоминаем
		}
		else
		{
			header('WWW-Authenticate: Basic realm=""');
			header('HTTP/1.0 401 Unauthorized');
			// echo 'Error login/password';
			exit;
		}
	}
}

# проверка залогированности с учетом сессии
# возвращает true — если логин и пароль верный
function mso_is_auth($OPTIONS)
{
	if (!isset($_SESSION)) session_start();

	if (
		!isset($_SERVER['PHP_AUTH_USER']) 
		|| strcmp($_SERVER['PHP_AUTH_USER'], $OPTIONS['username']) != 0 
		|| !isset($_SERVER['PHP_AUTH_PW']) 
		|| strcmp($_SERVER['PHP_AUTH_PW'], $OPTIONS['password']) != 0 
		|| !isset($_SESSION[$OPTIONS['session']]) 
		|| !$_SESSION[$OPTIONS['session']]
	)
		return false;
	else
		return true;
}


# end of file