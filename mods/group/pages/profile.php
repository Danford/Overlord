<?php 
/*
 * 
 *  use group object and its properties
 * 
 *  `name`, `owner`, `short`, `detail`, `avatar`, `group`, `city_id`
 *  
 *  `privacy`= 1 - public, 2- closed, 3 - secret
 *  
 *  `membership` - 0 not member 1 - member 2 - admin
 *  
 *  `invited` - boolean, only set if not a member and privacy > 1
 *  
 *  get_city() - returns string of city, state or ""
 *  
 *   get_member_count() -- see tin
 *   
 * 
 *   if( $group->membership > 0 ){
 *   
 *      leave button
 *      
 *    } elseif(  $group->privacy == 1 or $group->invited == true ){ 
 *    
 *      join button     
 *    
 *    } elseif( $group->privacy == 2 ) {
 *    
 *      request to join
 *    
 *    }
 *   
 * 
 */

include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");

include($oe_modules['group']."conf/plugin.conf.php");

include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

include($oe_plugins['writing']."conf/conf.php");
include($oe_plugins['writing']."lib/writing.lib.php");

include($oe_plugins['thread']."conf/conf.php");
include($oe_plugins['thread']."lib/thread.lib.php");

function get_words($sentence, $count = 10) {
	return implode(' ', array_slice(explode(' ', $sentence), 0, $count));
}

function LoadImage($profileId, $id)
{
	if ($id != "" && $id != 0)
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

$memberGroups = $group->get_members(0, 8);

$photos = get_photos(0, 15);
$photosLen = count($photos);

$writings = get_writings(0, 15);
$writingsLen = count($writings);

$threads = get_threads(0, 15);
$threadsLen = count($threads);

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
			<p id="name"><?php echo $group->name; ?></p>
			<?php LoadImage($group->id, $group->avatar); ?>
			<?php if ($group->check_membership($user->id) <= 1): ?>
			<a href="edit"><div class="button">Edit Group</div></a>
			<?php endif; ?>
			<?php if ($group->check_membership($user->id) <= 2): ?>
			<a href="/group/<?php echo $group->id; ?>/invitations/"><div class="button">Invite</div></a>
			<?php endif; ?>
		</div>
		<div class="stamp stamp--left tile">
			<div id="details">
				<?php if ($group->city_name() == "") : ?>
				<p id="location">City: Not Listed</p>
				<?php else : ?>
				<p id="location">City: <?php echo $group->city_name() ; ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="stamp stamp--left tile">
			<?php foreach ($memberGroups as $groupName => $members) : ?>
			<?php if (count($members) == 0) continue; ?>
			<div id="members">
				<a href="/group/<?php echo $group->id; ?>/members/"><div id="head"><?php echo $groupName; ?> - <?php echo count($members); ?></div></a>
				<div id="body">
				
					<?php foreach ($members as $member) : ?>
					<a href="/profile/<?php echo $member->id; ?>/">
						<div class="member tile">
							<div class="name">
								<?php echo $member->screen_name; ?>
							</div>
							<img class="loading" onload="ImageLoaded(this)" src="<?php echo $member->profile_picture(); ?>"/>
							<?php //PrintFriendlistInteractions($member); ?>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div id="about-me" class="grid-item grid-item--large tile flex-main">
			<?php if ($group->short == "" && $members['owner'][0]->id == $user->id) : ?>
			<p>This group doesn't yet have an "About Me" section. Add it by editing your group.<p>
			<?php elseif ($group->short == "") : ?>
			<p>This group has not yet provided anything for the "About Me" section of thier profile.</p>
			<?php else : ?>
			<p><?php echo $group->short; ?></p>
			<?php endif; ?>
		</div>
		<div class="stamp stamp--top tile">
			<div class="ui-group filters">
				<div class="button-group js-radio-button-group" data-filter-group="category">
					<button class="button is-checked" data-filter="">All</button>
					<button class="button" data-filter=".photo">Photos <span class="filter-count"></span></button>
					<button class="button" data-filter=".writing">Writings <span class="filter-count"></span></button>
				</div>
			</div>
			<div class="ui-group sortings">
				<div class="button-group sort-by-button-group">
					<button class="button is-checked" data-sort-by="">None</button>
					<button class="button" data-sort-by="date">Date</button>
					<button class="button" data-sort-by="title">Title</button>
					<button class="button" data-sort-by="category">Photo / Writing</button>
				</div>
				<p>* Not yet implemented</p>
			</div>
			<?php /* todo: reuse this if they are admin or owner 
					if ($user->id == $profile->id) : 
			<div>
				<button class="button" onclick='AddTile("Upload Photo", "/profile/<?php echo $profile->id ?>/photo/upload/");'>Upload Photo</button>
				<button class="button" onclick='AddTile("Create Writing", "/profile/<?php echo $profile->id ?>/writing/write/");'>Create Writing</button>
				<button class="button" onclick='AddTile("Create Group", "/group/create");'>Create Group</button>
			</div>
			endif;*/ ?>
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
		<?php endfor; ?>
		<?php foreach ($threads as $thread) : ?>
		<?php $date = new DateTime($thread['edited']); ?>
		<a href="/group/<? echo $group->id; ?>/thread/<?php echo $thread['id']; ?>/">
			<div class="grid-item--large grid-item tile" data-updated="<?php echo $date->getTimestamp(); ?>">
				<div id="title"><h2><?php echo $thread['title']; ?></h2></div>
				<div id="date-updated"><?php echo $thread['edited']; ?></div>
				<div id="excerpt"><?php echo $thread['detail']; ?></div>
			</div>
		</a>
		<?php endforeach; ?>
	</div>
</article>
<?php $page->footer(); ?>