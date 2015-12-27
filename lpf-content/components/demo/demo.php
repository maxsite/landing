<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Demo-component
*/

$def_options = array(
	'text' => 'Hello!',
	'class' => 't-red'
);

$OPTIONS = array_merge($def_options, $OPTIONS);

if ($OPTIONS['class'])
	echo '<span class="' . $OPTIONS['class'] . '">' . $OPTIONS['text'] . '</span>';
else
	echo $OPTIONS['text'];


# end of file