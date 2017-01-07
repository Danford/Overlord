<?php

// insert into settings if not already defined.

$default['table'] = 'invitations' ;
$default['view'] = 'invitations' ;

foreach( $default as $setting => $value ){
    if( !isset( $oepc[$tier]['invitations'][$setting] ) ){
        $oepc[$tier]['invitations'][$setting] = $value ;
    }
}
