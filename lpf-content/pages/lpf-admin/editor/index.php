<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!mso_check_auth('<p class="t-center mar20"><a href="?login" class="button">Login</a></p>')) return;

?>
<div class="layout-center-wrap"><div class="layout-wrap">

<?= mso_snippet('menu') ?>

<?php 

$directory = _ss(LPF_CONTENT_DIR);
$r = new RecursiveDirectoryIterator($directory);

$files = _getFiles($r, 0, $directory);

if(strpos(PAGES_DIR, LPF_CONTENT_DIR) === false)
{
	$directory = _ss(PAGES_DIR);
	$r1 = new RecursiveDirectoryIterator($directory);
	$files1 = _getFiles($r1, 0, $directory);
	
	$files = array_merge($files, $files1);
}


$content_file = '';
$file_path = '';

$select = '<option value="" selected>-</option>';
foreach ($files as $file)
{
	if (strpos($file, '/' . CURRENT_PAGE_ROOT . '/') !== false) continue; // не выводим админку
	
	$class = 't-gray500';
	
	if (strpos($file, '.css') !== false) $class = 't-green';
	if (strpos($file, '.js') !== false) $class = 't-orange';
	if (strpos($file, '/index.php') !== false) $class = 't-black';
		
	if (strpos($file, 'optgroup') === false)
	{
		$select .= '<option value="' . base64_encode($file) . '" class="' . $class . '">' . $file . '</option>';
	}
	else
		$select .= $file;
}

?>

<p class="mar30-t">Select file <select id="select_file" class="w-auto"><?= $select ?></select> <button class="button b-hide-imp pad5-tb bg-blue400 hover-bg-red400" type="button" id="delete_file" style="vertical-align: top;" onClick="return(confirm('Delete this file?'))">Delete file</button> <span id="success"></span></p>

<form method="post" id="edit_form" action="">
	
	<label class="mar20-r t90"><input type="checkbox" id="panel-select"> Simple</label>
	
	<span id="panel-html">
		<span class="pad5 cursor-pointer bg-orange200 hover-bg-blue200 b-inline t90 w30px t-center" title="Bold" onClick="addText('<b>', '</b>', 'content');">B</span>&nbsp;
		<span class="italic pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Italic" onClick="addText('<i>', '</i>', 'content');">I</span>&nbsp;
		<span class="i-link icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Link" onClick="addText('<a href=&quot;&quot;>', '</a>', 'content');"></span>&nbsp;
		<span class="i-image icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Image" onClick="addText('<img src=&quot;&quot; width=&quot;&quot; height=&quot;&quot; alt=&quot;&quot; title=&quot;&quot;>', '', 'content');"></span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H1" onClick="addText('<h1>', '</h1>', 'content');">H1</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H2" onClick="addText('<h2>', '</h2>', 'content');">H2</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H3" onClick="addText('<h3>', '</h3>', 'content');">H3</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H4" onClick="addText('<h4>', '</h4>', 'content');">H4</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H5" onClick="addText('<h5>', '</h5>', 'content');">H5</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H6" onClick="addText('<h6>', '</h6>', 'content');">H6</span>&nbsp;
		<span class="i-list-ul icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="List" onClick="addText('<ul>', '</ul>', 'content');"></span>&nbsp;
		<span class="i-dot-circle-o icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="List elem" onClick="addText('<li>', '</li>', 'content');"></span>&nbsp;
		<span class="i-indent icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Blockquote" onClick="addText('<blockquote>', '</blockquote>', 'content');"></span>&nbsp;
	</span>
	<span id="panel-simple" class="b-hide">
		<span class="pad5 cursor-pointer bg-green200 hover-bg-blue200 b-inline t90 w30px t-center" title="P" onClick="addText('_ ', '\n', 'content');">P</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H1" onClick="addText('h1 ', '\n', 'content');">H1</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H2" onClick="addText('h2 ', '\n', 'content');">H2</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H3" onClick="addText('h3 ', '\n', 'content');">H3</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H4" onClick="addText('h4 ', '\n', 'content');">H4</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H5" onClick="addText('h5 ', '\n', 'content');">H5</span>&nbsp;
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="H6" onClick="addText('h6 ', '\n', 'content');">H6</span>&nbsp;
		
		<span class="i-list-ul icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="List" onClick="addText('\nul\n* ', '\n/ul\n', 'content');"></span>&nbsp;
		<span class="i-dot-circle-o icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="List elem" onClick="addText('* ', '', 'content');"></span>&nbsp;
		
		<span class="i-indent icon0 pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Blockquote" onClick="addText('bq\n', '\n/bq\n', 'content');"></span>&nbsp;
		
		<span class="italic pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Italic" onClick="addText('__', '__', 'content');">I</span>&nbsp;
		
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Bold" onClick="addText('**', '**', 'content');">B</span>&nbsp;
		
		<span class="pad5 cursor-pointer bg-gray200 hover-bg-blue200 b-inline t90 w30px t-center" title="Code" onClick="addText('@', '@', 'content');">@</span>&nbsp;
		
	</span>
	
	<textarea name="content" id="content" class="w100 h500px bg-gray50 mar5-t"><?= $content_file ?></textarea>
	<input type="hidden" id="file_path" name="file_path" value="<?= $file_path ?>">
	<p><button id="b-save" class="button" type="submit">✔ Save</button></p>
</form>

<script>
	var PHPVAR = {};
	PHPVAR.current_url = "<?= CURRENT_URL ?>";
</script>


</div></div>
