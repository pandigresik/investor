<div class="col-md-12">
    <div class="table-resposive">
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
                        $prosentaseBayar = $d['terbayar'] > 0 ? round($d['terbayar'] / $d['amount_total'],2) : 0;
                        echo '<tr>
                        <td>'.$d['name'].'</td>
                        <td>'.($d['amount']*100).'</td>
                        <td>'.convertElemenTglWaktuIndonesia($d['date_order']).'</td>
                        <td>'.$d['jml_cicilan'].'</td>
                        <td><label class="label label-success"><i class="glyphicon glyphicon-credit-card"></i>  bayar  '.$prosentaseBayar.' %</label>'.'</td>
                        <td>'.angkaRibuan($d['amount_total']).'</td>
                        <td>'.angkaRibuan($d['terbayar']).'</td>
                        </tr>';
                        echo '<tr class="detail hide"></tr>';
                    }
                }
            ?>
        </tbody>
        </table>
    </div>
</div>