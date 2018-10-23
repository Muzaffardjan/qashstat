<?php  
namespace Users\Authentication;

interface DatabaseTableInterface
{	
	/**
     * Gets identity by given credential
     *
     * @return null|ResultIdentityInterface
     */
	public function getUser($credential);
} 
?>