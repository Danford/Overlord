<?php

// DIRECTORIES

/*
 *  Note that oe_root, oe_core and oe_lib are *filesystem* paths, whereas oe_css and oe_js
 *  refer to *urls.*
 *
 *  Really, though, there's no good reason to change them.
 */

define( 'oe_mod', oe_root.'mods/' ) ;
define( 'oe_plugin', oe_root.'plugins/' ) ;
define( 'oe_lib', oe_core.'_lib/' ) ;
define( 'oe_config', oe_core.'_conf/' ) ;
define( 'oe_includes', oe_core.'includes/' ) ;
define( 'oe_css', httproot.'css/' ) ;
define( 'oe_js', httproot.'js/' ) ;
define( 'oe_log', oe_root."oe_logs/" ) ;
define( "oe_images", oe_root."oe_images/" ) ;

define( 'oe_frontend', oe_root.'frontend/' ) ;
define( 'oe_isotope', oe_frontend."html/modules/isotope/" ) ;


// the number of login failures before an account is locked.

    define( 'max_login_fails', 5 ) ;

// the number of login failures before an account is locked.

    define( 'max_login_fails', 5 ) ;

// the number of "keep me logged in" tokens that are out there

    define( 'max_login_tokens', 2 ) ;
    
// the duration of "keep me logged in" tokens, in days

    define( 'persistent_login_duration', 14 ) ;

    define( 'sql_error_log', oe_log."/sqlerrors.log" ) ;
    define( 'debug_log', oe_log."/debug.log" ) ;
    define( 'security_log', oe_log."/security.log" ) ;

// how long the site will wait for an update verification without barfing.

    define( 'verify_interval', .25 );
    define( 'verify_timeout', 10 );
	
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
	$gender[5]["label"] = 'Genderfluid' ;
	$gender[5]["abbr"] = "GF" ;
	$gender[6]["label"] = 'Genderqueer' ;
	$gender[6]["abbr"] = "GQ" ;
	$gender[7]["label"] = 'Non-Binary' ;
	$gender[7]["abbr"] = "NB" ;
	

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