<?php

    class SiteConfig_MailChimp extends DataExtension
    {
    
        private static $db = array(
            'MailchimpApiKey' => 'Varchar(255)',
            'MailchimpListId' => 'Varchar(255)',
        );
                
        public function updateCMSFields(FieldList $fields)
        {
            $tab = $fields->findOrMakeTab('Root.Developer.MailChimp');
			$tab->push( new TextField('MailchimpApiKey', 'MailChimp API Key'));
			$tab->push( new TextField('MailchimpListId', 'MailChimp List ID'));
        }
    }
