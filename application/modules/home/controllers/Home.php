<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Home extends MY_Controller
{
    public $title = 'E-Absensi Unit';

    public function index($referencesId = null)
    {
        
        $objMenu = [
            
        ];
        $data['menu'] = generateMenu($objMenu);
        $data['user'] = unserialize($this->session->userdata('dataUser'));
        $data['waktuAkses'] = convertElemenTglWaktuIndonesia(date('Y-m-d H:i:s'));
        $data['title'] = $this->title;
        $this->load->view('home/home', $data);
    }
}
