<?php
$installer = $this;
$installer->startSetup();
$installer->run("
     ALTER TABLE {$this->getTable('storelocator')} ADD COLUMN `image_name` TEXT NOT NULL;
");
$installer->endSetup();
