<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row top_tiles">          
    <?php 
        if(!empty($summaries)){
            foreach ($summaries as $key => $card) {
                echo $card;
            }
        }
    ?>
    </div>

    <div class="x_content table-responsive">
        <table class="table table-bordered">
        <thead>
            <tr>
            <th>sales order</th>
            <th>%pembiayaan</th>
            <th>order_date</th>
            <th>jumlah_cicilan</th>
            <th>status</th>
            <th>total</th>
            <th>terbayar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(!empty($data)){
                    foreach($data as $d){
                        $prosentaseBayar = $d['terbayar'] > 0 ? round(($d['terbayar'] / $d['amount_total'] * 100 ),2)  : 0;
                        echo '<tr>
                        <td onclick="SO.showDetail(this,'.$d['id'].')"><span class="link_span">'.$d['name'].'</span></td>
                        <td>'.($d['amount']*100).'</td>
                        <td>'.convertElemenTglWaktuIndonesia($d['date_order']).'</td>
                        <td>'.$d['jml_cicilan'].'</td>
                        <td><label class="label label-success"><i class="glyphicon glyphicon-credit-card"></i>  bayar  '.$prosentaseBayar.' %</label>'.'</td>
                        <td>'.angkaRibuan($d['amount_total']).'</td>
                        <td>'.angkaRibuan($d['terbayar']).'</td>
                        </tr>';
                        echo '<tr class="detail" style="display:none"><td class="detail_so" colspan="7"></td></tr>';
                    }
                }
            ?>
        </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
var SO = {
    showDetail: function(elm,_id){
        var _tr = $(elm).closest('tr');
        var _tr_detail = _tr.next('.detail');
        var _td_detail = _tr_detail.find('td.detail_so');
        if(empty(_td_detail.html())){
            $.get('home/dashboard/detail_so/'+_id,{},function(data){
                _td_detail.html(data);
            },'html');
        }
        if(_tr_detail.is(':hidden')){
            _tr_detail.show();
        }else{
            _tr_detail.hide();
        }
    }
};
</script>