<?php

class Role_model extends MY_Model
{   
    public $has_many = array('role_menu' => array('primary_key' => 'roles_id'));
    protected $columnTableData = ['id', 'name', 'status', 'created_at'];
    protected $headerTableData = [
        [['data' => 'Id'], ['data' => 'Nama Peran'], ['data' => 'Status'], ['data' => 'Dibuat Pada'], ['data' => 'Aksi']],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    protected $form = array(
        'name' => array(
            'id' => 'name',
            'label' => 'Nama Peran',
            'placeholder' => 'misal editor',
            'required' => 'required',
            'value' => '',
        ),
        'kodeleveljabatan' => array(
            'id' => 'kodeleveljabatan',
            'label' => 'Kode level Jabatan',
            'placeholder' => 'Kode level Jabatan HRIS',
            'value' => '',
        ),
        'status' => array(
            'id' => 'status',
            'label' => 'Status',
            'required' => 'required',
            'type' => 'dropdown',
            'options' => array(
                'A' => 'Aktif',
                'I' => 'Non Aktif',
            ),
            'value' => '',
        ),
        'submit' => array(
            'id' => 'submit',
            'type' => 'submit',
            'label' => 'Simpan',
        ),
    );
}

