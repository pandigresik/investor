<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/** Generate by crud generator model pada 2019-06-03 11:32:28
 *   method index, add, edit, delete, detail dapat dioverride jika ingin melakukan custom pada controller tertentu
 *   Author afandi.
 */
class Delegasi extends MY_Controller
{
    public $title = 'Data Delegasi';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Delegasi_model', 'delegasi');
        $this->model = $this->delegasi;
    }

    public function add($referenceId = null)
    {
        $this->includes = array(
            'js' => array(
                'assets/js/master/delegasi.js',
            ),
        );

        parent::add($referenceId);
    }

    public function edit()
    {
        $this->includes = array(
            'js' => array(
                'assets/js/master/delegasi.js',
            ),
        );
        parent::edit();
    }

    public function setTableConfig()
    {
        $this->table->key_record = array($this->model->getKeyName());
        $this->table->setHiddenField(['id', 'start_date', 'end_date','status_asli']);
        $this->table->extra_columns = [
            'btnEdit' => [
                    'data' => generatePrimaryButton('<i class="fa fa-pencil"></i>', ['onclick' => 'App.editRecord(this)', 'data-url' => site_url($this->pathView.'/'.$this->actionMethodEdit)]),
                ],
        ];
    }

    /** override function parent class */
    public function save(){
        $this->load->model('Delegasi_detail_model','ddm');
        $data = $this->input->post('data');
        
        list($id_delegasi,$_role) = explode('_',$data['id_delegasi']);
        $data['id_delegasi'] = $id_delegasi;
        $data['count_otorisasi'] = count($data['otorisasi']);
        $detailOtorisasi = $data['otorisasi'];
        unset($data['otorisasi']);
        $where = $this->input->post('key');
        
		if(!empty($where)){
            $this->ddm->delete_by(['delegasi_id' => $where['id']]);
            unset($data['id_penyetuju']);
            unset($data['jabatan_penyetuju']);    
            $this->load->model('User_model','um');
            $users = $this->um->as_array()->fields(['id','name','role_id'])->get_by(['id' => $id_delegasi]);
            $data['info_delegasi'] = $users['name'];
        }else{
            $where = ['id' => NULL];
            list($id_penyetuju,$_role) = explode('_',$data['id_penyetuju']);
            $data['id_penyetuju'] = $id_penyetuju;
            $this->load->model('User_model','um');
            $users = convertArr($this->um->as_array()->fields(['id','name','role_id'])->get_many_by(['id' => [$id_penyetuju,$id_delegasi]]),'id');

            $data['info_penyetuju'] = $users[$id_penyetuju]['name'];
            $data['info_delegasi'] = $users[$id_delegasi]['name'];
        }
        $savedId = $this->model->saveData($where,$data);
        /** simpan ke  */
        if($savedId){
            $idDelegasi = !empty($where['id']) ? $where['id'] : $savedId;
            foreach($detailOtorisasi as $do){
                $this->ddm->insert(['delegasi_id' => $idDelegasi, 'menu_id' => $do]);
            }
        }
        $this->result['status'] = 1;
        $this->result['message'] = 'Sudah disimpan';
        
        $this->display_json($this->result);
    }
}
