<?php

namespace Mehrizi\Bootstrap;

require_once "../vendor/autoload.php";

class Customizer
{
	var $variables_str = '';

	/**
	 * Customizer constructor.
	 * @param array $variables
	 */
	public function __construct ( $variables = [] )
	{
		foreach ( $variables as $var => $value )
		{
			$this->variables_str .= "$" . $var . ": " . $value . " ;";
		}
	}

	/**
	 * @param mixed $echo : true/false or path string
	 * @param bool $minified
	 */
	public function build ( $echo = false, $minified = true )
	{
		$unique = sha1 ($this->variables_str);
		if (file_exists ("../cache/$unique.css")) // read from cache if exists
		{
			$content = file_get_contents ("../cache/$unique.css");
		}
		else
		{
			$str  = "";
			$scss = new \Leafo\ScssPhp\Compiler();
			if ( $minified )
				$scss->setFormatter ( "Leafo\ScssPhp\Formatter\Crunched" );

			// before bootstrap imports
			$path  = "../scss/_before/";
			$files = glob ( $path . "*.scss" );
			foreach ( $files as $file )
				$str .= "@import \"$file\";";

			// Bootstrap impementation
			$str = $this->variables_str . "\n@import \"../vendor/twbs/bootstrap/scss/bootstrap.scss\";";

			// after bootstrap imports
			$path  = "../scss/";
			$files = glob ( $path . "*.scss" );
			foreach ( $files as $file )
				$str .= "@import \"$file\";";

			$content = $scss->compile ( $str );
			file_put_contents ("../cache/$unique.css",$content);
		}

		if ($echo === false)
			return $content;

		if ($echo === true)
		{
			echo $content;
			return true;
		}

		file_put_contents ($echo,$content);
		return true;

	}

	public static function clear_cache()
	{
		$files = glob ( "../cache/*.css" );
		foreach ( $files as $file )
			unlink($file);
	}

	/**
	 * @param $path
	 * @param bool $relative_path
	 * @return array
	 */
	static function folder_files ( $path, $relative_path = false )
	{
		$files  = scandir ( $path );
		$return = [];
		foreach ( $files as $file )
		{
			if ( in_array ( $file, [ ".", ".." ] ) )
				continue;

			if ( file_exists ( $path . $file ) )
			{
				$return[] = ( $relative_path ? "" : $path ) . $file;
			}
		}

		return $return;

	}
}