<?php
// download.php – Petals by Montse

include 'components/connect.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] == ''){
   header('location:user_login.php');
   exit;
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['format'])){
   $format = $_GET['format'];

   // 📄 DESCARGA EN PDF
   if($format == 'pdf'){

      require_once('tcpdf/tcpdf.php');

      // Crear PDF
      $pdf = new TCPDF();
      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor('Petals by Montse');
      $pdf->SetTitle('Historial de Pedidos - Petals by Montse');
      $pdf->AddPage();

      // Cabecera estilizada
      $content = '
      <h1 style="text-align:center; color:#d36c8c;">Historial de Pedidos</h1>
      <p style="text-align:center; font-size:12px;">Gracias por confiar en Petals by Montse 🌸<br>
      Aquí encontrarás el detalle de tus pedidos realizados.</p>
      <br><br>
      <table border="1" cellpadding="6" style="font-size:10px; border-color:#d36c8c;">
         <thead style="background-color:#fbe8ed;">
            <tr>
               <th><b>Fecha</b></th>
               <th><b>Nombre</b></th>
               <th><b>Correo</b></th>
               <th><b>Teléfono</b></th>
               <th><b>Dirección</b></th>
               <th><b>Método de Pago</b></th>
               <th><b>Productos</b></th>
               <th><b>Total</b></th>
               <th><b>Estado</b></th>
            </tr>
         </thead>
         <tbody>';

      // Consultar pedidos
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
      $select_orders->execute([$user_id]);

      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
            $status_color = $fetch_orders['payment_status'] == 'pendiente' ? 'color:red;' : 'color:green;';
            $content .= '
               <tr>
                  <td>'.$fetch_orders['placed_on'].'</td>
                  <td>'.$fetch_orders['name'].'</td>
                  <td>'.$fetch_orders['email'].'</td>
                  <td>'.$fetch_orders['number'].'</td>
                  <td>'.$fetch_orders['address'].'</td>
                  <td>'.$fetch_orders['method'].'</td>
                  <td>'.$fetch_orders['total_products'].'</td>
                  <td>$'.$fetch_orders['total_price'].'</td>
                  <td style="'.$status_color.'">'.$fetch_orders['payment_status'].'</td>
               </tr>';
         }
      } else {
         $content .= '
            <tr>
               <td colspan="9" style="text-align:center; color:#888;">No se han encontrado pedidos.</td>
            </tr>';
      }

      $content .= '
         </tbody>
      </table>
      <br><br>
      <p style="text-align:center; font-size:10px; color:#555;">
         Petals by Montse • Floristería artesanal en línea 🌷 <br>
         San Salvador, El Salvador
      </p>';

      $pdf->writeHTML($content, true, false, true, false, '');
      $pdf->Output('historial_pedidos_petals.pdf', 'D');
      exit();

   }

   // 📊 DESCARGA EN CSV
   elseif($format == 'csv'){

      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename=historial_pedidos_petals.csv');

      $output = fopen('php://output', 'w');

      // Encabezados
      fputcsv($output, [
         'Fecha', 'Nombre', 'Correo electrónico', 'Número de teléfono',
         'Dirección', 'Método de pago', 'Productos', 'Total ($)', 'Estado del pago'
      ]);

      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
      $select_orders->execute([$user_id]);

      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
            fputcsv($output, [
               $fetch_orders['placed_on'],
               $fetch_orders['name'],
               $fetch_orders['email'],
               $fetch_orders['number'],
               $fetch_orders['address'],
               $fetch_orders['method'],
               $fetch_orders['total_products'],
               '$'.$fetch_orders['total_price'],
               $fetch_orders['payment_status']
            ]);
         }
      } else {
         fputcsv($output, ['No se encontraron pedidos']);
      }

      fclose($output);
      exit();
   }
}
?>
