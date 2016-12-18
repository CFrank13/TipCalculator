<html>
	<head>
		<title>Tip Calculator</title>
		<style type="text/css">
			.outputFieldStyle{
				width: 250;
				border-color: #00FF00;
				text-align: left;
				margin: auto;
			}
			fieldset{
				background-color: white;
				margin: auto;
				width: 300px;
			}
			body{
				background-color: #006600; 
			}
			input[type="text"]{
				width: 100px;
				height: 21px;
			}
			input[type="radio"]{
				margin: 0 10px 0 10px;
			}
		</style>

		<?php 					/* Function Definitions */
			// Instance Variables 
			$subtotal = "";
			$custom_tip = "";
			$tip_val = "";
			$percentages = array(".10", ".15", ".20");
			$split = "";
			
			// Initializes form defaults/values on page load
			function init(){
				// Initialize text boxes to defaults if not set already
				isset($_POST['subtotal']) 
					? set_subtotal($_POST['subtotal']) : set_subtotal(0);
				isset($_POST['custom_tip'])
					? set_custom_tip($_POST['custom_tip']) : set_custom_tip(0);
				isset($_POST['split'])
					? set_split($_POST['split']) : set_split(1);
					
				// Initialize tip percentage to use
				if(empty($_POST["tip_percentages"])){
					set_tip_val(".15");
				}
				else if(is_custom()){
					set_tip_val($_POST["custom_tip"]/100);
				}else{
					set_tip_val($_POST["tip_percentages"]);
				}
			}
			
			// Creates list of radio button options 
			// to select a tip % from
			function output_tip_options(){
				$output = "";
				
				// Create a button for each percentage
				foreach($GLOBALS['percentages'] as $p){
					$output.= '<input type="radio" name="tip_percentages" value="' .$p .'"';
					if(get_tip_val() != null && get_tip_val() == $p){
						$output.= "checked";
					}
					$output.= '/>' .(int)(floatval($p)*100) .'%';
				}
				echo $output;
			}
			
			//Calculates tip and total bill
			function get_result(){
				$output = '';
				$tip = get_subtotal() * get_tip_val();
				$total = get_subtotal() + $tip;
				if(get_split() > 1){
					$split_tip = floatval($tip) / get_split();
					$split_total = floatval($total) / get_split();
					$split_tip = number_format(floatval($split_tip), 2, '.', ',');
					$split_total = number_format(floatval($split_total), 2, '.', ',');
				}	
				//format values
				$tip = number_format(floatval($tip), 2, '.', ',');
				$total = number_format(floatval($total), 2, '.', ',');
				
				$output.= 'Tip: $' .$tip;
				$output.= '<br/>';
				$output.= 'Total: $' .$total;
				if(get_split() > 1){
					$output.= '<br/>';
					$output.= 'Tip Each: $' .$split_tip;
					$output.= '<br/>';
					$output.= 'Total Each: $' .$split_total;
				}
				echo $output;
			}
			
			// Formats and outputs result box 
			function output_result(){
				echo '<br/><fieldset class="outputFieldStyle">'; 
				get_result();
				echo '</fieldset>';
			}
		
			//Checks for form submission
			function is_post_request(){
				return $_SERVER['REQUEST_METHOD'] == 'POST';
			}
			
			//Checks for custom tip
			function is_custom(){
				return $_POST["tip_percentages"] == "custom";
			}
			
			//Checks for blank field entry in subtotal text box
			function is_blank($val){
				return $val == "" || trim($val) == ""; 
			}
			
			//Checks if a number is an integer
			function is_whole_number($input){
				return(ctype_digit(strval($input)));
			}
			
			//Checks validity of subtotal field entry
			function is_valid_entry($val){
				return !is_blank($val) && is_numeric(trim($val)) && $val > 0;
			}
			
			// If the checked value is not the default valid entry,
			// or does not satisfy additional boolean check (default true for no check), 
			// modify html code
			function error_condition($val, $b_check, $err_message, $no_err_default){
				if(is_post_request() && !is_valid_entry($val)){
					echo $err_message;
				}
				else if(!$b_check){
					echo $err_message;
				}
				else{
					echo $no_err_default;
				}
			}
			
			/* get / set */		
			function get_subtotal(){
				return $GLOBALS['subtotal'];
			}		
			function set_subtotal($sub){
				$GLOBALS['subtotal'] = $sub;
			}		
			function get_custom_tip(){
				return $GLOBALS['custom_tip'];
			}		
			function set_custom_tip($tip){
				$GLOBALS['custom_tip'] = $tip;
			}		
			function get_tip_val(){
				return $GLOBALS['tip_val'];
			}			
			function set_tip_val($tip){
				$GLOBALS['tip_val'] = $tip;	
			}
			function get_split(){
				return $GLOBALS['split'];
			}			
			function set_split($split){
				$GLOBALS['split'] = $split;	
			}
			
		?>
		<?php 
			init();
		?>
	</head>
	<body>	
		<!--Calculator Form-->
		<form action="TipCalculator.php" method="post">
			<fieldset>
				<!--Title-->
				<center><h3>Tip Calculator</h3></center>
				<br/>		
				
				<!--Subtotal text box-->
				<?php error_condition(get_subtotal(), true, '<b>', ''); ?>
				<label style="color: <?php error_condition(get_subtotal(), true, 'red', 'black'); ?>">
					Bill Subtotal:
				</label>
				<?php error_condition(get_subtotal(), true, '</b>', ''); ?>
				$<input type="text" name="subtotal" value="<?php echo $subtotal; ?>" />
				<br/><br/>		
				
				<!--Tip percentage radio buttons-->
				<?php error_condition(get_tip_val(), true, '<b>', ''); ?>
				<label style="color: <?php error_condition(get_tip_val(), true, 'red', 'black'); ?>">
					Tip Percentage:
				</label>
				<br/><br/>
				<?php error_condition(get_tip_val(), true, '</b>', ''); ?>
				<?php output_tip_options(); ?>
				<br/>
				
				<!--Custom Tip radio button / text input-->
				<input type="radio" name="tip_percentages" value="custom" 
					<?php 
						// Check this radio button if it was the last checked button
						if(is_post_request() && is_custom()){
							echo 'checked';
						}
					?>
				/> Custom:
				<input type="text" name="custom_tip" value="<?php echo $custom_tip; ?>"/> %
				<br/><br/>
				
				<!--Bill Split text box-->
				<?php error_condition(get_split(), is_whole_number(get_split()), '<b>', ''); ?>
				<label style="color: <?php error_condition(get_split(), is_whole_number(get_split()), 'red', 'black'); ?>">
					Split:
				</label>
				<?php error_condition(get_split(), is_whole_number(get_split()), '</b>', ''); ?>
				<input type="text" name="split" value="<?php echo $split; ?>"/> person(s)
				<br/><br/>
				
				<!--Submit button-->
				<center><input type="submit" name"submit" value="Submit" /></center>
				
				<!--Form Processing-->
				<?php 
					if(is_post_request() && is_valid_entry(get_subtotal()) 
							&& (!is_custom() || is_valid_entry(get_custom_tip()))
							&& (is_valid_entry(get_split()) && is_whole_number(get_split()))){
						output_result();
					}
				?>
			</fieldset>
		</form>
	</body>
</html>