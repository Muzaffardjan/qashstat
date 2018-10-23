<?php  
namespace Users\Authentication;

interface ResultIdentityInterface
{
     /**
     * Gets password of identity
     *
     * @return string
     */
     public function getPassword();

	/**
     * Gets string representation of an identity object
     *
     * @return string
     */
	public function toString();

	/**
     * Sets string state of object to instance
     *
     * @return null
     */
	public function fromString($string);
} 
?>