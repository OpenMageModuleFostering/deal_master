    <?php
     
    $installer = $this;
     
    $installer->startSetup();
     
    $installer->run("
     
    -- DROP TABLE IF EXISTS {$this->getTable('deal')};
    CREATE TABLE {$this->getTable('deal')} (
      `deal_id` int(11) unsigned NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `sku` varchar(50) NOT NULL default '',
      `product_id` int(11) NOT NULL,
      `discount_type` smallint(6) NOT NULL default '0',
      `start_date` datetime NULL,
      `end_date` datetime NULL,
      `regular_price` float  NOT NULL,
      `deal_price` float  NOT NULL,
      `discount` float NULL,
      `discount_amount` float NULL,
      `qty` int(11) NOT NULL default '0',
      `sold_qty` int(11) NOT NULL default '0',
      `featured` int(11) NOT NULL default '0',
      `content` text NOT NULL default '',
      `status` smallint(6) NOT NULL default '0',
      `style` smallint(6) NOT NULL default '0',
      `created_time` datetime NULL,
      `update_time` datetime NULL,
      PRIMARY KEY (`deal_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     
        ");
     
    $installer->endSetup();
