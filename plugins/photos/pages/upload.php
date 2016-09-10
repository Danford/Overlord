<?php
/*
 *
*    Form should contain
*
*        oe_api          photo
*        oe_call         upload
*        oe_module       $parent[0]['type']
*        oe_module_id    $parent[0]['id']
*        oe_parent       to which module or plugin is this attached?
*                        if omitted, oe_module is assumed
*        oe_parent_id    to which item in that module or plugin?
*                        if omitted, oe_module_id is assumed



        privacy         options defined by module
        title           string, optional
        description     text, optional

        parentavatar    checkbox          only if $pluginconf[0]['admin'] == true 

        album - 'None', 'New', or ID
        new_album_title - string, optional
        new_album_description - string, optional
        photo - the actual file
        albumavatar     checkbox          only if $pluginconf[0]['admin'] == true and $useAlbums == true
*
*
*
*
*/