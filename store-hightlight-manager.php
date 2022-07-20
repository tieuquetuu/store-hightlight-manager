<?php
/***
 *
 * Plugin Name: Store Hight Light Manager
 * Plugin URI:
 * Description: Chức năng quản lí sản phẩm thống kê số liệu, vv và vv ...
 * Author: Hiếu Small
 * Version: 0.0.1
 * Author URI: https://hieusmall.github.io
 *
 * @package  StoreHightLight
 * @version  1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Plugin Root Folder Path.
if (!defined('STORE_HIGHT_LIGHT_PLUGIN_DIR_URL')) {
    define('STORE_HIGHT_LIGHT_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
}

// Plugin Root Folder Path.
if (!defined('STORE_HIGHT_LIGHT_PLUGIN_DIR_PATH')) {
    define('STORE_HIGHT_LIGHT_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}

if (!defined('STORE_HIGHT_LIGHT_GOOGLE_CREDENTIALS')) {
    define('STORE_HIGHT_LIGHT_GOOGLE_CREDENTIALS', STORE_HIGHT_LIGHT_PLUGIN_DIR_PATH . 'credentials.json');
}

if (!defined('STORE_HIGHT_LIGHT_PUBLIC_GOOGLE_CREDENTIALS')) {
    define('STORE_HIGHT_LIGHT_PUBLIC_GOOGLE_CREDENTIALS', STORE_HIGHT_LIGHT_PLUGIN_DIR_URL . 'credentials.json');
}

if (!defined('STORE_HIGHT_LIGHT_GOOGLE_ANALYSTIC_PROPERTIES')) {
    define('STORE_HIGHT_LIGHT_GOOGLE_ANALYSTIC_PROPERTIES', '321878431');
}

// Run this function when WPGraphQL is de-activated
register_deactivation_hook(__FILE__, 'store_hightlight_deactivation_callback');
register_activation_hook(__FILE__, 'store_hightlight_activation_callback');

// Bootstrap the plugin
if (!class_exists('StoreHightLight\StoreHL')) {
    require_once __DIR__ . '/src/StoreHL.php';
}


if (!function_exists('store_hightlight_init')) {
    /**
     * Function that instantiates the plugins main class
     *
     * @return object
     */
    function store_hightlight_init()
    {
        /**
         * Return an instance of the action
         */
        return StoreHightLight\StoreHL::instance();
    }
}
store_hightlight_init();

if (defined('WP_CLI') && WP_CLI) {
    require_once 'cli/wp-cli.php';
}

//use HightLightStore\StoreHLGA4;

if ($_GET['debug'] == 'vip') {

//    var_dump(HightLightStore\StoreHLGa4::instance()->ThongKeSoLieuHeThong());
//    var_dump(class_exists(''));
    echo "<pre>";

    $report_str = HightLightStore\StoreHLGa4::instance()->ThongKeSoLieuHeThong(
        array(
            "date_ranges" => array(
                'start_date' => '2022-07-01', // Bắt đầu từ trước
                'end_date' => 'today', // Tới hôm nay
            )
        )
    );

//    $report_json = json_decode($report_str);

    print_r($report_str);

    echo "</pre>";
    die();

}