<?php
/**
 * Clase oficial de conexión con Pagadito.
 * Versión corregida por Singnia (integración Render 2025)
 * Basada en SDK v1.5.1 de Pagadito.com
 *
 * @author Pagadito
 * @version PHP 1.5.1 (modificada)
 * @link https://dev.pagadito.com/index.php?mod=docs&hac=apipg#php
 */

class Pagadito {

    private $uid;
    private $wsk;
    private $apipg;
    private $apipg_sandbox;
    private $format_return;
    private $response;
    private $sandbox_mode;
    private $op_connect_key;
    private $op_exec_trans_key;
    private $op_get_status_key;
    private $op_get_exchange_rate_key;
    private $details;
    private $custom_params;
    private $currency;
    private $allow_pending_payments;

    // ---------------------------------------------------------------
    // CONSTRUCTOR
    // ---------------------------------------------------------------
    public function __construct($uid, $wsk){
        $this->uid = $uid;
        $this->wsk = $wsk;
        $this->config();
    }

    // ---------------------------------------------------------------
    // CONEXIÓN Y TRANSACCIÓN
    // ---------------------------------------------------------------
    public function connect(){
        $params = [
            'operation'     => $this->op_connect_key,
            'uid'           => $this->uid,
            'wsk'           => $this->wsk,
            'format_return' => $this->format_return
        ];
        $this->response = $this->call($params);
        return $this->get_rs_code() === "PG1001";
    }

    public function exec_trans($ern){
        if ($this->get_rs_code() !== "PG1001") {
            return false;
        }

        $params = [
            'operation'     => $this->op_exec_trans_key,
            'token'         => $this->get_rs_value(),
            'ern'           => $ern,
            'amount'        => $this->calc_amount(),
            'details'       => json_encode($this->details),
            'custom_params' => json_encode($this->custom_params),
            'currency'      => $this->currency,
            'format_return' => $this->format_return,
            'allow_pending_payments' => $this->allow_pending_payments
        ];

        $this->response = $this->call($params);

        if ($this->get_rs_code() === "PG1002") {
            $val = $this->get_rs_value();

            // ✅ Corrección: el valor puede ser objeto o string
            if (is_object($val) && property_exists($val, 'url')) {
                $redirect_url = urldecode($val->url);
            } elseif (is_string($val)) {
                $redirect_url = urldecode($val);
            } else {
                throw new Exception("Respuesta inválida: no se encontró URL de redirección en get_rs_value().");
            }

            header("Location: " . $redirect_url);
            exit;
        }

        return false;
    }

    public function get_status($token_trans){
        if ($this->get_rs_code() !== "PG1001") {
            return false;
        }

        $params = [
            'operation'     => $this->op_get_status_key,
            'token'         => $this->get_rs_value(),
            'token_trans'   => $token_trans,
            'format_return' => $this->format_return
        ];

        $this->response = $this->call($params);
        return $this->get_rs_code() === "PG1003";
    }

    // ---------------------------------------------------------------
    // MÉTODOS AUXILIARES PÚBLICOS
    // ---------------------------------------------------------------
    public function add_detail($quantity, $description, $price, $url_product = ""){
        $this->details[] = [
            "quantity"      => $quantity,
            "description"   => $description,
            "price"         => $price,
            "url_product"   => $url_product
        ];
    }

    public function set_custom_param($code, $value){
        $this->custom_params[$code] = $value;
    }

    public function enable_pending_payments(){
        $this->allow_pending_payments = "true";
    }

    public function get_rs_code(){ return $this->return_attr_response("code"); }
    public function get_rs_message(){ return $this->return_attr_response("message"); }
    public function get_rs_value(){ return $this->return_attr_response("value"); }
    public function get_rs_datetime(){ return $this->return_attr_response("datetime"); }

    public function get_rs_status(){ return $this->return_attr_value("status"); }
    public function get_rs_reference(){ return $this->return_attr_value("reference"); }
    public function get_rs_date_trans(){ return $this->return_attr_value("date_trans"); }

