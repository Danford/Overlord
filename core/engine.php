<?php
    
    include_once( oe_core.'_conf/oe_config.php' ) ;
	include_once( oe_lib.'oe_lib.php' ) ;
	include_once( oe_lib.'mysqli_minion.php' ) ;
	include_once( $oe_modules['group'].'lib/group_minion.php' ) ;
	include_once( oe_lib.'user_minion.php' ) ;
	
	session_start() ;
	
	$db = new mysqli_minion( $sql_config ) ;
	
	if( ! isset( $_SESSION['user'] ) ) {
	    $_SESSION['user'] = new user_minion() ;
	}
	
	$user =& $_SESSION['user'] ;
	 
	// POST PROCESSING
	
	if( isset( $_POST['oe_module'] ) or isset( $_POST['oe_api'] ) )
	{
	    // form submissions
	    
	    if( isset( $_POST['oe_api'] ) ){
	        $postmodule = $_POST['oe_api'] ;
	    } else {
	        $postmodule = $_POST['oe_module'] ;
	    }
        
	    if( isset( $oe_modules[ $postmodule ] ) and file_exists( $oe_modules[ $postmodule ]."post.php" ) ) {
	    
	        include( oe_lib.'post_minion.php' ) ;
	    
	        $post = new post_minion( isset( $_POST['oe_api'] ) ) ; 
	        
	        include( $oe_modules[ $postmodule ].'post.php') ;
	        die();
	    }
	    
        
        /*
         * If we got to this spot,  oe_form is not valid.
         */
        
        die( '<b>Error OE1:</b> Please report this to the webmaster.' ) ;
	}
	
	/*
	 *  Environment Setup
	 *  
	 *  First we build the $uri array, which we move through to determine where we are and what we're doing.
	 *  
	 *  
	 */

	if ( strpos( $_SERVER['REQUEST_URI'], '?' ) )
	{
		$fulluri = substr( $_SERVER['REQUEST_URI'], 0, strpos( $_SERVER['REQUEST_URI'], '?' ) ) ;
	}
	else
	{
		$fulluri = $_SERVER['REQUEST_URI'] ;
	}
	
	$uri = explode( '/', $fulluri ) ;
	
	/*
	 *  Now, one or more of the values here is useless.
	 * 
	 * 	The first value here is always going to be blank.  And if overlord lives in a subdirectory, then
	 *  the next value or values will be filled by that.  So, we need to skip ahead to the appropriate spot.
	 */
	
	$pos = substr_count( httproot, '/' ) ;
		
	if( ( $uri[$pos] == 'index.php' ) or ( ! isset( $uri[ $pos ] ) ) or ( $uri[ $pos ] == '' ) ) 
	{
		if( $user->is_logged_in() ){
		    $uri[$pos] = 'dashboard' ;
		} else {
		    $uri[$pos] = './main' ;
		}
	}
	
	/*
	 * This takes care of the few circumstances in which the URL does not tell us 
	 * explicitly what we want to show the user.  Now, if the URL is valid, it's going to be either 
	 * a page, a module, or a database entry.
	 * 
	 * The default home page, './main' can be any one of these.
	 * 
	 * ./404 Can, as well, but it also has to be defined as $oe_404_type in oe_core/_lib/config.php.  
	 */
	
	if( isset( $oe_modules[ $uri[ $pos ] ] ) ) 
	{
	    
		/*
		 * A module is a script or set of scripts kept in a subdirectory for 
		 * easier management.  Modules are defined in $oe_modules[], which identifies 
		 * the location of the module. The "handler" of that module takes over processing from here.
		 */
				
		if( file_exists( $oe_modules[ $uri[ $pos ] ]."handler.php" ) )
		{
			$pos++ ;						 
			include( $oe_modules[ $uri[ $pos - 1 ]]."handler.php"  ) ;
		}
			/*
			 * Note that we do not die(). 
			 * A module may choose to handle its own 404 and
			 * database-resident pages, but it can just as easily fail
			 * out to here and let the rest of this script handle that.
			 * 
			 * The module can also predefine default stylesheets, templates,
			 * and external javascript files. A clever person-- or at least a 
			 * person who has read these comments and has need of such a method-- 
			 * could create a handler that does nothing but override those 
			 * defaults (where needed), and define it as a module instead of a
			 * virtual module. 
			 */  
	}
	
	if( isset( $oe_pages[ $uri [ $pos ] ] ) )
	{
		/*
		 * pages are one shot scripts that display one page-- display, for example,
		 * one type of information stored in the database in a unique way.
		 * 
		 * This does not preclude the use of the uri commands for details, but the
		 * explanation is that this is one file. 
		 * 
		 * Also note that if ./main is configured as a page in config.php, it will be
		 * loaded here. The same can be said of ./404, but it will only load here if it 
		 * has been forced.
		 * 
		 */ 
		
		$pos++ ;
		
		if( ! isset( $oe_config['module'] ) ){
		    $oe_config['module'] = "./main" ;
		}
		
		include( $oe_pages[ $uri[ $pos - 1 ] ] ) ;
		
		/*
		 *  If a page does not die(), it is assumed it didn't like the input and 
		 *  has fallen back here to throw a 404 page.
		 */
		
		$uri[ $pos ] = './404' ;
	}

	// okay, it's not a page based in an actual file.  Let's check the DB.
	
    include( oe_core.'dbpagemodule.php' ) ;
	
	// if we've come all the way here, it's time to throw a 404.

	http_404() ;
	$oe_config['module'] = "./main" ;
	include( oe_pages['404'] ) ;
	die();
	
	?>