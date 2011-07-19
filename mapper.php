<?php

/**
 * setup path environment variables for the installation script
 *
 * @param	parentmapping	instance of parsed xml file, current mapping position
 * @param	url				relative path for application specifying the current path within the mapping tree
 * @param	path			absolute path for application specifying the current path within the mapping tree
 */

function PrepareMappings($ParentMapping, $Url, $Path, $Domain)
{
	//check for special PHP permissions
	//must be done with xpath otherwise check not possible (XML parser problem with attributes)

	if($ParentMapping && $ParentMapping !== null)
	{
		$ParentMapping->registerXPathNamespace('p', 'http://apstandard.com/ns/1/php');
		$Result = $ParentMapping->xpath('p:permissions');

		if($Result[0]['writable'] == 'true')
		{
			//fixing file permissions to writeable

			if(is_dir($Path))
			{
				chmod($Path, 0775);
			}
			else
			{
				chmod($Path, 0664);
			}
		}

		if($Result[0]['readable'] == 'false')
		{
			//fixing file permissions to non readable

			if(is_dir($Path))
			{
				chmod($Path, 0333);
			}
			else
			{
				chmod($Path, 0222);
			}
		}
	}

	//set environment variables

	 $EnvVariable = str_replace("/", "_", $Url);
	 putenv('WEB_' . $EnvVariable . '_DIR=' . $Path);
	 putenv('BASE_URL_HOST=' . $Domain);
	 putenv('BASE_URL_PATH=' . $Path);
	 putenv('BASE_URL_SCHEME=http');
	
	//resolve deeper mappings
	if($ParentMapping && $ParentMapping !== null)
	{
		foreach($ParentMapping->mapping as $Mapping)
		{
			//recursive check of other mappings

			if($Url == '/')
			{
				PrepareMappings($Mapping, $Url . $Mapping['url'], $Path . $Mapping['url']);
			}
			else
			{
				PrepareMappings($Mapping, $Url . '/' . $Mapping['url'], $Path . '/' . $Mapping['url']);
			}
		}
	}
}
?>
