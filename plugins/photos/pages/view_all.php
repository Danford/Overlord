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
<article id="photos">
	<div class="grid">
		<div class="grid-sizer"></div>
		<?php foreach ($photos as $photo) : ?>
		<a href="/profile/<?php echo $photo['owner']->id; ?>/photo/<?php echo $photo['id']; ?>">
			<div class="grid-item tile">
				<div id="title"><?php echo $photo['title']; ?></div>
				<div id="photo">
					<img src="/profile/<?php echo $photo['owner']->id; ?>/photo/<?php echo $photo['id']; ?>.png" />
				</div>
				<div id="description"><?php echo $photo['description']; ?></div>
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
	  masonry: {
	    columnWidth: '.grid-sizer'
	  }
	
	});
$grid.imagesLoaded().progress( function() {
	  $grid.isotope('layout');
	});
$grid.on( 'hover', '.grid-item', function() {
	  // change size of item by toggling gigante class
	  $( this ).toggleClass('grid-item--hover');
	  $grid.isotope('layout');
	});

</script>
<?php $page->footer(); ?>