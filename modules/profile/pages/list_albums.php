<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( $profile->name." - Albums" ) ;

$page->header() ;

foreach( $profile->get_album_list() as $album ){
    
    print( '<a href="/profile/'.$profile->id.'/album/'.$album['album_id'].'">'.$album['title'].'</a><br/>' );

    if( $album['photos'] != 0 ){
        print( $album['photos'].' Photo' );
        
        if( $album['photos'] > 1 ){ print( 's' ); }
        
        print( ' ' );
    }
    if( $album['prose'] != 0 ){
        print( $album['prose'].' Writing' );
        
        if( $album['prose'] > 1 ){ print( 's' ); }
        
        print( ' ' );
    }
    if( $album['videos'] != 0 ){
        print( $album['videos'].' Video' );
        
        if( $album['videos'] > 1 ){ print( 's' ); }
        
        print( ' ' );
    }
    
    print( $album['description'].'<br />' );
    
}

$page->footer() ; ?>