<?php

    /*
     *  This "module" is really just a script to load pages from a database.  It is called by the engine 
     *  to attempt to load pages of the "_core" module, but it can be used in two other contexts, such as:
     *  
     *  - If a module is defined in oe_php as this script, then it exists as a "virtual module" which is
     *      just a collection of pages in the database.
     *      
     *  - A module handler can refer to this script to store some of its pages in the database, either 
     *      directly or simply by not die()ing.
     * 
     */


    if( ( ! isset( $uri[$pos] ) ) or $uri[$pos] == "" ){ $uri[$pos] = "./main" ; }

	$db->query( "select * from oe_pages where 
										module='".$oe_config['module']."' 
										and 
										url_key='".$uri[ $pos ]."'" ) ;
	
	if( ( $oe_page = $db->assoc() ) != false ) // we have a match, so it's a database page.  load it now.
	{		    
	    include( oe_lib.'page_minion.php' ) ;
	    
	    $p = new page_minion( $oe_page['title '], $oe_page['externalcss'], $oe_page['externaljs'] ) ;
	    
	    $p->add_header_tag( $oe_page['otherheaders'] ) ;
	    
	    $p->set_body_calls( $oe_page['bodycalls'] ) ;
	    
	    $p->header( ! isset( $oe_404 ) ) ;
	    
	    print( $oe_page['bodycontent'] ) ;
	    
	    $p->footer() ;
	}

?>