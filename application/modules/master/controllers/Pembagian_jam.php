<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pembagian_jam extends MY_Controller
{
    public $title = 'Pembagian Jam Masuk';
    //protected $showButtonRight = false;
    protected $showButtonLeft = false;
    private $filterDetails = [];
    /*protected $viewPage = [
        'index' => 'report/default',
    ];*/

    public function __construct()
    {
        parent::__construct();
        $this->load->model('working_times_model','wtm');
        $this->model = $this->wtm;
        $this->includes = array('js' => array(
				'assets/js/master/working_times.js',
			),
        );
    }

	public function setIndexData()
    {
		$this->title = 'Pendaftaran '.$this->title;
        $dataModel = $this->setTableData();
		foreach($dataModel as $keyModel=>$valModel) {
			$dataModel[$keyModel]['batas_awal_jam_masuk'] = substr($dataModel[$keyModel]['batas_awal_jam_masuk'],0,5);
			$dataModel[$keyModel]['batas_akhir_jam_masuk'] = substr($dataModel[$keyModel]['batas_akhir_jam_masuk'],0,5);
			$dataModel[$keyModel]['status'] = ($dataModel[$keyModel]['status']=='A') ? 'Aktif' : 'Tidak Aktif';
		}
        $templateTable = $this->config->item('table');
        $key = $this->input->post('key');
        $this->setTableConfig();
        $this->table->set_heading($this->model->getHeading());
        $this->table->set_template($templateTable);

        return ['table' => $this->table->generate($dataModel), 'title' => $this->title, 'btnAdd' => $this->setBtnAdd($key)];
    }

    public function setTableConfig()
    {
        $this->table->extra_columns = ['detail' => [
				'data' => '<i class="btn fa fa-file-text" data-url="'.site_url($this->pathView.'/'.$this->actionMethodEdit).'" onclick="App.detailRecord(this)"></i>',
			],
		];
		$this->table->key_record = array($this->model->getKeyName());
		$this->table->withNumber = false;
    }
	
	protected function _formEdit($data = array(), $where = array())
    {
        $this->linkBack = $this->pathView.'/'.$this->actionMethodIndex;
		$this->setButtonRight(); // buat tombol kembali
		if (!empty($data)) {
			$data->batas_awal_jam_masuk = substr($data->batas_awal_jam_masuk,0,5);
			$data->batas_akhir_jam_masuk = substr($data->batas_akhir_jam_masuk,0,5);
			$this->title = 'Perubahan '.$this->title;
			$this->model->setPropertiesForm('nama', 'disabled', 'disabled');
			$this->model->setPropertiesForm('jam_kerja', 'disabled', 'disabled');
			$this->model->setPropertiesForm('batas_awal_jam_masuk', 'disabled', 'disabled');
			$this->model->setPropertiesForm('batas_akhir_jam_masuk', 'disabled', 'disabled');
		} else {
			$this->title = 'Pendaftaran '.$this->title;
			$this->model->setPropertiesForm('status', 'disabled', 'disabled');
		}
		
        $form_options = $this->model->getFormOptions($data, $where);
        $dataForm = array(
            'form_header' => array('data-actiontype' => 'save', 'data-nexturl' => site_url($this->pathView.'/'.$this->actionMethodIndex), 'action' => site_url($this->pathView.'/'.$this->actionMethodSave)),
            'form_options' => $form_options,
        );
        $this->loadView('layout/form', $dataForm);
    }

    private function getCaptionTable()
    {
        return $this->captionTable;
    }

    private function setCaptionTable($captionTable)
    {
        $this->captionTable = $captionTable;
    }

    /**
     * Get the value of filterDetails.
     */
    public function getFilterDetails()
    {
        return $this->filterDetails;
    }

    /**
     * Set the value of filterDetails.
     *
     * @return self
     */
    public function setFilterDetails($filterDetails)
    {
        $this->filterDetails = $filterDetails;
    }
    
    public function save()
    {
		$user = $_SESSION['idUser'];
        $data = $this->input->post('data');
        $where = $this->input->post('key');
        if (empty($where)) {
            $where = [$this->model->getKeyName() => null];
        }
		if (empty($where['id'])) {
			$id_arr = explode(" ", $data['nama']);
			$id_working_times = $id_arr[0][0].(isset($id_arr[1][0]) && !empty($id_arr[1][0]) ? $id_arr[1][0] : '');
			$data['id'] = $this->model->getIDWorking($id_working_times); //generate ID untuk melakukan insert data, gunakan function untuk mendapatkan data generate
			$data['status'] = 'A';
			$data['created_at'] = sekarang()->format('Y-m-d');
			$data['created_by'] = $user;
		} else {
			$data['updated_at'] = sekarang()->format('Y-m-d');
			$data['updated_by'] = $user;
		}
		$saveData = $this->model->saveData($where, $data);
        if ($saveData || empty($saveData)) {
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil disimpan';
        }

        $this->display_json($this->result);
    }
}
