<?php
    echo '<div class="col-md-12">';
    if(!empty($data)){
        echo '<table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Product</th>
                <th>Deskripsi</th>
                <th>Quantity</th>
                <th>Harga Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';
        foreach($data as $d){
            echo '<tr>
                <td>'.$d['name'].'</td>
                <td>'.$d['description_sale'].'</td>
                <td>'.angkaRibuan($d['product_uom_qty']).'</td>
                <td>'.angkaRibuan($d['price_unit']).'</td>
                <td>'.angkaRibuan($d['price_total']).'</td>
            </tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }else{
        echo '<div>Data tidak ditemukan</div>';
    }
    echo '</div>';
?>