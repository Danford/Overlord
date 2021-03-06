<?php

require_once( oe_frontend . 'html/tags/script.php' ) ;

/*
 *   js_minion() {
 * 
 *      this is just placeholder for any javascript magic we
 *      will want to do later at the very least for optimization
 *      and SEO page rank.
 */

class js_minion extends serveable_minion {
    var $html_minion ;
    var $jsFiles ;
    var $jsFooterFiles ;
    var $jsRaw ;

    function __construct( $html_minion ) {
        $this->html_minion = $html_minion ;
        $this->jsFiles = array() ;
        $this->jsFooterFiles = array() ;
        $this->jsRaw = array() ;
    }

    function addFile( $path, $footer = false ) {
        if ( $footer == false )
        	$this->jsFiles[] = $path ;
        else
        	$this->jsFooterFiles[] = $path ;
    }

    function addRaw( $raw ) {
        $this->jsRaw[] = $raw ;
    }

    function Cook() {
        foreach ( $this->jsFiles as $file ) {
            $this->html_minion->head->AddElement( new Script( $file ) ) ;
        }

        foreach ( $this->jsFooterFiles as $file ) {
        	$this->html_minion->footer->AddElement( new Script( $file ) ) ;
        }
        
        if ( count( $this->jsRaw ) > 0 ) {
            foreach ($this->jsRaw as $raw) {
                $this->html_minion->head->AddElement( new Script( null, $raw ) ) ;
            }
        }

        parent::Cook() ;
    }
    
    function Serve() {
        parent::Serve() ;
    }
}

?>