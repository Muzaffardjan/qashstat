<?php 
namespace Media\Model;

/**
* Directory helper class to manipulate directories
*/
class DirectoryHelper	
{
	/**
	 * Check if directory is empty
 	 *  
	 * @param string $directory path to directory to check
	 * @return bool true if directory is empty, false otherwise
	 * @throws \RuntimeException if $directory is empty string
 	 * @throws \UnexpectedValueException if $directory is not readable
	 */
	public static function isEmpty($directory)
	{
		$iterator = new \DirectoryIterator($directory);
		
		foreach ($iterator as $fileInfo)
		{
			if ($fileInfo->isDot()) continue;
			
			// If directory contains at least one file or directory it is not empty
			return false;
		}

		// If we can't find anything directory is empty
		return true;
	}

	/**
	 * Delete all files in directory
	 * 
	 * @param string $directory path to directory
	 * @return bool status of operation
	 * @throws \RuntimeException if $directory is empty string
 	 * @throws \UnexpectedValueException if $directory is not readable
	 */		
	public static function emptyDirectory($directory)
	{
		$iterator = new \DirectoryIterator($directory);
		
		foreach ($iterator as $fileInfo)
		{
			if ($fileInfo->isDot()) continue;

			if ($fileInfo->isDir())
			{
				if ( ! self::deleteDirectory())
					return false;
			}
			elseif ($fileInfo->isFile())
			{
				if ( ! unlink($fileInfo->getPathname()))
					return false;
			}
		}

		return true;
	}

	/**
	 * Delete directory with files in it
	 * 
	 * @param string $directory path to directory
	 * @return bool status of operation
	 * @throws \RuntimeException if $directory is empty string
 	 * @throws \UnexpectedValueException if $directory is not readable
	 */
	public static function deleteDirectory($directory)
	{
		if (self::emptyDirectory($directory))
		{
			if (rmdir($directory))
			{
				return true;
			}
		}

		return false;
	}
}
?>