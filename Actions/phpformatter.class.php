<?php 
/**
* This file contains the phpformatter class that allows to format PHP source code.
*
* @package phpformatter
*/

/**
* phpformatter class
*
* This class allows to handle format PHP code or PHP files directly.
*
* @package phpformatter
* @author Setec Astronomy
* @version 1.0
* @abstract Format PHP source code.
* @copyright 2004
* @example sample.php A sample code.
* @link http://setecastronomy.stufftoread.com
*/

class phpformatter {
	
	/**
	* Default constructor
	*
	* This is the default constructor of phpformatter class.
	*/
	// public function __construct ()
	function phpformatter () {

	}

	/**
 	* @access private
	*/
	// private file_get_contents ($filename) {
	function file_get_contents ($filename) {
		if (function_exists ('file_get_contents')) {
			return @file_get_contents ($filename);
		} else {
		   $fd = fopen("$filename", "rb");
		   $content = fread ($fd, filesize ($filename));
		   fclose ($fd);
		   return $content;		
		}
		return false;
	}

	/**
 	* @access private
	*/
	// private function file_put_contents ($filename, $content) {
	function file_put_contents ($filename, $content) {
		if (is_dir (dirname ($filename))) {
			if (function_exists ('file_put_contents')) {
				return file_put_contents ($filename, $content);
			} else {
			   $fd = fopen ($filename, "wb");
			   $return = fwrite ($fd, $content);
			   fclose ($fd);
			   return $return !== false;
			}
		} 
		return false;
	}
	
	/**
	* The format_file method
	*
	* This method format the PHP source code of the given file and save it.
	* @param string $filename the filename to be formatted.
	* @return boolean it returns the status of the action.
	*/
	function format_file ($filename = '') {
		$code = $this->file_get_contents ($filename);
		if ($code !== false) {
			$formatted = $this->format_string($code);
			if ($formatted !== false) {
				return $this->file_put_contents ($filename, $formatted);
			}
		} 
		return false;
	}
	
