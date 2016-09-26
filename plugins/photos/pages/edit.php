<?php
  /*
   * 
   *    values are provided as $original.  use $form->fill_with_values( $original )
   *    at the moment there is no prefill for the avatar fields
   *    but the functionality works for 'parentavatar' checkbox
   * 
   *    Form should contain
   *    
   *        oe_api          photo
   *        oe_call         edit
   *        oe_module       $oepc[0]['type']
   *        oe_module_id    $oepc[0]['id']
   *        oe_parent       to which module or plugin is this attached?
   *                        if omitted, oe_module is assumed
   *        oe_parent_id    to which item in that module or plugin?
   *                        if omitted, oe_module_id is assumed

            photo_id

    		privacy         int, options defined by module as $privacyoptions ;
    		title           string, optional 75 chars max, no html
    		description     text, optional 255 chars max, no html
    		
    		parentavatar    checkbox      only if admin ( $oepc[0]['admin'] == true ; )
    		
    		
    		album fields - not yet implemented
    		
    		  - may be part of an include, as any plugin that wants to use albums
    		    will want it.
    		
    		album - 'None', 'New', or ID
    		new_album_title - string, optional
    		new_album_description - string, optional 
    		
    		albumavatar     checkbox      only if admin and albums are set 
		                                  ( $oepcinconf[ $tier ]['photo']['useAlbum'] == true )
    		
   * 
   * 
   * 
   * 
   */