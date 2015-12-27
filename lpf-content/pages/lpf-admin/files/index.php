<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!mso_check_auth('<p class="t-center mar20"><a href="?login" class="button">Login</a></p>')) return;

// адрес загрузки относительно корня сайта
$upload_dir =  'uploads/';

// разрешенные для зугрузки типы файлов
$upload_ext = 'txt|gif|jpg|jpeg|png|svg|html|htm|css|js|zip';

// $upload_ext = 'mp3|gif|jpg|jpeg|png|svg|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz';

// создадим каталог загрузки если его нет
if (!is_dir(BASEPATH . $upload_dir)) @mkdir(BASEPATH . $upload_dir, 0777);

// строим список подкаталогов
$directory = BASE_DIR . $upload_dir;
$directory = str_replace('\\', '/', $directory);

// все подкаталоги
$dirs = _getSubDirs($directory);
// pr($dirs);

// текущий подкаталог в URL указывается относительно /BASE_DIR/$upload_dir/ в base64
// lpf-admin/files?dir=dHdv  -> dHdv
$req_dir = mso_url_request(false, 'dir', true);
$sub_dir = @base64_decode($req_dir);

// нет такого подкаталога — обнуляем get-запрос
if (!in_array($sub_dir, $dirs)) $req_dir = $sub_dir = '';

// $current_url = PAGES_URL . CURRENT_PAGE_ROOT;

// для передачи в аякс-запрос каталог должен завершаться /
if ($sub_dir) $sub_dir .= '/';

// pr($sub_dir);
// pr($req_dir);

// корневой upload_dir
$select = '<option value="" selected></option>';

foreach ($dirs as $dir)
{
	$val = base64_encode($dir);
	$sel = ($val == $req_dir) ? ' selected' : '';
	$select .= '<option value="' . $val . '"' . $sel . '>' . $dir . '</option>';
}

?>

<div class="layout-center-wrap"><div class="layout-wrap"><?= mso_snippet('menu') ?></div></div>

<div class="layout-center-wrap"><div class="layout-wrap">

	<div class="flex">
	
		<div class="">Select dir: <b><?= $upload_dir ?></b> <select id="select_up_dir" class="w-auto"><?= $select ?></select></div>

	<div>
		<form action="" method="POST">
			<input type="hidden" name="dir" value="<?= $upload_dir . $sub_dir ?>">
			<input type="hidden" name="upload_dir" value="<?= $upload_dir ?>">
			<span>Create subdir</span> <input type="text" name="subdir" placeholder="name subdir..." value=""> <button type="submit" name="create_subdir">Create</button>
		</form>
	</div>
	
	</div>
	
</div></div>

<div class="layout-center-wrap"><div class="layout-wrap">

<hr>

<h3>File Upload</h3>

<form>
	<input type="hidden" id="upload_max_file_size" name="upload_max_file_size" value="20000000">
	<input type="hidden" id="upload_action" name ="upload_action" value="">
	<input type="hidden" id="upload_ext" name ="upload_ext" value="<?= $upload_ext ?>">
	<input type="hidden" id="upload_dir" name ="upload_dir" value="<?= $upload_dir . $sub_dir ?>">
	<div>
		<div id="upload_filedrag">or drop files here</div>
		<input type="file" id="upload_fileselect" name="upload_fileselect[]" multiple="multiple">
	</div>
</form>

<div id="upload_progress"></div>
<div class="pad20-tb mar10-tb" id="upload_messages"></div>

</div></div>


<!-- список файлов текущего каталога -->
<div class="layout-center-wrap"><div class="layout-wrap">
<h2>Files</h2>
<div id="all_files_result">Loading...</div>
</div></div>


<?php /* это php-переменные для js-скриптов */ ?>
<script>
	var PHPVAR = {};
	PHPVAR.current_url = "<?= CURRENT_URL ?>";
	PHPVAR.dir = "<?= $upload_dir . $sub_dir ?>";
</script>
