<?php
namespace Moss\security;

/**
 * Security token
 *
 * @package Moss Security
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface TokenInterface extends \Serializable {

	/**
	 * Returns set authentication credentials
	 *
	 * @return array
	 */
	public function credentials();

	/**
	 * Removes credentials
	 *
	 * @return $this
	 */
	public function eraseCredentials();

	/**
	 * Returns true if token is authenticated
	 *
	 * @return bool
	 */
	public function isAuthenticated();

	/**
	 * Sets auth key
	 *
	 * @param null|string $auth
	 *
	 * @return $this
	 */
	public function authenticate($auth = null);
}