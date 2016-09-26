<?php

/*
 *	Gravity HTTP Request Settings Screen
 */


?>


 <h3><?php echo self::name ?> Settings</h3>

 
<form method="post" id="gform_http_settings">
<?php 
//echo "<pre>";
//print_r($form); 
//echo "</pre>";
?>
 <table class="gforms_form_settings">

 <tbody>

 	<tr>
	 	<td colspan="2"><h4 class="gf_settings_subgroup_title"><?php echo $form['title'] ?> Settings</h4></td>
 	</tr>

 	<tr>
        <th>
			<label for="form_http_url" style="display:block;">HTTP URL<a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_http_url" title="Enter the URL you wish to post the form submission to"><i class="fa fa-question-circle"></i></a></label>
        </th>
        <td>
            <input type="text" id="form_http_url" name="form_http_url" class="fieldwidth-3" value="<?php echo $form['http_request_settings']['form_http_url'] ?>">
        </td>
    </tr>
 	
    <tr>
        <th>
            Request Type<a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_request_type" title="Select HTTP Request Type"><i class="fa fa-question-circle"></i></a>
        </th>
        <td>
            <select id="form_request_type" name="form_request_type" onchange="">
            	<option value="post_request" <?php 
            		if($form['http_request_settings']['http_request_type'] == 'post_request') {
                		echo 'selected="selected"';
            		};?> >POST Request</option>
            	<option value="get_request" <?php 
            		if($form['http_request_settings']['http_request_type'] == 'get_request') {
                		echo 'selected="selected"';
            		};?> >GET Request</option>
            </select>
        </td>
    </tr>
    
    <tr>
	    <th>
		    Active?
	    </th>
	    
	    <td>
		    <input type="checkbox" id="http_request_active" <?php if($form['http_request_settings']['http_request_active']) { 
		    					echo 'checked'; 
		    					
		    			} ?> name="http_request_active" value="1" >
	    </td>
	    
    </tr>

    <tr>
	    <td colspan="2"><h4 class="gf_settings_subgroup_title"><?php echo $form['title'] ?> Mapping</h4></td>
    </tr>

    <?php

	//$i=0;

	foreach ($form['fields'] as $i => $field):

	$field_id = $field['id']; ?>

    <tr>
	    	<th>
				<?php

				if ($field['adminLabel']) {
				
					echo $field['adminLabel'];
				
				} elseif ($field['label']) {
				
					echo $field['label'];
				
				} ?>

	    	</th><!-- end row header -->

	    	<td>

			<?php if ($field['inputs']) { // if field has multiple inputs defined ?>

	    		<label for="field_<?php echo $field_id; ?>_serialize">Serialize Multi-Field Input?</label>
			    <select class="serialize_select" id="field_<?php echo $field_id; ?>_serialize" name="field_<?php echo $field_id; ?>_serialize" onchange="updateSerializeOption('field_<?php echo $field_id; ?>_serialize');">
                	<option value="map" <?php if($field['http_output'] == 'map') echo 'selected="selected"' ?>>Map Inputs</option>
					<option value="serialize" <?php if($field['http_output'] == 'serialize') echo 'selected="selected"' ?>>Serialize</option>
				</select>
			
				<fieldset id="field_<?php echo $field_id; ?>_serialize_map" class="additional_options">
				
				<?php foreach ($field['inputs'] as $k => $input) : ?>

					<?php $input_id = str_replace('.', '_', $input['id']); ?>
											
					<label for="field_<?php echo $input_id; ?>_map" ><?php echo $input['label']; ?></label>
					<input type="text" class="field_map_name" id="field_<?php echo $input_id; ?>_map" name="field_<?php echo $input_id; ?>_map" class="fieldwidth-2" value="<?php echo $field['http_map']; ?>">

				<?php endforeach; ?>
				</fieldset>

				<fieldset id="field_<?php echo $field_id; ?>_serialize_input" class="additional_options">
		    		<label for="field_<?php echo $field_id; ?>_map">Serialized Output Map</label>
					<input type="text" class="field_map_name" id="field_<?php echo $field_id; ?>_map" name="field_<?php echo $field_id; ?>_map" class="fieldwidth-2" value="<?php echo $field['http_map']; ?>">
				</fieldset>

				<?php } else { // field a simple one-input field

					if ($field['choices']) { // if field is an options list that can send either value or label ?>

					<label for="field_<?php echo $field_id; ?>_value_output">Send label or value</label>
			    	<select id="field_<?php echo $field_id; ?>_value_output" name="field_<?php echo $field_id; ?>_value_output" onchange="">
                		<option value="label" <?php if($field['value_output'] == 'label') echo 'selected="selected"' ?>>Label</option>
						<option value="value"  <?php if($field['value_output'] == 'value') echo 'selected="selected"' ?>>Value</option>
					</select>
					
					<?php } ?>

				<label for="field_<?php echo $field_id ?>_map">Parameter</label>
		    	<input type="text" class="field_map_name" id="field_<?php echo $field_id ?>_map" name="field_<?php echo $field_id ?>_map" class="fieldwidth-3" value="<?php echo $field['http_map']; ?>">

		    	<?php  } ?>

		    	</td>
	    </td>
    </tr>

    <?php

$i++;
endforeach; ?>



 </tbody>

 </table>
 
 
<?php wp_nonce_field('gforms_save_http_settings', 'gforms_save_http_settings') ?>

<input type="hidden" id="date_updated" name="date_updated" value="<?php echo date('D, d M Y H:i:s'); ?>">

<input type="hidden" id="gform_http_settings_meta" name="gform_http_settings_meta" value="">
 
<input type="button" id="gform_save_settings" name="gform_save_settings" value="Update Form Settings" class="button-primary gfbutton" onclick="SaveFormSettings();">

</form>


 <script>

	    var form = <?php echo json_encode($form); ?>;

        jQuery(document).ready(function($){

            HandleUnsavedChanges('#gform_http_settings');

            ToggleConditionalLogic(true, 'form_button');

            jQuery('tr:hidden .gf_animate_sub_settings').hide();
            
            $('.serialize_select').each(function(){
	            
	            updateSerializeOption($(this).attr('name'));
	            
            })

         //   jQuery(document).trigger('gform_load_form_settings', [form]);

        });

        /**
        * New Form Settings Functions
        */

        function SaveFormSettings() {

            hasUnsavedChanges = false;

            // allow users to update form with custom function before save
            if(window["gform_before_update"]){
                form = window["gform_before_update"](form);
                if(window.console)
                    console.log('"gform_before_update" is deprecated since version 1.7! Use "gform_pre_form_settings_save" php hook instead.');
            }


            jQuery("#gform_http_settings_meta").val(jQuery.toJSON(form));
            jQuery("form#gform_http_settings").submit();

        }

        function updateSerializeOption(field){

	        console.log(field);

	        var option = jQuery('#'+field).val();

	        console.log(option);

	        if(option == 'map'){
		        jQuery('#'+field+'_input').hide('slow');
		        jQuery('#'+field+'_map').show('slow');
	        }

	        if(option == 'serialize'){
		        jQuery('#'+field+'_input').show('slow');
		        jQuery('#'+field+'_map').hide('slow');
	        }

        }

        function HandleUnsavedChanges(elemId) {

            hasUnsavedChanges = false;

            jQuery(elemId).find('input, select, textarea').change(function(){
                hasUnsavedChanges = true;
            });

            window.onbeforeunload = function(){
                if(hasUnsavedChanges)
                    return 'You have unsaved changes.';
            }

        }




 </script>

