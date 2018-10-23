<?php  
namespace Admin\FilesExplorer;

use Zend\Validator\File\Extension;
use Zend\Validator\File\ExcludeExtension;
use Admin\FilesExplorer\Exception\NotAllowedException;
use Admin\FilesExplorer\Exception\PathNotExistsException;
use Admin\FilesExplorer\Exception\DirectoryExistsException;

class FilesExplorer
{
	protected $root = 'public/';
	protected $validators = array();
	protected $filters = array();
	protected $allowed = array(
		'0777',
		'0'
	);

	public function __construct($config)
	{	
		if(isset($config['root']))
		{
			$this->root = $config['root'];
		}

		if(isset($config['validators']))
		{
			if(isset($config['validators']['extension']) && isset($config['validators']['extension']['denied']))
			{
				$this->validators['extension'] = new ExcludeExtension($config['validators']['extension']['denied']);
			}

			if(isset($config['validators']['extension']) && isset($config['validators']['extension']['allowed']))
			{
				$this->validators['extension'] = new Extension($config['validators']['extension']['allowed']);
			}
		}

		$this->filters['realpath'] 	= new \Zend\Filter\RealPath();
		$this->filters['dir'] 		= new \Zend\Filter\Dir();
	}

	public function setRoot($root)
	{
		$this->root = $root;

		return $this;
	}

	public function getRoot()
	{
		return $this->root;
	}

	public function createFolder($path, $name)
	{
		$realpath = $this->realpath($path);

		if(is_dir($realpath . '/' . $name))
		{
			throw new DirectoryExistsException;
		}

		mkdir($realpath.'/'.$name, 0777);
	}

	public function delete($source)
	{
		$realpath = $this->realpath($source);

		if(is_dir($realpath) && substr(sprintf('%o', fileperms($realpath)), -4) != '0777')
		{
			throw new NotAllowedException;
		}

		if(is_file($realpath) && !in_array(substr(sprintf('%o', fileperms($realpath)), -4), $this->allowed) && !in_array(substr(sprintf('%o', dirname($realpath)), -4), $this->allowed))
		{
			throw new NotAllowedException;
		}

		if(is_dir($realpath))
		{
			$this->deleteDirectory($realpath);
		}
		else
		{
			unlink($realpath);
		}
	}

	public function move($from, $to)
	{
		$realpathFrom 	= $this->realpath($from);
		$realpathTo 	= $this->realpath($to);

		if(is_dir($realpathFrom) && substr(sprintf('%o', fileperms($realpathFrom)), -4) != '0777')
		{
			throw new NotAllowedException;
		}

		if(is_file($realpathFrom) && !in_array(substr(sprintf('%o', fileperms($realpathFrom)), -4), $this->allowed) && !in_array(substr(sprintf('%o', dirname($realpathFrom)), -4), $this->allowed))
		{
			throw new NotAllowedException;
		}

		if(is_dir($realpathTo) && substr(sprintf('%o', fileperms($realpathTo)), -4) != '0777')
		{
			throw new NotAllowedException;
		}

		if(is_file($realpathTo) && !in_array(substr(sprintf('%o', fileperms($realpathTo)), -4), $this->allowed) && !in_array(substr(sprintf('%o', dirname($realpathTo)), -4), $this->allowed))
		{
			throw new NotAllowedException;
		}

		$filter = new \Zend\Filter\File\Rename($realpathTo);
		$filter->filter($realpathFrom);
	}

	public function rename($source, $newname)
	{
		$path = dirname($source);
		$realpath = $this->realpath($path);

		if(is_dir($realpath) && substr(sprintf('%o', fileperms($realpath)), -4) != '0777')
		{
			throw new NotAllowedException;
		}

		if(is_file($realpath) && !in_array(substr(sprintf('%o', fileperms($realpath)), -4), $this->allowed) && !in_array(substr(sprintf('%o', dirname($realpath)), -4), $this->allowed))
		{
			throw new NotAllowedException;
		}

		$filter = new \Zend\Filter\File\Rename($realpath . '/' . $newname);
		$filter->filter($realpath . '/' . basename($source));
	}

	public function getPath($path, $keysArray = null, $base = '')
	{	
		$defaultKeys = array(
			'name' 		=> 'name',
			'extension' => 'extension',
			'path' 		=> 'path',
			'size' 		=> 'size',
			'type' 		=> 'type',
			'children' 	=> 'children',
			'permissions' => 'permissions',
		);

		if($keysArray)
		{
			foreach($keysArray as $key => $value)
			{
				if(isset($defaultKeys[$key]))
				{
					$defaultKeys[$key] = $value;
				}
			}
		}

		if(strpos($path, '.') !== false)
		{
			throw new NotAllowedException;
		}

		if(!$path || $path == '/')
		{
			$realpath = $this->filters['realpath']->filter($this->root);
		}
		else
		{
			$path = $this->root . '/' . str_replace(basename($this->root), '', ltrim($path, '/'));
			$realpath = $this->filters['realpath']->filter($path);
		}

		if(!$realpath)
		{
			throw new PathNotExistsException;
		}

		$structure = array();
		$iterator = new \DirectoryIterator($realpath);

		foreach($iterator as $file)
		{
			if($file->isDot() || !$file->isDir())
				continue;

			$structure[] = array(
				$defaultKeys['name'] 		=> $file->getBasename(),
				$defaultKeys['extension'] 	=> $file->getExtension(),
				$defaultKeys['path']		=> basename($this->root).str_replace(array(realpath($this->root), '\\'), array('', '/'), $file->getPathname()),
				$defaultKeys['size'] 		=> $file->getSize(),
				$defaultKeys['type'] 		=> $file->getType(),
				$defaultKeys['children'] 	=> count(scandir($file->getPathname())) - 2,
				$defaultKeys['permissions'] => substr(sprintf('%o', $file->getPerms()), -4),
			);
		}

		foreach($iterator as $file)
		{
			if($file->isDot() || $file->isDir())
				continue;

			if(!$this->validators['extension']->isValid($file->getPathname()))
			{
				continue;
			}

			$structure[] = array(
				$defaultKeys['name'] 		=> $file->getBasename(),
				$defaultKeys['extension'] 	=> $file->getExtension(),
				$defaultKeys['path']		=> basename($this->root).str_replace(array(realpath($this->root), '\\'), array('', '/'), $file->getPathname()),
				$defaultKeys['size'] 		=> $file->getSize(),
				$defaultKeys['type'] 		=> $file->getType(),
			);
		}

		return $structure;
	}

	protected function realpath($path)
	{
		if(!$path || $path == '/')
		{
			$realpath = $this->filters['realpath']->filter($this->root);
		}
		else
		{
			$path = $this->root . '/' . str_replace(basename($this->root), '', ltrim($path, '/\\'));
			$realpath = $this->filters['realpath']->filter($path);
		}

		return $realpath;
	}

	protected function deleteDirectory($path)
	{
		$iterator = new \DirectoryIterator($path);

		foreach($iterator as $file)
		{
			if($file->isDot())
				continue;

			if($file->isDir())
			{
				$this->deleteDirectory($file->getPathname());
			}
			else
			{
				unlink($file->getPathname());
			}
		}

		rmdir($path);
	}
}
?>