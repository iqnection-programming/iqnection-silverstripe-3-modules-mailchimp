YOU MUST HAVE CLIENT'S MAILCHIMP LOGIN 

Copy Mailchimp folder. Copy Mailchimp class to /mysite/code. Add MailchimpApiKey and MailchimpListId fields in SilverStripe.

Log in to MailChimp as the client: https://login.mailchimp.com

Make an API key in the client's MailChimp. At the time this was written, this was located under Account > Extras > API Keys.

Get the ID for the Mailchimp list. This is NOT the ID you see in the URL when clicking a list. Go to Lists and select the list you want to use, then go to Settings > List name and defaults. The List ID is located at the top of the right column on this page. 

Create an extension for your page, and add the following function to the Controller_Extension:

public function onAfterSubmit($submission){
	$site_config = SiteConfig::current_site_config();
				
	$_mailchimp = new Mailchimp($site_config->MailchimpApiKey);
				
	$_list_id = $site_config->MailchimpListId;
				
	$current_mailchimp = $_mailchimp->lists->memberInfo($_list_id,array(array("email"=>$submission->Email)));
				
	if(!$current_mailchimp['success_count']){
		//Person isn't on MailChimp, so we add.					
		try{				
			$result = $_mailchimp->lists->subscribe($_list_id,array("email"=>$submission->Email),array(),'html',false);
		}catch(Exception $e){ self::Log(print_r($e, true)); }
	}			
}
 