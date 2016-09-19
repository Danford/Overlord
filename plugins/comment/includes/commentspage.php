<?php

include_once $oe_plugins['comment']."lib/comment.lib.php";
include_once $oe_plugins['comment']."conf/conf.php";

if( $uri[$pos] == "page" ){
    $page = $uri[ $pos + 1 ] ;
} else {
    $page = 1 ;
}

/*
 *  use get_comments( ( $page - 1) * $oepc[$tier]['comment']['page'], $oepc[$tier]['comment']['page'] ) to retrieve an array
 *
 *     id - comment id
 *     comment - comment text
 *     owner - profile object
 *
 *
 *  it is up to you to decide whether going from page to page is an ajax thing, or actually going to the next page.
 *  If ajax, use API call getComments.
 *
 */