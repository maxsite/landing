<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


$OPTIONS = array(
	'text_error' => '<div class="w300px b-center t-center t-white bg-red pad10 mar10-tb">Incorect login/password</div>', // text error
	'login_form' => PAGES_DIR . CURRENT_PAGE_ROOT . '/auth/auth-login-form.php',
);

$OPTIONS['users'] = [
	['username' => 'admin', 'password' => 'admin'],
	['username' => 'admin1', 'password' => 'admin1']
];

# end of file