<?php

namespace IQnection\Mailchimp\FormPage;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms;
use SilverStripe\SiteConfig\SiteConfig;

class FormPage extends DataExtension
{
	private static $db = [
		'MailchimpListID' => 'Varchar(20)'
	];
	
	public function updateCMSFields(Forms\FieldList $fields)
	{
		$api = new \IQnection\Mailchimp\Mailchimp(SiteConfig::current_site_config()->MailchimpApiKey);
		$lists = $api->getLists();
		$listOptions = [];
		foreach($lists as $list)
		{
			$listOptions[$list['id']] = $list['name'];
		}
		$fields->addFieldToTab('Root.FormControls', Forms\DropdownField::create('MailchimpListID','Add Submissions to List:')
			->setSource($listOptions)
			->setEmptyString('-- Select --') );
		return $fields;
	}
}