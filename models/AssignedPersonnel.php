<?php

/**
 * Class AssignedPersonnel (Entity)
 * I represent a link between a User and an SCP Anomaly.
 */
class AssignedPersonnel
{
    // --- Attributes ---
    private $user_id;
    private $scp_id;
    private $role;

    /**
     * I construct the relationship object.
     */
    public function __construct($user_id, $scp_id, $role)
    {
        $this->user_id = $user_id;
        $this->scp_id = $scp_id;
        $this->role = $role;
    }

    // ==================== GETTERS ====================

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getScpId()
    {
        return $this->scp_id;
    }

    public function getRole()
    {
        return $this->role;
    }

    // ==================== SETTERS ====================

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setScpId($scp_id)
    {
        $this->scp_id = $scp_id;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }
}
