<?php

/**
 * create a database if necessary and setup environment variables
 *
 * @param	xml			instance of a valid xml object with a parsed APP-META.xml file
 * @param	row			current entry from the database for app to handle
 * @param	task		numeric code to specify what to do
 */

function PrepareDatabase($Xml, $NewDatabase, $DbPassword, $DatabaseHost)
{

	global $db_root;
	
	$Xml->registerXPathNamespace('db', 'http://apstandard.com/ns/1/db');
	$XmlDb = new DynamicProperties;
	$XmlDb->db->id = getXPathValue($Xml, '//db:id');
	
	if($XmlDb->db->id)
	{
		mysql_connect('localhost', 'root', 'root');
		mysql_query('DROP DATABASE IF EXISTS `' . $NewDatabase . '`');
		mysql_query('CREATE DATABASE IF NOT EXISTS `' . $NewDatabase . '`');
		mysql_query('GRANT ALL PRIVILEGES ON `' . $NewDatabase . '`.* TO `' . $NewDatabase . '`@`' . $DatabaseHost . '` IDENTIFIED BY \'password\'');
		mysql_query('SET PASSWORD FOR `' . $NewDatabase . '`@`' . $DatabaseHost . '` = PASSWORD(\'' . $DbPassword . '\')');

		mysql_query('FLUSH PRIVILEGES');

		//get first mysql access host

		//$AccessHosts = array_map('trim', explode(',', $this->Hosts));

		//environment variables

		putenv('DB_' . $XmlDb->db->id . '_TYPE=mysql');
		putenv('DB_' . $XmlDb->db->id . '_NAME=' . $NewDatabase);
		putenv('DB_' . $XmlDb->db->id . '_LOGIN=' . $NewDatabase);
		putenv('DB_' . $XmlDb->db->id . '_PASSWORD=' . $DbPassword);
		putenv('DB_' . $XmlDb->db->id . '_HOST=' . $DatabaseHost);
		putenv('DB_' . $XmlDb->db->id . '_PORT=3306');
		putenv('DB_' . $XmlDb->db->id . '_VERSION=' . mysql_get_server_info());
	}
}

function getXPathValue($xmlobj = null, $path = null, $single = true)
{
	$result = null;
	
	$tmpxml = new DynamicProperties;
	$tmpxml = ($xmlobj->xpath($path)) ? $xmlobj->xpath($path) : false;

	if($result !== false)
	{
		$result = ($single == true) ? (string)$tmpxml[0] : $tmpxml;
	}
	return $result;
}
?>
