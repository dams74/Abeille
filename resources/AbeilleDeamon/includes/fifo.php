<?php

include("../lib/Tools.php");
require_once dirname(__FILE__).'/../../../../../core/php/core.inc.php';


ob_implicit_flush();

class fifo {


	var $fp;
	var $to;

    /**
     * Create a first In first Out file according to given parameters
     * fifo constructor.
     * @param $file
     * @param $mode /!\ the permissions of the created file are (mode & ~umask).
     */
	function fifo( $file, $mode, $readWrite )
	{
		if( !file_exists( $file ) ) 
		{
			print "creating fifo $file for $mode\n";
			if( !posix_mkfifo( $file, $mode ) )
			{
				die( "could not create named pipe $file\n" );
			}
			chown($file, "www-data");
		}
		//print "opening fifo $file for $readWrite\n";
		$this->fp = fopen( $file, $readWrite );
        // prevent fread / fwrite blocking
        stream_set_blocking($this->fp, false);
	}

	function read() {
	// reads a line from a fifo file
		$line = '';
		do 
		{
			$c = fgetc( $this->fp );
			if( ($c != '') and ($c != "\n") and !feof( $this->fp ) ) $line .= $c;
		} while( ($c != '') and ($c != "\n") and !feof( $this->fp ) );
		return $line;
	}

	function write( $data ) {
		fputs( $this->fp, $data );
		fflush( $this->fp );
	}
	
	function close()
	{
		fclose($this->fp);
	}

}

?>