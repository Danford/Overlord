<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( $profile->name." - Album - ".$album['title'] ) ;

$page->header() ;

print( '<h1>'.$album['title'].'</h1>' );

print( $album['description'].'<br />' );


if( $album['photos'] != 0 ){
    print( $album['photos'].' Photo' );

    if( $album['photos'] > 1 ){ print( 's' ); }

    print( '<br/>' );
    
    $rowcount = 0 ;
    foreach( $profile->get_photo_list( 0, 99999999, $album['album_id'] ) as $photo ){
    
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
    
}

if( $album['prose'] != 0 ){
    print( $album['prose'].' Writing' );

    if( $album['prose'] > 1 ){ print( 's' ); }

    print( '<br />' );
    

    foreach( $profile->get_prose_list( 0, 9999999, $album['album_id'] ) as $writing ){
    
        print( '<a href="/profile/'.$profile->id."/writing/".$writing['prose_id'].'">'.$writing['title'].'</a><br />' );
        print( '&nbsp;&nbsp;'.$writing['subtitle'].'<br />' );
    
    }
}

if( $album['videos'] != 0 ){
    print( $album['videos'].' Video' );

    if( $album['videos'] > 1 ){ print( 's' ); }

    print( '<br />' );
    
    foreach( $profile->get_video_list( 0, 9999999, $album['album_id'] ) as $video ){
        
        // placeholder
        
    }
}



$page->footer() ; ?>