<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Organisasi extends MY_Controller {
    public $title = 'Data User';
    function __construct(){
        parent::__construct();
        $this->load->model('hris/organisasi_model','org');
        $this->model = $this->org;
    }

    public function searchPaging()
    {
        $this->model->setColumnTableData(['KODEORG as id','NAMAORG as text']);
        $q = $this->input->post('q');
        $currentPage = $this->input->post('page');
        $limit = $this->model->getPerpage();
        $offset = !empty($currentPage) ? ($currentPage - 1 * $limit)   : 0;
        $where = ' NAMAORG like \'%'.strtoupper($q).'%\'';
        
        $tmp = $this->paginate($limit, $offset,$where);
        $result = [];
        if(!empty($tmp['data'])){
            $this->result['status'] = 1;
            $this->result['items'] = $tmp['data'];
        }
        
        $this->result['pagination'] = !empty($tmp['data']) ? true : false;
        $this->display_json($this->result);
    }
}
