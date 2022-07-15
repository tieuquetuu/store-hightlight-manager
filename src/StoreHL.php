<?php

namespace StoreHightLight;

use HightLightStore\StoreHLPageTemplater;

class StoreHL
{
    private static $instance = NULL;

    public static function instance() {
        if ( ! isset( self::$instance ) || ! ( self::$instance instanceof StoreHL ) ) {
            self::$instance = new StoreHL();
            self::$instance->setup_constants();
            if ( self::$instance->includes() ) {
                self::$instance->actions();
                self::$instance->filters();
            }
        }

        /**
         * Return the HLStore Instance
         */
        return self::$instance;
    }

    public static function setup_constants() {
// Set main file path.
        $main_file_path = dirname( __DIR__ ) . '/store-hightlight-manager.php';

        // Plugin version.
        if ( ! defined( 'STORE_HL_VERSION' ) ) {
            define( 'STORE_HL_VERSION', '0.0.1' );
        }

        // Plugin Folder Path.
        if ( ! defined( 'STORE_HL_PLUGIN_DIR' ) ) {
            define( 'STORE_HL_PLUGIN_DIR', plugin_dir_path( $main_file_path ) );
        }

        // Plugin Root File.
        if ( ! defined( 'STORE_HL_PLUGIN_FILE' ) ) {
            define( 'STORE_HL_PLUGIN_FILE', $main_file_path );
        }

        // Whether to autoload the files or not.
        if ( ! defined( 'STORE_HL_AUTOLOAD' ) ) {
            define( 'STORE_HL_AUTOLOAD', true );
        }

        // The minimum version of PHP this plugin requires to work properly
        if ( ! defined( 'STORE_HL_MIN_PHP_VERSION' ) ) {
            define( 'STORE_HL_MIN_PHP_VERSION', '7.4' );
        }
    }

    /**
     * Include required files.
     * Uses composer's autoload
     *
     * @since  0.0.1
     * @return bool
     */
    public static function includes() {
        if ( defined( 'STORE_HL_AUTOLOAD' ) && true === STORE_HL_AUTOLOAD ) {

            if ( file_exists( STORE_HIGHT_LIGHT_PLUGIN_DIR_PATH . 'vendor/autoload.php' ) ) {
                // Autoload Required Classes.
                require_once STORE_HIGHT_LIGHT_PLUGIN_DIR_PATH . 'vendor/autoload.php';
            }

            // If GraphQL class doesn't exist, then dependencies cannot be
            // detected. This likely means the user cloned the repo from Github
            // but did not run `composer install`
//            if ( ! class_exists( 'HightLightStore\StoreHL' ) ) {
//                return false;
//            }
        }

        require_once STORE_HIGHT_LIGHT_PLUGIN_DIR_PATH . 'src/StoreHLPageTemplater.php';
        require_once STORE_HIGHT_LIGHT_PLUGIN_DIR_PATH . 'src/StoreHLGA4.php';

        return true;
    }

