<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!mso_check_auth('<p class="t-center mar20"><a href="?login" class="button">Login</a></p>')) return;

?>

<div class="layout-center-wrap"><div class="layout-wrap"><?= mso_snippet('menu') ?></div></div>

<div class="layout-center-wrap"><div class="layout-wrap">

	<h2>Create a new page (or add new file)</h2>

	<form id="form_create_page" action="" method="POST">
		<input type="hidden" name="add_file" value="">
		<input type="hidden" name="add_dir" value="">
		
		<p><label>Folder for the new page: <input type="text" id="new_page" name="new_page" placeholder="name page..." value="" required></label> <button class="button pad5-tb" id="create_new_page" type="button" name="create_new_page" style="vertical-align: top;">Create (or add)</button> <span class="b-inline mar20-l t-red" id="result_check_page"></span></p>
		
		<p class="mar5-b">Add files:</p>
		
		<ul class="out-list mar30-l">
		
			<li><label><input type="checkbox" name="add_file" value="index.php" checked> index.php <span class="b-inline mar10-l t-gray">(main file)</span></label></li>
			
			<li class="mar20-t">Optional files:</li>
			
			<li class="mar10-t"><label><input type="checkbox" name="add_file" value="variables.php"> variables.php <span class="b-inline mar10-l t-gray">(variables for the page)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="functions.php"> functions.php <span class="b-inline mar10-l t-gray">(functions for the page)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="head.php"> head.php <span class="b-inline mar10-l t-gray">(additional data for the section HEAD)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="header.php"> header.php <span class="b-inline mar10-l t-gray">(site header)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="footer.php"> footer.php <span class="b-inline mar10-l t-gray">(footer)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="init.php"> init.php <span class="b-inline mar10-l t-gray">(special file: initialization)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="_server.php"> _server.php <span class="b-inline mar10-l t-gray">(special file)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="_post.php"> _post.php <span class="b-inline mar10-l t-gray">(special file: POST-data)</span></label></li>
			
			<li><label><input type="checkbox" name="add_file" value="_post2.php"> _post2.php <span class="b-inline mar10-l t-gray">(special file: POST-data)</span></label></li>
			
		</ul>
		
		<p class="mar30-t mar5-b">Add folders:</p>
		
		<ul class="out-list mar30-l">
			<li><label><input type="checkbox" name="add_dir" value="css"> css & style.css</label></li>
			
			<li><label><input type="checkbox" name="add_dir" value="js"> js & js/autoload & js/lazy & my.js</label></li>
			
		</ul>
		
	</form>
	
</div></div>

<script>
	var PHPVAR = {};
	PHPVAR.current_url = "<?= CURRENT_URL ?>";
</script>
