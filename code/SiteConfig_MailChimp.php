<?php
	
	class SiteConfig_MailChimp extends DataExtension 
	{
	
		private static $db = array(
			'MailchimpApiKey' => 'Varchar(255)',
			'MailchimpListId' => 'Varchar(255)',
		);
				
		public function updateCMSFields(FieldList $fields) 
		{
			// only admins can modify these fields
			if (Permission::check('ADMIN'))
			{
				$fields->addFieldToTab('Root.MailChimp', new TextField('MailchimpApiKey','MailChimp API Key'));
				$fields->addFieldToTab('Root.MailChimp', new TextField('MailchimpListId','MailChimp List ID'));
			}
		}
		
	}