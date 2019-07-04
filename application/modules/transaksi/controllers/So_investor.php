<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class So_investor extends MY_Controller
{
    public $title = 'Data Mapping Investor';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales_order_investor_model','menus');
        $this->model = $this->menus;
    }

    protected function setIndexData()
    {
        $this->model->setJoinPartner(TRUE);
        $this->model->setJoinSO(TRUE);   
        return parent::setIndexData();
    }

    public function setTableConfig()
    {
        $this->table->key_record = array($this->model->getKeyName());
        $this->table->setHiddenField(['id']);
        $this->table->extra_columns = [
            'btnEdit' => [
                'data' => generatePrimaryButton('<i class="fa fa-pencil"></i>', ['onclick' => 'App.editRecord(this)', 'data-url' => site_url($this->pathView.'/'.$this->actionMethodEdit)])
                    .' '.generateDangerButton('<i class="fa fa-recycle"></i>', ['onclick' => 'App.deleteRecord(this)', 'data-urlmessage' => site_url($this->pathView.'/deleteMessage'), 'data-nexturl' => site_url($this->pathView.'/'.$this->actionMethodIndex), 'data-url' => site_url($this->pathView.'/'.$this->actionMethodDelete)]),
                    
                ],
        ];
    }

    public function save(){
        $_POST['data']['amount'] = $_POST['data']['amount']/100;
        parent::save();
    }
}
