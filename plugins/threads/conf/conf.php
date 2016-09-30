<?php

// insert into settings if not already defined.

$default['table'] = 'thread' ;
$default['view'] = 'thread' ;
$default['threads_per_page'] = 5 ;
$default['comments_per_page'] = 15 ;

foreach( $default as $setting => $value ){
    if( isset( $oepc[$tier]['thread'][$setting] ) ){
        $oepc[$tier]['thread'][$setting] = $value ;
    }
}