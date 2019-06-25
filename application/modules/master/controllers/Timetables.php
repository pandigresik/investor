<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timetables extends MY_Controller
{
    public $title = 'Timetables';
    protected $showButtonLeft = false;
    private $filterDetails = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('timetables_model','tm');
        $this->model = $this->tm;
        $this->includes = array('js' => array(
				'assets/js/master/timetables.js',
			),
        );
    }

	public function setIndexData()
    {
		$this->title = 'Pengaturan '.$this->title;
        $dataModel = $this->setTableData();
		foreach($dataModel as $keyModel=>$valModel) {
			$jml_lokasi = empty($dataModel[$keyModel]['lokasi_berlaku']) ? 0 : count(explode(",", $dataModel[$keyModel]['lokasi_berlaku']));
			$dataModel[$keyModel]['jam_masuk'] = substr($dataModel[$keyModel]['jam_masuk'],0,5);
			$dataModel[$keyModel]['jam_pulang'] = substr($dataModel[$keyModel]['jam_pulang'],0,5);
			$dataModel[$keyModel]['lokasi_berlaku'] = $jml_lokasi;
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
			$data->kode = $data->id;
			$data->jam_pulang = substr($data->jam_pulang,0,5);
			$data->tgl_berlaku = tglIndonesia($data->tgl_berlaku);
			$data->tgl_berakhir = isset($data->tgl_berakhir) && !empty($data->tgl_berakhir) ? $data->tgl_berakhir : '-';
			$data->lokasi_berlaku = explode(",", $data->lokasi_berlaku);
			$this->title = 'Perubahan '.$this->title;
			$this->model->setPropertiesForm('kode', 'disabled', 'disabled');
			$this->model->setPropertiesForm('working_times_id', 'disabled', 'disabled');
			$this->model->setPropertiesForm('jam_masuk', 'disabled', 'disabled');
			$this->model->setPropertiesForm('jam_kerja', 'disabled', 'disabled');
			$this->model->setPropertiesForm('jam_pulang', 'disabled', 'disabled');
			$this->model->setPropertiesForm('tgl_berlaku', 'disabled', 'disabled');
			$this->model->setPropertiesForm('tgl_berakhir', 'disabled', 'disabled');
		} else {
			$this->title = 'Pendaftaran '.$this->title;
			$this->model->setPropertiesForm('kode', 'type', 'hidden');
			$this->model->setPropertiesForm('tgl_berakhir', 'disabled', 'disabled');
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
		unset($data['kode']);
		unset($where['kode']);
        if (empty($where)) {
            $where = [$this->model->getKeyName() => null];
        }
		$data['lokasi_berlaku'] = implode(",", $data['lokasi_berlaku']);
		if (empty($where['id'])) {
			$data['id'] = $this->model->getIDTimetables($data['working_times_id']); //generate ID untuk melakukan insert data, gunakan function untuk mendapatkan data generate
			$data['status'] = 'A';
			$data['created_at'] = sekarang()->format('Y-m-d');
			$data['created_by'] = $user;
		} else {
			$data_awal = $this->model->getEditData(array('id'=>$where['id']),true);
			$status_awal = $data_awal['status'];
			if ($status_awal!==$data['status']) {
				if ($data['status']=='N') {
					$data['tgl_berakhir'] = tglSetelah('1')->format('Y-m-d');
				} else if ($data['status']=='A') {
					$data['tgl_berlaku'] = tglSetelah('1')->format('Y-m-d');
					$data['tgl_berakhir'] = null;
				}
			}
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
