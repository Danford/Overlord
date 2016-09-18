<?php


$default['table'] = 'writing' ;

// the database table used for inserts & updates

$default['view'] = 'writing' ;

// the database table or view used for queries

// insert into settings if not already defined.

foreach( $default as $setting => $value ){
    if( isset( $oepc[$tier]['writing'][$setting] ) ){
        $oepc[$tier]['writing'][$setting] = $value ;
    }
}