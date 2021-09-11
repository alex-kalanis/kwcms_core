<?php

// This file passes the content of the Readme.md file in the same directory
// through the Markdown filter. You can adapt this sample code in any way
// you like.

// Install PSR-4-compatible class autoloader
spl_autoload_register(function($class){
	require str_replace('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
});
// If using Composer, use this instead:
//require 'vendor/autoload.php';

// Get Markdown class
use Michelf\MarkdownExtra\Markdown;

// Read file and pass content through the Markdown parser
$text = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Readme.md');
$html = Markdown::defaultTransform($text);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>PHP Markdown Lib - Readme</title>
	</head>
	<body>
		<?php
			// Put HTML content in the document
			echo $html;
		?>
	</body>
</html>
