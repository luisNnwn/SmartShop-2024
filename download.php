<?php
// download.php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};


if(isset($_GET['format'])){
   $format = $_GET['format'];

   if($format == 'pdf'){

    require_once('tcpdf/tcpdf.php');

    $pdf = new TCPDF();
    $pdf->AddPage();

    $content = '<h1>Historial de Pedidos - SmartShop</h1>';
    $content .= '<table border="1">';
    $content .= '<tr><th>Colocado el</th><th>Nombre</th><th>Correo electrónico</th><th>Número de teléfono</th><th>Dirección</th><th>Forma de pago</th><th>Sus pedidos</th><th>Precio total</th><th>Estado del pago</th></tr>';

    $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
    $select_orders->execute([$user_id]);
    while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
        $content .= '<tr>';
        $content .= '<td>' . $fetch_orders['placed_on'] . '</td>';
        $content .= '<td>' . $fetch_orders['name'] . '</td>';
        $content .= '<td>' . $fetch_orders['email'] . '</td>';
        $content .= '<td>' . $fetch_orders['number'] . '</td>';
        $content .= '<td>' . $fetch_orders['address'] . '</td>';
        $content .= '<td>' . $fetch_orders['method'] . '</td>';
        $content .= '<td>' . $fetch_orders['total_products'] . '</td>';
        $content .= '<td>$' . $fetch_orders['total_price'] . '</td>';
        $content .= '<td style="color:' . ($fetch_orders['payment_status'] == 'pendiente' ? 'red' : 'green') . ';">' . $fetch_orders['payment_status'] . '</td>';
        $content .= '</tr>';
    }

    $content .= '</table>';
    $pdf->writeHTML($content, true, false, true, false, '');

    $pdf->Output('historial_pedidos.pdf', 'D');
    exit();

   } elseif($format == 'csv'){

    $headers = array('Colocado el día', 'Nombre', 'Correo electrónico', 'Número de teléfono', 'Dirección', 'Forma de pago', 'Sus pedidos', 'Precio total', 'Estado del pago');

    $output = fopen('php://output', 'w');

    fputcsv($output, $headers);

    $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
    $select_orders->execute([$user_id]);
    while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
        $data = array(
            $fetch_orders['placed_on'],
            $fetch_orders['name'],
            $fetch_orders['email'],
            $fetch_orders['number'],
            $fetch_orders['address'],
            $fetch_orders['method'],
            $fetch_orders['total_products'],
            '$' . $fetch_orders['total_price'],
            $fetch_orders['payment_status']
        );
        fputcsv($output, $data);
    }

    fclose($output);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=historial_pedidos.csv');
    exit();
}

}
?>
