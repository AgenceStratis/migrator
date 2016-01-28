<?php

namespace Stratis\Migrator;

function config ( $fileConf ) {
	
	$options = array(
		'file', 'database_type', 'database_name', 'server',
		'username', 'password', 'charset', 'table' );
	
	$baseConf = array(
		'source' => array( 'type', 'options' => $options ),
		'dest' => array( 'type', 'options' => $options ),
		'processors' => array( 'values' => array(), 'fields' => array() ));
	
	return array_merge( $baseConf, $fileConf );
}
