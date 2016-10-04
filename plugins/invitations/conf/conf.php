<?php

// insert into settings if not already defined.

foreach( $default as $setting => $value ){
    if( isset( $oepc[$tier]['invitations'][$setting] ) ){
        $oepc[$tier]['invitations'][$setting] = $value ;
    }
}
