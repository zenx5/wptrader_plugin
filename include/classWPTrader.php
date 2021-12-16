<?php



class WP_Trader {
     protected static $production = true;

    public $menu = [
        'page_title' => 'My Plugin',
        'menu_title' => 'My Plugin',
        'capability' => 'my_plugin',
        'menu_slug' => 'trader',
        'icon' => 'dashicons-admin-generic'
    ];
    public static $settings = [
        'db_created' => false,
        'plugin_active' => false,
        'wpt_rates' => [],
        /**
         *  The struct of the items of the array 
         *  [
         *      mountdown => { float },
         *      mountup => { float },
         *      rate => { float }
         *  ]
         */
        'wpt_investments' => [
           /* [
                "usuario" => 1,
                "fecha" => "9-3-2021",
                "monto" => 50
            ],
            [
                "usuario" => 2,
                "fecha" => "9-10-2021",
                "monto" => 300
            ],
            [
                "usuario" => 1,
                "fecha" => "11-1-2021",
                "monto" => 100
            ]*/
        ],
        'wpt_users' => [
            /*[
                "id" => 1,
                "nombre" => "Octavio",
                "apellido" => "Martinez",
                "cedula" => "18917255",
                "correo" => "octavio@mail.com",
                "pais" => "Venezuela",
                "ciudad" => "Maturin",
                "direccion" => "Urb las Carolinas M10#5 ",
                "postalcode" => "8045",
                "telefono" => "04124587931",
                "monto" => 0,
            ],
            [
                "id" => 2,
                "nombre" => "Javier",
                "apellido" => "Martinez",
                "cedula" => "19819231",
                "correo" => "javier@mail.com",
                "pais" => "Venezuela",
                "ciudad" => "Bolivar",
                "direccion" => "Urb las Carolinas M10#5 ",
                "postalcode" => "8045",
                "telefono" => "04124792931",
                "monto" => 0,
            ]*/
        ],
        'wpt_user_fields' => [ 
            "monto" => "Monto",
            "telefono" => "Telefono",
            "postalcode" => "Codigo Postal",
            "pais" => "Pais",
            "correo" => "Correo",
            "cedula" => "Cedula",
            "apellido" => "Apellido",
            "nombre" => "Nombre",
            "id" => "ID"
        ]
    ];

    function __construct(){

    }

    public static function get( $key , $as_string  = false ) {
        return $as_string?json_encode( get_option( $key ) ):get_option( $key );
    }

    public static function active() {
        self::create_db();
        self::update_settings('plugin_active', true);
    }

    public static function deactive() {
        $defaultSettings = [
            'db_created' => false,
            'plugin_active' => false,
            'wpt_rates' => [],
            'wpt_investments' => [
              /*  [
                    "usuario" => 1,
                    "fecha" => "9-3-2021",
                    "monto" => 50
                ],
                [
                    "usuario" => 2,
                    "fecha" => "9-10-2021",
                    "monto" => 300
                ],
                [
                    "usuario" => 1,
                    "fecha" => "11-1-2021",
                    "monto" => 100
                ]*/
            ],
            'wpt_users' => [
             /*   [
                    "id" => 1,
                    "nombre" => "Octavio",
                    "apellido" => "Martinez",
                    "cedula" => "18917255",
                    "correo" => "octavio@mail.com",
                    "pais" => "Venezuela",
                    "ciudad" => "Maturin",
                    "direccion" => "Urb las Carolinas M10#5 ",
                    "postalcode" => "8045",
                    "telefono" => "04124587931",
                    "monto" => 0,
                ],
                [
                    "id" => 2,
                    "nombre" => "Javier",
                    "apellido" => "Martinez",
                    "cedula" => "19819231",
                    "correo" => "javier@mail.com",
                    "pais" => "Venezuela",
                    "ciudad" => "Bolivar",
                    "direccion" => "Urb las Carolinas M10#5 ",
                    "postalcode" => "8045",
                    "telefono" => "04124792931",
                    "monto" => 0,
                ]*/
            ],
            'wpt_user_fields' => [ 
                "monto" => "Monto",
                "telefono" => "Telefono",
                "postalcode" => "Codigo Postal",
                "pais" => "Pais",
                "correo" => "Correo",
                "cedula" => "Cedula",
                "apellido" => "Apellido",
                "nombre" => "Nombre",
                "id" => "ID"
            ]
        ];
        foreach( $defaultSettings as $key => $value ) {
            self::update_settings( $key, $value );
        }
    }

