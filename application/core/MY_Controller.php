<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends MX_Controller
{
    protected $result = array(
            'status' => 0,
            'message' => '',
            'content' => '',
    );
    /**
     * Common data.
     */
    public $user;
    public $includes = array('css' => array(), 'js' => array());
    public $title = '';
    protected $data = array();
    protected $model;
    protected $pathView;
    protected $linkRoute;
    protected $linkClass;
    protected $linkBack;
    protected $btnRight;
    protected $btnLeft;
    protected $showButtonRight = true;
    protected $showButtonLeft = true;
    protected $actionMethodIndex = 'index';
    protected $actionMethodAdd = 'add';
    protected $actionMethodEdit = 'edit';
    protected $actionMethodDelete = 'delete';
    protected $actionMethodSave = 'save';
    protected $actionMethodDetail = 'detail';
    protected $additionalInfo;
    protected $filters = [];
    protected $filterPage = null;
    protected $captionTable = null;
    protected $withPagination = true;
    protected $viewPage = [
        'index' => 'master/default',
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $CI = &get_instance();
        $module = $CI->router->fetch_module();
        $class = $CI->router->fetch_class();
        $method = $CI->router->fetch_method();
        $this->linkRoute = $module.'/'.$class.'/'.$method;
        $this->linkClass = $module.'/'.$class;
        $this->pathView = $module.'/'.$class;

        $isLogin = $this->session->userdata('isLogin');
        
        if (!$isLogin) {
            if ($CI->input->is_ajax_request()) {
                $CI->output->set_status_header(401, 'Session Has Expired');
                die();
            } else {
                redirect('user/user/login?#'.$this->linkRoute);
            }
        }

        //$this->getAccess();
        if (ENVIRONMENT == 'development') {
            if (!$this->input->is_ajax_request()) {
                $this->output->enable_profiler(true);
            }
        }

        //log_message('error',json_encode($this->session->userdata()));
    }

    public function getAccess()
    {
        $hasPermission = $this->getAccessPermission($this->linkRoute);

        if (!$hasPermission) {
            $message = 'Anda tidak mempunyai hak untuk mengakses url '.$this->linkRoute;
            if ($this->input->is_ajax_request()) {
                $this->output->set_status_header(403, $message);
                die();
            } else {
                die($message);
            }
        }
    }

    /* $route adalah url yang akan diakses misal pobb/probe/kendaraanmasuk/index */
    public function getAccessPermission($route = '')
    {
        $restrictroute = unserialize($this->session->userdata('restrictroute'));
        $permission = unserialize($this->session->userdata('permission'));

        $is_restrict = in_array($route, $restrictroute) ? 1 : 0;
        if ($is_restrict) {
            return in_array($route, $permission);
        }

        return true;
    }

    public function do_upload($file, $config = array())
    {
        if (empty($config)) {
            $config = array(
                'upload_path' => 'uploads/',
                'allowed_types' => 'doc|pdf|docx',
                'max_size' => 10240,
            );
        }
        $result = array();
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($file)) {
            $result['status'] = 0;
            $result['data'] = array('error' => $this->upload->display_errors());
        } else {
            $result['status'] = 1;
            $result['data'] = array('upload_data' => $this->upload->data());
        }

        return $result;
    }

    public function check_attachment($files)
    {
        /* jika ada attachment, maka cek dulu apakah pernah diupload atau tidak */
        $config_upload = $this->config->item('upload_param');
        $max_memo_length = 120;

        $filename = 'uploads/'.ubahNama($files['name']);
        if (!file_exists($filename)) {
            if (strlen(ubahNama($files['name'])) > $max_memo_length) {
                $this->result['message'] = 'Nama file maximal '.$max_memo_length.' karakter';
                display_json($this->result);
                exit;
            } else {
                $upload = $this->do_upload('attachment');
                if (!$upload['status']) {
                    $this->result['message'] = $upload['data'];
                    display_json($this->result);
                    exit;
                }
            }
        }
    }

    public function loadView($view, $dataView = array())
    {
        $contentView = $this->load->view($view, $dataView, true);
        //$this->setButtonRight();
        //$this->setButtonLeft();
        $btnRight = $this->getButtonRight();
        $btnLeft = $this->getButtonLeft();
        $this->load->view('layout/main', array('contentView' => $contentView, 'title' => $this->title, 'includes' => $this->includes, 'btnLeft' => $btnLeft, 'btnRight' => $btnRight, 'additionalInfo' => $this->getAdditionalInfo()));
    }

    public function setButtonRight($btn = null)
    {
        $this->btnRight = !empty($btn) ? $btn : generateBackButton('Kembali', ['onclick' => 'App.backUrl(this)']);
    }

    public function getButtonRight()
    {
        if (!$this->getShowButtonRight()) {
            return null;
        }

        return $this->btnRight;
    }

    public function setButtonLeft($btn = null)
    {
        $this->btnLeft = $btn;
    }

    public function getButtonLeft()
    {
        if (!$this->getShowButtonLeft()) {
            return null;
        }

        return $this->btnLeft;
    }

    /**
     * Get the value of showButtonRight.
     */
    public function getShowButtonRight()
    {
        return $this->showButtonRight;
    }

    /**
     * Set the value of showButtonRight.
     *
     * @return self
     */
    public function setShowButtonRight($showButtonRight)
    {
        $this->showButtonRight = $showButtonRight;
    }

    /**
     * Get the value of showButtonRight.
     */
    public function getShowButtonLeft()
    {
        return $this->showButtonLeft;
    }

    /**
     * Set the value of showButtonRight.
     *
     * @return self
     */
    public function setShowButtonLeft($showButtonLeft)
    {
        $this->showButtonLeft = $showButtonLeft;
    }

    public function display_json($data)
    {
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
    }

    /** untuk CRUD master */
    public function index($referenceId = null)
    {
        $buttonAdd = $this->setBtnAdd();
        $buttonFilter = generateFilterButton('Filter');
        $this->setButtonRight($buttonFilter.'&nbsp;'.$buttonAdd);
        $this->loadView($this->viewPage['index'], $this->setIndexData());
    }

    protected function setIndexData()
    {
        $key = $this->input->post('key');
        $divTable = $this->setDivTable();

        return ['table' => $divTable, 'title' => $this->title];
    }

    protected function setBtnAdd($key = null)
    {
        return generateAddButton('Tambah', ['onclick' => 'App.addRecord(this)', 'data-url' => site_url($this->pathView.'/add')]);
    }

    public function setDivTable()
    {
        $this->setTableConfig();
        $links = '';
        if ($this->getWithPagination()) {
            $limit = $this->getFilters('limit');
            $offset = $this->getFilters('offset');
            if (is_null($limit)) {
                $limit = $this->model->getPerpage();
            }

            if (is_null($offset)) {
                $offset = 0;
            }

            $this->removeFilters('limit');
            $this->removeFilters('offset');
            $where = $this->getFilters();

            $tmp = $this->paginate($limit, $offset, $where);
            $dataModel = $tmp['data'];
            $this->table->setStartNumber(($offset + 1));
            $links = '<div class="col-md-12"><div id="divPagination" class="pull-right">'.$tmp['links'].'</div></div>';
        } else {
            $dataModel = $this->setTableData();
        }

        $templateTable = $this->config->item('table');
        $this->table->set_heading($this->model->getHeading());
        $this->table->set_template($templateTable);

        $table = $this->table->generate($dataModel);
        $filterPage = $this->getFilterPage();

        return
        '<div class="row">
            <div class="col-md-12" id="divfilterpage">'.$filterPage.'</div>
            <div class="col-md-12">'.$table.' '.$links.'</div>
        </div>';
    }

    public function setTableData()
    {
        return $this->model->columnTable()->as_array()->get_all();
    }

    public function setTableConfig()
    {
        $this->table->key_record = array($this->model->getKeyName());
        $this->table->extra_columns = [
            'btnEdit' => [
                'data' => generatePrimaryButton('<i class="fa fa-pencil"></i>', ['onclick' => 'App.editRecord(this)', 'data-url' => site_url($this->pathView.'/'.$this->actionMethodEdit)]).' '.generateDangerButton('<i class="fa fa-recycle"></i>', ['onclick' => 'App.deleteRecord(this)', 'data-urlmessage' => site_url($this->pathView.'/deleteMessage'), 'data-nexturl' => site_url($this->pathView.'/'.$this->actionMethodIndex), 'data-url' => site_url($this->pathView.'/'.$this->actionMethodDelete)]),
                ],
            ];
    }

    public function add($referenceId = null)
    {
        $this->_formEdit();
    }

    public function edit()
    {
        $where = $this->input->post('key');
        $data = $this->model->getEditData($where, false);
        $this->_formEdit($data, $where);
    }

    protected function _formEdit($data = array(), $where = array())
    {
        $this->setButtonRight();
        $form_options = $this->model->getFormOptions($data, $where);
        $dataForm = array(
            'form_header' => array('data-actiontype' => 'save', 'data-nexturl' => site_url($this->pathView.'/'.$this->actionMethodIndex), 'action' => site_url($this->pathView.'/'.$this->actionMethodSave)),
            'form_options' => $form_options,
        );
        $this->loadView('layout/form', $dataForm);
    }

    public function save()
    {
        $data = $this->input->post('data');
        $where = $this->input->post('key');
        if (empty($where)) {
            $where = [$this->model->getKeyName() => null];
        }
        $saved = $this->model->saveData($where, $data);
        if ($saved) {
            $this->result['status'] = 1;
            $this->result['message'] = 'Sudah disimpan';
        }

        $this->display_json($this->result);
    }

    public function detail()
    {
    }

    public function delete()
    {
        $where = $this->input->post('key');
        if ($this->model->delete_by($where)) {
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil dihapus';
        }

        $this->display_json($this->result);
    }

    public function deleteMessage()
    {
        $where = $this->input->post('key');
        $data = $this->model->get_by($where);
        echo 'Apakah anda yakin akan menghapus data mesin finger ini '.json_encode($data).' ?';
    }

    public function toPdf($config = [], $html, $name = null)
    {
        ob_start();
        $this->load->library('Pdf');
        $orientation = isset($config['orientation']) ? $config['orientation'] : 'P';
        $page = isset($config['page']) ? $config['page'] : 'A4';
        $pdf = new Pdf($orientation, PDF_UNIT, $page, true, 'UTF-8', false);

        //$pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($name, 'I');
        ob_end_flush();
    }

    protected function saveAttachment()
    {
        $image = $this->input->post('attachment');
        $path_baru = 'assets/uploads';
        list($type, $data) = explode(';', $image);
        list(, $dataImage) = explode(',', $data);
        $extension = explode('/', $type);
        $path_baru_photo = $path_baru.'/'.date('YmdHis').'.'.end($extension);

        if (file_put_contents($path_baru_photo, base64_decode($dataImage))) {
            return $path_baru_photo;
        } else {
            return null;
        }
    }

    protected function getTeritoriUser()
    {
        return unserialize($this->session->userdata('teritori'));
    }

    protected function getIdUser()
    {
        return $this->session->userdata('idUser');
    }

    protected function getGenderUser()
    {
        $dataUser = unserialize($this->session->userdata('dataUser'));
        $gender = strtolower($dataUser['JENISKELAMIN']);
        $jenisKelamin = $this->config->item('jenisKelamin');

        return isset($jenisKelamin[$gender]) ? $jenisKelamin[$gender] : 'S';
    }

    protected function getDataUser()
    {
        $dataUser = unserialize($this->session->userdata('dataUser'));

        return $dataUser;
    }

    protected function getNamaUser()
    {
        $dataUser = unserialize($this->session->userdata('dataUser'));

        return $dataUser['NAMABP'];
    }

    protected function getNIK()
    {
        $dataUser = $this->getDataUser();

        return $dataUser['NIK'];
    }

    protected function getFilterPage()
    {
        $this->defaultFilterPage();

        return $this->filterPage;
    }

    protected function setFilterPage($filterPage)
    {
        $this->filterPage = $filterPage;
    }

    protected function defaultFilterPage()
    {
        $this->filterPage = '';
    }

    public function search($offset = null)
    {
        $this->setFilters($this->input->post('data'));
        $this->removeEmptyFilter();
        if (!empty($offset)) {
            $this->addFilters('offset', $offset);
        }

        $this->index();
    }

    private function removeEmptyFilter()
    {
        if (!empty($this->filters)) {
            if (is_array($this->filters)) {
                foreach ($this->filters as $k => $v) {
                    if (empty($v)) {
                        unset($this->filters[$k]);
                    }
                }
                if (isset($this->filters['search'])) {
                    unset($this->filters['search']);
                }
            }
        }
    }

    public function searchAjax()
    {
        $where = $this->input->post('data');
        $fields = $this->input->post('fields');
        $order = $this->input->post('order');
        $single = $this->input->post('single');
        if (!empty($order)) {
            foreach ($order as $k => $v) {
                $this->model->order_by($k, $v);
            }
        }
        if (!empty($single)) {
            $tmp = $this->model->fields($fields)->get_by($where);
        } else {
            $tmp = $this->model->fields($fields)->get_many_by($where);
        }

        if (!empty($tmp)) {
            $this->result['status'] = 1;
            $this->result['content'] = $tmp;
        }

        $this->display_json($this->result);
    }

    /**
     * Get the value of filterDetails.
     */
    protected function getFilterDetails()
    {
        return $this->filterDetails;
    }

    /**
     * Set the value of filterDetails.
     *
     * @return self
     */
    protected function setFilterDetails($filterDetails)
    {
        $this->filterDetails = $filterDetails;
    }

    /** menambahkan fungsi paginate untuk generate paging */
    public function paginate($limit, $offset = 0, $where = null)
    {
        $_result = [];
        $config = $this->config->item('pagination');
        $config['base_url'] = $this->pathView.'/search';
        $config['total_rows'] = empty($where) ? $this->model->count_all() : $this->model->count_by($where);
        $config['per_page'] = $limit;

        $this->pagination->initialize($config);
        $this->model->order_by($this->model->getKeyName(), 'desc');

        $_result['data'] = empty($where) ? $this->model->columnTable()->as_array()->limit($config['per_page'], $offset)->get_all() : $this->model->columnTable()->as_array()->limit($config['per_page'], $offset)->get_many_by($where);
        $_result['links'] = $this->pagination->create_links();

        return $_result;
    }

    /**
     * Get the value of withPagination.
     */
    public function getWithPagination()
    {
        return $this->withPagination;
    }

    /**
     * Set the value of withPagination.
     *
     * @return self
     */
    public function setWithPagination($withPagination)
    {
        $this->withPagination = $withPagination;
    }

    /**
     * Get the value of additionalInfo.
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * Set the value of additionalInfo.
     *
     * @return self
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }

    public function getJabatanAtasan()
    {
        $jabatanAtasan = unserialize($this->session->userdata('jabatanAtasan'));

        return $jabatanAtasan;
    }

    public function getJabatanBawahan()
    {
        $jabatanAtasan = unserialize($this->session->userdata('jabatanBawahan'));

        return $jabatanAtasan;
    }

    /**
     * Get the value of filters.
     */
    public function getFilters($key = null)
    {
        $result = null;
        $result = is_null($key) ? $this->filters : (isset($this->filters[$key]) ? $this->filters[$key] : null);

        return $result;
    }

    /**
     * Set the value of filters.
     *
     * @return self
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function addFilters($key, $value)
    {
        $this->filters[$key] = $value;
    }

    public function removeFilters($key)
    {
        if (isset($this->filters[$key])) {
            unset($this->filters[$key]);
        }
    }
}

/* Controller untuk pengajuan absent */
class Absent_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model->setUserId($this->getIdUser());
        $dataUser = $this->getDataUser();
        $this->model->setNik($dataUser['NIK']);
        $this->model->setNamaUser($dataUser['NAMABP']);
        $this->model->setPathUrl($this->pathView);
    }

    public function add($referenceId = null)
    {
        $this->includes = array('js' => array(
            'assets/js/transaksi/absensi.js',
            ),
        );
        $this->setButtonRight(); // buat tombol kembali
        $gender = $this->getGenderUser();
        $genderList = ['S'];
        if (!empty($gender)) {
            array_push($genderList, $gender);
        }
        $this->model->setGenderAbsent($genderList);
        parent::add($referenceId);
    }

    public function setTableConfig()
    {
        $this->table->key_record = array('id');
        $this->table->setHiddenField(['id', 'urutan', 'approval_count']);
    }

    protected function setIndexData()
    {
        $this->addFilters('user_id', $this->getIdUser());
        return parent::setIndexData();
    }

    public function getAtasan()
    {
        $nikAtasan = unserialize($this->session->userdata('nikAtasan'));

        return $nikAtasan;
    }

    public function getBawahan()
    {
        $nikBawahan = unserialize($this->session->userdata('nikBawahan'));

        return $nikBawahan;
    }

    public function edit()
    {
        $this->includes = array('js' => array(
            'assets/js/transaksi/absensi.js',
            ),
        );
        $where = $this->input->post('key');
        $data = $this->model->getEditData($where, false);
        if ($this->canApprove($data)) {
            $this->model->setApproval(true);
        } else {
            $userIdAbsent = $this->getIdUser();
            if ($data->status == 'R') {
                if ($data->user_id == $userIdAbsent) {
                    $this->model->setResubmit(true);
                }
            }
        }
        $this->_formEdit($data, $where);
    }

    private function canApprove($data)
    {
        $result = 0;
        $status = $this->config->item('status');
        if ($data->status == 'A') {
            $urutanApproval = intval($data->urutan) + 1;
            $userApproval = $this->getIdUser();
            $this->load->model('Absent_user_model', 'aum');
            $absentDetail = $this->aum->get_by(['absent_id' => $data->id, 'urutan' => $urutanApproval, 'user_id' => $userApproval]);
            if (!empty($absentDetail)) {
                $result = 1;
            }
        }

        return $result;
    }

    public function save()
    {
        $image = $this->input->post('attachment');
        $data = $this->input->post('data');
        $where = $this->input->post('key');
        $attachment = null;
        if (!empty($image)) {
            $attachment = $this->saveAttachment();
            if (is_null($attachment)) {
                $this->result['message'] = 'Gagal simpan attachment';
                $this->display_json($this->result);

                return;
            }
            $data['attachment'] = $attachment;
        }

        if (empty($where)) {
            $where = [$this->model->getKeyName() => null];
        }
        $saved = $this->model->saveData($where, $data);
        $this->result = $saved;

        $this->display_json($this->result);
    }

    public function approve()
    {
        $where = $this->input->post('key');
        $saved = $this->model->approve($where);
        $this->result = $saved;

        $this->display_json($this->result);
    }

    public function reject()
    {
        $where = $this->input->post('key');
        $data = $this->input->post('data');
        $saved = $this->model->reject($where, $data);
        $this->result = $saved;

        $this->display_json($this->result);
    }
}

/* konfigurasi untuk client koneksi ke webservice oracle*/
class Client_Controller extends MX_Controller
{
    public function __construct()
    {
        // Load the library
        parent::__construct();
        $this->load->library('rest');
        $this->load->config('serverws');
        $config = $this->config->item('ws');
        $this->init($config);
    }

    public function init($config = null)
    {
        if (!empty($config)) {
            $this->rest->initialize($config);
        }
    }
}

class RESTSECURE_Controller extends REST_Controller
{
    protected $result = array(
            'status' => 0,
            'message' => '',
            'content' => '',
    );
    protected $decodedToken;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('authorization', 'jwt'));
        $this->checkToken();
    }

    private function checkToken()
    {
        $headers = $this->input->request_headers();
        $result = false;
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {
                $this->decodedToken = $decodedToken;
                $result = true;
            }
        }

        if (!$result) {
            $this->response('Unauthorized', 401);

            return;
        }
    }
}
