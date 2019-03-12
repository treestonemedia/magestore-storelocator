<?php

$installer = $this;
$installer->startSetup();

$installer->run("
   
        
    ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `monday_status` smallint(6) NOT NULL default '1'
    AFTER `longtitude`,
    ADD COLUMN `monday_open` varchar(5) NOT NULL default ''
    AFTER `monday_status`,
    ADD COLUMN `monday_close` varchar(5) NOT NULL default ''
    AFTER `monday_open`
    ;
    
    ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `tuesday_status` smallint(6) NOT NULL default '1'
    AFTER `monday_close`,
    ADD COLUMN `tuesday_open` varchar(5) NOT NULL default ''
    AFTER `tuesday_status`,
    ADD COLUMN `tuesday_close` varchar(5) NOT NULL default ''
    AFTER `tuesday_open`
    ;
   
    ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `wednesday_status` smallint(6) NOT NULL default '1'
    AFTER `tuesday_close`,
    ADD COLUMN `wednesday_open` varchar(5) NOT NULL default ''
    AFTER `wednesday_status`,
    ADD COLUMN `wednesday_close` varchar(5) NOT NULL default ''
    AFTER `wednesday_open`
    ;
    
     ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `thursday_status` smallint(6) NOT NULL default '1'
    AFTER `wednesday_close`,
    ADD COLUMN `thursday_open` varchar(5) NOT NULL default ''
    AFTER `thursday_status`,
    ADD COLUMN `thursday_close` varchar(5) NOT NULL default ''
    AFTER `thursday_open`
    ;
    
    ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `friday_status` smallint(6) NOT NULL default '1'
    AFTER `thursday_close`,
    ADD COLUMN `friday_open` varchar(5) NOT NULL default ''
    AFTER `friday_status`,
    ADD COLUMN `friday_close` varchar(5) NOT NULL default ''
    AFTER `friday_open`
    ;
    
    ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `saturday_status` smallint(6) NOT NULL default '1'
    AFTER `friday_close`,
    ADD COLUMN `saturday_open` varchar(5) NOT NULL default ''
    AFTER `saturday_status`,
    ADD COLUMN `saturday_close` varchar(5) NOT NULL default ''
    AFTER `saturday_open`
    ;
    
     ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `sunday_status` smallint(6) NOT NULL default '1'
    AFTER `saturday_close`,
    ADD COLUMN `sunday_open` varchar(5) NOT NULL default ''
    AFTER `sunday_status`,
    ADD COLUMN `sunday_close` varchar(5) NOT NULL default ''
    AFTER `sunday_open`
    ;
	
	DROP TABLE IF EXISTS {$this->getTable('storelocator_specialday')};
    CREATE TABLE {$this->getTable('storelocator_specialday')}  (
        `storelocator_specialday_id` int(11) NOT NULL auto_increment,
        `store_id` text NOT NULL,
        `date` date NOT NULL,
        `specialday_date_to` date NOT NULL,      
        `specialday_time_open` varchar(5) NOT NULL,
        `specialday_time_close` varchar(5) NOT NULL,        
        `comment` varchar(255) default NULL,
      PRIMARY KEY  (`storelocator_specialday_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
    
    DROP TABLE IF EXISTS {$this->getTable('storelocator_holiday')};
    CREATE TABLE {$this->getTable('storelocator_holiday')}  (
        `storelocator_holiday_id` int(11) NOT NULL auto_increment,
        `store_id` text NOT NULL,
        `date` date NOT NULL,
        `holiday_date_to` date NOT NULL,     
        `comment` varchar(255) default NULL,
      PRIMARY KEY  (`storelocator_holiday_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
    ");

$installer->endSetup();