    public static function uninstall() {
        
    }

    public static function init() {
        add_action( 'admin_head', array('WP_Trader', 'dependecies') );
        add_action( 'admin_menu', array('WP_Trader', 'create_menu') );
        add_action( 'admin_footer', array('WP_Trader', 'app') );
        add_action( 'wp_ajax_wpt_save_data', array('WP_Trader', 'wpt_save_data') );
        add_action( 'wp_ajax_wpt_delete_data', array('WP_Trader', 'wpt_delete_data') );
        //add_action( 'wp_ajax_wpt_edit_data', array('WP_Trader', 'wpt_edit_data') );
        add_shortcode( 'statususer', array('WP_Trader', 'shortcode' ) );
        self::$settings['wpt_users'] = get_option('wpt_users');
        self::$settings['wpt_investment'] = get_option('wpt_investment');

    }

    public static function shortcode($atts,$content ){
        return "<h2>Mi shortcode 3</h2>";
    }

    public static function javascript_ajax(){
        ?>
            <script type="text/javascript">
                jQuery( document ).ready( 
                    $ => 
                    {
                        let data = {
                            "action" : "wpt_save_data",
                        };

                        jQuery.post(ajaxurl, data, 
                            response => 
                            {
                                console.log( response );
                            }
                        );
                    }
                )
            </script>
        <?php
    }

    public static function wpt_save_data(){
        $target = $_POST['target'];
        $value = json_decode( str_replace("\\","",$_POST['value']), true );
        $id = $_POST['index'];
        $content = json_decode( get_option( $target ), true );
        if( $id == -1 ) {
            $content[] = $value;
        }else{
            $aux = [];
            foreach( $content as $index => $element ) {
                if( $element['id'] == $id ) {
                    $content[ $index ] = $value;
                }
            }
        }        
        update_option($target, json_encode( $content ) );
        echo json_encode( $value );
        wp_die();
    }

    public static function wpt_delete_data(){
        $target = $_POST['target'];
        $index = $_POST['index'];
        $content = json_decode( get_option( $target ), true );
        $aux = [];
        foreach( $content as $element ) {
            if( $element['id'] != $index ) {
                $aux[] = $element;
            }
        }
        update_option($target, json_encode( $aux ) );
        echo json_encode( $aux );
        wp_die();
    }

    public static function create_menu(){
        add_menu_page(
            'My Plugin',
            'My Plugin',
            'manage_options',
            WP_PLUGIN_DIR.'/wp-trader/admin/view/all.php',
            null,
            'dashicons-admin-generic',
            5
        );
    }
    public static function create_db(){
        self::update_settings('wpt_rates', array() );
        self::update_settings('wpt_users', array() );
        self::update_settings('wpt_user_fields', self::$settings['wpt_user_fields'] );
        self::update_settings('db_created', true);
    }

    public static function update_settings($key, $value) {
        if( isset( self::$settings[$key] ) ) {
            update_option( $key, json_encode( $value ));
            self::$settings[$key] = $value;
        }
    }



    /*** RENDER */
    public static function dependecies(){
        ?>
            <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
            <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
            <script type="text/javascript">
            <?php
                include "countries.js";
                include "classwpt.js";
            ?>
            let $t = new WPTrader(<?=self::get('wpt_user_fields',false)?>,<?=self::get('wpt_users', false)?>,<?=self::get('wpt_investments',false)?>,<?=self::get('wpt_rates',false)?>);
            </script>
        <?php
    }

    public static function app(){
        echo "<script type='text/javascript'>";
        include 'app.js';
        echo "</script>";
    }

}