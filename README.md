# Mailchimp Module for SilverStripe 4

Provides functionality to add contacts to Mailchimp
Directly integrates with IQnection's Base Pages module

## Setup:
Log in to MailChimp as the client: https://login.mailchimp.com

Create a Mailchimp API key. At the time this was written, this was located under Account > Extras > API Keys.

If using the IQnection BasePages module, a field will automatically be added to the form controls tab in the CMS to select which list submissions should be added to.

### To add contacts using your own controller:
Create an extension for your page, and add the following function to the Controller Extension:

class FormSubmission extends DataObject
{
	public function onBeforeWrite()
	{
		parent::onBeforeWrite();
		if (!$this->ID)
		{
			$mc = new Mailchimp(SiteConfig::current_site_config()->MailchimpApiKey);
			$result = $mc->addContact($this->Email, 'my_mailchimp_list_id', $this->FirstName, $this->LastName);
		}
		return $this;
	}
} 