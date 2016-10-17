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

$page = new page_minion("Profile");

$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");

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

$groups = $profile->get_groups();

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
	<div class="grid">
		<div class="grid-sizer"></div>
	
		<div class="stamp stamp--left tile">
			<p id="name"><?php echo $profile->screen_name; ?></p>
			<img class="loading" onload="ImageLoaded(this)" src="/profile/<?php echo $profile->id .'/photo/'. $profile->avatar; ?>.png"/>
			<?php PrintUserInteractions(); ?>
		</div>
		<div class="stamp stamp--left tile">
			<div id="details">
				<p id="age">Age: <?php echo $profile->age; ?></p>
				<p id="location">City: <?php echo $profile->city_name() ; ?></p>
				<p id="gender">Gender: <?php echo $gender[$profile->gender]['label']; ?></p>
			</div>
		</div>
		<?php if ($user->id != $profile->id) : ?>
		<div class="stamp stamp--left tile">
			<div id="mutual-friends">
				<a href="/profile/<?php echo $profile->id; ?>/friends/"><div id="head">Mutual Friends - <?php echo count($mutualFriends); ?></div></a>
				<div id="body">
					<?php foreach ($mutualFriends as $friend) : ?>
					<a href="/profile/<?php echo $friend->id; ?>/">
						<div class="friend tile">
							<div class="name">
								<?php echo $friend->screen_name; ?>
							</div>
							<img class="loading" onload="ImageLoaded(this)" src="<?php echo $friend->profile_picture(); ?>"/>
							<?php PrintFriendlistInteractions($friend); ?>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="stamp stamp--left tile">
			<div id="friends">
				<a href="/profile/<?php echo $profile->id; ?>/friends/"><div id="head">Friends - <?php echo $profile->get_friends_count(); ?></div></a>
				<div id="body">
					<?php foreach ($friends as $friend) : ?>
					<a href="/profile/<?php echo $friend->id; ?>/">
						<div class="friend tile">
							<div class="name">
								<?php echo $friend->screen_name; ?>
							</div>
							<img class="loading" onload="ImageLoaded(this)" src="<?php echo $friend->profile_picture(); ?>"/>
							<?php PrintFriendlistInteractions($friend); ?>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div id="about-me" class="grid-item grid-item--large tile flex-main">
			<p><?php echo $profile->detail; ?></p>
		</div>
		<div class="stamp stamp--top tile">
			<div class="ui-group filters">
				<div class="button-group js-radio-button-group" data-filter-group="category">
					<button class="button is-checked" data-filter="">All</button>
					<button class="button" data-filter=".photo">Photos <span class="filter-count"></span></button>
					<button class="button" data-filter=".writing">Writings <span class="filter-count"></span></button>
					<button class="button" data-filter=".group">Groups <span class="filter-count"></span></button>
				</div>
			</div>
			<div class="ui-group sortings">
				<div class="button-group sort-by-button-group">
					<button class="button is-checked" data-sort-by="">None</button>
					<button class="button" data-sort-by="date">Date</button>
					<button class="button" data-sort-by="title">Title</button>
					<button class="button" data-sort-by="category">Photo / Writing</button>
					<button class="button" data-sort-by="likes">Likes *</button>
					<button class="button" data-sort-by="views">Views *</button>
					<button class="button" data-sort-by="shares">Shares *</button>
					<button class="button" data-sort-by="comments">Comments *</button>
				</div>
				<p>* Not yet implemented</p>
			</div>
		</div>
		<?php for ($i = 0; $i < $loopLength; $i++) : ?>
		<?php if ($i < $photosLen) : ?>
		<?php $date = new DateTime($photos[$i]['timestamp']); ?>
		
		<a href="/profile/<?php echo $photos[$i]['owner']->id; ?>/photo/<?php echo $photos[$i]['id']; ?>">
			<div class="grid-item tile photo" data-category="photo" data-date="<?php echo $date->getTimestamp(); ?>">
				<div id="title"><h3><?php echo $photos[$i]['title']; ?></h2></div>
				<img class="loading" onload="ImageLoaded(this)" src="/profile/<?php echo $photos[$i]['owner']->id; ?>/photo/<?php echo $photos[$i]['id']; ?>.png" />
				<div id="description"><p><?php echo $photos[$i]['description']; ?></p></div>
			</div>
		</a>
		<?php endif; ?>
		<?php if ($i < $writingsLen) : ?>
		<?php $date = new DateTime($writings[$i]['last_updated']); ?>
		<a href="/profile/<?php echo $writings[$i]['owner']->id; ?>/writing/<?php echo $writings[$i]['id']; ?>">
			<div class="grid-item tile writing" data-category="writing" data-date="<?php echo $date->getTimestamp(); ?>">
				<div id="title"><h3><?php echo $writings[$i]['title']; ?></h2></div>
				<div id="subtitle"><h4><?php echo $writings[$i]['subtitle']; ?></h3></div>
				<img class="loading" onload="ImageLoaded(this)" src="/images/noavatar.png" />
				<div id="excerpt"><?php echo get_words($writings[$i]['copy'], 55); ?></div>
			</div>
		</a>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
