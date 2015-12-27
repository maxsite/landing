<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!mso_check_auth('<p class="t-center mar20"><a href="?login" class="button">Login</a></p>')) return;

?>
<div class="layout-center-wrap"><div class="layout-wrap">

<?= mso_snippet('menu') ?>

<?php 

$directory = BASE_DIR . 'lpf-content/';
$directory = str_replace('\\', '/', $directory);

$r = new RecursiveDirectoryIterator($directory);

$files = _getFiles($r, 0, $directory);
$content_file = '';
$file_path = '';

$select = '<option value="" selected>-</option>';

foreach ($files as $file)
{
	if (strpos($file, '/' . CURRENT_PAGE_ROOT . '/') !== false) continue; // не выводим админку
	
	$class = 't-gray500';
	
	if (strpos($file, '.css') !== false) $class = 't-green';
	if (strpos($file, '.less') !== false) $class = 't-green';
	if (strpos($file, '.js') !== false) $class = 't-orange';
	if (strpos($file, '/index.php') !== false) $class = 'bold';
		
	if (strpos($file, 'optgroup') === false)
	{
		$select .= '<option value="' . base64_encode($file) . '" class="' . $class . '">' . $file . '</option>';
	}
	else
		$select .= $file;
}

?>

<p class="mar30-t">Select file <select id="select_file" class="w-auto"><?= $select ?></select> <span id="success"></span></p>

<form method="post" id="edit_form" action="">
	<textarea name="content" id="content" class="w100 h500px bg-gray50"><?= $content_file ?></textarea>
	<input type="hidden" id="file_path" name="file_path" value="<?= $file_path ?>">
	<p><button id="b-save" class="button" type="submit">✔ Save</button></p>
</form>

<script>
	var PHPVAR = {};
	PHPVAR.current_url = "<?= CURRENT_URL ?>";
</script>


</div></div>