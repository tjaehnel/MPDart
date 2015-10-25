<?php 
require_once('config.php');
require_once('getid3/getid3.php');

$coverfile = $_GET['coverfile'];

// Only allow requests to cover.jpg and folder.jpg
// Strip the suffix if the request is valid
$isValidRequest = false;
if(substr($coverfile, -9) == "cover.jpg")
{
	$coverdir = substr($coverfile, 0, -9);
	$isValidRequest = true;
}
if(substr($coverfile, -10) == "folder.jpg")
{
	$coverdir = substr($coverfile, 0, -10);
	$isValidRequest = true;
}

if($isValidRequest)
{
	$coverdir = BASE_MUSIC_DIR.'/'.$coverdir;
	if($handle = opendir($coverdir))
	{
		// Iterate through all files in the directory
		// until we find a music file that includes a cover
		while (false !== ($entry = readdir($handle)))
		{
			$mp3file = $coverdir."/".$entry;
			if(is_dir($mp3file))
				continue;
			// Fetch image from ID3 information
			$getID3 = new getID3;
			$id3Information = $getID3->analyze($mp3file);
			if(isset($id3Information['comments']['picture'][0])){
	   			$Image=$id3Information['comments']['picture'][0]['data'];
				break;
			}
		}
		closedir($handle);
	}
	if(isset($Image)){
		// send the cover image to the browser
		header('Content-Type: image/jpeg');
		echo $Image;
	} else {
		header("HTTP/1.0 404 Not Found");
	}
}
else
{
	header("HTTP/1.0 404 Not Found");
}


