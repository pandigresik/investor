<?php

class Sales_order_model extends MY_Model
{   
    public $_table = 'sale_order';
    
    public function __construct()
    {
        parent::__construct();
    }

    
    protected function setOptionDataForm($where = array())
    {
        //$parentMenu = $this->dropdown('id', 'name');
        
        // $this->form['icon']['options'] = $this->listICon();
    }
}
