<?php  
namespace Users\ZfcRbac\Service;

use ZfcRbac\Service\AuthorizationService as ZfcAuthorizationService;

class AuthorizationService extends ZfcAuthorizationService
{
	/**
	 * Superuser role name holder 
	 *
	 * @var string
	 */
	protected $superuser;

	/**
	 * Sets Superuser role name
	 * 
	 * @return AuthorizationService 
	 */
	public function setSuperuser($role)
	{
		$this->superuser = (string) $role;
	}

	/**
	 * Gets superuser name
	 *
	 * @return string|null
	 */
	public function getSuperuser()
	{
		return $this->superuser;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isGranted($permission, $context = null)
	{
		// if is superuser grant all
		if(null !== ($superuser = $this->getSuperuser()))
		{
			$roles = $this->roleService->getIdentityRoles();

			// role name case insensitive
			$superuser = strtolower($superuser);

			foreach($roles as $role)
			{
				if(strtolower($role->getName()) === $superuser)
				{
					return true;
				}
			}
		}

		return parent::isGranted($permission, $context);
	}
}
?>