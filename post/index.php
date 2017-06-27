<?php
		ob_start();
		session_start();
		
		
		$userinfo = array(
    	'USER' => 'foo'
    );
    
		if(isset($_GET['logout'])) {
	    $_SESSION['user'] = '';
	    setcookie('rotondeSession','', time()-86400, '/');
	    header('Location:  http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	    exit;
		}

		if (isset($_COOKIE['rotondeSession'])) {
		  	$_SESSION['user'] = $_COOKIE['rotondeSession'];
		} 
		
		else if (isset($_POST['user'])) {
		  	
		    if($userinfo[$_POST['user']] == $_POST['password']) {
		        $_SESSION['user'] = $_POST['user'];
		        setcookie('rotondeSession',$_POST['user'], time()+86400*30, '/');
		   
		    } else {
		       echo 'invalid login';
		    }
		}
		ob_end_flush();
	
		if ($_SESSION['user']) {
 	   date_default_timezone_set('Europe/Berlin');
 	   $f = '../feed.json';
 	   $feed = json_decode(file_get_contents($f), true);
 	   
 			 
 	   if (!empty($_POST) || !empty($_GET)) {
 	       
 	       // require timestamp and text   
 	       if (
 	           isset($_POST['text'])
 	           && isset($_POST['timestamp'])
 	           && isset($_POST['submit'])   
 	       ) {
 	         
 	           // read post data
 	           
 	           $entry['text'] = $_POST['text'];
 	           $entry['time'] = $_POST['timestamp'];

 							if ($_POST['pos']) {
 								$entry['position'] = $_POST['pos'];
 							}
 	           
 							if ($_POST['ref']) {
 								$entry['ref'] = $_POST['ref'];
 							}
 	
 							if ($_POST['media']) {
 								$entry['media'] = $_POST['media'];
 							}
 	
 							if ($_POST['url']) {
 								$entry['url'] = $_POST['url'];
 							}							
 							           
 	           // write to json
 	           
 	           array_push( $feed['feed'], $entry );
 	       }
 	       
 	       // update json file
 	       file_put_contents($f, json_encode($feed, JSON_UNESCAPED_SLASHES));
 	   }
 	  }
?>



<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
	<meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" sizes="144x144" href="apple-touch-icon-144x144.png">

    
	<title>Radius &middot; new Rotonde post</title>
	<link rel="stylesheet" href="../assets/css/post.css">
	
	<script>
        window.onload = function() {
            var input = document.getElementById('text').focus();
        }	
   </script>
</head>

<body>
		<header>
			<a href="#top"><h1>Radius &middot; Rotonde timeline</h1></a>
		</header>
		
	   <?php if ($_SESSION['user']): ?>

    <form method="post" action="?">
        
        <textarea id="text" name="text"></textarea>
        <label for="text">Text</label>
        
        <br/>

        <input id="pos" name="pos" type="text">
        <label for="pos">Position</label>
        
        <br/>
        
        <input id="ref" name="ref" type="text">
        <label for="ref">Ref</label>
        
        <br/>
        
        <input id="media" name="media" type="text">
        <label for="media">Media</label>
        
        <br/>
        
        <input id="url" name="url" type="text">
        <label for="url">URL</label>
        
        <br/>
        
				<input id="timestamp" name="timestamp" type="hidden" value="<?= time() ?>">
                      
        <input type="submit" name="submit" value="Post">
    </form>
        	   	<a class="logout" id="logout" href="?logout=1">logout</a>
	<?php else: ?>
    	
	    <main class="login">
            <form name="login" action="" method="post">

    	        <input type="text" name="user" value="" />
    	        <label for="user">Username</label>
    	        <br/>
    	            	        
               <input type="password" name="password" value="" />
               <label for="password">Password</label>
    	        
    	        <input type="submit" name="submit" value="Submit" />
    	   </form>	
	    </main>
    	
	<?php endif; ?>

</body>