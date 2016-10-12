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

include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");
include($oe_modules['profile']."conf/plugin.conf.php");

include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

include($oe_plugins['writing']."conf/conf.php");
include($oe_plugins['writing']."lib/writing.lib.php");

$page = new page_minion("Profile");

$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");

function UserInteraction($apiCall, $buttonText)
{
	global $profile;
	$form = new form_minion($apiCall, 'profile');
	$form->header();
	$form->hidden('user', $profile->id);
	$form->submit_button($buttonText);
	$form->footer();
}

function AddFriend()
{
	UserInteraction('addFriend', 'Request Friend');
}

function RemoveFriend()
{
	UserInteraction('removeFriend', 'Remove Friend');
}

function CancelRequest()
{
	UserInteraction('cancelFriendrq', 'Cancel Request');
}

function ConfirmFriend()
{
	UserInteraction('confirmFriend', 'Confirm Friend');
}

function DenyFriend()
{
	UserInteraction('denyFriend', 'Deny Request');
}

function BlockUser()
{
	UserInteraction('blockUser', 'Block User');
}

function UnblockUser()
{
	UserInteraction('unblockUser', 'Unblock User');
}

function PrintUserInteractions()
{
	global $profile;
	global $user;

	$friend = $profile;

	if ($user->id != $friend->id)
	{
		if (!$user->is_friend($friend->id))
		{
			if ($profile->friend_request_status() == "outgoing")
				CancelRequest();
			elseif ($profile->friend_request_status() == "incoming")
			{
				ConfirmFriend();
				DenyFriend();
			}
			else
				AddFriend();
		}
		else
			RemoveFriend();

			if (!$user->is_blocked($friend->id))
				BlockUser();
			else
				UnblockUser();
	}
}

function PrintFriendlistInteractions($friend)
{
	global $profile;
	global $user;

	if (!isset($friend))
		$friend = $profile;

		if ($user->id != $friend->id)
		{
			if (!$user->is_friend($friend->id))
			{
				if ($profile->friend_request_status() == "outgoing")
					CancelRequest();
				else
					AddFriend();
			}
		}
}

function get_words($sentence, $count = 10) {
	return implode(' ', array_slice(explode(' ', $sentence), 0, $count));
}

$friends = $profile->get_friends_as_array(0, 9);

$mutualFriends = array();

foreach ($friends as $friend)
{
	if ($user->is_friend($friend->id))
		$mutualFriends[] = $friend;
}

$photos = get_photos(0, 15);
$photosLen = count($photos);

$writings = get_writings(0, 15);
$writingsLen = count($writings);

$loopLength = $photosLen;
if ($loopLength < $writingsLen)
	$loopLength = $writingsLen;

?>
<script type="text/javascript">
//
//Executed by onload from html for images loaded in grid.
//

function ImageLoaded(img){
	var $img = $(img);
		$img.removeClass('loading');
	$img.parent().find('.cssload-fond').css('display', 'none');

	if (typeof $grid != 'undefined')
		$grid.isotope('layout');
};

