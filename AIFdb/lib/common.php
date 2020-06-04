<?php
require_once(INSTALLDIR.'/lib/common_fn.php');
require_once('PEAR.php');
require_once('DB/DataObject.php');
require_once('DB/DataObject/Cast.php');

$config['db'] = &PEAR::getStaticProperty('DB_DataObject','options');

$config['db'] =
    array('database' => 'SET IN config.php',
          'schema_location' => INSTALLDIR . '/classes',
          'class_location' => INSTALLDIR . '/classes',
          'require_prefix' => 'classes/',
          'class_prefix' => '',
          'db_driver' => 'MDB2',
          'quote_identifiers' => false);

require_once(INSTALLDIR.'/config.php');
require_once(INSTALLDIR.'/lib/action.php');

require_once(INSTALLDIR.'/classes/Descriptors.php');
require_once(INSTALLDIR.'/classes/Locutions.php');
require_once(INSTALLDIR.'/classes/NodeSets.php');
require_once(INSTALLDIR.'/classes/People.php');
require_once(INSTALLDIR.'/classes/Schemes.php');
require_once(INSTALLDIR.'/classes/User.php');
require_once(INSTALLDIR.'/classes/DescriptorFulfillment.php');
require_once(INSTALLDIR.'/classes/Edges.php');
require_once(INSTALLDIR.'/classes/ExternalLinks.php');
require_once(INSTALLDIR.'/classes/NodeSetMappings.php');
require_once(INSTALLDIR.'/classes/Nodes.php');
require_once(INSTALLDIR.'/classes/FormEdges.php');
require_once(INSTALLDIR.'/classes/SchemeFulfillment.php');
