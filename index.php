<?php


/*
 *  Overlord version 1.1
 *  
 *  /index.php provides site-specific configuration. Information that goes here is specific to the SERVER and not the application.
 *  
 */

define( 'httproot', '/' );

/*
 *  If this script is located at 'http://yourserver.com/' then leave this as '/'.
 *  If this script is located at 'http://yourserver.com/somesubdirectory/', set this to '/somesubdirectory/'.
 *
 *  httproot MUST end with the trailing slash.
 */

define( 'oe_root', './' );

/*
 * The subdirectory where the site files are kept.
 *
 * This should be an ABSOLUTE, not a relative URL.  Some files may wish not to be found;
 * when that happens, they will 'include' back to this file, with the $oe_404 flag set.
 * If this value is relative, you will get errors on your 404 page.
 *
 * For a development server, or debugging through an editor that recognises includes,
 * you can set this to ./whatever/
 *
 */


define( 'oe_core', oe_root.'core/' ) ;

define( 'oe_seed', 'WhipMeBeatMeMakeMeWriteBadCode' );

/*
 * oe_seed is prepended passwords to create the md5 hash.
 * Technically this is site configuration rather than server config,
 * but it's something you won't change, and you don't want anyone else
     * to have.  And that's what this config file is for.
     */

 include( oe_core."engine.php" ) ;

 ?>