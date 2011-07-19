<?php

/**
 * extract complete directories from a zipfile
 *
 * @param	filename		path to zipfile to extract
 * @param	directory		which directory in zipfile to extract
 * @param	destination		destination directory for files to extract
 * @param	guid			id for user and group of all files and dir
 * @return	success true/error false
 */

function ExtractZip($Filename, $Directory, $Destination)
//function ExtractZip($Filename, $Directory, $Destination, $Guid)
{
	if(!file_exists($Filename))return false;

	//fix slash notation for correct paths

	if(substr($Directory, -1, 1) == '/')$Directory = substr($Directory, 0, strlen($Directory) - 1);

	if(substr($Destination, -1, 1) != '/')$Destination.= '/';

	//open zipfile to read its contents

	$ZipHandle = zip_open(realpath($Filename));
	if(is_resource($ZipHandle))
	{
		while($ZipEntry = zip_read($ZipHandle))
		{
			if(substr(zip_entry_name($ZipEntry), 0, strlen($Directory)) == $Directory)
			{
				//fix relative path from zipfile

				$NewPath = zip_entry_name($ZipEntry);
				$NewPath = substr($NewPath, strlen($Directory));
				echo $NewPath;
				//directory

				if(substr($NewPath, -1, 1) == '/')
				{
					if(!file_exists($Destination . $NewPath))mkdir($Destination . $NewPath, 0777, true);
				}
				else
				{
					//files

					if(zip_entry_open($ZipHandle, $ZipEntry))
					{
							// handle new directory
							$dir = dirname($Destination.$NewPath);
							if (!file_exists($dir)) {
								mkdir ($dir, 0777, true);
							}

							$File = fopen($Destination . $NewPath, "wb");

							if($File)
							{
								while($Line = zip_entry_read($ZipEntry))
								{
									fwrite($File, $Line);
								}

								fclose($File);
						}
						else
						{
							return false;
						}
					}
				}
			}
		}

		zip_close($ZipHandle);
		return true;
	}
	else
	{
		$ReturnLines = array();
		$ReturnVal = - 1;

		//on 64 bit systems the zip functions can fail -> use exec to extract the files

		$ReturnLines = safe_exec('unzip -o -qq ' . escapeshellarg(realpath($Filename)) . ' ' . escapeshellarg($Directory . '/*') . ' -d ' . escapeshellarg(sys_get_temp_dir()), $ReturnVal);

		if($ReturnVal == 0)
		{
			//fix absolute structure of extracted data

			if(!file_exists($Destination))mkdir($Destination, 0777, true);
			safe_exec('cp -Rf ' . sys_get_temp_dir() . '/' . $Directory . '/*' . ' ' . escapeshellarg($Destination));
			self::UnlinkRecursive(sys_get_temp_dir() . '/' . $Directory . '/');
			return true;
		}
		else
		{
			return false;
		}
	}
	
//set right file owner
//safe_exec('chown ' . (int)$Guid . ':' . (int)$Guid . ' -R ' . escapeshellarg($Destination));
			
	return false;
}
?>
