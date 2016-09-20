<?php

// insert into settings if not already defined.

foreach( $default as $setting => $value ){
    if( isset( $oepc[$tier]['PLUGIN_NAME'][$setting] ) ){
        $oepc[$tier]['PLUGIN_NAME'][$setting] = $value ;
    }
}