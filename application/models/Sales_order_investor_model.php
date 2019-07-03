<?php

class Sales_order_investor_model extends MY_Model
{   
    public $_table = 'ks_sales_order_investor';
    
    protected $columnTableData = ['id','amount','sales_order_id','partner_id'];
    protected $headerTableData = [
        [['data' => 'Id'], ['data' => 'Pembiayaan'], ['data' => 'Sales Order'], ['data' => 'Investor']],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    protected $form = array(
        'name' => array(
            'id' => 'name',
            'label' => 'Name',
            'placeholder' => 'nama menu',
            'required' => 'required',
            'value' => '',
        ),
        'route' => array(
            'id' => 'route',
            'label' => 'Route',
            'placeholder' => 'route atau url ',
            'value' => '',
        ),
        'icon' => array(
            'id' => 'icon',
            'label' => 'Icon',
            'placeholder' => 'icon font awesome',
            'required' => 'required',
            'type' => 'input',
            'value' => '',
        ),
        'parent_id' => array(
            'id' => 'parent_id',
            'label' => 'Menu Reference',
            'type' => 'dropdown',
            'required' => 'required',
            'options' => array(),
            'value' => '',
        ),
        'status' => array(
            'id' => 'status',
            'label' => 'Status',
            'required' => 'required',
            'type' => 'dropdown',
            'options' => array(
                '1' => 'Aktif',
                '0' => 'Non Aktif',
            ),
            'value' => '',
        ),
        'descriptions' => array(
            'id' => 'descriptions',
            'label' => 'Deskripsi',
            'type' => 'textarea',
            'placeholder' => 'Deskripsi menu',
            'required' => 'required',
            'value' => '',
        ),
        'submit' => array(
            'id' => 'submit',
            'type' => 'submit',
            'label' => 'Simpan',
        ),
    );

    protected function setOptionDataForm($where = array())
    {
        $parentMenu = $this->dropdown('id', 'name');
        $parentMenu[0] = 'Menu Utama';
        ksort($parentMenu);
        $this->form['parent_id']['options'] = $parentMenu;
        // $this->form['icon']['options'] = $this->listICon();
    }
}