    public static function actions() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__ , 'load_front_end_scripts' ) );
        add_action( 'plugin_loaded', function() { StoreHLPageTemplater::get_instance(); } );

        add_action( 'cron_check_end_day', array(__CLASS__, 'check_end_day') );
        add_action( 'cron_send_mail', array(__CLASS__, 'check_end_day_send_mail') );

        add_action( 'init', array(__CLASS__, 'schedule_cron_check_end_day') );
    }

    public static function filters() {

    }


    /**
     * @namespace: load_scripts
     * @description : Load các script cần thiết
     * @author : hieusmall
     */
    public static function load_front_end_scripts() {
        global $query_class, $post;

        $product_slug = get_query_var('product');

        if (!$product_slug) {
            $product_slug = get_query_var('nha-dat');
        }

        $is_satellite_site = isset($query_class) && !is_null($query_class) ? TRUE : FALSE;
        $is_main_site = $is_satellite_site ? FALSE : TRUE;

        $product = NULL;
        $productUrl = NULL;
        $is_hl_product = FALSE;

        if ($is_main_site) {

            if ($post->post_type == 're') {
                $product = $post;
            }

        } elseif ($is_satellite_site) {
            $product = strlen($product_slug) > 0 ? $query_class::cmplugin_get_product_by_slug($product_slug) : NULL;
        }

        $is_hl_product = isset($product) && !is_null($product);
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $host_name = $_SERVER['HTTP_HOST'];
        $user_cookie = $_SERVER['HTTP_COOKIE'];
        $request_uri = $_SERVER['REQUEST_URI'];
        $referer = $_SERVER['HTTP_REFERER'];
        $protocol = $_SERVER['SERVER_PROTOCOL'];

        $translation_array = array(
            // 'site_url'              =>  $protocol . "://" . $host_name . $request_uri,
            'hostname'              =>  $host_name,
            'is_main_site'          =>  $is_main_site,
            'is_hightlight_product' =>  $is_hl_product,
            'user_ip'               =>  $user_ip,
            'user_cookie'           =>  $user_cookie,
            'referer'               =>  $referer,
            'product_id'            =>  $product->ID,
            'product_slug'          =>  $product->post_name,
            'product_title'         =>  $product->post_title,
            // 'product_url'           =>  $productUrl,
            'author_id'             =>  $product->post_author,
        );

        wp_enqueue_style( 'hightlight-store-css',  STORE_HIGHT_LIGHT_PLUGIN_DIR_URL . "assets/css/main.css");
        wp_enqueue_script( 'hightlight-store-js', STORE_HIGHT_LIGHT_PLUGIN_DIR_URL . 'assets/js/main.js', array( 'jquery' ), '', true );
        // Localize the script with new data
        wp_localize_script( 'hightlight-store-js', 'hightlight_client_object', $translation_array );
    }

    public static function check_end_day(){
        $query_args = array(
            'post_type'   => 're',
            'post_status' =>'publish',
            'sort_order' => 'desc',
            'posts_per_page' => -1
        );
        $the_query = new WP_Query( $query_args );
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();
                global $post;

                if(!empty(get_post_meta($the_query->post->ID)["end_day"][0]) && strtotime(date("Ymd"))>strtotime(get_post_meta($the_query->post->ID)["end_day"][0]))
                {
                    $my_post = array(
                        'ID'           => $the_query->post->ID,
                        'post_status'   => 'pending'
                    );
                    wp_update_post( $my_post );
                }


            endwhile;


        endif;

        // Reset Post Data
        wp_reset_postdata();
    }

    public static function check_end_day_send_mail(){
        $day_before = 3;
        $query_args = array(
            'post_type'   => 're',
            'post_status' =>'publish',
            'sort_order' => 'desc',
            'posts_per_page' => -1
        );
        $the_query = new WP_Query( $query_args );
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();
                global $post;

                if(!empty(get_post_meta($the_query->post->ID)["end_day"][0]) && strtotime(date("Ymd")) + $day_before*86400==strtotime(get_post_meta($the_query->post->ID)["end_day"][0]))
                {
                    $author_id = get_post_field('post_author', $the_query->post->ID);
                    $user_email = get_the_author_meta( 'user_email' , $author_id );

                    //php mailer variables
                    $to = $user_email;
                    $subject = "Thông báo gia hạn dịch vụ";
                    $message = "Dịch vụ cần được gia hạn: ".get_permalink($the_query->post->ID);

                    //Here put your Validation and send mail
                    $sent = wp_mail( $to, $subject, $message);
                }
            endwhile;


        endif;

        // Reset Post Data
        wp_reset_postdata();
    }

    public static function schedule_cron_check_end_day() {
        if ( ! wp_next_scheduled('cron_check_end_day') ) {
            //condition to makes sure that the task is not re-created if it already exists
            wp_schedule_event( strtotime(date("Ymd"))+24*60*60+1, 'daily', 'cron_check_end_day' );
        }
        if ( ! wp_next_scheduled('cron_send_mail') ) {
            //condition to makes sure that the task is not re-created if it already exists
            wp_schedule_event( strtotime(date("Ymd"))+24*60*60+1, 'daily', 'cron_send_mail' );
        }
    }
}