<?php

class User_model extends MY_Model
{
    protected $_table = 'res_users';
    public $belongs_to = array('role');
    private $withRole = FALSE;
    protected $columnTableData = ['users.id as id', 'username', 'users.name as name', 'email', 'roles.name as role_name', 'users.status as status'];
    protected $headerTableData = [
        [['data' => 'Id'], ['data' => 'Username'], ['data' => 'Nama'], ['data' => 'Email'], ['data' => 'Role / Peran'], ['data' => 'Status'], ['data' => 'Aksi']],
    ];

    protected $before_get = array('joinRole');

    
    public function joinRole()
    {
        if($this->getWithRole()){
            $this->_database->join('roles', 'roles.id = users.role_id', 'left');
        }
    }

    /**
     * Get the value of withRole
     */ 
    public function getWithRole()
    {
        return $this->withRole;
    }

    /**
     * Set the value of withRole
     *
     * @return  self
     */ 
    public function setWithRole($withRole)
    {
        $this->withRole = $withRole;
    }
}
