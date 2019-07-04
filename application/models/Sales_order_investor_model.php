<?php

class Sales_order_investor_model extends MY_Model
{   
    public $_table = 'ks_sales_order_investor';
    private $joinPartner = FALSE;
    private $joinSO = FALSE;
    protected $columnTableData = ['ks_sales_order_investor.id','sale_order.name as sales_order','sale_order.amount_total','(sale_order.amount_total - sale_order.margin) as beli' ,'res_partner.name as investor','(ks_sales_order_investor.amount * 100) as amount','(ks_sales_order_investor.amount * (sale_order.amount_total - sale_order.margin)) as modal'];
    protected $headerTableData = [
        [['data' => 'Sales Order'],['data' => 'Total Transaksi'],['data' => 'Pembelian'], ['data' => 'Investor'],['data' => 'Pembiayaan (%)'],['data' => 'Pembiayaan (Rp)'],['data' => 'Aksi']],
    ];
    protected $before_get = ['joinSO','joinPartner'];
    public function __construct()
    {
        parent::__construct();
    }

    protected $form = array(
        'sales_order_id' => array(
            'id' => 'sales_order_id',
            'label' => 'Sales Order',
            'class' => 'select2_single',
            'placeholder' => 'Pesanan pelanggan',
            'required' => 'required',
            'value' => '',
            'type' => 'dropdown',
            'options' => [''],
        ),
        'amount' => array(
            'id' => 'amount',
            'label' => 'Jumlah Pembiayaan (%)',
            'data-tipe' => 'angka',
            'max' => 100,
            'placeholder' => 'pembiayaan',
            'value' => '',
        ),
        
        'partner_id' => array(
            'id' => 'partner_id',
            'label' => 'Investor',
            'class' => 'select2_single',
            'type' => 'dropdown',
            'required' => 'required',
            'options' => [''],
            'value' => '',
        ),
        'submit' => array(
            'id' => 'submit',
            'type' => 'submit',
            'label' => 'Simpan',
        ),
    );

    protected function setOptionDataForm($where = array())
    {
        $this->form['partner_id']['options'] = $this->getAvailableInvestor();
        $this->form['sales_order_id']['options'] = $this->getAvailableSO();
    }

    private function getAvailableSO(){
        $result = [''];
        $this->load->model('sales_order_model','som');
        $subquery = 'id not in (select sales_order_id from '.$this->_table.' group by sales_order_id having sum(amount) >= 1)';
        $tmp = $this->som->as_array()->fields(['id','concat(name,\' - \',amount_total) as name'])->get_many_by($subquery);
        if(!empty($tmp)){
            $result = dropdown($tmp,'id','name');
        }
        return $result;        
    }

    private function getAvailableInvestor(){
        $result = [''];
        $this->load->model('partner_model','pm');
        //$tmp = $this->pm->as_array()->fields(['id','name'])->get_many_by(['supplier' => 't']);
        $tmp = $this->pm->as_array()->fields(['id','name'])->get_all();
        if(!empty($tmp)){
            $result = dropdown($tmp,'id','name');
        }
        
        return $result;        
    }

    public function joinSO(){
        if($this->getJoinSO()){
            $this->_database->join('sale_order','sale_order.id = ks_sales_order_investor.sales_order_id');
        }
    }

    public function joinPartner(){
        if($this->getJoinPartner()){
            $this->_database->join('res_partner','res_partner.id = ks_sales_order_investor.partner_id');
        }
    }

    public function getJoinPartner(){
        return $this->joinPartner;
    }

    public function setJoinPartner($joinPartner){
        $this->joinPartner = $joinPartner;
        return $this;
    }

    public function getJoinSO(){
        return $this->joinSO;
    }

    public function setJoinSO($joinSO){
        $this->joinSO = $joinSO;
        return $this;
    }
}
