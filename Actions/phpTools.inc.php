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

        // Try to find the parse error text and chop it off
	$syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);

	// If the error text above was matched, throw an exception containing the syntax error
	if($count > 0)
		return $syntaxError;
	else
		return true;
}

?>