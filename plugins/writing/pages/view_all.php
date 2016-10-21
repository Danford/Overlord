<?php


/*
 *  include( $oe_plugins['writing']."lib/writing.lib.php" ) ; and use get_writings( $start, $end, $album )
 *  
 *   or writing api getWriting 
 *   
 *   array of:
 *      `id`,
 *      `title`,
 *      `subtitle`,
 *      `privacy`, 
 *      `timestamp`,
 *      `last_updated`
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */

function get_words($sentence, $count = 10) {
  return implode(' ', array_slice(explode(' ', $sentence), 0, $count));
}

include(oe_frontend."page_minion.php");
include($oe_plugins['writing']."lib/writing.lib.php");
$page = new page_minion("Writings");
$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");

$writings = get_writings();
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
<article id="writings">
	<div class="sortings">
		<div class="ui-group">
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
	<div class="grid">
		<div class="grid-sizer"></div>
		<?php foreach ($writings as $writing) : ?>
		<?php $date = new DateTime($writing['last_updated']); ?>
		<a href="/profile/<?php echo $profile->id; ?>/writing/<?php echo $writing['id']; ?>">
			<div class="grid-item tile" data-date="<?php echo $date->getTimestamp(); ?>">
				<div id="title"><h2><?php echo $writing['title']; ?></h2></div>
				<div id="subtitle"><h3><?php echo $writing['subtitle']; ?></h3></div>
				<img class="loading" onload="ImageLoaded(this)" src="/images/noavatar.png" />
				<div align="center" class="cssload-fond">
					<div class="cssload-container-general">
							<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"></div></div>
							<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"></div></div>
							<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"></div></div>
							<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"></div></div>
					</div>
				</div>
				<div id="date-updated"><?php echo $writing['last_updated']; ?></div>
				<div id="excerpt"><?php echo get_words($writing['copy'], 55); ?></div>
				<div id="full-copy"><?php echo $writing['copy']; ?></div>
			</div>
		</a>
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