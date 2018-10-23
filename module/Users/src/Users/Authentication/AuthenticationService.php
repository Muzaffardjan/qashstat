<?php  
namespace Users\Authentication;

class AuthenticationService 
	extends 
		\Zend\Authentication\AuthenticationService 
	implements 
		\ZfcRbac\Identity\IdentityInterface
{
	public function getRoles()
	{
		$identity = $this->getStorage()->read();

		if(!($identity instanceof \ZfcRbac\Identity\IdentityInterface))
		{
			throw new \Exception('identity must be instance of ZfcRbac\Identity\IdentityInterface');
		}

		return $identity->getRoles();
	}	
}
?>