<?php
namespace moss\security;

/**
 * Security user provider
 *
 * @package Moss Security
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface UserProviderInterface
{

    /**
     * Returns true if provider can handle credentials
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function supportsCredentials(array $credentials);

    /**
     * Creates token from credentials
     *
     * @param array $credentials
     *
     * @return $this
     */
    public function tokenize(array $credentials);

    /**
     * Returns true if provider can handle token
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token);

    /**
     * Authenticates token in provider
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function authenticate(TokenInterface $token);

    /**
     * Returns user instance matching token
     *
     * @param TokenInterface $token
     *
     * @return UserInterface
     */
    public function get(TokenInterface $token);

    /**
     * Updates user data
     *
     * @param TokenInterface $token
     *
     * @return $this
     */
    public function refresh(TokenInterface $token);
}
