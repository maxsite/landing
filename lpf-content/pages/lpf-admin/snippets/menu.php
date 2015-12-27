<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$URL_ADMIN = BASE_URL . CURRENT_PAGE_ROOT;

?>
<p class="mar20-t mar30-b my-menu t-blue700 bg-blue100 hover-no-color">
	<span class="b-inline mar20-r pad5 / bg-blue400 t-yellow300 bold / links-no-color hover-no-color hover-no-underline" style="text-shadow: 1px 1px rgb(53, 127, 183), 2px 2px rgb(53, 127, 183), 3px 3px rgb(53, 127, 183), 4px 4px rgb(53, 127, 183);"><a title="Landing Page Framework" href="//lpf.maxsite.com.ua/">LPF</a></span>
	
	<a class="i-dashboard" href="<?= $URL_ADMIN ?>">Admin</a> 
	<a class="mar20-l i-edit" href="<?= $URL_ADMIN ?>/editor">Editor</a>
	<a class="mar20-l i-upload" href="<?= $URL_ADMIN ?>/files">Upload files</a> 
	<a class="mar20-l i-newspaper-o" href="<?= $URL_ADMIN ?>/new_page">New page</a> 

	<span class="b-inline b-right pad5 pad10-r">
		<a class="i-home" href="<?= BASE_URL ?>">Home</a> 
		<a class="mar20-l i-sign-out" href="?logout">Logout</a>
	</span>
</p>

<script> $(function () { changeClassOnCurrent(".my-menu a", "bold t-blue700", "active"); })</script>
