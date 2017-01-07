<?php
    /*
     *  
     *  You've got $invitables
     *  
     *  $invitables['people'] = array of profile objects
     *  $invitables['group'] = array of group objects
     * 
     *  
     * 
     * 
     * 
     * 
     */

include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");
include($oe_modules['group']."lib/group.lib.php");

$page = new page_minion($group->name . " - Group Members");

$page->header();

$page->addjs(oe_js . "isotope.pkgd.min.js");
$page->addjs(oe_js . "imagesloaded.pkgd.js");
$page->addjs(oe_js . "isotope.js", true);

$memberGroups = $group->get_members();


?>
<article id="group-invitations">
	<div class="grid">
		<div class="grid-sizer"></div>
		<?php foreach ($invitables['people'] as $name => $invitable) : ?>
		<a href="/profile/<?php echo $invitable->id; ?>/">
			<div class="grid-item tile">
				<div class="name">
					<?php echo $invitable->screen_name; ?>
				</div>
				<img src="<?php echo $invitable->profile_picture(); ?>"/>
				<?php PrintGroupInteractions($group, $invitable); ?>
			</div>
		</a>		
		<?php endforeach; ?>
	</div>
</article>

<?php $page->addjs("/js/isotope.js", true); ?>
<?php $page->footer(); ?>