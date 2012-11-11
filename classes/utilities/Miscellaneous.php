<?php

namespace utilities;

class Miscellaneous {
	
	static public function isValidDigest($digest)
	{
		return strlen($digest)==64 && ctype_xdigit($digest);
	}
	
	static public function isValidConfirmationId($digest)
	{
		return strlen($digest)==32 && ctype_xdigit($digest);
	}
	
	static private $allowedCharacters = null;
	
	static public function initAllowedCharacters()
	{
		self::$allowedCharacters = array();

		$char = 'a';
		$value = ord($char);
		
		while($value<=ord('z'))
		{
			self::$allowedCharacters[] = $char;
			$value++;
			$char = chr($value);
		}
		
		$char = 'A';
		$value = ord($char);
		
		while($value<=ord('Z'))
		{
			self::$allowedCharacters[] = $char;
			$value++;
			$char = chr($value);
		}
		
		$char = '0';
		$value = ord($char);
		
		while($value<=ord('9'))
		{
			self::$allowedCharacters[] = $char;
			$value++;
			$char = chr($value);
		}
	}
	
	static public function passwordFromBytes($bytes)
	{
		if(!self::$allowedCharacters)
		{
			self::initAllowedCharacters();
		}
		$hex = bin2hex($bytes);
		$res="";
		for($i=0 ; $i<strlen($bytes) ; $i++)
		{
			$byte = hexdec(substr($hex, 2*$i,2));
			$index = $byte%count(self::$allowedCharacters);
			$res .= self::$allowedCharacters[$index];
		}
		return $res;
	}
	
	static public function generateRandomPassword($length)
	{
		$random_bytes = openssl_random_pseudo_bytes($length);
		return self::passwordFromBytes($random_bytes);
	}
	
	static public function checkHeight($height)
	{
		return $height>=1. && $height<= 2.8;
	}
	
	static public function checkWeight($weight)
	{
		return $weight>=30 && $weight<= 200;
	}
	
	static public function checkSize($size)
	{
		return $size>=32 && $size<= 52;
	}
}

?>