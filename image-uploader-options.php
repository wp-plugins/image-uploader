<?php
/* Image Uploader Options */

/*  Copyright 2008 Chris Barber  (web: www.studioyucca.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


$location = 'options-general.php?page=image-uploader/image-uploader-options.php'; 

/*Lets add some default options if they don't exist*/

add_option('wpiu_email', get_settings('admin_email'));
add_option('wpiu_prefix', '['.get_bloginfo('name').'] ');
add_option('wpiu_success_msg', __('Thanks for your submission!', 'iu'));
add_option('wpiu_error_msg', __('Please fill in the required fields.', 'iu'));
add_option('wpiu_question', __('3 + 2 =', 'iu'));
add_option('wpiu_answer', __('5', 'iu'));

add_option('wpiu_email_host', __('localhost', 'iu'));
add_option('wpiu_email_auth', __('true', 'iu'));
add_option('wpiu_email_user', __('postmanpat@example.com', 'iu'));
add_option('wpiu_email_pass', __('j3ss', 'iu'));

add_option('wpiu_max_file_size', __('250','iu'));
add_option('wpiu_accepted_extensions', __('gif|png|jpg|jpeg','iu'));
add_option('wpiu_accepted_dimensions', __('320px*260px|280px*520px|280px*520px','iu'));
add_option('wpiu_accepted_types', __('Logos|Websites|All Print Design (Poster/Flyers/Stationery)','iu'));

/*Get options for form fields*/
$wpiu_email 				= stripslashes(get_option('wpiu_email'));
$wpiu_prefix 				= stripslashes(get_option('wpiu_prefix'));
$wpiu_success_msg 			= stripslashes(get_option('wpiu_success_msg'));
$wpiu_error_msg 			= stripslashes(get_option('wpiu_error_msg'));
$wpiu_question				= stripslashes(get_option('wpiu_question'));
$wpiu_answer 				= stripslashes(get_option('wpiu_answer'));

$wpiu_email_host			= stripslashes(get_option('wpiu_email_host'));
$wpiu_email_auth			= stripslashes(get_option('wpiu_email_auth'));
$wpiu_email_user			= stripslashes(get_option('wpiu_email_user'));
$wpiu_email_pass			= stripslashes(get_option('wpiu_email_pass'));

$wpiu_max_file_size 		= stripslashes(get_option('wpiu_max_file_size'));
$wpiu_accepted_extensions 	= stripslashes(get_option('wpiu_accepted_extensions'));
$wpiu_accepted_types 		= stripslashes(get_option('wpiu_accepted_types'));
$wpiu_accepted_dimensions 	= stripslashes(get_option('wpiu_accepted_dimensions'));

?>

