<?php
/*
Plugin Name: Traffic Source Capture
Plugin URI: https://devinlabs.com/
Description: This plugin is is used for Capture Traffic Source.
Version: 4.1.2
Author: Devinlabs
*/

/* Describe what the code snippet does so you can remember later on */
/* Currently we are posting data here: http://devinlabs.com/testpost/index.php */
add_action('wp_head', 'trafficsourcecapture');

function trafficsourcecapture(){


	/* PUT YOUR POSTING URL HERE */
		$posturl = 'https://michaelcreationn.com/test.php';
		
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$ref_url = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:'Not Available';
       $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
       $main_url = $_SERVER['SCRIPT_URI'];
       $url_id = $_SERVER['QUERY_STRING'];
	  
		
	  $_SESSION['traffic_current_ip'] = $_SERVER['REMOTE_ADDR'];

	
	$allips = array();
	
	$urlfildata = file(plugin_dir_path(__FILE__).'capturedurl.txt', FILE_IGNORE_NEW_LINES);
	
	foreach($urlfildata as $url){
		$ipwurl = explode(',',$url);			
		$allips[] = trim($ipwurl[0]);			
	}
				  	
			if (!isset($_SESSION['trafficapture'])) {
				
					$ipadr = $ipaddress;		
						$ref_url = $ref_url;
						$main_url = $main_url;
						$url_id = $url_id;
						
						$params = array(
						  'ip'=>$ipadr,
						  'ref_url'=>$ref_url,
						  'main_url'=>$main_url,
						  'url_id'=>$url_id,
						);
						
						
						$ch = curl_init();

						curl_setopt($ch, CURLOPT_URL,$posturl);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$server_output = curl_exec($ch);

						curl_close ($ch);		
						
						
				$fp = fopen(plugin_dir_path(__FILE__).'capturedurl.txt', 'a');
				//opens file in append mode  
				fwrite($fp, $ipaddress.' , '.$actual_link.PHP_EOL); 
				fclose($fp); 
				
				$_SESSION['trafficapture'] = time();
				
			} else {
				if ((time() - $_SESSION['trafficapture'] > 1 * 60))  {
										
					$ipadr = $ipaddress;		
					$ref_url = $ref_url;
					$main_url = $main_url;
					$url_id = $url_id;

					$params = array(
					  'ip'=>$ipadr,
					  'ref_url'=>$ref_url,
					  'main_url'=>$main_url,
					  'url_id'=>$url_id,
					);


					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL,$posturl);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$server_output = curl_exec($ch);

					curl_close ($ch);		


					$fp = fopen(plugin_dir_path(__FILE__).'capturedurl.txt', 'a');
					//opens file in append mode  
					fwrite($fp, $ipaddress.' , '.$actual_link.PHP_EOL); 
					fclose($fp); 

					//unset($_SESSION['trafficapture']);
					$_SESSION['trafficapture'] = time();

					
				}
				
			}
			
?>

<?php
}


	add_action( 'admin_menu', 'extra_trafficsourcecapture' );  
	
	function extra_trafficsourcecapture(){   
		$page_title = 'Traffic Source Capture';  
		$menu_title = 'Traffic Source Capture';  
		$capability = 'manage_options';  
		$menu_slug  = 'traffic-source-capture';
		$function   = 'admin_trafficsourcecapture';  
		$icon_url   = 'dashicons-media-code'; 
		$position   = 20;  
		
		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function,$icon_url,$position ); 
		
	} 
	
	if( !function_exists("admin_trafficsourcecapture") ) { function admin_trafficsourcecapture(){
	  
		$urlfildata = file(plugin_dir_path(__FILE__).'capturedurl.txt', FILE_IGNORE_NEW_LINES);
		?>  
			<h1>IP ADDRESS,Visited URL</h1> 
			
			<table style="width:100%">
			
		  <tr>
			<td colspan="2">
			<?php foreach($urlfildata as $key => $url){ ?>
			<form id="sourceform<?php echo $key; ?>" action="" method="POST" >
			    <pre><?php 
						$ipwurl = explode(',',$url);	
						
						$ipadr = trim($ipwurl[0]);		
						$ref_url = trim($ipwurl[1]);
						$ipaddress = explode('?',trim($ipwurl[1]));		
						$main_url = $ipaddress[0];
						$url_id = $ipaddress[1];
						
						echo "IP: ".$ipadr.'<br/>';
						echo "Ref URL: ".$ref_url.'<br/>';
						echo "MAIN URL: ".$main_url.'<br/>';
						echo "URL ID: ".$url_id.'<br/>';
					 ?>
				</pre>
				<br/>
				<!-- <input type="hidden" value="<?php echo $ipadr; ?>" name="ip_address" />
				<input type="hidden" value="<?php echo $ref_url; ?>" name="reference_url" />
				<input type="hidden" value="<?php echo $main_url; ?>" name="main_url" />
				<input type="hidden" value="<?php echo $url_id; ?>" name="url_id" />
				<input type="text" name="posturl" onchange="changeaction<?php //echo $key; ?>(this.value);" id="posturl" value="" />
					<button type="submit" name="postbtn">Post This Source</button>
					<script>
						function changeaction<?php //echo $key; ?> (posturl){
							jQuery("#sourceform<?php //echo $key; ?>").attr('action', posturl);
						}
					</script>
				<br/> -->	
			</form>		
				<?php 	
						echo "<br/><hr><br/>";
				} ?>	
			</td>
		  </tr>
		</table>
			
		<?php } 
		
		} ?>