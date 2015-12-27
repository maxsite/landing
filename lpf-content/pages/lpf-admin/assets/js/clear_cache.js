$('#clear_cache').click(function(){
	$.post(
		PHPVAR.current_url,
		{
			clear_cache: '',
		},
		function(data) 
		{
			$('#result_clear_cache').show();
			$("#result_clear_cache").html(data);
			$('#result_clear_cache').fadeOut(5000);
		}
	);
});
