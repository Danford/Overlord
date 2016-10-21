<?php

/*
 *   page_minion( $title[, $externalcss, $externaljs, $doctype ] ) {
 * 
 *      $externalcss and $externaljs can be single values, arrays, or comma delimited strings
 * 
 */

require_once(oe_frontend . "html_minion.php") ;
require_once(oe_frontend . "css_minion.php") ;
require_once(oe_frontend . "js_minion.php") ;

class page_minion {
    var $html_minion ;
    var $css_minion ;
    var $js_minion ;
    
    var $is_outputed ;
    
    function __construct( $title, $externalcss = '', $externaljs = '', $doctype='' ) {
        $this->html_minion = new html_minion( $title ) ;
        $this->css_minion = new css_minion( $this->html_minion ) ;
        $this->js_minion = new js_minion( $this->html_minion ) ;
        
        $this->addcss ( $externalcss ) ;
        $this->addjs ( $externaljs ) ;
        
        $this->addcss( "/css/main.css" );
        
        $this->is_outputed = false;
    }
    
    function __destruct()
    {
        if ($this->is_outputed == false)
        {
            $this->footer();
            $this->is_outputed = true;
        }
    }
    
    function addjs( $js, $footer = false ) {
        
        if( $js != '' ) {
            if( is_array( $js ) ) {
                foreach( $js as $j ) {
                    $this->js_minion->addFile( $j ) ;
                }
            } else {
                foreach( explode( ',', $js ) as $j ) {
                    $this->js_minion->addFile( $j ) ;
                }
            }            
        }
    }
    
    function addcss( $css ) {
        
        if( $css != '' ){
            
            if( is_array( $css ) ) {
                foreach( $css as $c ) {
                    $this->css_minion->addFile( $c ) ;
                }
            } else {
                foreach( explode( ',', $css ) as $c ) {
                    $this->css_minion->addFile( $c ) ;                         
                }
            }            
        }
    }
    
    function header( $send_ok = true ) {
        // Collect anything printed to the output buffer so that advanced html_minion features are completely optional.
        // Although it is not recommended to use normal output functions anymore as it will make updates and implementing
        // new features more difficult.
        ob_start() ;
    }

    function footer() {
        $content = ob_get_contents() ;
        
        // seems a little weird but this statement checks if ob_get_contents() has data aka if something was printed to the output buffer.
        // if something was printed to the output buffer this adds that to the main content section in our html_minion.
        if ( isset( $content ) ) {
            $this->html_minion->content->AddContent( $content ) ;
        }
        
        ob_end_clean() ;
        $this->css_minion->Cook();
        $this->js_minion->Cook();
        $this->html_minion->Serve() ;
        
        $this->is_outputed = true;
        
        die() ;
    }
    
}
?>