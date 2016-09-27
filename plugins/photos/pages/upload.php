<?php
  /*
   * 
   *    Form should contain
   *    
   *        oe_api          photo
   *        oe_call         upload
   *        oe_module       $oepc[0]['type']
   *        oe_module_id    $oepc[0]['id']
   *        oe_parent       to which module or plugin is this attached?
   *                        if omitted, oe_module is assumed
   *        oe_parent_id    to which item in that module or plugin?
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

echo "This is upload.php";

include(oe_frontend . "page_minion.php");

$page = new page_minion("Upload Photo");
$page->header();

$oe_api = "photo";
$oe_call = "upload";

global $oepc;
global $privacyoptions;

$oe_module = $oepc[0]['type'];
$oe_module_id = $oepc[0]['id'];

$oe_parent = ""; // I believe this should be set by a parameter created from the 
$oe_parent_id = "";

print_r($privacyoptions);

?>

<div id="upload-photo-form">
	<form action="/">
		<input name="oe_api" hidden="true" value="<?php echo $oe_api; ?>"/>
		<input name="oe_call" hidden="true" value="<?php echo $oe_call; ?>"/>
		<input name="oe_module" hidden="true" value="<?php echo $oe_module; ?>"/>
		<input name="oe_module_id" hidden="true" value="<?php echo $oe_module_id; ?>"/>
		<input name="oe_parent" hidden="true" value="<?php echo $oe_parent; ?>"/>
		<input name="oe_parent_id" hidden="true" value="<?php echo $oe_parent_id; ?>"/>
		
		<p>Uploaded Image: <input name="photo" type="file" multiple="multiple" /></p>
		
		<p>Privacy: 
			<select name="privacy">
				<option value="">Value</option>
				<option value="">Value</option>
				<option value="">Value</option>
			</select>
		</p>
		
		<p>Title: <input name="title" type="text"/></p>
		<p>Description: <input name="description" type="text" multiple="multiple"/></p>
		
		<input name="parentavatar" title="Make Avatar" type="checkbox"/>
		
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
	</form>
</div>
<?php $page->footer(); ?>