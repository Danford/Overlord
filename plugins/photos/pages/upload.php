<?php
  /*
   * 
   *    Form should contain
   *    
   *        oe_api          photo
   *        oe_call         upload
   *        oe_module       $oepc[0]['type']
   *        oe_module_id    $oepc[0]['id']
   *        oe_plug       to which module or plugin is this attached?
   *                        if omitted, oe_module is assumed
   *        oe_plug_id    to which item in that module or plugin?
   *                        
                
            photo           the actual file
            
    		privacy         int, options defined by module as $privacyoptions ;
    		title           string, optional 75 chars max, no html
    		description     text, optional 255 chars max, no html
    		
    		parentavatar    checkbox      only if admin ( $oepc[0]['admin'] == true )
    		
    		album fields
    		
    		  - may be part of an include, as any plugin that wants to use albums
    		    will want it.
    		
    		album - 'None', 'New', or ID
    		new_album_title - string, optional
    		new_album_description - string, optional 
    		
    		albumavatar     checkbox      only if admin and albums are set 
		                                  ( $oepc[ $tier ]['photo']['useAlbum'] == true )
   */


include(oe_frontend . "page_minion.php");

include( oe_lib."form_minion.php" ) ;

$page = new page_minion("Upload Photo");

$page->header();

// you don't need....
// $oe_api = "photo";
// $oe_call = "upload";

// instead do this:

   $form = new form_minion("upload", "photo" ); 

// in this one specific case, you'll also need...

   $form->has_file() ;
      

// these aren't strictly necessary.   
   
global $oepc;
global $privacyoptions;


/* none of this is needed anymore 
$oe_module = $oepc[0]['type'];
$oe_module_id = $oepc[0]['id'];

$oe_parent = ""; // I believe this should be set by a parameter created from the 
$oe_parent_id = "";

*/

print_r($privacyoptions);

?>

<div id="upload-photo-form">

<?php
        /* and instead of....	    

	    <form action="/">
		<input name="oe_api" hidden="true" value="<?php echo $oe_api; ?>"/>
		<input name="oe_call" hidden="true" value="<?php echo $oe_call; ?>"/>
		
		<input name="oe_module" hidden="true" value="<?php echo $oe_module; ?>"/>
		<input name="oe_module_id" hidden="true" value="<?php echo $oe_module_id; ?>"/>
		<input name="oe_parent" hidden="true" value="<?php echo $oe_parent; ?>"/>
		<input name="oe_parent_id" hidden="true" value="<?php echo $oe_parent_id; ?>"/>
		
		you just use:  */

        $form->header() ;
		
        //  but you can do hidden variables like this:  $form->hidden("oe_module", $oe_module ) ;
        
        ?>
		<p>Uploaded Image: <?php $form->file_input("photo") ; ?></p>
		
		<p>Privacy:
		
			<?php $form->select( "privacy", $privacyoptions ) ;
			
			
			/* rather than>
		 
			<select name="privacy">
				<option value="">Value</option>
				<option value="">Value</option>
				<option value="">Value</option>
			</select> */ ?>
		</p>
		
		<p>Title: <?php $form->text_field("title" ); ?></p>
		<p>Description: <?php $form->text_field("title" ); ?></p>
		
		<?php /*
		
		  <input name="parentavatar" title="Make Avatar" type="checkbox"/> 
		  
		      The title didn't work.
		  
		      */
			?>
			
			<p>Make Avatar <?php $form->checkbox("parentavatar") ; ?></p>
		
		<?php  
		  
		  $form->submit_button("Upload Photo" ) ;
		
		/*
		
		<p>Album: 
			<select name="album">
				<option value="None">None</option>
				<option value="New">New</option>
				<?php foreach ($albums as $album): ?>
				<option value="<?php echo $album['id']; ?>"><?php echo $album['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>Album Title: <input name="new_album_title" type="text"/></p>
		<p>Album Description: <input name="new_album_description" type="text"/></p>
		<input name="albumavatar" title="Album Avatar" type="checkbox"/>

	   I'm skipping all of the above for now.
	
	</form>
	
	   The above is replaced by:  */
			
			$form->footer() ; // it's not just cosmetic, it does session cleanup.

            $page->footer(); ?></div>