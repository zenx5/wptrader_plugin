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
    protected static $settings = [
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
        'wpt_investment' => [
            [
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
            ]
        ],
        'wpt_users' => [
            [
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
            ]
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
        return $as_string?json_encode( self::$settings[ $key ] ):self::$settings[ $key ];
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
            'wpt_investment' => [
                [
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
                ]
            ],
            'wpt_users' => [
                [
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
                ]
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
        $content = [
            "index" => $_POST['index'],
            "value" => json_decode( str_replace("\\","",$_POST['value']), true )
        ];
        echo json_encode( $content );
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
        self::update_settings("wpt_rates", array() );
        self::update_settings("wpt_users", array() );
        self::update_settings("wpt_user_fields", self::$settings['wpt_user_fields'] );
        self::update_settings('db_created', true);
    }

    public static function update_settings($key, $value) {
        if( isset( self::$settings[$key] ) ) {
            if( update_option( $key, json_encode( $value ) ) ) {
                self::$settings[$key] = $value;
            }          
            else{
                echo "Valor de $key no se pudo actualizar a ".json_encode($value)."<br>";
            }
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
            let $t = new WPTrader(<?=self::get('wpt_user_fields',true)?>,<?=self::get('wpt_users', true)?>,<?=self::get('wpt_investment',true)?>,<?=self::get('wpt_rates',true)?>);
            </script>
        <?php
    }

    public static function app(){
        echo "<script type='text/javascript'>";
        include 'app.js';
        echo "</script>";
    }

}