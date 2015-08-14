<?php

require 'vendor/autoload.php';

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$adapter = new Local('./input');

$filesystem = new Filesystem($adapter);
$input_pdfs = $filesystem->listContents('./', true);


ob_start();
passthru("mkdir output");
ob_end_clean();

foreach ($input_pdfs as $_pdf)
{
	$_file_name = $_pdf['filename'];
	$_file_ext  = $_pdf['extension'];

	$source = "".$_file_name.".".$_file_ext."";
	$conversion_hash = md5($source);

	print "Converting ".$source." to ".$conversion_hash." HTML and Images.\n";

	ob_start();
	$cmd = array();
	$cmd[] = "mkdir output/".$conversion_hash."";
	$cmd[] = "mkdir text_output/".$conversion_hash."";

	// TO HTML AND IMAGE PARSER [pdftohtml]
	$cmd[] = "/usr/bin/pdftohtml input/".$source." ./output/".$conversion_hash."/";
	
	// TO TEXT PARSER [pdftotext]
	$cmd[] = "/usr/bin/pdftotext input/".$source." >> ".$conversion_hash.".txt";
	$cmd[] = "mv ./output/".$conversion_hash."/*.html ./";
	$cmd[] = "mv ./output/".$conversion_hash."/*.txt ./";

	$cmd[] = "mv ./text_output/".$conversion_hash."/*.html ./";
	$cmd[] = "mv ./text_output/".$conversion_hash."/*.txt ./";

	foreach ($cmd as $_exec)
	{
		passthru($_exec);
		$stdout = ob_get_contents();
	}
	ob_end_clean();
}
