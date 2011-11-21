<?php

function CheckSyntax($fileName)
{ 
        // If it is not a file or we can't read it throw an exception
	if(!is_file($fileName) || !is_readable($fileName))
		throw new Exception("Cannot read file ".$fileName);

        // Sort out the formatting of the filename
	$fileName = realpath($fileName);

        // Get the shell output from the syntax check command
	$cmd='php -l "'.$fileName.'"';
	$output = shell_exec($cmd);

	$pos=strpos($output,'Errors parsing');
        // Try to find the parse error text and chop it off
	if ($pos!==false)
		return $output;
	else
		return true;
}

?>