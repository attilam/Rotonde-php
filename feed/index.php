<?php
	ini_set("allow_url_fopen", 1);	  
	date_default_timezone_set('Europe/Berlin');

	// options
	$imgSize = 600; // image thumbnail width
	$embedSize = 250; // youtube embed size
	 
	// get my feed
	$f = '../feed.json'; // replace with your feed URL
	$myFeed = json_decode(file_get_contents($f)); 	
	
	// get list of portals i'm following
	$following = $myFeed->portal;

	// HACK: add myself to the feed
	if (!in_array('rotonde.attilam.com', $following))
		$following[] = 'rotonde.attilam.com';

	// helper function to sort timeline by time of entry
	function cmp($a, $b) { 
		if ($a->time == $b->time) { return 0; } 
		return ($a->time > $b->time) ? -1 : 1;
	} 
		
	// get followed feeds and build timeline
	$timeline = array();	
	$num = 1;

	foreach($following as $followingUrl) {
		$user = json_decode(file_get_contents('http://'.$followingUrl));
		
			foreach ($user->feed as $userPost) {
				// attach user info to each post
				$userPost = (object) array_merge( (array)$userPost, array( 'user' => $user->profile, 'orig' => $userPost, 'num' => $num ) );
				$timeline[] = $userPost;

				$num++;
			}
	}
	
	// sort by timestamp
	usort($timeline, 'cmp');
?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

	<link rel="apple-touch-icon" sizes="114x114" href="../assets/images/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="../assets/images/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="../assets/images/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="../assets/images/favicons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="../assets/images/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="../assets/images/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="../assets/images/favicons/manifest.json">
	<link rel="mask-icon" href="../assets/images/favicons/safari-pinned-tab.svg" color="#000">
	<link rel="shortcut icon" href="../assets/images/favicons/favicon.ico">

	<title>Radius &middot; Rotonde timeline</title>
	<link rel="stylesheet" href="../assets/css/feed.css">
	<style type="text/css">
		.jsonSource {
			font-family: "Lucida Console", Monaco, monospace;
			background-color: white;
			color: #222;
			padding: 8px;
		}
	</style>
	<script type="text/javascript">
		function toggle(id) {
			let div = document.getElementById(id)
			div.style.display = div.style.display == "none" ? "block" : "none"
		}
	</script>
</head>

<body>
	<header>
		<a href="#top"><h1>Radius &middot; Rotonde timeline</h1></a>
	</header>
	
	<main>
		<a class="btn" href="../post/">Post entry</a>
		<ul class="rotondeTimeline" id="rotondeTimeline">
		<?php foreach ($timeline as $entry) :

			$position = $entry->position != '' ? $entry->position : $entry->user->position;
			$location = $entry->location != '' ? $entry->location : $entry->user->location;

			$positionTitle = $position;
			if ($position != '') {
				$latlong = explode(", ", $position);
				
				// get place name from open street map
				$curl_handle=curl_init();
				curl_setopt($curl_handle, CURLOPT_URL,'http://nominatim.openstreetmap.org/reverse?format=json&lat='.$latlong[0].'&lon='.$latlong[1].'&zoom=18&addressdetails=1');
				curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Radius &middot; Rotonde timeline');
				$return = json_decode(curl_exec($curl_handle));
				curl_close($curl_handle);
			
				($return->address->city != '') ? $positionTitle = $return->address->city : $positionTitle = $return->address->state;
			}
		?>
			
			<li>
				<main>
					<?php
						// parse media property 
						if ($entry->media != '' ) {
							if (strpos($entry->media, 'youtube') == 0) {
								// image
								$entryMedia = '<a target="_blank" href="'.$entry->media.'"><img src="'.$entry->media.'" onerror=this.style.display="none" alt="" width="'.$imgSize.'" /></a>';
								
							} else {
								// youtube video
								parse_str( parse_url( $entry->media, PHP_URL_QUERY ), $results);   
								$entryMedia = '<iframe width="'.$embedSize.'" height="auto" src="https://www.youtube-nocookie.com/embed/'.$results['v'].'?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>';

							}
							
							echo $entryMedia;
							$entryMedia = '';
						}
					?>
					
					<p>
						<?= $entry->text ?>
						
						<?php if ($entry->url != '') : ?>
							&rarr; <a href="<?= $entry->url ?>" style="border-color: <?= $entry->user->color ?>;">Link</a>
						<?php endif ?>	
					</p>
				</main>
				<footer>
					<span class="userColor" style="color: <?= $entry->user->color ?>;"></span>
					<img class="avatar" src="<?= $entry->user->avatar ?>" onerror=this.style.display="none" alt="Rotonde avatar" width="25" height="25"/>
					<ul class="meta">
						<li class="user">
							<span class="userName"><?= $entry->user->name ?></span>
							<?php if ($entry->ref != '') : ?>
								<span class="reference"> ⤷ <?= $entry->ref ?></span>
							<?php elseif ($position != '') : ?>
								<span class="position"> from <a href="https://www.google.de/maps/place/<?= $position ?>" style="border-color: <?= $entry->user->color ?>;"><?= $positionTitle ?></a></span>
							<?php else : ?>
								<span class="userLocation"> from <a href="https://www.google.de/maps/place/<?= $location ?>" style="border-color: <?= $entry->user->color ?>;"><?= $location ?></a></span>
							<?php endif ?>
							</span>
						</li>
						<li class="time">
							<?= date('m.d.y - H:m:s', $entry->time) ?> (<?= $entry->time ?>)
							<a onclick="toggle('json_<?= $entry->num ?>')" style="cursor: pointer;">※</a>
						</li>
						<li class="jsonSource" id="json_<?= $entry->num ?>" style="display: none;">
							<?php
								echo json_encode($entry->orig, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
							?>
						</li>
					</ul>
				</footer>
			</li>
		
		<?php endforeach ?>
		</ul>
	</main>	
</body>
