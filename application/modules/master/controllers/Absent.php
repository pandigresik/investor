<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/** Generate by crud generator model pada 2019-05-31 15:53:22
 *   method index, add, edit, delete, detail dapat dioverride jika ingin melakukan custom pada controller tertentu
 *   Author afandi.
 */
class Absent extends MY_Controller
{
    public $title = 'Data Absent';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('absent_type_model', 'absent_type');
        $this->model = $this->absent_type;
    }

    public function setTableConfig()
    {
        $this->table->setHiddenField(['id']);
        parent::setTableConfig();
        
    }
}
