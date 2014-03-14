<?php
/*
Plugin Name: Events Manager Pro State Sales Tax
Author: 
*/
add_filter('option_dbem_bookings_tax', 'custom_event_tax_filter');

function  custom_event_tax_filter( $value ) {
		
	//	if ( is_admin() ) {
	//		return $value;
	//	}

		$tax_rate = 0;
		
		if( is_user_logged_in() ) {
			
			$user = wp_get_current_user();
			
			$country_id = get_user_meta( $user->ID , 'dbem_state', true);
					
		} else {
			if( isset( $_REQUEST['dbem_state'] ) ) {
				$country_id = $_REQUEST['dbem_state'];
			}

		}
		
		if( isset( $country_id ) ) {
			$tax_rate = get_option( 'dbem_state_'.$country_id.'_tax_rate');
		}
		
		return $tax_rate;
}
add_action('admin_menu', 'event_state_tax_option_page');


function event_state_tax_option_page() {
	add_menu_page('EMP Tax Settings', 'EMP Tax Settings', 'administrator', 'tax_settings', 'tax_settings_page');
}

function tax_settings_page() {
		
			if( isset( $_POST['statelist'] ) ) {
				update_option( ( 'dbem_state_tax_list') , $_POST[ 'statelist' ] );
			}
	
			if( $country_array = get_option( ( 'dbem_state_tax_list') ) ) {
					$country_array = explode( ',', $country_array );
			}
			
			
			if( isset( $_POST['formsubmit'] ) ) {
					foreach( $country_array as $value ) {
						
						$value = str_replace(' ', '', $value);
						if( isset( $_POST[ $value ] ) ) update_option( ( 'dbem_state_'.$value.'_tax_rate') , $_POST[ $value ] );
					}			
			}
						
			echo "<h2>Add States  </h2>";
			
			echo "<form method='post'>";
			echo "<input type='hidden' value='process' name='liststate' />";
			echo "<input style='width:400px' type='text' value='". get_option( 'dbem_state_tax_list') ."' name='statelist' />";
			
			echo "<input type='submit' value='Update state list' name='submit' />";
			echo "<br><label> (use commas to separate multiple values) </label>";
			echo "</form>";
			
			echo "<br><h2>Tax Rate Percent  </h2>";
			echo "<form method='post'>";
			echo "<input type='hidden' value='process' name='formsubmit' />";
			
			
			if( ! is_array( $country_array ) ){
				
				echo "<em>Please add state list above</em>";
				
			} else {
			
				foreach( $country_array as $value ){
					$value1 = str_replace(' ', '', $value);
				?>
						<div style="float:left; width:300px">
							<label style="width:150px; display:block; float:left"><?php echo $value; ?></label>
							<input style="float:left" type="text" value="<?php echo get_option( 'dbem_state_'.$value1.'_tax_rate'); ?>" name="<?php echo $value1 ?>" />
						</div>	
	
				<?php }
				
				echo "<input type='submit' value='submit' name='submit' />";
				echo "</form>";
				
			}
}




add_filter('emp_forms_output_field','custom_field__fun',2,3);

function custom_field__fun($output,$this,$field) {

	if( $field[type] == 'dbem_state' ){

		$output = '';
		$output .= '<p class="input-dbem_state input-user-field">';
 		$output .= '<label for="dbem_state">';
 		$output .= 'State <span class="em-form-required">*</span></label>';
 		$output .= '<select name="dbem_state" id="dbem_state" class="input" value="">';
 		
 		$state_list = array('AL'=>"Alabama",
 				'AK'=>"Alaska",
 				'AZ'=>"Arizona",
 				'AR'=>"Arkansas",
 				'CA'=>"California",
 				'CO'=>"Colorado",
 				'CT'=>"Connecticut",
 				'DE'=>"Delaware",
 				'DC'=>"District Of Columbia",
 				'FL'=>"Florida",
 				'GA'=>"Georgia",
 				'HI'=>"Hawaii",
 				'ID'=>"Idaho",
 				'IL'=>"Illinois",
 				'IN'=>"Indiana",
 				'IA'=>"Iowa",
 				'KS'=>"Kansas",
 				'KY'=>"Kentucky",
 				'LA'=>"Louisiana",
 				'ME'=>"Maine",
 				'MD'=>"Maryland",
 				'MA'=>"Massachusetts",
 				'MI'=>"Michigan",
 				'MN'=>"Minnesota",
 				'MS'=>"Mississippi",
 				'MO'=>"Missouri",
 				'MT'=>"Montana",
 				'NE'=>"Nebraska",
 				'NV'=>"Nevada",
 				'NH'=>"New Hampshire",
 				'NJ'=>"New Jersey",
 				'NM'=>"New Mexico",
 				'NY'=>"New York",
 				'NC'=>"North Carolina",
 				'ND'=>"North Dakota",
 				'OH'=>"Ohio",
 				'OK'=>"Oklahoma",
 				'OR'=>"Oregon",
 				'PA'=>"Pennsylvania",
 				'RI'=>"Rhode Island",
 				'SC'=>"South Carolina",
 				'SD'=>"South Dakota",
 				'TN'=>"Tennessee",
 				'TX'=>"Texas",
 				'UT'=>"Utah",
 				'VT'=>"Vermont",
 				'VA'=>"Virginia",
 				'WA'=>"Washington",
 				'WV'=>"West Virginia",
 				'WI'=>"Wisconsin",
 				'WY'=>"Wyoming");

 		foreach ($state_list as $state) {
			
 				$output .= '<option value="'.$state.'">'.$state.'</option>';
 			
 		} 	
 			
		$output .= '</select>';
		$output .= '</p>';
	}
	
	return $output;
	
}



function my_custom_js() {

?>
<script>
	jQuery(document).ready(function(){
		jQuery('.dbem_country').val('US');
		$val =   parseFloat( jQuery('.ticket-price strong').text().replace('$', ""), 10) ;

	 	$val += $val * 0.0825;
		$new_val = $val.toFixed(2);
		jQuery('.ticket-price').append(' <br ><span>8.25% sales tax will apply to TX customers for a total price of $'+$new_val+'. </span>');
	});
</script>
<?php }
add_action('wp_head', 'my_custom_js');