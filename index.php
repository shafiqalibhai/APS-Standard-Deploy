<?php

require ("reader.php");
require ("propertiesmaker.php");
require ("extracter.php");
require ("mapper.php");
require ("dbmaker.php");
require ("installer.php");

$AbsolutePath = '/var/www/vhost/';
$VhostPath = 'test2';
$xmlHtdocsPath = 'htdocs/';
$PackagePath = 'packages/';
//$PackageFilename = 'joomla-1.5.22-2.app.zip';
//$ApsVersion = '1.2';
$PackageFilename = 'Drupal-6.19-2.app.zip';
//$PackageFilename = 'typo3-4.4.2-5.app.zip';
$ApsVersion = '1.0';
$NewDatabase = 'aps_launch_'.'1';
$DbPassword = 'aps_launch_'.'1';
$DatabaseHost = 'localhost';
$Domain = 'localhost';


//check for basic functions, classes and permissions
$Error = '';
if(!class_exists('SimpleXMLElement') || !function_exists('zip_open'))
{
	echo $Error.= '<li>System Packages for Zip are missing</li>';
	exit;
}

//reading
$Xml = GetXmlFromZip($PackagePath.$PackageFilename);
print_r($Xml);

//extracting
if(!file_exists($AbsolutePath . $VhostPath ))mkdir($AbsolutePath . $VhostPath, 0777, true);
ExtractZip($PackagePath . $PackageFilename, $xmlHtdocsPath, $AbsolutePath . $VhostPath );
ExtractZip($PackagePath . $PackageFilename, 'scripts', $AbsolutePath . $VhostPath . '/install_scripts/');

//mapping
PrepareMappings($Xml->mapping, $Xml->mapping['url'], $AbsolutePath . $VhostPath . '/', $Domain);

//makingdb
PrepareDatabase($Xml, $NewDatabase, $DbPassword, $DatabaseHost);

//installing
echo RunInstaller($Xml, $AbsolutePath, $VhostPath, $ApsVersion);

?>