    public function mode_sandbox_on(){ $this->sandbox_mode = true; }
    public function change_format_json(){ $this->format_return = "json"; }
    public function change_format_xml(){ $this->format_return = "xml"; }
    public function change_format_php(){ $this->format_return = "php"; }

    public function change_currency_usd(){ $this->currency = "USD"; }
    public function change_currency_gtq(){ $this->currency = "GTQ"; }
    public function change_currency_hnl(){ $this->currency = "HNL"; }
    public function change_currency_nio(){ $this->currency = "NIO"; }
    public function change_currency_crc(){ $this->currency = "CRC"; }
    public function change_currency_pab(){ $this->currency = "PAB"; }
    public function change_currency_dop(){ $this->currency = "DOP"; }

    // ---------------------------------------------------------------
    // CONFIGURACIÓN INTERNA
    // ---------------------------------------------------------------
    private function config(){
        $this->apipg                    = "https://comercios.pagadito.com/apipg/charges.php";
        $this->apipg_sandbox            = "https://sandbox.pagadito.com/comercios/apipg/charges.php";
        $this->format_return            = "json";
        $this->sandbox_mode             = false;
        $this->op_connect_key           = "f3f191ce3326905ff4403bb05b0de150";
        $this->op_exec_trans_key        = "41216f8caf94aaa598db137e36d4673e";
        $this->op_get_status_key        = "0b50820c65b0de71ce78f6221a5cf876";
        $this->op_get_exchange_rate_key = "da6b597cfcd0daf129287758b3c73b76";
        $this->details                  = [];
        $this->custom_params            = [];
        $this->currency                 = "USD";
        $this->allow_pending_payments   = "false";
    }

    // ---------------------------------------------------------------
    // FUNCIONES PRIVADAS
    // ---------------------------------------------------------------
    private function return_attr_response($attr){
        if (is_object($this->response) && property_exists($this->response, $attr)) {
            return $this->response->$attr;
        }
        return null;
    }

    private function return_attr_value($attr){
        if (!$this->return_attr_response("value")) {
            return null;
        }

        switch ($this->format_return) {
            case "json":
            case "xml":
                if (is_object($this->response->value) && property_exists($this->response->value, $attr)) {
                    return $this->response->value->$attr;
                }
                break;
            case "php":
                if (is_array($this->response->value) && array_key_exists($attr, $this->response->value)) {
                    return $this->response->value[$attr];
                }
                break;
        }
        return null;
    }

    private function call($params){
    try{
        $ch = curl_init($this->sandbox_mode ? $this->apipg_sandbox : $this->apipg);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->format_post_vars($params));

        // Seguridad estricta para producción (y funciona igual en prod):
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_CAINFO, "/etc/ssl/certs/ca-certificates.crt");
        curl_setopt($ch, CURLOPT_CAPATH, "/etc/ssl/certs");

        $response = curl_exec($ch);
        if ($response === false) {
            // Deja registro útil en logs PHP/Render
            error_log('[Pagadito cURL] ' . curl_error($ch));
        }
        curl_close($ch);

        return $this->decode_response($response);
    } catch (Exception $err) {
        error_log('[Pagadito Exception] ' . $err->getMessage());
        return null;
    }
}



    private function format_post_vars($vars){
        $formatted = "";
        foreach ($vars as $key => $value) {
            $formatted .= $key . '=' . urlencode($value) . '&';
        }
        return rtrim($formatted, '&');
    }

    private function decode_response($response){
        switch ($this->format_return) {
            case "php": return unserialize($response);
            case "xml": return simplexml_load_string($response);
            case "json":
            default:    return json_decode($response);
        }
    }

    private function calc_amount(){
        $amount = 0;
        foreach ($this->details as $detail) {
            $amount += $detail["quantity"] * $detail["price"];
        }
        return $amount;
    }
}
?>
