<?php
/*
Plugin Name: Image Uploader Plugin
Plugin URI: http://www.studioyucca.com
Description: This plugin will allow users to upload images to the site, but they will be restricted to an exact size depending on the type of upload (graphics, websites, etc)
It can be easily added to a page by using the code [imageuploaderform].
Author: Chris Barber, Studioyucca.com
Version: 1.0.2
Author URI: http://www.studioyucca.com
*/

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

function wpiu_is_malicious($input) 
{
	$is_malicious = false;
	$bad_inputs = array("\r", "\n", "mime-version", "content-type", "bcc:", "cc:", "to:","*","document.cookie","onclick","onload");
	foreach($bad_inputs as $bad_input) 
	{
		if(strpos(strtolower($input), strtolower($bad_input)) !== false) 
		{
			$is_malicious = true; break;
		}
	}
	return $is_malicious;
}

function wpiu_is_challenge($input) 
{
	$is_challenge = false;
	$answer = get_option('wpiu_answer');
	$answer = htmlentities(stripslashes(attribute_escape($answer)));
	if($input == $answer) 
	{
		$is_challenge = true;
	}
	return $is_challenge;
}

/*Wrapper function which calls the form.*/
function wpiu_callback($content)
{
	$wpiu_error_array = array();
	$wpiu_max_file_size 		= stripslashes(get_option('wpiu_max_file_size'));
	$wpiu_accepted_extensions 	= explode("|",stripslashes(get_option('wpiu_accepted_extensions')));
	$wpiu_accepted_types 		= explode("|",stripslashes(get_option('wpiu_accepted_types')));
	$wpiu_accepted_dimensions 	= explode("|",stripslashes(get_option('wpiu_accepted_dimensions')));
	
	$wpiu_email_host			= stripslashes(get_option('wpiu_email_host'));
	$wpiu_email_auth			= stripslashes(get_option('wpiu_email_auth'));
	$wpiu_email_user			= stripslashes(get_option('wpiu_email_user'));
	$wpiu_email_pass			= stripslashes(get_option('wpiu_email_pass'));	
	/* Run the input check. */

    if(isset($_POST['wpiu_stage'])) // If the input check returns true (ie. there has been a submission & input is ok)
	{
		$_POST['wpiu_your_name'] 	= htmlspecialchars(stripslashes(attribute_escape($_POST['wpiu_your_name'])));
		$_POST['wpiu_url'] 			= htmlentities(stripslashes(attribute_escape($_POST['wpiu_url'])));
		$_POST['wpiu_email'] 		= htmlentities(stripslashes(attribute_escape($_POST['wpiu_email'])));
		$_POST['wpiu_design_type']	= htmlentities(stripslashes(attribute_escape($_POST['wpiu_design_type'])));
		$_POST['wpiu_design_title']	= htmlentities(stripslashes(attribute_escape($_POST['wpiu_design_title'])));
		$_POST['wpiu_design_url']	= htmlentities(stripslashes(attribute_escape($_POST['wpiu_design_url'])));
		$_POST['wpiu_response']	 	= htmlentities(stripslashes(attribute_escape($_POST['wpiu_response'])));

		if(empty($_POST['wpiu_your_name']))
		{	
			$wpiu_error_array[] = "<p><em>".__('Must specify a name','iu')."</em></p>";	
		}
		
		if(empty($_POST['wpiu_url']))
		{
			$wpiu_error_array[] = "<p><em>".__('Must specify your url','iu')."</em></p>";			
		}	
		
		if(empty($_POST['wpiu_email']))		
		{
			$wpiu_error_array[] = "<p><em>".__('Must specify an email address','iu')."</em></p>";	
		}
		else if(!is_email($_POST['wpiu_email']))
		{
			$wpiu_error_array[] = "<p><em>".__('Must specify a valid email','iu')."</em></p>";
		}

		if(empty($_POST['wpiu_design_type']))
		{			
			$wpiu_error_array[] = "<p><em>".__('Must specify a design type','iu')."</em></p>";
		}
	
		if(empty($_POST['wpiu_design_title']))
		{			
			$wpiu_error_array[] = "<p><em>".__('Must specify a design title','iu')."</em></p>";
		}
		
		if(empty($_POST['wpiu_design_url']))
		{			
			$wpiu_error_array[] = "<p><em>".__('Must specify a design url','iu')."</em></p>";
		}		
	
		if(empty($_POST['wpiu_response']))
		{
			$wpiu_error_array[] = "<p><em>".__('Must answer the anti-robot question','iu')."</em></p>";
		} 		 
	
		if (!wpiu_is_challenge($_POST['wpiu_response'])) 
		{
			$wpiu_error_array[] = "<p><em>".__('Your answer was incorrect','iu')."</em></p>";			
		}
		
		if(!is_uploaded_file($_FILES['wpiu_file']['tmp_name']))
		{
			$wpiu_error_array[] = "<p><em>".__('Must specify a file to upload','iu')."</em></p>";
		}
		else
		{
			// if the file has been uploaded, we want to check that the file has the EXACT dimensions required for that type and is of an allowed extension
			$wpiu_filename 	= strtolower($_FILES['wpiu_file']['name']);
			$wpiu_file_ext	= strrchr($wpiu_filename, ".");
			$wpiu_file_ext = str_replace(".", "", $wpiu_file_ext);
			$wpiu_boolean = false;
			
			for($wpiu_k = 0; $wpiu_k<count($wpiu_accepted_extensions); $wpiu_k++)
			{
				if($wpiu_accepted_extensions[$wpiu_k] == $wpiu_file_ext)
				{
					$wpiu_boolean = true;
				}
			}
			
			if(!$wpiu_boolean)
			{
				$wpiu_error_array[] = "<p><em>".__('Error, the file uploaded was of the type: '.$wpiu_file_ext.' which is not allowed.','iu')."</em></p>";
			}
						
			$wpiu_image_info = getimagesize($_FILES['wpiu_file']['tmp_name']);
			$wpiu_image_width = $wpiu_image_info[0];
			$wpiu_image_height = $wpiu_image_info[1];
			
			$wpiu_accepted_size_for_this_type = $wpiu_accepted_dimensions[($_POST['wpiu_design_type']-1)];
			
			$wpiu_accepted_size_for_this_type = str_replace("px", "", $wpiu_accepted_size_for_this_type);

			$wpiu_accepted_sizes = explode("*", $wpiu_accepted_size_for_this_type);
			
			if($wpiu_image_width != $wpiu_accepted_sizes[0])
			{
				// incorrect width
				$wpiu_error_array[] = "<p><em>".__('Error, the file uploaded has the wrong width of: '.$wpiu_image_width.', we expected a size of: '.$wpiu_accepted_sizes[0].'.','iu')."</em></p>";
			}
			
			if($wpiu_image_height != $wpiu_accepted_sizes[1])
			{
				// incorrect height
				$wpiu_error_array[] = "<p><em>".__('Error, the file uploaded has the wrong height of: '.$wpiu_image_height.', we expected a size of: '.$wpiu_accepted_sizes[1].'.','iu')."</em></p>";
			}
		} 	
		
		if(wpiu_is_malicious($_POST['wpiu_your_name']) || wpiu_is_malicious($_POST['wpiu_email']) ||  wpiu_is_malicious($_POST['wpiu_subject'])) 
		{
			$wpiu_error_array[] = "<p><em>".__('You can not use any of the following in the Name or Email fields: a linebreak, or the phrases \'mime-version\', \'content-type\', \'cc:\' \'bcc:\'or \'to:\'.','iu')."</em></p>";
		}
	
		if(!count($wpiu_error_array))
		{
			if(is_uploaded_file($_FILES['wpiu_file']['tmp_name']))
			{
				$wpiu_filename 	= date("h-i-s")."-".strtolower($_FILES['wpiu_file']['name']);
				$wpiu_file_ext	= strrchr($wpiu_filename, ".");
				
				$wpiu_date = date("d-m-Y");
				$wpiu_upload_path = stripslashes(get_option('upload_path'))."/".$wpiu_date."/";
				$wpiu_file_path = "/wp-content/uploads/".$wpiu_date."/";	
			
				mkdir_r($wpiu_upload_path);
				if(move_uploaded_file($_FILES['wpiu_file']['tmp_name'], $wpiu_upload_path.$wpiu_filename))
				{
			 		$_POST['wpiu_file'] = $wpiu_file_path.$wpiu_filename;
					
					$wpiu_recipient 	= get_option('wpiu_email');
					$wpiu_subject   	= get_option('wpiu_prefix')." Submission";
					$wpiu_success_msg 	= get_option('wpiu_success_msg');					
					$wpiu_success_msg 	= stripslashes($success_msg);
					
					$wpiu_name = attribute_escape($_POST['wpiu_your_name']);
					$wpiu_url = attribute_escape($_POST['wpiu_url']);
					$wpiu_email = attribute_escape($_POST['wpiu_email']);
					$wpiu_design_type = attribute_escape($_POST['wpiu_design_type']);
					$wpiu_design_title = attribute_escape($_POST['wpiu_design_title']);
					$wpiu_design_url = attribute_escape($_POST['wpiu_design_url']);					
					$wpiu_submission_link = stripslashes(get_option('siteurl')).$_POST['wpiu_file'];
					 
					$wpiu_fullmsg  = "Name: ".$wpiu_name." ".__('wrote:','iu')."\n";
					$wpiu_fullmsg .= "Website: <a href=\"".$wpiu_url."\">".$wpiu_url."</a>\n";
					$wpiu_fullmsg .= "Email: ".$wpiu_email."\n";
					$wpiu_fullmsg .= "Design Type: ".$wpiu_accepted_types[($wpiu_design_type-1)]."\n";
					$wpiu_fullmsg .= "Design Title: ".$wpiu_design_title."\n";
					$wpiu_fullmsg .= "Design Url: <a href=\"".$wpiu_design_url."\">".$wpiu_design_url."</a>\n\n";
					$wpiu_fullmsg .= "Submission Url: <a href=\"".$wpiu_submission_link."\">".$wpiu_submission_link."</a>\n\n";
					
					$wpiu_fullmsg .= "".__('IP:','iu')."" . get_ip();
					
					$wpiu_body = nl2br($wpiu_fullmsg);
					$wpiu_altbody = strip_tags($wpiu_body);					
					
					include "phpmailer/class.phpmailer.php";
					
					$wpiu_mail = new PhpMailer();
				
					$wpiu_mail->IsSMTP();
					$wpiu_mail->Host	= $wpiu_email_host;
					$wpiu_mail->SMTPAuth = $wpiu_email_auth;
					$wpiu_mail->Username = $wpiu_email_user;
					$wpiu_mail->Password = $wpiu_email_pass;
										
					$wpiu_mail->From = $wpiu_email;
					$wpiu_mail->FromName = $wpiu_name;
					$wpiu_mail->AddReplyTo($wpiu_email);
					$wpiu_mail->IsHTML(true);
					$wpiu_mail->Subject = $wpiu_subject;
					$wpiu_mail->Body = $wpiu_body;
					$wpiu_mail->AltBody = $wpiu_altbody;
					$wpiu_mail->AddAddress($wpiu_recipient);
					$wpiu_mail->Send();
					
					
					$results = '<p class="successmsg">' . $success_msg . '</p>';
					$homelink = '<p class="successmsg"><a href="'. get_bloginfo('url') .'/">'. __('Return home','iu') .'</a></p>';
					print $results . $homelink;					
				}
			}
		}
    }
	// if the form has been submitted, but there is errors, show the form. OR if the form hasn't been submitted, show the form
	if ((isset($_POST['wpiu_stage']) && count($wpiu_error_array)) || !isset($_POST['wpiu_stage']))
	{
		$question = htmlentities(stripslashes(get_option('wpiu_question')));
		?>
		<form id="imageuploaderform" action="<?php print get_permalink(); ?>" enctype="multipart/form-data" method="post"> 
			<?php 
			if(count($wpiu_error_array))
			{
				for($wpiu_p = 0; $wpiu_p < count($wpiu_error_array); $wpiu_p++)
				{
					print $wpiu_error_array[$wpiu_p]; 
				}
			}
			?>
			<p>* Required fields</p> 
			<fieldset> 
			<legend>Submission Details</legend> 
				<label for="wpiu_your_name">Your Name / Your Agency Name: *</label> 
				<input type="text" class="text" name="wpiu_your_name" id="wpiu_your_name" size="30" maxlength="50" value="<?php print $_POST['wpiu_your_name']; ?>" tabindex="1" /> 
			 
				<label for="wpiu_url">Your URL:*</label> 
				<input type="text" name="wpiu_url" id="wpiu_url" size="30" maxlength="50" value="<?php print $_POST['wpiu_url']; ?>" tabindex="3" /> 
			 
				<label for="wpiu_email">Email:*</label> 
				<input class="text" type="text" name="wpiu_email" id="wpiu_email" size="30" maxlength="50" value="<?php print $_POST['wpiu_email']; ?>" tabindex="5"/> 
			 
				<label for="wpiu_design_type">What Kind of Design is it?:*</label> 
				<select id="wpiu_design_type" name="wpiu_design_type" tabindex="6"> 
					<option value="">Please select</option>
					<?php	
					for($wpiu_i = 0; $wpiu_i<count($wpiu_accepted_types); $wpiu_i++)
					{
						?>
						<option value="<?php print ($wpiu_i + 1); ?>"
						<?php
						if(isset($_POST['wpiu_design_type']) && !empty($_POST['wpiu_design_type']))
						{
							print ($_POST['wpiu_design_type'] == ($wpiu_i + 1))?("selected=\"selected\""):("");
						}
						?>>
						<?php print $wpiu_accepted_types[$wpiu_i]; ?></option>
						<?php
					}                
					?>                
				</select> 
						
				<label for="wpiu_design_title">The Title of the Design:*</label> 
				<input class="text" type="text" name="wpiu_design_title" id="wpiu_design_title" size="30" maxlength="50" value="<?php print $_POST['wpiu_design_title']; ?>" tabindex="7"/> 
			 
				<label for="wpiu_design_url">Design URL: *</label> 
				<input class="text" type="text" name="wpiu_design_url" id="wpiu_design_url" size="30" maxlength="50" value="<?php print $_POST['wpiu_design_url']; ?>" tabindex="9"/> 
			 
				<label for="wpiu_response">3 + 2 = *</label> 
				<input class="text" type="text" name="wpiu_response" id="wpiu_response" size="30" maxlength="50" value="<?php print $_POST['wpiu_response']; ?>" tabindex="11"/> 
			 
				 
				<input type="hidden" class="hiddenfield" name="wpiu_stage" value="process" /> 
			</fieldset> 
			<br/>
			<fieldset> 
			<legend>Upload Submission Image</legend> 
				<p>Ensure your image is:
				<ul>
				<?php	
					for($wpiu_k = 0; $wpiu_k<count($wpiu_accepted_types); $wpiu_k++)
					{
						?>
							<li><strong><?php print $wpiu_accepted_dimensions[$wpiu_k]; ?></strong> - for <?php print $wpiu_accepted_types[$wpiu_k]; ?></li>
						<?php
					}
				?>
				</ul> 
				<input class="text" type="file" name="wpiu_file" id="wpiu_file" value="<?php print $_POST['wpiu_file']; ?>"/>
			</fieldset>
			<br/>
			<input type="submit" name="Submit" value="Send" id="iusubmit" tabindex="7" />
		</form>         	
		<?php
    }
}


