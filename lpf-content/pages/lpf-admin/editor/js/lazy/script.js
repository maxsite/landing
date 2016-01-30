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


function addText(t, t2, elem){
	var editor = document.getElementById(elem);
	
	if (document.selection) {
		editor.focus();
		sel = document.selection.createRange();
		sel.text = t + sel.text + t2;
		editor.focus();
	}
	else if (editor.selectionStart || editor.selectionStart == '0') {
		var startPos = editor.selectionStart;
		var endPos = editor.selectionEnd;
		var cursorPos = endPos;
		var scrollTop = editor.scrollTop;
		if (startPos != endPos) {
			editor.value = editor.value.substring(0, startPos)
						  + t
						  + editor.value.substring(startPos, endPos)
						  + t2
						  + editor.value.substring(endPos, editor.value.length);
			cursorPos = startPos + t.length
		}
		else {
			editor.value = editor.value.substring(0, startPos)
							  + t
							  + t2
							  + editor.value.substring(endPos, editor.value.length);
			cursorPos = startPos + t.length;
		}
		editor.focus();
		editor.selectionStart = cursorPos;
		editor.selectionEnd = cursorPos;
		editor.scrollTop = scrollTop;
	}
	else {
		editor.value += t + t2;
	}
}

$('#panel-select').change(function()
{
	if ($(this).prop("checked"))
	{
		$('#panel-html').hide();
		$('#panel-simple').show();
	}
	else
	{
		$('#panel-html').show();
		$('#panel-simple').hide();
	}
})