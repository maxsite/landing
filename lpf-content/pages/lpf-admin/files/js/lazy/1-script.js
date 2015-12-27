$("#select_up_dir").change(function() 
{
	var url = $(this).val(); 
	if (url) url = '?dir=' + url; 
	window.location = PHPVAR.current_url + url; 
});

function update_file_list()
{
	// console.log(PHPVAR);
	$.post(
		PHPVAR.current_url,
		{
			load_files: "",
			dir: PHPVAR.dir
		},
		function(data)
		{
			$("#all_files_result").html(data);
		}
	);
}

update_file_list();
