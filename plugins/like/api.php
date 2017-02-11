<?php

require_once("lib/like.lib.php");

switch ($apiCall) {
    
    case "like":
        if (like()) {
        	$post->json_reply("SUCCESS");
        	$post->return_to_form();
        } else {
        	$post->json_reply("FAIL");
        	die();
        }

        break;
        
    case "unlike":
        
        $response = unlike();
        
        if ($response == -1) {
            $post->json_reply("FAIL");
            die();
        }
        
        $post->json_reply("SUCCESS");
        $post->return_to_form();
        
        break;
        
    case "countLikes":
		$likesCount = countLikes();
        
        $post->json_reply("SUCCESS", ['count' => $likesCount]);
        die();
    
        break;
        
    case "getLikes":
        
        $likes = getLikes();
        
        $post->json_reply("SUCCESS", $likes);
        die();
        
        break;
        
    case 'doIlike':
        
		$isLiked = isLiked();
        
        if ($isLiked == -1) {
            $post->json_reply("FAIL");
        } elseif ($isLiked == false) {
            $post->json_reply("SUCCESS", ['isLiked' => false]);            
        } elseif ($isLiked == true) {
            $post->json_reply("SUCCESS", ['isLiked' => true]);            
        }
    
        die();

    	break;
}