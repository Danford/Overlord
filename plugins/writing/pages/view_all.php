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
include(oe_frontend."page_minion.php");
include($oe_plugins['writing']."lib/writing.lib.php");
$page = new page_minion("Writings");
$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");

$writings = get_writings();
?>
<pre><?php print_r($writings); ?></pre>
<article id="writings">
	<div class="grid">
		<div class="grid-sizer"></div>
		<?php foreach ($writings as $writing) : ?>
		<a href="/profile/<?php echo $profile->id; ?>/writing/<?php echo $writing['id']; ?>">
			<div class="grid-item tile">
				<div id="title"><h2><?php echo $writing['title']; ?></h2></div>
				<div id="subtitle"><h3><?php echo $writing['subtitle']; ?></h3></div>
				<div id="photo"><img src="/images/noavatar.png" /></div>
				<div id="date-updated"><?php echo $writing['last_updated']; ?></div>
				<div id="excerpt">This will preview the writing content.</div>
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

</script>
<?php $page->footer(); ?>