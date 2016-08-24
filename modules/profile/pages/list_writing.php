<?php

include( oe_lib."page_minion.php" ) ;

$page = new page_minion( $profile->name." - Writing" ) ;

$page->header() ;

foreach( $profile->get_prose_list() as $writing ){
    
    print( '<a href="/profile/'.$profile->id."/writing/".$writing['prose_id'].'">'.$writing['title'].'</a><br />' );
    print( '&nbsp;&nbsp;'.$writing['subtitle'].'<br />' );
    
}

$page->footer() ; ?>