/* Can't use WP's function here, so lets borrow the superb one supplied with the WP Contact Form III
Author: Kristin K. Wangen
Author URI: http://wangenweb.com/
*/
function get_ip()
{
	if (isset($_SERVER))
	{
 		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
 		{
  			$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
 		}
 		else if (isset($_SERVER["HTTP_CLIENT_IP"]))
 		{
  			$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
 		}
 		else
 		{
 			$ip_addr = $_SERVER["REMOTE_ADDR"];
 		}
	}
	else
	{
 		if (getenv('HTTP_X_FORWARDED_FOR'))
 		{
  			$ip_addr = getenv('HTTP_X_FORWARDED_FOR');
 		}
 		else if (getenv('HTTP_CLIENT_IP'))
 		{
  			$ip_addr = getenv('HTTP_CLIENT_IP');
 		}
 		else
 		{
  			$ip_addr = getenv('REMOTE_ADDR');
 		}
	}
	return $ip_addr;
}

/*CSS Styling*/
function wpiu_css() 
{ 
	?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php print WP_PLUGIN_URL; ?>/image-uploader/image-uploader.css" />
	<?php 
} 

function wpiu_add_options_page()
{
	add_submenu_page('options-general.php', 'Image Uploader Settings', 'Image Uploader', 10, 'image-uploader/image-uploader-options.php'); 
}

function mkdir_r($dir_name, $rights=0777)
{
    $dirs = explode('/', $dir_name);
    $dir='';
    foreach ($dirs as $part)
	{
        $dir.=$part.'/';
        if(!is_dir($dir) && strlen($dir)>0)
            @mkdir($dir, $rights);
    }
}

/* Action calls for all functions */
add_action('wp_head', 'wpiu_css');
add_action('admin_menu', 'wpiu_add_options_page');
add_shortcode('imageuploaderform', 'wpiu_callback');
?>