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

        $m2 = new \stdClass;
        $m2->id = 2;
        $m2->parent_id = 0;
        $m2->name = 'Transaksi';
        $m2->route = '';
        $m2->icon = '<i class=\'fa fa-table\'></i>';

        $m3 = new \stdClass;
        $m3->id = 3;
        $m3->parent_id = 2;
        $m3->name = 'Pembiayaan Order';
        $m3->route = 'transaksi/so_investor';
        $m3->icon = '<i class=\'fa fa-table\'></i>';

        $objMenu = [
            $m1 ,$m2, $m3
        ];
        $data['menu'] = generateMenu($objMenu);
        $data['user'] = unserialize($this->session->userdata('dataUser'));
        $data['waktuAkses'] = convertElemenTglWaktuIndonesia(date('Y-m-d H:i:s'));
        $data['title'] = $this->title;
        $this->load->view('home/home', $data);
    }
}
