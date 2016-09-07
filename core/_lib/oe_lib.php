<?php

function oe_time( $timestamp='' ) // returns date as YYYY-MM-DD HH:MM:SS 
{
    if( $timestamp == '' )
    {
        $timestamp = time() ;
    }

    return date( 'Y-m-d H:i:s', $timestamp ) ;
}

function oe_time_adjust( $oe_time, $offset ) // adjusts a formatted date by $offset hours
{
    return oe_time( strtotime( $oe_time ) + ( $offset * 3600 ) ) ;
}

function friendly_time( $oe_time ){

    $time = strtotime( $oe_time ) ;

    return date( 'l, F jS, Y g:i A', $time );

}


function http_ok()
{
    // forces the browser to recognize a page as a real page, rather than a 404 page.

    header( 'HTTP/1.0 200 OK' ) ;
}

function http_404()
{
    // forces a browser to recognize a legitimate page as a 404.
    // This is useful for people directly accessing scripts that they shouldn't be.

    header( 'HTTP/1.0 404 Not Found' ) ;
}


function createGUID() {
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = substr($charid, 0, 8).$hyphen
    .substr($charid, 8, 4).$hyphen
    .substr($charid,12, 4).$hyphen
    .substr($charid,16, 4).$hyphen
    .substr($charid,20,12) ;
    return $uuid;
}


function security_restrict() {
    
    // if secure_only is set to true, then any access will be redirected to the https:// equivalent
    
    if( secure_only and substr( $_SERVER["SCRIPT_URI"], 0, 5) == "http:"  ) {
    
        header( "Location:  https://".substr( $_SERVER["SCRIPT_URI"], 6 ) ) ;
        die() ;
    }
    
}

function image_link( $type, $id = null ){
    
    if ( ( $type == 'avatar' or $type == 'userthumb' ) and ( $id == 0 or $id == null )) {
            return '/images/noavatar.png' ;
    } else {
        return '/imgs/'.$type.'/'.$id.'.png' ;
    }    
    
}

function process_user_supplied_html( $input ) {

    global $htmlawed_config ;
    global $htmlawed_spec ;
    
    include_once oe_lib.'htmLawed.php' ;
    
    $output = htmLawed( $input, $htmlawed_config, $htmlawed_spec ) ;
    
    // TODO parse output for onsite embed codes
    // TODO parse for <a> tag and set target attribute
    
    return $output ;
    
}

function prevent_html( $string ){

    // for text that isn't supposed to have html
    
    $string = str_replace('<', '&lt;', $string ) ;
    $string = str_replace('>', '&gt;', $string ) ;
    
    return $string ;
    
}



function debug( $string ) {

    $f = @fopen( debug_log , 'a' ) ;
    fwrite( $f, oe_time()."---- DEBUG -----".PHP_EOL ) ;
    fwrite( $f, $string.PHP_EOL ) ;
    fwrite( $f, oe_time()."---- DEBUG -----".PHP_EOL ) ;
    fclose( $f );

}


function security_report( $string ) {

    $f = @fopen( debug_log , 'a' ) ;
    fwrite( $f, oe_time()."----".PHP_EOL ) ;

    fwrite( $f, "IP ADDRESS:".$_SERVER['REMOTE_ADDR']." port ".$_SERVER['REMOTE_PORT'].PHP_EOL ) ;
    fwrite( $f, "TARGET: ".$_SERVER['REQUEST_URI'].PHP_EOL ) ;
    fwrite( $f, "REF: ".$_SERVER['HTTP_REFERER'].PHP_EOL ) ;
    
    if( isset( $_POST ) and is_array( $_POST )) {

        fwrite( $f, PHP_EOL ) ;
        fwrite( $f, "POST: ".render_array_as_string( $_POST ) );
        fwrite( $f, PHP_EOL ) ;
    }

    fwrite( $f, PHP_EOL ) ;
        
    fwrite( $f, $string.PHP_EOL ) ;
    fwrite( $f, oe_time()."----".PHP_EOL ) ;
    fclose( $f );

}



function render_array_as_string( $array ){
    
    $output = $array[0] ;
    
    for( $i = 1 ; $i < count( $array ) ; $i++ ){
        
        $output .= ", ".$output[i] ;
    }
    
    return $output ;
}

function verify_number( $number ){
    return ( preg_match( '/^[0-9]*$/', $number ) != 0 ) ;
}

function number_or_die( $number ){

    if( preg_match( '/^[0-9]*$/', $number ) == 0 ){ die() ; }

}


function log_activity( $type, $ref, $user_id='' ){
    global $db ;
    global $user ;

    if( $user_id == '' ) { $user_id = $user->id ; }

    $db->insert( "INSERT INTO `user_activity` SET `user_id`='".$user_id."', `type`='".$type."',
                            `ref`='".$ref."', `timestamp`='".oe_time()."'" ) ;

}

function notify_user( $user_id, $type, $ref  ){
    global $db ;

    $db->insert( "INSERT INTO `user_notifications` SET `user_id`='".$user_id."', `type`='".$type."',
                            `ref`='".$ref."', `timestamp`='".oe_time()."'" ) ;
}

function user_age( $birthdate ) {

    $today = oe_time() ;

    $by = substr($birthdate, 0, 4 ) ;
    $ty = substr($today, 0, 4 ) ;
    $bm = substr($birthdate, 4, 2 ) ;
    $tm = substr($today, 4, 2 ) ;

    if( $bm > $tm ) {
        return ( $ty - ( $by + 1 )) ;
    } elseif( $bm < $tm ) {
        return ( $ty - $by ) ;
    } elseif ( substr( $birthdate, 7, 2 ) < substr($today, 7, 2 ) ) {
        return ( $ty - ( $by + 1 ) ) ;
    } else {
        return ( $ty - $by ) ;
    }
}



function user_likes_item( $type, $item ){
    global $user ;
    global $db ;
    $db->query( "SELECT `liked_by` FROM `profile_".$type."_like` WHERE `".$type."_id` ='".$item."'
        AND `liked_by`='".$user->id."' LIMIT 1" ) ;
    $c = ( $db->num_rows() == 1 ) ;
    $db->free() ;
    return $c ;
}