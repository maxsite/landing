<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!mso_check_auth('<p class="t-center mar20"><a href="?login" class="button">Login</a></p>')) return;

?>
<div class="layout-center-wrap"><div class="layout-wrap">

<?= mso_snippet('menu') ?>

</div></div>

<div class="layout-center-wrap"><div class="layout-wrap">


<div class="">
	<span>Landing Page Framework <b>v.<?= LPF_VERSION ?></b></span>
	<span id="clear_cache" class="mar30-l / bg-blue t-white pad5-tb pad10-rl / cursor-pointer / hover-bg-blue600">Clear cache</span><span class="mar10-l" id="result_clear_cache"></span>
	
</div>


<h3>All pages</h3>

<form action="" method="POST">
<ul class="out-list mar20-b">

<?php
$all_dirs = mso_directory_map(PAGES_DIR, true);

$i = 1;
$show_delete_button = 'b-hide';

foreach($all_dirs as $page)
{
	$class_li = (($i++ % 2) == 1) ? 'bg-gray100' : '';
	
	$url = ($page == 'home') ? '' : $page;
	
	if ($page == 'home' or $page == '404' or $page == 'lpf-admin' or $page == CURRENT_PAGE)
		$disabled = ' disabled';
	else
		$disabled = '';
	
	if (!$disabled and $show_delete_button) $show_delete_button = '';
		
	echo '<li class="' . $class_li . ' pad10"><label><input type="checkbox" name="page[]" value="' . $page . '"' . $disabled . '> <i class="mar10-l i-newspaper-o"></i> ' . $page . '</label> <a target="_blank" href="' . BASE_URL . $url . '" class="t-gray">link</a>';
}

?>
</ul>
<button class="<?= $show_delete_button ?> button" type="submit" name="delete_pages" onClick="return(confirm('Delete pages?'))">Delete select pages</button></form>
</div></div>

<script>
	var PHPVAR = {};
	PHPVAR.current_url = "<?= CURRENT_URL ?>";
</script>

<?php /*  стрипт подключаем отдельно, чтобы использовать lazy-каталог как общий всех страниц */ ?>
<?= mso_load_script(CURRENT_PAGE_URL . 'assets/js/clear_cache.js') ?>
