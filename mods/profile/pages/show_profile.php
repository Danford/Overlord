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
include($oe_modules['profile']."lib/friends_api.php");
include($oe_modules['profile']."conf/plugin.conf.php");

include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

include($oe_plugins['writing']."conf/conf.php");
include($oe_plugins['writing']."lib/writing.lib.php");
function get_words($sentence, $count = 150) {
	return implode(' ', array_slice(explode(' ', $sentence), 0, $count));
}

function print_words($sentence, $count = 150) {
	$words = explode(' ', $sentence);
	
	$limited_words = array_slice($words, 0, $count);
	echo implode(' ', $limited_words);
	
	if (count($words > $count))
		return true;
	return false;
}

function LoadImage($profileId, $id)
{
	if ($id != "")
	{
		echo '<img class="loading" onload="ImageLoaded(this)" src="/profile/'. $profileId .'/photo/'. $id .'.png"/>';
	}
	else
	{
		echo '<img class="loading" onload="ImageLoaded(this)" src="/images/noavatar.png" />';
	}
}

$page = new page_minion("Profile");

$page->header();

$page->addjs(oe_js . "isotope.pkgd.min.js");
$page->addjs(oe_js . "imagesloaded.pkgd.js");
$page->addjs(oe_js . "isotope.js", true);
$page->addjs(oe_js . "tinymce/tinymce.min.js");
$page->addjs(oe_js . "invoketinymce.js");

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
$groupsLen = count($groups);

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
	$img.parent().find('.cssload-fond').toggleClass('hidden');

	if (typeof $grid != 'undefined')
		$grid.isotope('layout');
};

</script>
<article id="profile">
	<div class="grid">
		<div class="grid-sizer"></div>
	
		<div class="stamp stamp--left tile">
			<p id="name"><?php echo $profile->screen_name; ?></p>
			<?php LoadImage($profile->id, $profile->avatar); ?>
			<?php PrintUserInteractions(); ?>
		</div>
		<div class="stamp stamp--left tile">
			<div id="details">
				<p id="age">Age: <?php echo $profile->age; ?></p>
				<?php if ($profile->city_name() == "") : ?>
				<p id="location">City: Not Listed</p>
				<?php else : ?>
				<p id="location">City: <?php echo $profile->city_name() ; ?></p>
				<?php endif; ?>
				<?php if ($gender[$profile->gender]['label'] == "") : ?>
				<p id="gender">Gender: Not Listed</p>
				<?php else : ?>
				<p id="gender">Gender: <?php echo $gender[$profile->gender]['label'] ; ?></p>
				<?php endif; ?>
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
		<div id="about-me" class="grid-item grid-item--large tile about-me">
			<?php if ($profile->detail == "" && $profile->id == $user->id) : ?>
			<p>This profile doesn't yet have an "About Me" section. Add it by editing your profile.<p>
			<a href="/profile/"><div class="button">EditProfile</div></a>
			<?php elseif ($profile->detail == "") : ?>
			<p>This user has not yet provided anything for the "About Me" section of thier profile.</p>
			<?php else : ?>
			<div id="excerpt">
				<?php if (print_words($profile->detail, 200)) : ?>
				<p>Click to read more.</p>
				<?php endif; ?>
			</div>
			<div id="full" class="hidden"><?php echo $profile->detail; ?></div>
			<?php endif; ?>
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
			<?php if ($user->id == $profile->id) : ?>
			<div>
				<button class="button" onclick='AddTile("Upload Photo", "/profile/<?php echo $profile->id ?>/photo/upload/");'>Upload Photo</button>
				<button class="button" onclick='AddTile("Create Writing", "/profile/<?php echo $profile->id ?>/writing/write/");'>Create Writing</button>
				<button class="button" onclick='AddTile("Create Group", "/group/create");'>Create Group</button>
			</div>
			<?php endif; ?>
		</div>
		<?php for ($i = 0; $i < $loopLength; $i++) : ?>
		<?php if ($i < $photosLen) : ?>
		<?php $date = new DateTime($photos[$i]['timestamp']); ?>
		
		<div class="grid-item tile photo" data-category="photo" data-date="<?php echo $date->getTimestamp(); ?>">
			<div id="title"><h3><?php echo $photos[$i]['title']; ?></h2></div>
			<img class="loading" onload="ImageLoaded(this)" src="/profile/<?php echo $photos[$i]['owner']->id; ?>/photo/<?php echo $photos[$i]['id']; ?>.png" />
			<div id="description"><p><?php echo $photos[$i]['description']; ?></p></div>
		</div>
		<?php endif; ?>
		<?php if ($i < $writingsLen) : ?>
		<?php $date = new DateTime($writings[$i]['last_updated']); ?>
		<div class="grid-item tile writing" data-category="writing" data-date="<?php echo $date->getTimestamp(); ?>">
			<div id="title"><h3><?php echo $writings[$i]['title']; ?></h2></div>
			<div id="subtitle"><h4><?php echo $writings[$i]['subtitle']; ?></h3></div>
			<img class="loading" onload="ImageLoaded(this)" src="/images/noavatar.png" />
			<div id="excerpt"><?php echo get_words($writings[$i]['copy'], 55); ?></div>
			<div id="full" class="hidden"><?php echo $writings[$i]['copy']; ?></div>
		</div>
		<?php endif; ?>
		<?php if ($i < $groupsLen) : ?>
		<a href="/group/<?php echo $groups[$i]->id; ?>/">
			<div class="grid-item tile group" data-category="group">
				<div id="title"><h3><?php echo $groups[$i]->name; ?></h2></div>
				<img class="loading" onload="ImageLoaded(this)" src="/images/noavatar.png" />
				<div id="excerpt"><?php echo get_words($groups[$i]->detail, 55); ?></div>
			</div>
		</a>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
</article>
<?php $page->footer(); ?>