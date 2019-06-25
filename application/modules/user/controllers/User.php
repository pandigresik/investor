<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */

class User extends MX_Controller
{
    public $userLogin;
    public $permission;
    public $isLogin = false;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model', 'm_user');
        $this->load->library('session');
    }

    public function login()
    {
        $data['base_url'] = base_url();
        $this->load->view('user/login');
    }

    public function checkLogin()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $result = array(
            'status' => 0,
            'message' => 'Proses login',
            'content' => '',
        );
        $userLogin = $this->m_user->get_by(array('login' => $username));

        if ($userLogin) {
            $this->setDataLogin($userLogin);
            $result['status'] = 1;
            
        }
        //log_message('error',json_encode($this->session->userdata()));
        echo json_encode($result);
    }

    private function setDataLogin($userLogin)
    {
        $dataUser = array(
            'isLogin' => 1,
            'username' => $userLogin->login,
            'partner_id' => $userLogin->partner_id
        );

        $this->session->set_userdata($dataUser);
    }

    
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('user/user/login');
    }

    public function isLogin()
    {
        return $this->session->userdata('isLogin');
    }

    public function getUsername()
    {
        return $this->session->userdata('kode_user');
    }

    public function changePassword()
    {
        if (isset($_POST['newPassword'])) {
            $username = $this->getUsername();
            $newPassword = $this->input->post('newPassword');
            $oldPassword = $this->input->post('oldPassword');
            $result = array(
                'status' => 0,
                'message' => '',
            );
            if (!empty($username)) {
                $this->m_user->changePassword($username, $oldPassword, $newPassword);
                if ($this->m_user->affectedRow() > 0) {
                    $result['status'] = 1;
                    $result['message'] = 'Password telah berhasil dirubah.';
                } else {
                    $result['status'] = 0;
                    $result['message'] = 'Password gagal dirubah, password lama mungkin tidak sesuai.';
                }
            } else {
                $result['status'] = 0;
                $result['message'] = 'Login terlebih dahulu. ';
            }
            echo json_encode($result);
        } else {
            $data['nama'] = $this->input->post('nama_user');
            $this->load->view('changePassword', $data);
        }
    }

    public function getPassword()
    {
        return $this->password;
    }

    /* jadikan nilai id dari element array sebagai key-nya */
    // public function getPermission()
    // {
    // 	$username = $this->user->row();
    // 	$this->permission = $this->m_user->getPermission($username->id)->result_array();
    // 	return $this->arr2to1D($this->permission,'token');
    // }
    /* bangun daftar menu berdasarkan data dari workbook yang bisa diakses database
     * dan data config['workbook'] dari file user/config/permission.php
     */
    public function listMenu()
    {
        $permission = unserialize($this->session->userdata('permission'));
        $this->load->config('permission');
        $listWorkbook = $this->config->item('workbook');
        $listTmp = array();
        foreach ($listWorkbook as $id => $menu) {
            /* pakai isset karena bisa jadi permissionnya untuk aplikasi lain bukan untuk aplikasi ini */
            if (in_array($id, $permission)) {
                array_push($listTmp, $menu);
            }
        }
        /*
        foreach($permission as $id){
            /* pakai isset karena bisa jadi permissionnya untuk aplikasi lain bukan untuk aplikasi ini
            if(isset($listWorkbook[$id])) array_push($listTmp,$listWorkbook[$id]);
        }
        */
        return $listTmp;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setPermission()
    {
    }

    private function buildMenu($menu = array())
    {
        $CI = &get_instance();
        $cur_control = $CI->router->class;
        $this->load->config('permission');
        $nav = $this->config->item('list_menu');

        $listMenu = array();
        array_push($listMenu, $nav['home']);
        if (!empty($menu)) {
            foreach ($menu as $item) {
                /* tampilkan yang memiliki label menu saja */
                if (isset($nav[$item['id']])) {
                    array_push($listMenu, $nav[$item['id']]);
                }
            }
        }

        foreach ($listMenu as $key => $value) {
            $compared_str = str_replace('nav_', '', $value['id']);
            if ($cur_control == $compared_str) {
                $listMenu[$key]['class'] = 'active';
            } else {
                $listMenu[$key]['class'] = '';
            }
        }

        return $listMenu;
    }
}
