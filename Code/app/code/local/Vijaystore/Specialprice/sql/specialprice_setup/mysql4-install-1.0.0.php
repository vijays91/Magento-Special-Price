<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('specialprice/specialprice'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product Id')
    ->addColumn('special_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Special Price')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Status')	
	->addColumn('special_price_from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => false,
        ), 'Special Price From Date')
	->addColumn('special_price_to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => false,
        ), 'Special Price To Date')		
	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated At')		
    ->setComment('Set Special Price');	
$installer->getConnection()->createTable($table);
$installer->endSetup();

?>