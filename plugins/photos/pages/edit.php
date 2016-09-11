<?php
  /*
   * 
   *    Form should contain
   *    
   *        oe_api          photo
   *        oe_call         upload
   *        oe_module       $plug[0]['type']
   *        oe_module_id    $plug[0]['id']
   *        oe_parent       to which module or plugin is this attached?
   *                        if omitted, oe_module is assumed
   *        oe_parent_id    to which item in that module or plugin?
   *                        if omitted, oe_module_id is assumed

            photo_id

    		privacy         options defined by module
    		title           string, optional
    		description     text, optional
    		
    		parentavatar    checkbox      only if admin ( $plug[0]['admin'] == true ; )
    		
    		
    		leave room for
    		
    		album - 'None', 'New', or ID
    		new_album_title - string, optional
    		new_album_description - string, optional 
    		
    		albumavatar     checkbox      only if admin and albums are set 
		                                  ( $pluginconf[ $tier ]['photo']['useAlbum'] == true )
    		
   * 
   * 
   * 
   * 
   */