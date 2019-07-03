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
        $this->load->view('home/dashboard', ['data' => $data]);
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
