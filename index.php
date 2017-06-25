<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
	header("Access-Control-Allow-Headers: *");

  $f = 'feed.json'; // path to rotonde feed json file
  $feed = json_decode(file_get_contents($f), true);	

	echo json_encode($feed, JSON_UNESCAPED_SLASHES);
?>