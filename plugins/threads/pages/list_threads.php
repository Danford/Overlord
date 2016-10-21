<?php
/*
 *  $page is provided
 * 
 *  use command get_threads( $start, $limit )
 * 
 *  or API call getThreads
 *  
 *  
 *          either will give you array of:
 *          
 *              `id` - int
 *              `title` - string
 *              `owner` - profile object
 *              `detail`, text/html
 *              `sticky`, 1 or 0
 *              `locked`, 1 or 0 
 *              `created`, timestamp
 *              `edited`, timestamp of when 'detail' got changed.
 *              `msgcount`, int  
 *              `last_updated`, timestamp
 *              
 *              
 */

include(oe_frontend."page_minion.php");

$page = new page_minion("Group Threads");

$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");
$page->js_minion->addFile(oe_js . "isotope.js");

$threads = get_threads();

?>

<article id="threads">
	<div class="filters">
		<div class="ui-group">
			<div class="button-group js-radio-button-group" data-filter-group="category">
				<button class="button is-checked" data-filter="">All</button>
				<button class="button" data-filter=".sticky">Sticky<span class="filter-count"></span></button>
				<button class="button" data-filter=".locked">Locked<span class="filter-count"></span></button>
			</div>
		</div>
	</div>
	<div class="sortings">
		<div class="ui-group">
			<div class="button-group sort-by-button-group">
				<button class="button is-checked" data-sort-by="date">Date Created</button>
				<button class="button" data-sort-by="activity">Recent Activity</button>
				<button class="button" data-sort-by="title">Title</button>
				<button class="button" data-sort-by="comments">Comments</button>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid-sizer--full grid-sizer"></div>
		<?php foreach ($threads as $thread) : ?>
		<?php $date = new DateTime($thread['edited']); ?>
		<a href="/group/<? echo $group->id; ?>/thread/<?php echo $thread['id']; ?>/">
			<div class="grid-item--full grid-item tile" data-updated="<?php echo $date->getTimestamp(); ?>">
				<div id="title"><h2><?php echo $thread['title']; ?></h2></div>
				<div id="date-updated"><?php echo $thread['edited']; ?></div>
				<div id="excerpt"><?php echo $thread['detail']; ?></div>
			</div>
		</a>
		<?php endforeach; ?>
	</div>	
	<a href="/group/<?php echo $group->id; ?>/thread/create/"><div class="button">New Thread</div></a>
</article>
<?php $page->footer(); ?>