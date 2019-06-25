<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends MY_Controller {
    public $title = 'Data User';
//    protected $withPagination = FALSE;
    function __construct(){
        parent::__construct();
        $this->load->model('user_model','users');
        $this->load->model('role_model','roles');
        $this->model = $this->users;
        
    }

    public function index($referenceId = null){
        $this->model->setWithRole(TRUE);
        parent::index($referenceId);
    }
    /** override function parent class */
    public function save(){
        $data = $this->input->post('data');
		$where = $this->input->post('key');
		if(empty($where)){
            $generatePass = SecurityManager::encode($data['password']);
            $data['password_salt'] = $generatePass['password_salt'];
            $data['password'] = $generatePass['password'];
			$where = [$this->model->getKeyName() => NULL];
        }
        
        $userSaved = $this->model->saveData($where,$data);
        
        $this->result['status'] = 1;
        $this->result['message'] = 'Sudah disimpan';
        
        $this->display_json($this->result);
    }

    protected function setBtnAdd($key = null)
    {
        return generateAddButton('Tambah', ['onclick' => 'App.addRecord(this)', 'data-url' => site_url($this->pathView.'/add')])
               . generateAddButton('Import', ['onclick' => 'App.addRecord(this)', 'data-url' => site_url('master/importUser/')])
        ;
    }

    public function setTableConfig()
    {
        $this->table->key_record = array($this->model->getKeyName());
        $this->table->extra_columns = [
            'btnEdit' => [
                'data' => generatePrimaryButton('<i class="fa fa-pencil"></i>', ['onclick' => 'App.editRecord(this)', 'data-url' => site_url($this->pathView.'/'.$this->actionMethodEdit)])
                    .' '.generateDangerButton('<i class="fa fa-refresh"></i>', ['onclick' => 'App.updateRecord(this)', 'data-urlmessage' => site_url($this->pathView.'/resetMessage'), 'data-url' => site_url($this->pathView.'/resetPassword')])
                    
                ]
            ];
    }

    /** hapus dulu tabel yang berelasi */
    public function delete(){
        $where = $this->input->post('key');
        parent::delete();
    }

    public function setTableData(){
        $result = [];
        $tmp = $this->model->fields(['id','username','email','role_id','created_at','status'])->with('role')->as_array()->get_all();
        $heading = ['id','username','email','role','created_at','status'];
        
        foreach($tmp as $_tr){
            $_tmp = [];
            foreach($heading as $_h){
                $_tmp[$_h] = $_tr[$_h];
                if($_h == 'role'){
                    if(!empty($_tr[$_h])){
                        $_tmp[$_h] = $_tr[$_h]->name;
                    }
                }
            }
            array_push($result,$_tmp);
        }
        
        return $result;
    }

    public function searchPaging()
    {
        $this->model->setColumnTableData(['id','ref_nik','role_id']);
        $q = $this->input->post('q');
        $currentPage = $this->input->post('page');
        $limit = $this->model->getPerpage();
        $offset = !empty($currentPage) ? ($currentPage - 1 * $limit)   : 0;
        $where = 'status  = \'A\'';    
        if(is_numeric($q)){
            $where .= ' and ref_nik like \'%'.$q.'%\'';
        }else{
            $where .= ' and name like \'%'.$q.'%\'';
        }
        
        $tmp = $this->paginate($limit, $offset,$where);
        $result = [];
        if(!empty($tmp['data'])){
            $this->result['status'] = 1;
            //$this->result['items'] = $tmp['data'];
            $listUser = array_column($tmp['data'],'ref_nik');
            $usql = convertArr($tmp['data'],'ref_nik');
            $this->load->model('hris/User_hris_model','uhm');
            $uhris = $this->uhm->fields(['NIK','NAMABP','NAMAJABATAN'])->as_array()->get_many_by(['NIK' => $listUser]);
            if(!empty($uhris)){
                foreach($uhris as $us){
                    $nik = $us['NIK'];
                    if(isset($usql[$nik])){
                        array_push($result,['id' => $usql[$nik]['id'].'_'.$usql[$nik]['role_id'],'text' => $us['NAMABP'].' - '.$us['NAMAJABATAN']]);
                    }
                }
            }
            $this->result['items'] = $result;

        }
        
        $this->result['pagination'] = !empty($tmp['data']) ? true : false;
        $this->display_json($this->result);
    }

    protected function defaultFilterPage()
    {
        $filterNik = isset($this->filters['ref_nik']) ? $this->filters['ref_nik'] : null;
        $filterNama = isset($this->filters['name']) ? $this->filters['name'] : null;
        $filterRole = isset($this->filters['role_id']) ? $this->filters['role_id'] : null;
        $listRole = $this->roles->dropdown('id','name');
        if(!is_array($filterRole)){
            $filterRole = [$filterRole];
        }
        $this->load->model('hris/lokasi_model', 'lm');
        $lokasi = $this->lm->dropdown('KODELOKASI', 'NAMALOKASI');
        $form_options = [
            'start_date' => [
                'id' => 'lokasi',
                'label' => 'Filter',
                'type' => 'combine',
                'elements' => [
                    [
                        'id' => 'ref_nik',
                        'type' => 'input',
                        'value' => $filterNik,
                        'placeholder' => 'Ketik NIK',
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-2">',
                        ],
                    ],
                    [
                        'id' => 'name',
                        'type' => 'input',
                        'value' => $filterNama,
                        'placeholder' => 'Ketik Nama',
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-2">',
                        ],
                    ],
                    [
                        'id' => 'role_id',
                        'type' => 'dropdown',
                        'multiple' => 'multiple',
                        'class' => 'select2_multiple',
                        'value' => $filterRole,
                        'placeholder' => 'Pilih role',
                        'readonly' => 'readonly',
                        'options' => ['Pilih Role'] + $listRole,
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-3">',
                        ],
                    ],
                    [
                        'id' => 'search',
                        'type' => 'submit',
                        'label' => html_entity_decode('&#xf002;'),
                        'class' => 'fa-input',
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-1">',
                        ],
                    ],
                ],
            ],
        ];
        $this->form_builder->init([
            'default_control_label_class' => 'col-sm-1 control-label',
            'default_form_control_class' => 'col-sm-11',
        ]);
        $dataForm = array(
            'form_header' => array('data-actiontype' => 'search', 'action' => site_url($this->pathView.'/search')),
            'form_options' => $form_options,
        );
        $this->filterPage = '<div class="col-md-12">'.$this->load->view('layout/form', $dataForm, true).'</div>';
    }

    public function resetMessage()
    {
        $where = $this->input->post('key');
        $data = $this->model->get_by($where);
        $this->load->model('hris/User_hris_model','uhm');
        $uhris = $this->uhm->fields(['NIK','NAMABP','TGLLAHIR'])->as_array()->get_by(['NIK' => $data->ref_nik]);
        echo 'Apakah anda yakin akan mereset password user <strong>('.$uhris['NAMABP'].' - ' .$uhris['NIK'].')</strong> ini dengan password <strong>'.$uhris['TGLLAHIR'].'</strong> ?';
    }

    public function resetPassword()
    {
        $where = $this->input->post('key');
        $dataUser = $this->model->get_by($where);
        $this->load->model('hris/User_hris_model','uhm');
        $uhris = $this->uhm->fields(['NIK','NAMABP','TGLLAHIR'])->as_array()->get_by(['NIK' => $dataUser->ref_nik]);
        $generatePass = SecurityManager::encode($uhris['TGLLAHIR']);
        $data['password_salt'] = $generatePass['password_salt'];
        $data['password'] = $generatePass['password'];
        if ($this->model->update_by($where,$data)) {
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil diupdate';
        }

        $this->display_json($this->result);
    }

    public function searchTimetableAjax()
    {
        $where = $this->input->post('data');
        $tanggalabsensi = $where['tanggalabsensi'];
        $nik = $where['ref_nik'];
        unset($where['tanggalabsensi']);
        $fields = $this->input->post('fields');
        $this->db->join('jadwal_detail','jadwal_detail.nik=users.ref_nik and jadwal_detail.tanggalabsensi = \''.$tanggalabsensi.'\'');
        $this->db->join('timetables','timetables.id=jadwal_detail.timetables_id');
        $tmp = $this->model->fields(['ref_nik','jadwal_detail.timetables_id as timestables','convert(VARCHAR(5),jam_masuk,114) as jam_masuk','convert(VARCHAR(5),jam_pulang,114) as jam_pulang'])->as_array()->get_by($where);
        $this->load->model('hris/User_hris_model','uhm');
        $jabatan = $this->uhm->as_array()->fields(['NAMAJABATAN'])->get_by(['NIK' => $nik]);
         if(!empty($tmp)){
            $this->result['status'] = 1;
            $this->result['content'] = ['timestables' => $tmp, 'jabatan' => $jabatan['NAMAJABATAN']];
         }
         
         $this->display_json($this->result);

    }
}