</article>
<script>

// add css loading spinner after all tile images with the loading class
$('.tile > img.loading').after("<div align='center' class='cssload-fond'><div class='cssload-container-general'><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_1'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_2'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_3'></div></div><div class='cssload-internal'><div class='cssload-ballcolor cssload-ball_4'></div></div></div></div>");

$grid = $('.grid').isotope({
	// options
	itemSelector: '.grid-item',
	stamp: '.stamp',
	
	percentPosition: true,
	stager: 30,
	getSortData: {
		title: '#title',
		date: '[data-date]',
		category: '[data-category]'
	},
	
	masonry: {
		columnWidth: '.grid-sizer'
	},
});

var $filterButtons = $('.filters .button');

updateFilterCounts();

// store filter for each group
var filters = {};

//recalculate grid layout on image load
$grid.imagesLoaded().progress( function () {
	$grid.isotope('layout');
});

$('.filters').on( 'click', '.button', function() {
  var $this = $(this);
  // get group key
  var $buttonGroup = $this.parents('.button-group');
  var filterGroup = $buttonGroup.attr('data-filter-group');
  // set filter for group
  filters[ filterGroup ] = $this.attr('data-filter');
  // combine filters
  var filterValue = concatValues( filters );
  // set filter for Isotope
  $grid.isotope({ filter: filterValue });
  updateFilterCounts();
});

//sort items on button click
$('.sort-by-button-group').on( 'click', 'button', function() {
	var sortByValue = $(this).attr('data-sort-by');
	$grid.isotope({ sortBy: sortByValue });
});

//change is-checked class on buttons
$('.button-group').each( function( i, buttonGroup ) {
  var $buttonGroup = $( buttonGroup );
  $buttonGroup.on( 'click', 'button', function() {
    $buttonGroup.find('.is-checked').removeClass('is-checked');
    $( this ).addClass('is-checked');
  });
});
  
// flatten object by concatting values
function concatValues( obj ) {
  var value = '';
  for ( var prop in obj ) {
    value += obj[ prop ];
  }
  return value;
}

function updateFilterCounts()  {
  // get filtered item elements
  var itemElems = $grid.isotope('getFilteredItemElements');
  var $itemElems = $( itemElems );
  $filterButtons.each( function( i, button ) {
    var $button = $( button );
    var filterValue = $button.attr('data-filter');
    if ( !filterValue ) {
      // do not update 'any' buttons
      return;
    }
    var count = $itemElems.filter( filterValue ).length;
    $button.find('.filter-count').text( '(' + count +')' );
  });
}

</script>
<?php $page->footer(); ?>