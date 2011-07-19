<?php

/**
 * run the installation script and log errors if there are some
 *
 * @param	xml				instance of a valid xml object with a parsed APP-META.xml file
 * @param	absolutepath	path till vhosts dir
 * @param	vhostpath		virtual host directory inside absolutepath
 * @return	success true/error false
 */

function RunInstaller($Xml, $AbsolutePath, $VhostPath, $ApsVersion)
//function RunInstaller($Xml, $AbsolutePath, $VhostPath, $Guid)
{
	//installation
	//setup right path and run installation script
	if(!is_dir($AbsolutePath . $VhostPath . '/install_scripts/'))
	{
		echo 'Directory: '. $AbsolutePath . $VhostPath . '/install_scripts/ does not exist';
		return;
	}
	chdir($AbsolutePath . $VhostPath . '/install_scripts/');
	
	// make configure-script executable
	if($ApsVersion != '1.0')
	{
		$scriptname = (string)$Xml->service->provision->{'configuration-script'}['name'];
	} else {
		$scriptname = 'configure';
	}

	chmod($AbsolutePath . $VhostPath . '/install_scripts/'.$scriptname, 0755);
	//$Return = exec('php ' . escapeshellarg($AbsolutePath . $VhostPath . '/install_scripts/configure install'), $ReturnStatus);
	$Return = exec('php ' . escapeshellarg($AbsolutePath . $VhostPath . '/install_scripts/'.$scriptname) . ' install', $ReturnStatus);
	//installation succeeded
	//chown all files if installtion script has created some new files. otherwise customers cannot edit the files via ftp

	//safe_exec('chown ' . (int)$Guid . ':' . (int)$Guid . ' -R ' . escapeshellarg($AbsolutePath . $VhostPath . '/'));
	return $Return;
}

?>
