<?php

class serveable_minion {
    
    protected $elements = array();
    protected $cooked = null;
    
    function __construct()
    {
    }
    
    public function Cook() {
        if ( isset( $this->cooked ) )
            return $this->cooked ;
        
        $buffer = "" ;
        foreach ( $this->elements as $element ) {
            if ( is_a( $element, 'Element' ) ) {
                $buffer .= $element->Cook() ;
            } else {
                $buffer .= $element ;
            }
        }
        
        $cooked = buffer ;
        return $buffer ;
    }

    public function Serve() {
        if ( isset( $this->cooked ) )
            echo $this->cooked;
        
        foreach ( $this->elements as $element )
        {
            if ( is_a( $element, 'Element' ) )
                $element->Serve() ;
            else
                echo $element ;
        }
    }
}
?>