</script>
<article id="profile">
	<div id="left-sidebar">
		<div class="sidebar-container tile">
			<p id="name"><?php echo $profile->screen_name; ?></p>
			<div id="main-image">
				<img src="/profile/<?php echo $profile->id .'/photo/'. $profile->avatar; ?>.png"/>
				<?php PrintUserInteractions(); ?>
			</div>
			
		</div>
		<div class="sidebar-container tile">
			<div id="details">
				<p id="age">Age: <?php echo $profile->age; ?></p>
				<p id="location">City: <?php echo $profile->city_name() ; ?></p>
				<p id="gender">Gender: <?php echo $gender[$profile->gender]['label']; ?></p>
			</div>
		</div>
		<?php if ($user->id != $profile->id) : ?>
		<div class="sidebar-container tile">
			<div id="mutual-friends">
				<a href="/profile/<?php echo $profile->id; ?>/friends/"><div id="head">Mutual Friends - <?php echo count($mutualFriends); ?></div></a>
				<div id="body">
					<?php foreach ($mutualFriends as $friend) : ?>
					<a href="/profile/<?php echo $friend->id; ?>/">
						<div class="friend tile">
							<div class="name">
								<?php echo $friend->screen_name; ?>
							</div>
							<div class="profile-img">
								<img src="<?php echo $friend->profile_picture(); ?>"/>
							</div>
							<?php PrintFriendlistInteractions($friend); ?>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="sidebar-container tile">
			<div id="friends">
				<a href="/profile/<?php echo $profile->id; ?>/friends/"><div id="head">Friends - <?php echo $profile->get_friends_count(); ?></div></a>
				<div id="body">
					<?php foreach ($friends as $friend) : ?>
					<a href="/profile/<?php echo $friend->id; ?>/">
						<div class="friend tile">
							<div class="name">
								<?php echo $friend->screen_name; ?>
							</div>
							<div class="profile-img">
								<img src="<?php echo $friend->profile_picture(); ?>"/>
							</div>
							<?php PrintFriendlistInteractions($friend); ?>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<div id="main" class="tile">
		<p id="about-me"><?php echo $profile->detail; ?></p>
	</div>
	<div id="photo-albums" class="tile">
		<div class="button-group filter-button-group">
			<button class="button is-checked" data-filter="*">All</button>
			<button class="button is-checked" data-filter=".photo">Photos</button>
			<button class="button is-checked" data-filter=".writing">Writings</button>
		</div>
		<div class="button-group sort-by-button-group">
			<button data-sort-by="date">Date</button>
			<button data-sort-by="title">Title</button>
			<button data-sort-by="category">Photo / Writing</button>
			<button data-sort-by="likes">Likes *</button>
			<button data-sort-by="views">Views *</button>
			<button data-sort-by="shares">Shares *</button>
			<button data-sort-by="comments">Comments *</button>
			<p>* Not yet implemented</p>
		</div>
		<div class="grid">
			<div class="grid-sizer"></div>
			<?php for ($i = 0; $i < $loopLength; $i++) : ?>
			<?php if ($i < $photosLen) : ?>
			<?php $date = new DateTime($photos[$i]['timestamp']); ?>
			
			<a href="/profile/<?php echo $photos[$i]['owner']->id; ?>/photo/<?php echo $photos[$i]['id']; ?>">
				<div class="grid-item tile photo" data-category="photo" data-date="<?php echo $date->getTimestamp(); ?>">
					<div id="title"><h3><?php echo $photos[$i]['title']; ?></h2></div>
					<img class="loading" onload="ImageLoaded(this)" src="/profile/<?php echo $photos[$i]['owner']->id; ?>/photo/<?php echo $photos[$i]['id']; ?>.png" />
					<div align="center" class="cssload-fond">
						<div class="cssload-container-general">
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"></div></div>
						</div>
					</div>
					<div id="description"><?php echo $photos[$i]['description']; ?></div>
				</div>
			</a>
			<?php endif; ?>
			<?php if ($i < $writingsLen) : ?>
			<?php $date = new DateTime($writings[$i]['last_updated']); ?>
			<a href="/profile/<?php echo $writings[$i]['owner']->id; ?>/writing/<?php echo $writings[$i]['id']; ?>">
				<div class="grid-item tile writing" data-category="writing" data-date="<?php echo $date->getTimestamp(); ?>">
					<div id="title"><h3><?php echo $writings[$i]['title']; ?></h2></div>
					<div id="subtitle"><h4><?php echo $writings[$i]['subtitle']; ?></h3></div>
					<div id="photo">
						<img class="loading" onload="ImageLoaded(this)" src="/images/noavatar.png" />
						<div align="center" class="cssload-fond">
							<div class="cssload-container-general">
									<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"></div></div>
									<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"></div></div>
									<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"></div></div>
									<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"></div></div>
							</div>
						</div>
					</div>
					<div id="excerpt"><?php echo get_words($writings[$i]['copy'], 55); ?></div>
				</div>
			</a>
			<?php endif; ?>
			<?php endfor; ?>
		</div>
	</div>
</article>
<script>
$grid = $('.grid').isotope({
	// options
	itemSelector: '.grid-item',
	percentPosition: true,

	getSortData: {
		title: '#title',
		date: '[data-date]',
		category: '[data-category]'
	},
	
	masonry: {
		columnWidth: '.grid-sizer'
	},
});

//recalculate grid layout on image load
$grid.imagesLoaded().progress( function () {
	$grid.isotope('layout');
});

//filter items on button click
$('.filter-button-group').on( 'click', 'button', function() {
	var filterValue = $(this).attr('data-filter');
	$grid.isotope({ filter: filterValue });
});

//sort items on button click
$('.sort-by-button-group').on( 'click', 'button', function() {
	var sortByValue = $(this).attr('data-sort-by');
	$grid.isotope({ sortBy: sortByValue });
});
</script>
<?php $page->footer(); ?>