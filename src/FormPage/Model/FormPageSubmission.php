<?php

namespace IQnection\Mailchimp\FormPage\Model;

use IQnection\Mailchimp\Mailchimp;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms;
use SilverStripe\SiteConfig\SiteConfig;

class FormPageSubmission extends DataExtension
{
	private static $db = [
		'MailchimpData' => 'Text'
	];
	
	public function updateCMSFields(Forms\FieldList $fields)
	{
		$fields->removeByName('MailchimpData');
		$fields->findOrMakeTab('Root.Developer.Mailchimp');
		$fields->addFieldToTab('Root.Developer.Mailchimp', Forms\LiteralField::create('mcdata','<div style="max-width:100%;overflow:auto;"><pre><xmp>'.print_r(json_decode($this->owner->MailchimpData,1),1).'</xmp></pre></div>') );
	}
	
	public function onBeforeWrite()
	{
		if (!$this->owner->Exists())
		{
			$this->addToMailchimp();
		}
	}
	
	public function addToMailchimp()
	{
		if ($list_id = $this->owner->Page()->MailchimpListID)
		{
			$mc = new Mailchimp(SiteConfig::current_site_config()->MailchimpApiKey);
			$this->owner->extend('onBeforeMailchimpAdd',$cc);
			$result = $mc->addContact($this->owner->Email, $list_id, $this->owner->FirstName, $this->owner->LastName);
			$this->owner->MailchimpData = json_encode($result);
			$this->owner->extend('onAfterMailchimpAdd',$result);
		}
		return $this;
	}
}