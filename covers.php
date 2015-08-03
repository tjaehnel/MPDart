<?php 
require_once('config.php');
require_once('getid3/getid3.php');

// Only allow requests to cover.jpg and folder.jpg
// Strip the suffix if the request is valid
$isValidRequest = false;
if(substr($coverfile, -9) == "cover.jpg")
{
	$coverfile = substr($coverfile, 0, -9);
	$isValidRequest = true;
}
if(substr($coverfile, -10) == "folder.jpg")
{
	$coverfile = substr($coverfile, 0, -10);
	$isValidRequest = true;
}


if($isValidRequest)
{
	if($handle = opendir($coverfile))
	{
		// Iterate through all files in the directory
		// until we find a music file that includes a cover
		while (false !== ($entry = readdir($handle)))
		{
			$mp3file = $coverfile."/".$entry;
			if(is_dir($mp3file))
				continue;
			// Fetch image from ID3 information
			$getID3 = new getID3;
			$OldThisFileInfo = $getID3->analyze($mp3file);
			if(isset($OldThisFileInfo['comments']['picture'][0])){
	   			$Image='data:'.$OldThisFileInfo['comments']['picture'][0]['image_mime'].';charset=utf-8;base64,'.base64_encode($OldThisFileInfo['comments']['picture'][0]['data']);
				break;
			}
		}
		closedir($handle);
	}
	// send the cover image to the browser
	header('Content-Type: image/jpeg');
	echo $OldThisFileInfo['comments']['picture']['0']['data'];
}
else
{
	header("HTTP/1.0 404 Not Found");
}


