<?php

require_once( oe_frontend . 'html/tags/script.php' ) ;

/*
 *   css_minion() {
 * 
 *      
 * 
 */

class css_minion extends serveable_minion {
    var $html_minion ;
    var $cssFiles ;
    var $cssRaw ;
    
    function __construct( $html_minion )
    {
        $this->html_minion = $html_minion ;
        $this->cssFiles = array() ;
        $this->cssRaw = array() ;
    }
    
    function addFile( $path ) {
        $this->cssFiles[] = $path ;
    }

    function addRaw( $raw ) {
        $this->cssRaw[] = $raw ;
    }
    
    function Cook() {
        foreach ( $this->cssFiles as $file ) {
            $this->html_minion->head->AddElement( new Link( "stylesheet", $file ) ) ;
        }
        
        if ( count( $this->cssRaw ) > 0 ) {
            $style = $this->html_minion->head->AddTag( "style" ) ;
            
            foreach ( $this->cssRaw as $raw ) {
                $style->AddContent( $raw ) ;
            }
        }
    }

    function Serve() {
        
    }
}
?>