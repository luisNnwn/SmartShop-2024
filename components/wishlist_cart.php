<?php
// ================================================
// 🌷 COMPONENTE: wishlist_cart.php
// Manejador central de lógica para lista de deseos
// y carrito de compras en Petals by Montse.
// ================================================

if (isset($_POST['add_to_wishlist'])) {

    if ($user_id == '') {
        header('location:user_login.php');
        exit;
    }

    // Sanitización de entradas
    $pid   = filter_var($_POST['pid'], FILTER_SANITIZE_STRING);
    $name  = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);

    // Verificar si ya está en wishlist o carrito
    $check_wishlist = $conn->prepare("SELECT 1 FROM `wishlist` WHERE pid = ? AND user_id = ?");
    $check_wishlist->execute([$pid, $user_id]);

    $check_cart = $conn->prepare("SELECT 1 FROM `cart` WHERE pid = ? AND user_id = ?");
    $check_cart->execute([$pid, $user_id]);

    if ($check_wishlist->rowCount() > 0) {
        $message[] = '🌺 Este arreglo floral ya está en tu lista de favoritos.';
    } elseif ($check_cart->rowCount() > 0) {
        $message[] = '💐 Este producto ya está en tu carrito.';
    } else {
        $insert_wishlist = $conn->prepare("
            INSERT INTO `wishlist` (user_id, pid, name, price, image)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insert_wishlist->execute([$user_id, $pid, $name, $price, $image]);
        $message[] = '✨ Producto añadido a tu lista de favoritos.';
    }
}

// ========================================================
// 🛒 Lógica de añadir al carrito
// ========================================================
if (isset($_POST['add_to_cart'])) {

    if ($user_id == '') {
        header('location:user_login.php');
        exit;
    }

    // Sanitización
    $pid   = filter_var($_POST['pid'], FILTER_SANITIZE_STRING);
    $name  = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
    $qty   = filter_var($_POST['qty'], FILTER_SANITIZE_NUMBER_INT);

    // Comprobación si ya existe en carrito
    $check_cart = $conn->prepare("SELECT 1 FROM `cart` WHERE pid = ? AND user_id = ?");
    $check_cart->execute([$pid, $user_id]);

    if ($check_cart->rowCount() > 0) {
        $message[] = '💐 Este producto ya está en tu carrito.';
    } else {
        // Si está en wishlist, eliminarlo
        $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ? AND user_id = ?");
        $delete_wishlist->execute([$pid, $user_id]);

        // Insertar en carrito
        $insert_cart = $conn->prepare("
            INSERT INTO `cart` (user_id, pid, name, price, quantity, image)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
        $message[] = '🛍️ Producto añadido al carrito con éxito.';
    }
}
?>
