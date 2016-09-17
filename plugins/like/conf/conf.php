<?php
$default['table'] = 'like' ;
$default['view'] = 'like' ;


foreach( $default as $setting => $value ){
    if( isset( $oepc[$tier]['like'][$setting] ) ){
        $oepc[$tier]['like'][$setting] = $value ;
    }
}