<div class="wrap">
  <h2><?php _e('Image Uploader Options', 'iu') ?></h2>
	<form method="post" action="options.php">


	<?php wp_nonce_field('update-options'); ?>

		<h3><?php _e('General', 'iu') ?></h3>

	    <table width="100%" cellspacing="2" cellpadding="5" class="form-table">
	      <tr valign="top">
		<th scope="row"><label for="wpiu_email"><?php _e('E-mail Address:','iu') ?></label></th>
		<td><input name="wpiu_email" type="text" id="wpiu_email" value="<?php print $wpiu_email; ?>" size="40" tabindex="1" />
		<br />
		<?php _e('This address is where the email will be sent to.', 'iu') ?></td>
	      </tr>

	      <tr valign="top">
		<th scope="row"><label for="wpiu_prefix"><?php _e('Subject prefix:','iu') ?></label></th>
		<td><input name="wpiu_prefix" type="text" id="wpiu_prefix" value="<?php print $wpiu_prefix; ?>" size="40" tabindex="2" />
		<br />
		<?php _e('This the prefix for the subject of your email. Leave empty for no prefix', 'iu') ?></td>
	      </tr>

	     </table>
         
		<h3><?php _e('Accepted Images - Details', 'iu') ?></h3>

	    <table width="100%" cellspacing="2" cellpadding="5" class="form-table">
	      <tr valign="top">
		<th scope="row"><label for="wpiu_max_file_size"><?php _e('Max file size:','iu') ?></label></th>
		<td><input name="wpiu_max_file_size" type="text" id="wpiu_max_file_size" value="<?php print $wpiu_max_file_size; ?>" size="40" tabindex="1" />
		<br />
		<?php _e('The maxium file size allowed in kb.', 'iu') ?></td>
	      </tr>

	      <tr valign="top">
		<th scope="row"><label for="wpiu_accepted_extensions"><?php _e('Accepted Extensions:','iu') ?></label></th>
		<td><input name="wpiu_accepted_extensions" type="text" id="wpiu_accepted_extensions" value="<?php print $wpiu_accepted_extensions; ?>" size="40" tabindex="2" />
		<br />
		<?php _e('The image extensions that will be accepted, e.g. .jpg or .jpeg.', 'iu') ?><br />
        <?php _e('These must be added in the form of jpg|gif|png', 'iu') ?></td>
	      </tr>
          
	      <tr valign="top">
		<th scope="row"><label for="wpiu_accepted_types"><?php _e('Accepted types of uploads:','iu') ?></label></th>
		<td><input name="wpiu_accepted_types" type="text" id="wpiu_accepted_types" value="<?php print $wpiu_accepted_types; ?>" size="40" tabindex="2" />
		<br />
		<?php _e('The types of uploads accepted, e.g. Logos, Buisness Cards, Print Design, Websites', 'iu') ?><br />
        <?php _e('These must be added in the form of Logos|Websites|Print Design', 'iu') ?></td>
	      </tr>  
          
	      <tr valign="top">
		<th scope="row"><label for="wpiu_accepted_dimensions"><?php _e('Accepted dimensions of uploads:','iu') ?></label></th>
		<td><input name="wpiu_accepted_dimensions" type="text" id="wpiu_accepted_dimensions" value="<?php print $wpiu_accepted_dimensions; ?>" size="40" tabindex="2" />
		<br />
		<?php _e('The exact dimensions of the images', 'iu') ?><br />
        <?php _e('These must be added in the form of 320*260|520*280|520*280 (width x height)', 'iu') ?><br />
        <?php _e('These must also correspond with the types listed above. eg: Logos|Websites corresponds with 320*260|520*280', 'iu') ?></td>
	      </tr>                  

	     </table>         


		<h3><?php _e('Challenge Question', 'iu') ?></h3>
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		  <tr valign="top">
			<th scope="row"><label for="wpiu_question"><?php _e('What is your challenge question?', 'iu') ?></label></th>
			<td><input name="wpiu_question" id="wpiu_question" type="text" value="<?php print $wpiu_question; ?>" size="40" tabindex="3" />
			<br />
	<?php _e('This is a question asked to the contact form user to see if they are human.', 'iu') ?></td>
		  </tr>
		  <tr valign="top">
			<th scope="row"><label for="wpiu_answer"><?php _e('Correct response:', 'iu') ?></label></th>
			<td><input name="wpiu_answer" id="wpiu_answer" type="text" value="<?php print $wpiu_answer; ?>" size="40" tabindex="4" />
			<br />
	<?php _e('This is the exact response to the challenge question.', 'iu') ?> <br />
		  </tr>
		</table>



		<h3><?php _e('Messages', 'iu') ?></h3>
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		  <tr valign="top">
			<th scope="row"><label for="wpiu_success_msg"><?php _e('Success Message:', 'iu') ?></label></th>
			<td><textarea name="wpiu_success_msg" id="wpiu_success_msg" style="width: 80%;" rows="4" cols="50" tabindex="5"><?php print $wpiu_success_msg; ?></textarea>
			<br />
	<?php _e('When the form is sucessfully submitted, this is the message the user will see.', 'iu') ?></td>
		  </tr>
		  <tr valign="top">
			<th scope="row"><label for="wpiu_error_msg"><?php _e('Error Message:', 'iu') ?></label></th>
			<td><textarea name="wpiu_error_msg" id="wpiu_error_msg" style="width: 80%;" rows="4" cols="50" tabindex="6"><?php print $wpiu_error_msg; ?></textarea>
			<br />
	<?php _e('If the user skips a required field, this is the message he will see.', 'iu') ?> <br />
	<?php _e('You can apply CSS to this text by wrapping it in <code>&lt;p style="[your CSS here]"&gt; &lt;/p&gt;</code>.', 'iu') ?><br />
	<?php _e('ie. <code>&lt;p style="color:red;"&gt;Please fill in the required fields.&lt;/p&gt;</code>.', 'iu') ?></td>
		  </tr>
		</table>

		<h3><?php _e('Email Details (PHP Mailer)', 'iu') ?></h3>

	    <table width="100%" cellspacing="2" cellpadding="5" class="form-table">
	      <tr valign="top">
		<th scope="row"><label for="wpiu_email_host"><?php _e('E-mail Host:','iu') ?></label></th>
		<td><input name="wpiu_email_host" type="text" id="wpiu_email_host" value="<?php print $wpiu_email_host; ?>" size="40" tabindex="1" />
		<br />
		<?php _e('This is the email host', 'iu') ?></td>
	      </tr>

	      <tr valign="top">
		<th scope="row"><label for="wpiu_email_auth"><?php _e('E-mail Auth:','iu') ?></label></th>
		<td><input name="wpiu_email_auth" type="text" id="wpiu_email_auth" value="<?php print $wpiu_email_auth; ?>" size="40" tabindex="1" />
		<br />
		<?php _e('This is the email auth', 'iu') ?></td>
	      </tr>
          
	      <tr valign="top">
		<th scope="row"><label for="wpiu_email_user"><?php _e('E-mail User:','iu') ?></label></th>
		<td><input name="wpiu_email_user" type="text" id="wpiu_email_user" value="<?php print $wpiu_email_user; ?>" size="40" tabindex="1" />
		<br />
		<?php _e('This is the email user', 'iu') ?></td>
	      </tr>
          
	      <tr valign="top">
		<th scope="row"><label for="wpiu_email_pass"><?php _e('E-mail Pass: ','iu') ?></label></th>
		<td><input name="wpiu_email_pass" type="text" id="wpiu_email_pass" value="<?php print $wpiu_email_pass; ?>" size="40" tabindex="1" />
		<br />
		<?php _e('This is the email pass', 'iu') ?></td>
	      </tr>                    

	     </table>

		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="wpiu_email,wpiu_prefix,wpiu_success_msg,wpiu_error_msg,wpiu_question,wpiu_answer" />


		<p class="submit">
		<input type="submit" tabindex="8" name="Submit" value="<?php _e('Update Options','iu') ?>" />
		</p>


 </form>




</div>
