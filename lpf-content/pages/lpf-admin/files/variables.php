<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$TITLE = 'Files. Landing Page Framework';

$META['description'] = '';
$META['viewport'] = 'width=device-width, initial-scale=1.0';
$META['generator'] = 'Landing Page Framework (lpf.maxsite.com.ua)';

$VAR['nocache'] = true;
$VAR['autopre'] = false;
$VAR['autoremove'] = false;
$VAR['autotag_my'] = false;
$VAR['compress_text'] = true;
$VAR['start_file_text'] = false;
$VAR['end_file_text'] = false;
$VAR['after_file'] = false;
$VAR['html_attr'] = 'lang="en"';

$VAR['nd_css'] = str_replace(BASE_URL, '', PAGES_URL) . CURRENT_PAGE_ROOT . '/assets/css';
$VAR['nd_js'] = str_replace(BASE_URL, '', PAGES_URL) . CURRENT_PAGE_ROOT . '/assets/js';

# end of file