$('#b-save').fadeOut(0);

$('#select_file').change(function()
{
	var f = $("#select_file :selected").val();
	
	if (f)
	{
		$.post(PHPVAR.current_url, {file:f, load: 1},  function(response) 
		{
			$('#file_path').val(f);
			$('#content').val(response);
			$('#success').html('<span class="mar10-l t-green t130">✔</span> File upload');
			$('#success').show();
			$('#success').fadeOut(2000);
			$('#b-save').fadeOut(500);
		});
	}
})

$('#edit_form').submit(function()
{
	$.post(PHPVAR.current_url, $("#edit_form").serialize(),  function(response) 
	{
		$('#success').html(response);
		$('#success').fadeIn(300);
		$('#b-save').fadeOut(1000);
	});
	
	return false;
})

$('#content').keypress(function()
{
	if ($("#select_file :selected").val())
	{
		$('#b-save').fadeIn(300);
		$('#success').fadeOut(300);
	}
})

// CTRL+S in textarea —> save file
$("#content").keydown(function(eventObject)
{
	if (eventObject.ctrlKey && eventObject.which == 83) // CTRL+S
	{
		if ($("#select_file :selected").val() && $("#b-save").is(":visible"))
		{
			$('#edit_form').submit();
		}
		
		return false;
	}
});
