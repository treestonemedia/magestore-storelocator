<?php

$installer = $this;
$installer->startSetup();

$installer->run("
   
    
     ALTER TABLE  {$this->getTable('storelocator')}
    ADD COLUMN `rewrite_request_path` varchar(255) NOT NULL default ''
    AFTER `name`,
    ADD COLUMN `meta_keywords` text NOT NULL default ''
    AFTER `description`,
    ADD COLUMN `meta_contents` text NOT NULL default ''
    AFTER `meta_keywords`,
    ADD COLUMN `meta_title` text NOT NULL default ''
    AFTER `meta_keywords`;
    ALTER TABLE  {$this->getTable('storelocator_holiday')}
    ADD COLUMN `holiday_name` varchar(255) NOT NULL default ''
    AFTER `storelocator_holiday_id`;
    
    ALTER TABLE  {$this->getTable('storelocator_specialday')}
    ADD COLUMN `specialday_name` varchar(255) NOT NULL default ''
    AFTER `storelocator_specialday_id`;
    
    ALTER TABLE {$this->getTable('storelocator_holiday')} 
    MODIFY `store_id` text NOT NULL;
    
    ALTER TABLE {$this->getTable('storelocator_specialday')} 
    MODIFY `store_id` text NOT NULL;

    ");

$installer->endSetup();
