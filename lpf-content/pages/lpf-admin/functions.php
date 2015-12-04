<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function _getFiles($rdi, $depth=0, $dir) 
{
	$out = array();
	
	if (!is_object($rdi)) return $out;

	for ($rdi->rewind(); $rdi->valid(); $rdi->next()) 
	{
		if ($rdi->isDot()) continue;

		if ($rdi->isDir() || $rdi->isFile()) 
		{
			$cur = $rdi->current();
			$cur = str_replace('\\', '/', $cur);
			$cur = str_replace($dir, '', $cur);
			
			if ($rdi->isDir()) 
			{
				if ($depth == 0) 
				{
					$out[] = '<optgroup class="bg-gray400" label="' . $cur . '"></optgroup>';
				}
			}
			
			if ($rdi->isFile())
			{
				$file_ext = strtolower(str_replace('.', '', strrchr($cur, '.')));
				
				if (in_array($file_ext, array('php', 'txt', 'css', 'less', 'js', 'html', 'htm', 'ini', 'sass', 'scss'))) 
				{
					if (is_writable($rdi->getPathname())) $out[] = $cur;
				}
			}
			
			if ($rdi->hasChildren())
			{
				$out1 = _getFiles($rdi->getChildren(), 1 + $depth, $dir);
				$out = array_merge($out, $out1); 
			}
		}
	}
	
	return $out;
}


# end of file