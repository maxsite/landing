<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once(ENGINE_DIR . 'additions/auth-session.php');

	if (!mso_auth('<p class="t-center mar20"><a href="?login" class="button">Login</a></p>')) return;

?>
<div class="mar20">

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
	if (strpos($file, 'optgroup') === false)
	{
		$select .= '<option value="' . base64_encode($file) . '">' . $file . '</option>';
	}
	else
		$select .= $file;
}

?>

<p class="mar30-t">Select file <select id="select_file" class="w-auto"><?= $select ?></select> <span id="success"></span></p>

<?php

echo '<form method="post" id="edit_form" action=""><textarea name="content" id="content" class="w100 h500px bg-gray50">' . $content_file . '</textarea><input type="hidden" id="file_path" name="file_path" value="' . $file_path . '"><p><button id="b-save" class="button" type="submit">✔ Save</button></p></form>';
		
$AJAX = CURRENT_URL;

echo <<<EOF
<script>
jQuery(function($) {
	$('#b-save').fadeOut(0);
	$('#select_file').change(function(){
		var f = $("#select_file :selected").val();
		if (f)
		{
			$.post("{$AJAX}", {file:f, load: 1},  function(response) {
				$('#file_path').val(f);
				$('#content').val(response);
				$('#success').html('<span class="mar10-l t-green t130">✔</span> File upload');
				$('#success').show();
				$('#success').fadeOut(2000);
				$('#b-save').fadeOut(500);
			});
		}
	})
	$('#edit_form').submit(function(){
		$.post("{$AJAX}", $("#edit_form").serialize(),  function(response) {
			$('#success').html(response);
			$('#success').show();
			$('#success').fadeOut(5000);
			$('#b-save').fadeOut(1000);
		});
		return false;
	})
	$('#content').keypress(function(){
		$('#b-save').fadeIn(1000);
	})
});
</script>

EOF;

?>

<p class="mar20-tb"><a href="?logout">Logout</a>  | <a href="<?= BASE_URL ?>">Home</a></p>

</div>