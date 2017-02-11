<?php

include_once(oe_lib."form_minion.php");

function CreateLikeButton($module = NULL, $module_id = NULL, $plug = NULL, $plug_id = NULL) {
	global $basemodule;
	global $basemoduleID;
	global $lastplug;
	global $lastplugID;
	
	global $oepc;
	global $tier;
	
	$oldOepc = $oepc;
	$tier++;
	
	if (isset($module)) {
		$basemodule = $module;
		$oepc[0]['type'] = $module;
	}
	
	if (isset($module_id)) {
		$basemoduleID = $module_id;
		$oepc[0]['id'];
	}
	
	if (isset($plug)) {
		$lastplug = $plug;
		$oepc[$tier]['type'] = $plug;
	}
	
	if (isset($plug_id)) {
		$lastplugID = $plug_id;
		$oepc[$tier]['id'] = $plug_id;
	}
	
	$form = new form_minion('like', 'like');
	
?>
	<?php if (isLiked()) : ?>
	<div id="is-liked-button" >
		Like
		<?php $form->header(); ?>
		<?php $form->footer(); ?>
	</div>
	<?php else : ?>
	<div id="like-button" >
		Like
		<?php $form->header(); ?>
		<?php $form->footer(); ?>
	</div>
	<?php endif; ?>
	
<?php
	
	$tier--;
	$oepc = $oldOepc;
}

function DisplayLikes($module = NULL, $module_id = NULL, $plug = NULL, $plug_id = NULL) {
	global $basemodule;
	global $basemoduleID;
	global $lastplug;
	global $lastplugID;
	
	if (isset($module)) {
		$basemodule = $module;
	}
	
	if (isset($module_id)) {
		$basemoduleID = $module_id;
	}
	
	if (isset($plug)) {
		$lastplug = $plug;
	}
	
	if (isset($plug_id)) {
		$lastplugID = $plug_id;
	}
	
	$likes = getLikes();
	
	if (isLiked())
		$likesString = "You, ";
	else 
		$likesString = "";

	$likesCount = count($likes);
	for ($i = 0; $i < 3 && $i < $likesCount; $i++) {
		$likesString .= $likes['owner']->screen_name . ", ";
	}
	
	if ($likesCount > 3)
		$likesString .= "and ". $likesCount - 3 ." others liked this.";
	
?>
	<div id="likes">
		<p><?php echo $likesString; ?></p>
		<div id="liked-by" class="hidden">
			<?php foreach ($likes as $like) : ?>
			<p><?php echo $like['owner']->screen_name; ?></p>
			<?php endforeach; ?>
		</div>
	</div>
<?php
}

function getWhere() {
	global $basemodule;
	global $basemoduleID;
	global $lastplug;
	global $lastplugID;
	global $tier;
	
	$where = "`module`='".$basemodule."' AND `module_item_id`='".$basemoduleID."'" ;
	
	if( $tier > 0 ){
		$where .= "AND `plug` ='".$lastplug."'
        AND `plug_id`='".$lastplugID."'" ;
	}
	
	return $where;
}

function like() {
	global $oepc;
	global $tier;
	global $db;
	global $user;
	
	global $basemodule;
	global $basemoduleID;
	global $lastplug;
	global $lastplugID;
	
	if( $oepc[0]['contributor'] != true ) {
		return -1;
	}
        
	$o['module'] = $basemodule;
	$o['module_id'] = $basemoduleID;

	if( $tier > 0 ) {
		$o['plug'] = $lastplug;
		$o['plug_id'] = $lastplugID;
	}
        
	$o['owner'] = $user->id;
	$o['ip'] = get_client_ip();
	$o['timestamp'] = oe_time();
        
	$db->insert( "INSERT INTO `likes` SET ".$db->build_set_string_from_array($o));
        
	return true;
}

function unlike() {
	global $oepc;
	global $tier;
	global $db;
	global $user;

	$x = $db->get_field("SELECT `owner` FROM `likes` WHERE ". getWhere());

	if($x == false or ($user->id != x and $oepc[0]['admin'] != true)) {
		return -1;
	}
    
	// todo: this is no going to work.
	$db->update("DELETE ` FROM `likes` WHERE ". getWhere());
}
        
function countLikes() {
	global $oepc;
	global $tier;
	global $db;
	
	$x = $db->get_field( "SELECT COUNT(*) FROM `likes` WHERE ". getWhere());

	//$post->json_reply("SUCCESS", [ 'count' => $x ] );
	return $x;
}

function getLikes() {    
	global $oepc;
	global $tier;
	global $db;
	
	$db->query("SELECT `owner` FROM `likes` WHERE ". getWhere());

	$x = array();

	while (($l = $db->assoc()) != false) {
		$l['owner'] = new profile_minion($l['owner'], TRUE);
		$x[] = $l;
	}

	return $x;
}

function isLiked() {
	global $oepc;
	global $tier;
	global $db;
	global $user;
	
	$x = $db->get_field("SELECT COUNT(*) FROM `likes` WHERE ". getWhere() .
						" AND `owner`='". $user->id ."'");
   
	if ($x == 0) {
		return false;            
	} else {
		return true;          
	}
}