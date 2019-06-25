<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** Generate by crud generator model pada 2019-06-03 09:03:02
*   method index, add, edit, delete, detail dapat dioverride jika ingin melakukan custom pada controller tertentu
*   Author afandi
*/
class Setting extends MY_Controller {
    public $title = 'Data Settings';

    function __construct(){
        parent::__construct();
        $this->load->model('Setting_model','setting');
        $this->model = $this->setting;
    }

    public function setTableConfig()
    {
        $this->table->setHiddenField(['id']);
        parent::setTableConfig();
    }
}

