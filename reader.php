<?php

/**
 * reads the content of a xml metafile from disk and returns the parsed data
 *
 * @param	filename		xmlfile to parse
 * @return	success parsed xml content of file / error false
 */

function GetXmlFromFile($Filename)
{
	
	//check for basic functions and classes
	/*
	$Error = '';

	if(!class_exists('SimpleXMLElement')
	   || !function_exists('zip_open'))
	{
		$Error.= '<li>' . $lng['aps']['class_zip_missing'] . '</li>';
	}
	*/
	
	//read xml file from disk and return parsed data

	if(!file_exists($Filename))return false;
	$XmlContent = file_get_contents($Filename);
	$Xml = new SimpleXMLElement($XmlContent);
	return $Xml;
}

/**
 * fetches the xml metafile from a zipfile and returns the parsed data
 *
 * @param	filename		zipfile containing the xml meta data
 * @return	success parsed xml content of zipfile / error false
 */

function GetXmlFromZip($Filename)
{
	if(!file_exists($Filename))return false;

	//get content for xml meta data file from within the zipfile

	if($XmlContent = GetContentFromZip($Filename, 'APP-META.xml'))
	{
		//parse xml content

		$Xml = new SimpleXMLElement($XmlContent);
		return $Xml;
	}
	else
	{
		return false;
	}
}

/**
 * function extracts resources from a zipfile to return or save them on disk
 *
 * @param	filename		zipfile to read data from
 * @param	file			file within zip archive to read
 * @param	destination		optional parameter where to save file from within the zip file
 * @return	success content of file from zip archive / error false
 */

function GetContentFromZip($Filename, $File, $Destination = '')
{
	if(!file_exists($Filename))return false;
	$Content = '';

	//now using the new ZipArchive class from php 5.2

	$Zip = new ZipArchive;
	$Resource = $Zip->open(realpath($Filename));

	if($Resource === true)
	{
		$FileHandle = $Zip->getStream($File);

		if(!$FileHandle)return false;

		while(!feof($FileHandle))
		{
			$Content.= fread($FileHandle, 8192);
		}

		fclose($FileHandle);
		$Zip->close();
	}
	else
	{
		//on 64 bit systems the zip functions can fail -> use safe_exec to extract the files

		$ReturnLines = array();
		$ReturnVal = - 1;
		$ReturnLines = safe_exec('unzip -o -j -qq ' . escapeshellarg(realpath($Filename)) . ' ' . escapeshellarg($File) . ' -d ' . escapeshellarg(sys_get_temp_dir() . '/'), $ReturnVal);

		if($ReturnVal == 0)
		{
			$Content = file_get_contents(sys_get_temp_dir() . '/' . basename($File));
			unlink(sys_get_temp_dir() . '/' . basename($File));

			if($Content == false)return false;
		}
		else
		{
			return false;
		}
	}

	//return content of file from within the zipfile or save to disk

	if($Content == '')
	{
		return false;
	}
	else
	{
		if($Destination == '')
		{
			return $Content;
		}
		else
		{
			//open file and save content

			$File = fopen($Destination, "wb");

			if($File)
			{
				fwrite($File, $Content);
				fclose($File);
			}
			else
			{
				return false;
			}
		}
	}
}


?>
