<?php


/*
 *   I NEED TO CREATE THE API YOU WILL USE FOR THIS
 * 
 *      urls of thumbnails are "photo/thumb/id.png"
 *      
 *      examples:
 *      
 *          profile/1/photo/thumb.1.png
 *          group/1/photo/thumb.1.png
 *          group/1/event/2/photo/thumb.1.png
 *      
 *          
 * 
 *      get_photos( $start = 0. $end = 9999 ) ;
 *      
 *          returns array
 *          
 *              id
 *              
 *              owner -> profile object
 *              
 *              title
 *              description
 *              privacy
 *              
 *              timestamp
 * 
 * 
 * 
 *      album functionality will be added.  Eventually I would like this page
 *      to let you switch between a view of all photos and a list of albums
 *      and photos that are not in albums.
 * 
 * 
 */
include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");
include($oe_plugins['photo']."lib/photo.lib.php");

$page = new page_minion("Upload Photo");

$page->header();
$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");

$photos = get_photos();
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
<article id="photos">
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
		<?php foreach ($photos as $photo) : ?>
		<?php $date = new DateTime($photo['timestamp']); ?>
		<a href="/profile/<?php echo $photo['owner']->id; ?>/photo/<?php echo $photo['id']; ?>">
			<div class="grid-item tile photo" data-date="<?php echo $date->getTimestamp(); ?>">
				<div id="title"><h2><?php echo $photo['title']; ?></h2></div>
				<div id="photo">
					<img class="loading" onload="ImageLoaded(this)" src="/profile/<?php echo $photo['owner']->id; ?>/photo/<?php echo $photo['id']; ?>.png" />
					<div align="center" class="cssload-fond">
						<div class="cssload-container-general">
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"></div></div>
								<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"></div></div>
						</div>
					</div>
				</div>
				<div id="description"><?php echo $photo['description']; ?></div>
			</div>
		</a>
		<?php endforeach; ?>
	</div>
</article>
<script>
//load isotope grid
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
	}
});

//recalculate grid layout on image load
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