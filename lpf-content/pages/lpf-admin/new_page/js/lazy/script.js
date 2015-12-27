	
	function go()
	{
		if ($('#new_page').val() == '')
		{
			$("#result_check_page").html('Please enter the folder...');
			return;
		}
		
		$.post(
			PHPVAR.current_url,
			{
				check_page: $('#new_page').val()
			},
			function(data) 
			{
				if (data !== 'OK') 
				{
					$("#result_check_page").html(data);
				}
				else
				{
					$("#result_check_page").html(data);
					
					$.post(
						PHPVAR.current_url,
						{
							form_create_page: $('#form_create_page').serializeArray(),
						},
						function(data) 
						{
							$("#result_check_page").html(data);
						}
					);

				}
			}
		);
	}

	$('#form_create_page').submit(function() { 
		go();
		return false; 
	});

	$('#create_new_page').click(function(){
		go();
		return false;
	});
