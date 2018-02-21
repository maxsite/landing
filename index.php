<?php
/*	
	(с) Landing Page Framework (LPF) — http://lpf.maxsite.com.ua/
	(c) MAX — http://maxsite.org/
	Crimea this Ukraine!
*/

define('BASEPATH', dirname(realpath(__FILE__)) . '/');

if (file_exists(BASEPATH . 'lpf-content/config/environment.php')) 
	require(BASEPATH . 'lpf-content/config/environment.php');
else 
	define('ENGINE_DIR',  BASEPATH . 'lpf-core/engine/');

require_once(ENGINE_DIR . 'start.php');

?><!DOCTYPE HTML><html<?= ($VAR['html_attr']) ? ' ' . $VAR['html_attr'] : '' ?>><head><meta charset="UTF-8"><title><?= $TITLE ?></title><?php 
	mso_meta();
	mso_head();
	if ($fn = mso_fe($VAR['head_file1'])) require($fn);
	if ($fn = mso_fe($VAR['head_file'])) require($fn);
?></head><body<?= ($VAR['body_attr']) ? ' ' . $VAR['body_attr'] : '' ?>><?php
	if ($fn = mso_fe($VAR['start_file1'])) require($fn);
	if ($fn = mso_fe($VAR['start_file'])) require($fn);
	mso_output_text();
	if ($fn = mso_fe($VAR['end_file1'])) require($fn);
	if ($fn = mso_fe($VAR['end_file'])) require($fn);
	if ($fn = mso_fe($VAR['after_file'])) require($fn);
	mso_stat_out();
	mso_generate_static_page();

# end of file