<?php 

    /*
     * 
     *      $profile->screen_name
     *      $profile->age
     *      $profile->city_id
     *      $profile->city
     *      $profile->state
     *      $profile->gender - int, corresponds to $gender in /core/_conf/oe_config.php
     *      $profile->detail 
     *      $profile->avatar - the URL to be included in the img tag
     *      $profile->allow_contact - boolean, if no don't show a contact box unless they're a friend
     *      
     *      $profile->get_friends_as_array()
     *      $profile->get_friends_count()     
     * 
     */

include(oe_frontend . "page_minion.php");

$page = new page_minion("Profile");

$page->header();

$friends = $profile->get_friends_as_array(0, 9);
?>

<article id="profile">
	<div id="left-sidebar">
		<div class="sidebar-container tile">
			<p id="name"><?php echo $profile->screen_name; ?></p>
			<div id="main-image">
				<img src="/profile/<?php echo $profile->id .'/photo/'. $profile->avatar; ?>.png"/>
			</div>
		</div>
		<div class="sidebar-container tile">
			<div id="details">
				<p id="age">Age: <?php echo $profile->age; ?></p>
				<p id="location">City: <?php echo $profile->city_name() ; ?></p>
				<p id="gender">Gender: <?php echo $profile->gender; ?></p>
			</div>
		</div>
		<div class="sidebar-container tile">
			<div id="friends">
				<div id="head">Friends - (<?php echo $profile->get_friends_count(); ?>)</div>
				<div id="body">
					<?php foreach ($friends as $friend) : ?>
					<div class="friend">
						<div class="name">
							<?php echo $friend->screen_name; ?>
						</div>
						<div class="profile-img">
							<img src="<?php echo $friend->profile_thumbnail(); ?>"/>
						</div>
						<div class="button request-friend">Add Friend</div>
					</div>						
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<div id="main" class="tile">
		<p id="about-me"><?php echo $profile->detail; ?></p>
	</div>
</article>

<?php $page->footer(); ?>