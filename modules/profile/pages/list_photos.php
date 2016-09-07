<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( $profile->name." - Photos" ) ;
$page->header() ;
$page->js_minion->addFile("https://unpkg.com/isotope-layout@3.0/dist/isotope.pkgd.js");

?>

<div class="grid">
<?php foreach( $profile->get_photo_list() as $photo ) : ?>
    <?php $rowcount++ ; ?>
    
    <div class="grid-item">
    
    	<a href="/profile/<?php print( $profile->id ); ?>/photo/<?php print( $photo['photo_id']) ; ?>" title="<?php  print( $photo["title"] ) ; ?>">
    	<img src="<?php  print( image_link( 'userthumb', $photo['photo_id'])) ; ?>" />
    	</a>
    
    <?php if( $photo['likes'] > 0 ){ print( $photo['likes']." likes<br />") ; } ?>
    <?php if( $photo['comments'] > 0 ){ print( $photo['comments']." comments") ; } ?>
    
    </div>
<?php endforeach; ?>
</div>
<script>
var iso = new Isotope( '.grid', {
	  itemSelector: '.grid-item',
	  layoutMode: 'fitRows'
	});
</script>
<?php 

$page->footer() ;

?>