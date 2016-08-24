<?php

include( oe_lib."page_minion.php" ) ;

$page = new page_minion( $profile->name." - Photos" ) ;

$page->header() ;

$rowcount = 0 ;
foreach( $profile->get_photo_list() as $photo ){

    $rowcount++ ;
    
    ?><div style="inline-block">
    
    	<a href="/profile/<?php print( $profile->id ); ?>/photo/<?php print( $photo['photo_id']) ; ?>" title="<?php  print( $photo["title"] ) ; ?>">
    	<img src="<?php  print( create_image_link( 'userthumb', $photo['photo_id'])) ; ?>" />
    	</a>
    	<br />
    <?php

    if( $photo['likes'] > 0 ){ print( $photo['likes']." likes<br />") ; }
    if( $photo['comments'] > 0 ){ print( $photo['comments']." comments") ; }
    
    ?></div><?php 
    
    /* also available here-- $photo['description'] */
    
    if( $rowcount == 5 ){
    
        $rowcount = 0 ;
        print( '<br />' ) ;
    
    }
    
}


$page->footer() ; ?>