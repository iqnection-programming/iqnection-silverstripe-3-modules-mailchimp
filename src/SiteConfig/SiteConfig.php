<?php

namespace IQnection\Mailchimp\SiteConfig;

use IQnection\Mailchimp\Mailchimp;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms;

class SiteConfig extends DataExtension
{
	private static $db = [
		'MailchimpApiKey' => 'Varchar(255)'
	];
	
	public function updateCMSFields(Forms\FieldList $fields)
	{
		$tab = $fields->findOrMakeTab('Root.Developer.Mailchimp');
		$tab->push( Forms\TextField::create('MailchimpApiKey','Mailchimp API Key') );
		if ($mcAccountName = $this->getMailchimpAccountName())
		{
			$tab->push( Forms\HeaderField::create('mcAccountName','Mailchimp Account: '.$mcAccountName,3) );
		}
		if ($Lists = $this->getMailchimpLists())
		{
			$tab->push( $Lists );
		}
	}
	
	public function getMailchimpAccountName()
	{
		if (!$this->owner->MailchimpApiKey) {
			return null;
		}
		
		$MC = new Mailchimp($this->owner->MailchimpApiKey);
		try {
			$details = $MC->getAccountDetails();
		} catch(\Exception $e) {
			return null;
		}
		if (isset($details['account_name']))
		{
			return $details['account_name'];
		}
	}
	
	private function getMailchimpLists()
	{
		if (!$this->owner->MailchimpApiKey) {
			return null;
		}
		
		$MC = new Mailchimp($this->owner->MailchimpApiKey);
		try {
			$lists = $MC->getLists();
		} catch(\Exception $e) {
			return Forms\LiteralField::create('mcerror','<p>'.$e->getMessage().'</p>');
		}
		
		// get list from the params, or just the first ACTIVE list if no param was passed
		$the_lists = ArrayList::create();
		foreach ($lists as $list) 
		{
			$the_lists->push(ArrayData::create(array(
				'ID' => $list['id'],
				'Name' => $list['name'],
				'Contacts' => $list['stats']['member_count']
			)));
		}
		$gf_config = Forms\GridField\GridFieldConfig_Base::create();
		$gf_config->getComponentByType(Forms\GridField\GridFieldDataColumns::class)
			->setDisplayFields(array('ID'=>'List ID','Name'=>'Name','Contacts' => 'Contacts'));
		$gridField = Forms\GridField\GridField::create(
			'MailchimpLists',
			'Mailchimp Lists',
			$the_lists,
			$gf_config
		);
		return $gridField;
	}
}