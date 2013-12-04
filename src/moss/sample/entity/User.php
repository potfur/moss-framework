<?php
namespace moss\sample\entity;

use moss\security\UserInterface;

class User implements UserInterface
{

    private $id;
    private $role;
    private $right;

    /**
     * Construct
     *
     * @param int|string   $id
     * @param string|array $role
     * @param string|array $access
     */
    public function __construct($id, $role, $right)
    {
        $this->id = $id;
        $this->role = (array) $role;
        $this->right = (array) $right;
    }

    /**
     * Returns user identifier
     *
     * @return int|string
     */
    public function identify()
    {
        return $this->id;
    }

    /**
     * Returns all roles as an array
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->role;
    }

    /**
     * Returns true if user has role
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->role);
    }

    /**
     * Returns all role access as an array
     *
     * @return array
     */
    public function getRights()
    {
        return $this->right;
    }

    /**
     * Returns true if user has access
     *
     * @param string $right
     *
     * @return bool
     */
    public function hasRight($right)
    {
        return in_array($right, $this->right);
    }


}