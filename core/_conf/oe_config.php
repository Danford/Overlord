<?php

// DIRECTORIES

/*
 *  Note that oe_root, oe_core and oe_lib are *filesystem* paths, whereas oe_css and oe_js
 *  refer to *urls.*
 *
 *  Really, though, there's no good reason to change them.
 */

define( 'oe_mod', oe_root.'modules/' ) ;
define( 'oe_lib', oe_core.'_lib/' ) ;
define( 'oe_config', oe_core.'_conf/' ) ;
define( 'oe_includes', oe_core.'includes/' ) ;
define( 'oe_css', httproot.'css/' ) ;
define( 'oe_js', httproot.'js/' ) ;
define( 'ul_img_dir', oe_root.'oe_images/' ) ;
define( 'oe_log', oe_root."oe_logs/") ;


// the number of login failures before an account is locked.

    define( 'max_login_fails', 5 ) ;

// the number of "keep me logged in" tokens that are out there

    define( 'max_login_tokens', 2 ) ;
    
// the duration of "keep me logged in" tokens, in days

    define( 'persistent_login_duration', 14 ) ;
    
// maximum dimensions of uploaded images in pixels

    define( 'max_image_width', 1000 ) ;
    define( 'max_image_height', 800 ) ;
    
    define( 'thumbnail_size', 125 ) ;

    define( 'profile_image_size', 300) ;
    define( 'profile_thumb_size', 75 ) ;
    
//  MySQL CONFIG

    /*
     *  Built with the concept of multiple servers for upload and download
     *  
     *  This section can be edited to provide a round-robin if needed 
     */

    $sql_config[ 'insert' ]['host'] = 'db.codexfive.net' ;
    $sql_config[ 'insert' ]['db'] = 'codexfive_db' ;
    $sql_config[ 'insert' ]['user'] = 'kinkyrobot' ;
    $sql_config[ 'insert' ]['pass'] = 'p3rv3rse!d3l1ghts' ;
    
    $sql_config[ 'select' ] = $sql_config[ 'insert' ] ;

    define( 'sql_error_log', oe_log."/sqlerrors.log" ) ;
    define( 'debug_log', oe_log."/debug.log" ) ;
    define( 'security_log', oe_log."/security.log" ) ;


	
// SITE DEFAULTS

	/*
	 * 	To configure a default set of other headers (such as metatags) , uncomment and fill in the line below. 
	 *  Note that this is the raw HTML, and NOT an array.  
	 *  
	 *  It should be noted that the header function oe_html_headers will provide this value, if configured, on
	 *  EVERY page.  If you specify a value and do not want it on a given page, it will need to be blanked out.
	 */
	
		// $oe_config['headers'] = '' ;
		
	/*
	 * 	To configure a default stylesheet, uncomment and fill in the line below.
	 * 
	 *  It should be noted that the oe_html_headers will IGNORE this value if other stylesheets are specified. 
	 */
	
		// $oe_config['css'] = oe_css.'main.css' ;
/*
 * Default Pages - uncomment these if and only if they are not defined in the database.
 */

    $oe_pages[ './main' ] = oe_core.'pages/main.php' ;
	$oe_pages[ './404' ] = oe_core.'pages/404.php' ;
	$oe_pages[ 'logout' ] = oe_core.'login/pages/logout.php' ;

	
/*
 *  Gender config
 *  
 */
    $gender[0]["label"] = "" ;
    $gender[0]["abbr"] = "" ;
	$gender[1]["label"] = 'Male' ;
	$gender[1]["abbr"] = "M" ;
	$gender[2]["label"] = 'Female' ;
	$gender[2]["abbr"] = "F" ;
	$gender[3]["label"] = 'Trans Male' ;
	$gender[3]["abbr"] = "TM" ;
	$gender[4]["label"] = 'Trans Female' ;
	$gender[4]["abbr"] = "TF" ;
	$gender[5]["label"] = 'Gender Fluid' ;
	$gender[5]["abbr"] = "GF" ;
	$gender[6]["label"] = 'Gender Queer' ;
	$gender[6]["abbr"] = "GQ" ;
	$gender[7]["label"] = 'Non-Binary' ;
	$gender[7]["abbr"] = "NB" ;
	

/*
 *  modules-- basically subsets of scripts defined for a specific purpose-- are defined here.
 *
 *  the array key for a module is the uri value that will lead to it being called, and the
 *  value is the script that manages the module itself (the 'handler').
 */

	$oe_modules[ 'register' ] = oe_core.'register/' ;
	//$oe_modules[ 'sample' ] = oe_core.'dbpagemodule.php' ;
	$oe_modules[ 'login' ] = oe_core.'login/' ;
	$oe_modules[ 'profile' ] = oe_mod.'profile/' ;
	$oe_modules[ 'imgs' ] = oe_core.'images/' ;
	$oe_modules[ 'group' ] = oe_mod.'group/' ;
	$oe_modules[ 'groups' ] = oe_mod.'group/' ;
	$oe_modules[ 'event' ] = oe_mod.'event/' ;
	$oe_modules[ 'events' ] = oe_mod.'event/' ;
	
/*
 *  pages -- individual pages that exist as http://yoursite.com/whatever
 *  
 *  Note that a page can actually part of a module, but not 
 *  accessed via a subdirectory.
 * 
 */

	
/* htmLawed configuration */
	
	$htmlawed_config = array(
	    'safe'=>1, // Dangerous elements and attributes thus not allowed
	    'elements'=>'* -img' // no image tags are allowed
	);
	$htmlawed_spec = 'a = -*, title, href;' ; // The 'a' element can have only these attributes
	    
/*
 *  group configuration
 */
	
	// thread/message listings
	
	define( 'threads_per_page',  5  );
	define( 'messages_per_page',  5  );

	// membership listing
	
	define( 'members_per_column', 6 ) ;
	define( 'members_columns', 2 ) ;

/*
 *  year configuration
 */

	define( 'event_year_range', 3 ) ;
	?>