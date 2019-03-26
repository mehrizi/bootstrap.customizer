<?php

namespace Mehrizi\Bootstrap;
if ( file_exists ( __DIR__ . "/../vendor" ) )
{
	define ( "BCUSTOMIZER_VENDOR_REL_PATH", "/vendor" );
}
else
	define ( "BCUSTOMIZER_VENDOR_REL_PATH", "/../.." );
include_once __DIR__ . "/.." . BCUSTOMIZER_VENDOR_REL_PATH . "/autoload.php";

class Customizer
{
	var $variables_str        = '';
	var $include_paths        = [];
	var $include_paths_before = [];
	var $prefix               = '';
	var $suffix               = '';

	/**
	 * Customizer constructor.
	 * @param array $variables
	 */
	public function __construct ( $variables = [], $include_paths = [] )
	{
		if ( isset( $variables[ "_prefix" ] ) )
		{
			$this->prefix = $variables[ "_prefix" ];
			unset( $variables[ "_prefix" ] );
		}
		if ( isset( $variables[ "_suffix" ] ) )
		{
			$this->suffix = $variables[ "_suffix" ];
			unset( $variables[ "_suffix" ] );
		}
		foreach ( $variables as $var => $value )
		{
			$this->variables_str .= "$" . $var . ": " . $value . " ;";
		}
		$this->include_paths = $include_paths;
		if ( isset( $this->includes[ "_before" ] ) )
		{
			if ( !is_array ( $this->includes[ "_before" ] ) )
				$this->includes[ "_before" ] = [ $this->includes[ "_before" ] ];

			$this->include_paths_before = $this->includes[ "_before" ];
			unset( $this->includes[ "_before" ] );
		}
	}

	/**
	 * @param mixed $echo : true/false or path string
	 * @param bool $minified
	 * @param bool $cache
	 * @return bool|false|string
	 */
	public function build ( $echo = false, $minified = true, $cache = true )
	{
		$unique     = sha1 ( json_encode ( [ $this->variables_str, $this->include_paths, $this->include_paths_before, $this->prefix, $this->suffix ] ) );
		$cache_path = __DIR__ . "/../cache/$unique.css";
		if ( $cache && file_exists ( $cache_path ) ) // read from cache if exists
		{
			$content = file_get_contents ( $cache_path );
		}
		else
		{
			$str  = '';
			$scss = new \Leafo\ScssPhp\Compiler();
			if ( $minified )
				$scss->setFormatter ( "Leafo\ScssPhp\Formatter\Crunched" );

			$scss->setImportPaths ( __DIR__ . "/../" );

			// before bootstrap imports
			$path  = __DIR__ . "/scss/_before/";
			$files = glob ( $path . "*.scss" );
			foreach ( $files as $file )
				$str .= "@import \"scss/_before/" . basename ( $file ) . "\";";

			if ( count ( $this->include_paths_before ) )
			{
				foreach ( $this->include_paths_before as $path )
					$str .= "@import \"" . $path . "\";";
			}

			// Bootstrap impementation
			$str .= $this->variables_str . "\n@import \"" . BCUSTOMIZER_VENDOR_REL_PATH . "/twbs/bootstrap/scss/bootstrap.scss\";";

			// after bootstrap imports
			$path  = __DIR__ . "/scss/";
			$files = glob ( $path . "*.scss" );
			foreach ( $files as $file )
				$str .= "@import \"scss/" . basename ( $file ) . "\";";

			if ( count ( $this->include_paths ) )
			{
				foreach ( $this->include_paths as $path )
					$str .= "@import \"" . $path . "\";";
			}

			$content = $scss->compile ( $str );
			if ( !empty( $this->prefix ) )
				$content = $scss->compile ($this->prefix . $content . $this->suffix);
			file_put_contents ( __DIR__ . "/../cache/$unique.css", $content );
		}

		if ( $echo === false )
			return $content;

		if ( $echo === true )
		{
			echo $content;
			return true;
		}

		file_put_contents ( $echo, $content );
		return true;

	}

	public static function clear_cache ()
	{
		$files = glob ( __DIR__ . "/../cache/*.css" );
		foreach ( $files as $file )
			unlink ( $file );
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