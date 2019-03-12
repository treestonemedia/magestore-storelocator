<?php
$installer = $this;
$installer->startSetup();
$installer->run("
     ALTER TABLE {$this->getTable('storelocator')} ADD COLUMN `product_ids` TEXT NOT NULL;
");
$installer->endSetup();