	/**
	* The format_string method
	*
	* This method format the given PHP source code.
	* @param string $code the source code to be formatted.
	* @return mixed it returns the formated code or false if it fails.
	*/
	function format_string ($code = '') {
		$t_count = 0;
		$in_object = false;
		$in_at = false;
		$in_php = false;
		$in_object = false;
		$in_fct_classe = false;
		
		$result = '';
		$tokens = token_get_all ($code); 
		foreach ($tokens as $token) { 
			if (is_string ($token)) { 
				$token = trim ($token);
				if ($token == '{') {
					$t_count++; 
					$result = rtrim ($result) . ' ' . $token . "\r\n" . str_repeat ("\t", $t_count);					
				} elseif ($token == '}') {
					$t_count--; 
					$result = rtrim ($result) . "\r\n" . str_repeat ("\t", $t_count) . $token . "\r\n" . str_repeat ("\t", $t_count);										
				} elseif ($token == ';') {
					$result .= $token . "\r\n" . str_repeat ("\t", $t_count);										
				} elseif ($token == ':') {
					$result .= $token . "\r\n" . str_repeat ("\t", $t_count);										
				} elseif ($token == '(') {
					$result .= $token;										
				} elseif ($token == ')') {
					$result .= $token;										
				} elseif ($token == '@') {
					$in_at = true;
					$result .= $token;										
				} elseif ($token == '.') {
					$result = rtrim($result).$token;
				} elseif ($token == '=') {
					$result .= ' ' . $token . ' ';
				} else {
					$result .= $token;
				}
				
			} else { 
				list ($id, $text) = $token; 
				switch ($id) { 
				case T_OPEN_TAG:
				case T_OPEN_TAG_WITH_ECHO:
					$in_php = true;
					$result .= trim ($text);					
					break; 
				case T_CLOSE_TAG:
					$in_php = false;
					$result .= trim ($text);					
					break; 
				case T_OBJECT_OPERATOR:
					$result .= trim ($text);					
					$in_object = true;
					break; 
				case T_STRING:
					$trim_text=trim($text);
					if ($in_object) {
						if (($trim_text=='__set') || ($trim_text=='__get') || ($trim_text=='__call') || ($trim_text[0]=='$')) 
							$result = rtrim ($result) . ' ' . $trim_text;
						else
							$result .= $trim_text;
						$in_object = false;
					} elseif ($in_at) {
						if (($trim_text=='__set') || ($trim_text=='__get') || ($trim_text=='__call') || ($trim_text[0]=='$')) 
							$result = rtrim ($result) . ' ' . $trim_text;
						else
							$result = rtrim ($result) . $trim_text;					
						$in_at = false;
					} else {
						$result = rtrim ($result) . ' ' . trim ($text);					
					}
					break; 
				case T_ENCAPSED_AND_WHITESPACE:
				case T_WHITESPACE:
					$result .= trim ($text);					
					break; 
				case T_PRIVATE:
				case T_PUBLIC:
				case T_PROTECTED:
					$in_fct_classe=true;
					$result .= "\r\n" . str_repeat ("\t", $t_count) . trim ($text).' ';		 	
					break;
				case T_NEW:
					$result = rtrim ($result) . ' '  . trim ($text) . ' ';
					break;
				case T_RETURN:
				case T_GLOBAL: 
				case T_ELSEIF:
				case T_ELSE:
					$result = rtrim ($result) . "\r\n" . str_repeat ("\t", $t_count) . trim ($text). ' ';
					break; 
				case T_CASE:
				case T_DEFAULT:
					$result = rtrim ($result) . "\r\n" . str_repeat ("\t", $t_count - 1) . trim ($text) . ' ';
					break;
				case T_THROW:
					$result = rtrim ($result) . "\r\n" . str_repeat ("\t", $t_count) . trim ($text) . ' ';
					break; 
				case T_CATCH:
					$result .= ' ' . trim ($text);
					break; 
				case T_TRY:
					$result .= "\r\n" . str_repeat ("\t", $t_count) . trim ($text) . "\r\n";
					break; 
				case T_FUNCTION:
				case T_CLASS:
				case T_INTERFACE:
					if ($in_fct_classe)
						$result .= trim ($text) . ' ';
					else
						$result .= "\r\n" . str_repeat ("\t", $t_count) . trim ($text) . ' ';
					$in_fct_classe=false;
					break; 
				case T_EXTENDS:
				case T_IMPLEMENTS:
					$result = rtrim ($result) . ' ' . trim ($text) . ' ';
					break;
				case T_AND_EQUAL:
				case T_AS:
				case T_BOOLEAN_AND:
				case T_BOOLEAN_OR:
				case T_CONCAT_EQUAL:
				case T_DIV_EQUAL:
				case T_DOUBLE_ARROW:
				case T_IS_EQUAL:
				case T_IS_GREATER_OR_EQUAL:
				case T_IS_IDENTICAL:
				case T_IS_NOT_EQUAL:
				case T_IS_NOT_IDENTICAL:
				// case T_SMALLER_OR_EQUAL: // undefined constant ???
				case T_LOGICAL_AND:
				case T_LOGICAL_OR:
				case T_LOGICAL_XOR:
				case T_MINUS_EQUAL:
				case T_MOD_EQUAL:
				case T_MUL_EQUAL:
				case T_OR_EQUAL:
				case T_PLUS_EQUAL:
				case T_SL:
				case T_SL_EQUAL:
				case T_SR:
				case T_SR_EQUAL:
				case T_START_HEREDOC:
				case T_XOR_EQUAL:
					$result = rtrim ($result) . ' ' . trim ($text) . ' ';		 	
					break; 
				case T_COMMENT:
					$result = rtrim ($result) . "\r\n" . str_repeat ("\t", $t_count) . trim ($text) . "\r\n".str_repeat ("\t", $t_count);
					break;
				case T_ML_COMMENT:
					$result = rtrim ($result) . "\r\n";
					$lines = explode ("\n", $text);		 	
					foreach ($lines as $line) {
						$result .= str_repeat ("\t", $t_count) . trim ($line);
					}
					$result .= "\r\n".str_repeat ("\t", $t_count);
					break;
				case T_INLINE_HTML:
					$result .= $text;					
					break; 
				default: 
					$result .= trim ($text);					
					break; 
				} // switch($id) { 
			} // if (is_string ($token)) { 
		} // foreach ($tokens as $token) { 			
		return $result;		
	} // function format_string ($code = '') {
}

?>