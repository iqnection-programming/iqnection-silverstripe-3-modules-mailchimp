<?php

namespace IQnection\Mailchimp;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStrpie\Forms;
use SilverStripe\SiteConfig\SiteConfig;
use DrewM\MailChimp\MailChimp as MailchimpAPI;


class Mailchimp
{
	private $ApiKey;
	
	public function __construct($ApiKey=null)
    {
		if (!$ApiKey)
		{
	        $siteConfig = SiteConfig::current_site_config();
			$ApiKey = $siteConfig->MailchimpApiKey;
		}
        $this->setApiKey($ApiKey);
    }
	
	public function setApiKey($key)
	{
		$this->ApiKey = $key;
		return $this;
	}
	
	public function getApiKey()
	{
		return $this->ApiKey;
	}
	
	public function getAccountDetails()
	{
		$MC = new MailchimpAPI($this->getApiKey());
        try {
        	return $MC->get('/');
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
	}
	
	public function addContact($Email, $list_id=false, $FirstName = null, $LastName = null)
    {
        $errors = array();
        
        $mc = new MailchimpAPI($this->getApiKey());
        // attempt to fetch lists in the account, catching any exceptions
        try {
            $lists = $this->getLists();
        } catch (\Exception $ex) {
            foreach ($ex->getErrors() as $error) 
			{
                $errors[] = $error;
            }
        }
        
		// get list from the params, or just the first ACTIVE list if no param was passed
		$mcList = false;
		foreach ($lists as $list) 
		{
			if ( ($list_id == $list['id']) || (!$list_id) )
			{
				$mcList = $list;
				break;
			}
		}
        
		if ($mcList) 
		{
			$subscriberHash = $mc->subscriberHash($Email);
			$existing = $mc->get('lists/'.$list_id.'/members/'.$subscriberHash);
			if ( (!count($existing)) || ($existing['status'] == 404) )
			{
				$subscriberData = [
					'email_address' => $Email,
					'status' => 'subscribed'
				];
				if ($FirstName) { $subscriberData['merge_fields']['FNAME'] = $FirstName; }
				if ($LastName) { $subscriberData['merge_fields']['LNAME'] = $LastName; }
				try {
					$result = $mc->post('lists/'.$list_id.'/members',$subscriberData);
					return $result;
				} catch (\Exception $ex) {
					$errors[] = $ex->getErrors();
				}
			}
			else
			{
				$subscriberData = [];
				if ($FirstName) { $subscriberData['merge_fields']['FNAME'] = $FirstName; }
				if ($LastName) { $subscriberData['merge_fields']['LNAME'] = $LastName; }
				if (count($subscriberData))
				{
					try {
						$result = $mc->patch('lists/'.$list_id.'/members/'.$subscriberHash,$subscriberData);
						return $result;
					} catch (\Exception $ex) {
						$errors[] = $ex->getErrors();
					}
				}
			}
		}
        
        return $errors;
    }
    
    public function getLists($listID=null)
    {
        $MC = new MailchimpAPI($this->getApiKey());
		$request = 'lists';
		if ($listID)
		{
			$request .= '/'.$listID;
		}
		$lists = $MC->get($request);
        return $lists['lists'];
    }
}