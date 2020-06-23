<?php

if (!function_exists('mainTemplate')) {
    function mainTemplate()
    {
        include plugin_dir_path(__DIR__) . 'includes/templates/main.php';
    }
}

if (!class_exists('BaranApi')) {
    class BaranApi
    {
        protected $key, $client;

        function __construct($key)
        {
            $this->client = new SoapClient('http://onlineshop.baransys.com/OnlineShopService.asmx?wsdl');
            $this->key = $key;
        }

        public function products($minCount)
        {
            $result = $this->client->Get_Products($this->key, $minCount)->Get_ProductsResult;
            $result = json_decode($result);
            if (strtolower($result->Status) == 'ok') {
                return $result->Result;
            } else {
                return [];
            }
        }

        public function save($parameters)
        {
            if(is_array($parameters)){
                $parameters['ApiKey'] = $this->key;
            }elseif(is_object($parameters)) {
                $parameters->ApiKey = $this->key;
            }
            $result = $this->client->Save_Order($parameters)->Get_ProductsResult;
            $result = json_decode($result);
            if (strtolower($result->Status) == 'ok') {
                return true;
            } else {
                return false;
            }
        }
    }
}

if (!function_exists('wooProducts')) {
    function wooProducts()
    {
        $args = [
            'orderBy' => 'name',
        ];
        return wc_get_products($args);
    }
}


if(!function_exists('myCron')){
    function myCron()
    {
        if( !wp_next_scheduled( 'runAllCronJobs' ) ) {
            wp_schedule_event( current_time('timestamp'), 'hourly', 'runAllCronJobs' );
        }
    }
}

if(!function_exists('cronJobLogs')){
    function cronJobLogs(){
        global $wpdb;

        $table_name = $wpdb->prefix . "baran_logs";

        return $wpdb->get_results( "SELECT * FROM $table_name order by id desc limit 20" );
    }
}

if(!function_exists('syncToBaran')) {
    function syncToBaran()
    {
        $products = get_posts([
            'meta_key' => 'baranProductId',
            'post_type' => 'product'
        ]);
        $cli = new BaranApi(get_option('baranApiKey') ?? 'Baran3547');
        $apiProducts = $cli->products(1);
        $apiItem = null;
        $zero = get_option('minCount') ? intval(get_option('minCount')) : 2;
        foreach ($products as $product) {
            $product = new WC_Product($product->ID);
            $apiId = get_post_meta($product->get_id(), 'baranProductId')[0];
            if (is_numeric($apiId)) {
                foreach ($apiProducts as $item) {
                    if ($item->Code == $apiId) {
                        update_post_meta($product->get_id(), '_stock', $item->RemainCount);
                        update_post_meta($product->get_id(), '_price', $item->FinalPrice);
                        update_post_meta($product->get_id(), '_regular_price', $item->FinalPrice);
                        update_post_meta($product->get_id(), '_manage_stock', 'yes');
                        update_post_meta($product->get_id(), '_sold_individually', 'yes');
                        if ($item->FinalPrice != $item->SellPrice) {
                            update_post_meta($product->get_id(), '_sell_price', $item->SellPrice);
                        }
                        if ($item->RemainCount < $zero) {
                            update_post_meta($product->get_id(), '_stock_status', 'outofstock');
                        } else {
                            update_post_meta($product->get_id(), '_stock_status', 'instock');
                        }
                        break;
                    }
                }
            }
        }

        if(count($apiProducts)){
            $success = 1;
        }else {
            $success = 0;
        }
        global $wpdb;

        $table_name = $wpdb->prefix . 'baran_logs';
        $wpdb->insert( $table_name, array(
            'createdAt' => time(),
            'success' => $success
        ));
    }
}