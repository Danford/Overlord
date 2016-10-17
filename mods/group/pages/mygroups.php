<?php

    /*
     * 
     *   $ownergroups  "SELECT `id`, `name`, `short`, `avatar` FROM `group` WHERE `owner`='".$user->id."'"  ;
     * 
     *   $membergroups "SELECT `id`, `name`, `short`, `avatar`, `access` 
     *                          FROM `group_membership`, `group` 
     *                          WHERE `group_membership`.`user`='".$user->id."'
     *                          AND `group_membership`.`group` = '".$group->id."'
     *                          AND `group_membership`.`group` = `group`.`id`
     *                          AND `access` != 0
     *                          ORDER BY ACCESS DESC" ;
     *                          
     *                          
     *                              
     *  use array $groups, which contains three arrays: owned, admin, and member
     *  
     *      each of these is a list of groups ( `id`, `name`, `short`, `avatar` ) where user is
     *      the owner, an admin, or just a member
     *      
     *      
     *      
     *  You can also get this from the api (getMyGroups), which will return the three arrays (owned, admin, member)
     *  though if you do, remove the call to get_my_groups in module.php.
     *  
     *  
     * 
     */

include(oe_frontend."page_minion.php");

$page = new page_minion("My Groups");

$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");

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
<article id="groups">
	<div class="filters">
		<div class="ui-group">
			<div class="button-group js-radio-button-group" data-filter-group="category">
				<button class="button is-checked" data-filter="">All</button>
				<button class="button" data-filter=".owner">Owned<span class="filter-count"></span></button>
				<button class="button" data-filter=".admin">Admin<span class="filter-count"></span></button>
				<button class="button" data-filter=".member">Member<span class="filter-count"></span></button>
			</div>
		</div>
	</div>
	<div class="sortings">
		<div class="ui-group">
			<div class="button-group sort-by-button-group">
				<button class="button is-checked" data-sort-by="">None</button>
				<button class="button" data-sort-by="activity">Recent Activity</button>
				<button class="button" data-sort-by="title">Title</button>
				<button class="button" data-sort-by="members">Members</button>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid-sizer"></div>
		<?php foreach ($groups as $group_type) : ?>
		<?php foreach ($group_type as $group) : ?>
		<?php //$date = new DateTime($group['last_updated']); ?>
		<a href="/group/<?php echo $group['id']; ?>/">
			<div class="grid-item tile" data-updated="<?php //echo $date->getTimestamp(); ?>">
				<div id="title"><h2><?php echo $group['name']; ?></h2></div>
				<div id="photo">
					<?php if ($group['avatar'] == 0) : ?>
					<img class="loading" onload="ImageLoaded(this)" src="/images/noavatar.png" />
					<?php else : ?>
					<p>Not sure where to get group avatar image from.</p>
					<?php endif; ?>
					<div align="center" class="cssload-fond">
						<div class="cssload-container-general">
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"></div></div>
						</div>
					</div>
				</div>
				<div id="date-updated"><?php //echo $writing['last_updated']; ?></div>
				<div id="excerpt"><?php echo $group['short']; ?></div>
			</div>
		</a>
		<?php endforeach; ?>
		<?php endforeach; ?>
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
	},
			
	masonry: {
		columnWidth: '.grid-sizer'
	},
});
	
$grid.imagesLoaded().progress( function() {
	$grid.isotope('layout');
});

//sort items on button click
$('.sort-by-button-group').on( 'click', 'button', function() {
	var sortByValue = $(this).attr('data-sort-by');
	$grid.isotope({ sortBy: sortByValue });
});
</script>
<?php $page->footer(); ?>