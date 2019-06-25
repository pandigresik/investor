<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Home extends MY_Controller
{
    public $title = 'Kreditansyari';

    public function index($referencesId = null)
    {
        $m1 = new \stdClass;
        $m1->id = 1;
        $m1->parent_id = 0;
        $m1->name = 'Dashboard';
        $m1->route = 'home/dashboard';
        $m1->icon = '<i class=\'fa fa-table\'></i>';

        $objMenu = [
            $m1            
        ];
        $data['menu'] = generateMenu($objMenu);
        $data['user'] = unserialize($this->session->userdata('dataUser'));
        $data['waktuAkses'] = convertElemenTglWaktuIndonesia(date('Y-m-d H:i:s'));
        $data['title'] = $this->title;
        $this->load->view('home/home', $data);
    }
}
