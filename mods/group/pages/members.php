<?php

    /*
     * 
     *  use $group->get_members( $start, $limit )
     *  
     *  returns an array of
     *  
     *      'owner' - profile object of the owner
     *      'admins' - array of profile objects
     *      'members' array of profile objects
     * 
     *      
     *  if you're doing by page, admins will be first
     *  owner is always present and not included in the count
     *  
     *  
     * 
     * 
     * 
     */

include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");
include($oe_modules['profile']."lib/friends_api.php");

$page = new page_minion($group->name . " - Group Members");

$page->header();

$page->addjs(oe_js . "isotope.pkgd.min.js");
$page->addjs(oe_js . "imagesloaded.pkgd.js");
$page->addjs(oe_js . "isotope.js", true);

$memberGroups = $group->get_members();

?>
<article id="group-members">
	<div class="grid">
		<div class="grid-sizer"></div>
		<?php foreach ($memberGroups as $groupName => $members) : ?>
		<?php foreach ($members as $member) : ?>
		<a href="/profile/<?php echo $member->id; ?>/">
			<div class="grid-item tile <?php echo $groupName; ?>">
				<div class="name">
					<?php echo $member->screen_name; ?>
				</div>
				<img src="<?php echo $member->profile_picture(); ?>"/>
				<?php PrintFriendlistInteractions($member); ?>
			</div>
		</a>		
		<?php endforeach; ?>
		<?php endforeach; ?>
	</div>
</article>

<?php $page->addjs("/js/isotope.js", true); ?>
<?php $page->footer(); ?>