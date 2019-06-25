<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class ImportUser extends MY_Controller
{
    public $title = 'Data User HRIS';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model', 'um');
        $this->load->model('hris/user_hris_model', 'uhris');
        $this->model = $this->uhris;
        $this->includes = array(
            'js' => array(
                'assets/js/master/importUser.js',
            ),
        );
    }

    public function index($referenceId = null)
    {
        $buttonAdd = $this->setBtnAdd();
        $buttonFilter = generateFilterButton('Filter');
        $this->setButtonRight($buttonFilter.'&nbsp;'.$buttonAdd);
        $this->loadView($this->viewPage['index'], $this->setIndexData());
    }

    /** menambahkan fungsi paginate untuk generate paging */
    public function paginate($limit, $offset = 0, $where = null)
    {
        $_result = [];
        if (empty($this->filters)) {
            $_result['data'] = [];
            $_result['links'] = '';

            return $_result;
        }

        $config = $this->config->item('pagination');
        $config['base_url'] = $this->pathView.'/search';
        $config['total_rows'] = empty($where) ? $this->model->count_all() : $this->model->count_by($where);
        $config['per_page'] = $limit;

        $this->pagination->initialize($config);
        $_result['data'] = empty($where) ? $this->model->columnTable()->as_array()->limit($config['per_page'], $offset)->get_all() : $this->model->columnTable()->as_array()->limit($config['per_page'], $offset)->get_many_by($where);
        $_result['links'] = $this->pagination->create_links();

        return $_result;
    }

    public function setTableConfig()
    {
        $this->table->key_record = array($this->model->getKeyName());
        $this->table->setHiddenField(['RNUM']);
        $this->table->extra_columns = [
            'checkBox' => [
                'data' => '<input type="checkbox" />',
            ],
        ];
    }

    protected function setBtnAdd($key = null)
    {
        return generateAddButton('Import All', ['onclick' => 'ImportUser.importAll(this)', 'data-url' => site_url($this->pathView.'/importAll')])
        .generateAddButton('Import Terpilih', ['onclick' => 'ImportUser.importChecked(this)', 'data-url' => site_url($this->pathView.'/importTerpilih')])
        ;
    }

    protected function defaultFilterPage()
    {
        $filterNik = isset($this->filters['NIK']) ? $this->filters['NIK'] : null;
        $filterNama = isset($this->filters['NAMABP']) ? $this->filters['NAMABP'] : null;
        $filterLokasi = isset($this->filters['KODELOKASI']) ? $this->filters['KODELOKASI'] : null;
        $orgOptions = [''];
        $filterOrg = isset($this->filters['KODEORG']) ? $this->filters['KODEORG'] : null;
        if (!empty($filterOrg)) {
            if (!is_array($filterOrg)) {
                $filterOrg = [$filterOrg];
            }
            $this->load->model('hris/Organisasi_model', 'om');
            $orgOptions = $this->om->where('KODEORG in (\''.implode('\',\'', $filterOrg).'\')')->dropdown('KODEORG', 'NAMAORG');
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
                        'id' => 'KODELOKASI',
                        'type' => 'dropdown',
                        'class' => 'select2_single',
                        'value' => $filterLokasi,
                        'placeholder' => 'Pilih lokasi',
                        'readonly' => 'readonly',
                        'options' => array_merge(['Pilih Lokasi'], $lokasi),
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-3">',
                        ],
                    ],
                    [
                        'label' => 'Org',
                        'id' => 'KODEORG',
                        'multiple' => 'multiple',
                        'data-url' => 'master/organisasi/searchPaging',
                        'class' => 'select2_ajax',
                        'placeholder' => 'Pilih Organisasi',
                        'type' => 'dropdown',
                        'value' => $filterOrg,
                        'options' => $orgOptions,
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-3">',
                        ],
                    ],
                    [
                        'id' => 'NIK',
                        'type' => 'input',
                        'value' => $filterNik,
                        'placeholder' => 'Ketik NIK',
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-2">',
                        ],
                    ],
                    [
                        'id' => 'NAMABP',
                        'type' => 'input',
                        'value' => $filterNama,
                        'placeholder' => 'Ketik Nama',
                        'input_addons' => [
                            'pre_html' => '<div class="col-md-2">',
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

    public function importTerpilih()
    {
        $data = $this->input->post('data');   
        $userHris = $this->model->as_array()->fields(['NIK', 'NAMABP', 'KODELEVELJABATAN', 'TGLLAHIR'])->get_many_by(['NIK' => $data]);
        if (!empty($userHris)) {
            $this->createUser($userHris);
            $this->result['status'] = 1;
            $this->result['message'] = count($userHris).' data berhasil disimpan';
        }
        $this->display_json($this->result);
    }

    public function importAll()
    {
        $filterWhere = $this->input->post('data');   
        $userHris = $this->model->as_array()->fields(['NIK', 'NAMABP', 'KODELEVELJABATAN', 'TGLLAHIR'])->get_many_by($filterWhere);
        if (!empty($userHris)) {
            $this->createUser($userHris);
            $this->result['status'] = 1;
            $this->result['message'] = count($userHris).' data berhasil disimpan';
        }
        $this->display_json($this->result);
    }

    private function createUser($userHris){
        $this->load->model('Role_model', 'role');
        $rolesId = $this->role->dropdown('kodeleveljabatan', 'id');
        foreach ($userHris as $us) {
            $generatePass = SecurityManager::encode($us['TGLLAHIR']);
            $dataUser['password_salt'] = $generatePass['password_salt'];
            $dataUser['password'] = $generatePass['password'];
            $dataUser['username'] = $us['NIK'];
            $dataUser['ref_nik'] = $us['NIK'];
            $dataUser['name'] = $us['NAMABP'];
            $dataUser['role_id'] = isset($rolesId[$us['KODELEVELJABATAN']]) ? $rolesId[$us['KODELEVELJABATAN']] : null;
            $userSaved = $this->um->createUpdateUser(['ref_nik' => $us['NIK']], $dataUser);
        }
    }
}
