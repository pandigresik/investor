<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Dashboard extends MY_Controller
{
    
    public function index($referencesId = null)
    {
        $userId = $this->session->userdata('partner_id');
        $data = $this->dataSO($userId);
        $dataDashboard = [
            ['title' => 'Investasi','count' => 3000000, 'description' => 'Investasi awal','icon' => 'fa fa-briefcase'],
            ['title' => 'Pembiayaan','count' => 30, 'description' => 'Penjualan kredit yang dibiayai','icon' => 'fa fa-shopping-cart'],
            ['title' => 'Invoice','count' => '30 (60)', 'description' => 'Invoice yang sudah dibayar','icon' => 'fa fa-file-o'],
            ['title' => 'Bagi hasil','count' => '6000000', 'description' => 'Total keuntungan yang dibagi berdasarkan kesepakatan','icon' => 'fa fa-money'],
        ];
        $summaries = $this->generateCard($dataDashboard);
        $this->load->view('home/dashboard', ['data' => $data, 'summaries' => $summaries]);
    }

    private function generateCard($datas){
        $result = [];
        foreach($datas as $data){
            $tmp =
            '<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                <div class="icon"><i class="'.$data['icon'].'"></i></div>
                <div class="count">'.$data['count'].'</div>
                <h3>'.$data['title'].'</h3>
                <p>'.$data['description'].'</p>
                </div>
            </div>';
            array_push($result,$tmp);
        }
        return $result;
    }

    private function dataSO($userId){
        $sql = <<<SQL
        select so.id,so.name,soi.amount,so.state,so.date_order,so.invoice_status,so.amount_total,(select count(*) from account_invoice ai where ai.origin = so.name ) as jml_cicilan,(select sum(ai.amount_total) from account_invoice ai where ai.state = 'paid' and ai.origin = so.name )  as terbayar
from sale_order so
join ks_sales_order_investor soi on so.id = soi.sales_order_id
where soi.partner_id = {$userId}
SQL;
        return $this->db->query($sql)->result_array();
    }

    public function detail_so($id){
        $sql = <<<SQL
        select pt.name,pt.description_sale,sol.product_uom_qty,sol.purchase_price,sol.margin,sol.price_unit,sol.price_total 
        from sale_order_line sol
join product_product pp on pp.id = sol.product_id 
join product_template pt on pt.id = pp.product_tmpl_id
where sol.order_id = {$id} -- and sol.is_service = 'f'
SQL;
        $this->load->view('home/detail',['data' => $this->db->query($sql)->result_array()]);
    }
}
