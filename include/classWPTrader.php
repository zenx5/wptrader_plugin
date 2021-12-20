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
         *      id => { int },
         *      color => { string },
         *      mountdown => { float },
         *      mountup => { float },
         *      rate => { float }
         *  ]
         */
        'wpt_investments' => [],
        'wpt_users' => [],
        'wpt_settings' => [],
        'wpt_user_fields' => [ 
            "monto" => "Monto",
            "telefono" => "Telefono",
            "postalcode" => "Codigo Postal",
            "pais" => "Pais",
            "correo" => "Correo",
            "cedula" => "Cedula",
            "apellido" => "Apellido",
            "nombre" => "Nombre",
            "id" => "ID/wp_id"
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
            'wpt_investments' => [],
            'wpt_settings' => [],
            'wpt_users' => [],
            'wpt_user_fields' => [ 
                "monto" => "Monto",
                "telefono" => "Telefono",
                "postalcode" => "Codigo Postal",
                "pais" => "Pais",
                "correo" => "Correo",
                "cedula" => "Cedula",
                "apellido" => "Apellido",
                "nombre" => "Nombre",
                "id" => "ID/wp_id"
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
        add_shortcode( 'wpt_user_name', array('WP_Trader', 'shortcode_user_name' ) );
        add_shortcode( 'wpt_count_down', array('WP_Trader', 'shortcode_count_down' ) );
        add_shortcode( 'wpt_get_data', array('WP_Trader', 'shortcode_get_data' ) );
        self::$settings['wpt_users'] = get_option('wpt_users');
        self::$settings['wpt_investments'] = get_option('wpt_investments');
        self::$settings['wpt_settings'] = get_option('wpt_settings');

    }

    public static function shortcode_get_data( $atts, $content ) {
        $id = isset( $atts['id'] )?$atts['id']:get_current_user_id();
        if ( !isset( $atts['field'] ) ) {
            return "campo no especificado";
        };
        
        $users = json_decode( get_option('wpt_users'), true);
        
        foreach( $users as $user ) {
            if( $user['id'] == $atts['id'] ) {
                if ( !isset($user[$atts['field']]) ) {
                    return "campo no existente";
                };
                return $user[$atts['field']];
            }
        }
        return 'usuario no existente';
    }



    public static function get_time($id) {        
        $invesments = json_decode( get_option('wpt_investments'), true );
        foreach( $invesments as $invesment ) {
            if( $invesment['usuario'] == $id ) {
                $end = date_create( $invesment['fecha'] );
                $end->add( new DateInterval('P180D') );
                return date_diff( $end, date_create() );
            }
        }
    }

    public static function shortcode_count_down($atts,$content ){
        
        $users = json_decode( get_option('wpt_users'), true);
        
        $day = 0;
        $hour = 0;
        $minute = 0;
        $second = 0;
        $id = isset( $atts['id'] )?$atts['id']:get_current_user_id();
        
        foreach( $users as $user ) {
            if( $user['wpid'] == $id ) {
                $diff = self::get_time($user['id']);
            }
        }
        $day = $diff->days;
        $hour = $diff->h;
        $minute = $diff->i;
        $second = $diff->s;

        ob_start();
        ?>
            <script type="text/javascript">
                // Create a class for the element
                class CountDown extends HTMLElement {
                    constructor() {
                        // Always call super first in constructor
                        super();

                        this.elements = {
                            day: null,
                            hour: null,
                            minute: null, 
                            second: null
                        };
                        // Create a shadow root
                        const shadow = this.attachShadow({mode: 'open'});

                        // Create spans
                        const box = document.createElement('span');
                        box.setAttribute('class', 'cd-wrapper');
                        this.box = box;


                        const day = document.createElement('span');
                        day.setAttribute('class', 'cd-box');
                        this.day = 0;
                        day.textContent = this.day;
                        this.elements.day = day;

                        const hour = document.createElement('span');
                        hour.setAttribute('class', 'cd-box');
                        this.hour = 0;
                        hour.textContent = this.hour;
                        this.elements.hour = hour;

                        const minute = document.createElement('span');
                        minute.setAttribute('class', 'cd-box');
                        this.minute = 0;
                        minute.textContent = this.minute;
                        this.elements.minute = minute;

                        const second = document.createElement('span');
                        second.setAttribute('class', 'cd-box');
                        this.second = 0;
                        second.textContent = this.second;
                        this.elements.second = second;
                        
                        const style = document.createElement('style');
                        
                        style.textContent = `
                        .cd-message {
                            display: flex;
                            flex-direction: row;
                            justify-content: center;
                            font-weight: bold;
                            font-size: 120%;
                        }
                        .cd-wrapper {
                            display: flex;
                            flex-direction: row;
                            justify-content: space-around;
                        }

                        .cd-box {
                            width: 90px;
                            font-size: 50px;
                            text-align: center;
                            box-shadow: 0px 0px 10px rgb(0 0 0 / 30%);
                            border-radius: 10px;                        
                        }
                        .cd-box::after {
                            
                        }
                        `;

                        this.target = false;
                        this.loaded = false;

                        // Attach the created elements to the shadow dom
                        shadow.appendChild(style);
                        
                        shadow.appendChild(box);
                        box.appendChild(day);
                        box.appendChild(hour);
                        box.appendChild(minute);
                        box.appendChild(second);
                        this.idInterval = setInterval( _ => {
                            this.load();
                            this.tick();
                            this.render();
                            if( this.target ) {
                                clearInterval( this.idInterval );
                                this.box.textContent = this.message;
                                this.box.setAttribute("class", "cd-message")
                            }
                        }, 1000 );
                    }

                    load() {
                        if( ! this.loaded ) {   
                            this.day = this.getAttribute("data-day") || 0;
                            this.hour = this.getAttribute("data-hour") || 0;
                            this.minute = this.getAttribute("data-minute") || 0;
                            this.second = this.getAttribute("data-second") || 0;
                            this.message = this.getAttribute("data-message") || 0;
                            this.loaded = true;
                        }
                    }

                    render() {
                        this.elements.day.textContent = this.day;
                        this.elements.hour.textContent = this.hour;
                        this.elements.minute.textContent = this.minute;
                        this.elements.second.textContent = this.second;
                    }

                    tick() {
                        this.second--;
                        if( this.second < 0 ) {
                            this.second = 59;
                            this.minute--;
                            if( this.minute < 0 ) {
                                this.minute = 59;
                                this.hour--;
                                if( this.hour < 0 ) {
                                    this.hour = 23;
                                    this.day--;
                                    if( this.day < 0 ) {
                                        this.day = 0;
                                        this.hour = 0;
                                        this.minute = 0;
                                        this.second = 0;
                                        this.target = true;
                                    }
                                }
                            }
                        }
                    }
                }

                // Define the new element
                customElements.define('count-down', CountDown);
                
            </script>
            
            <count-down
                data-day="<?=$day?>"
                data-hour="<?=$hour?>"
                data-minute="<?=$minute?>"
                data-second="<?=$second?>"
                data-message="Su suscripcion ha expirado"
            ></count-down>
        <?php
        
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function shortcode_user_name($atts,$content ){
        $users = json_decode( get_option('wpt_users'), true);
        
        foreach( $users as $user ) {
            if( $user['id'] == $atts['id'] ) {
                return $user['nombre']." ".$user['apellido'];
            }
        }
        return "Usuario no existe";   
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
            'WP Trader Club',
            'WP Trader Club',
            'manage_options',
            WP_PLUGIN_DIR.'/wp-trader/admin/view/all.php',
            null,
            'https://api.iconify.design/ic/round-currency-exchange.svg?color=white',
            5
        );
    }
    public static function create_db(){
        self::update_settings('wpt_rates', array() );
        self::update_settings('wpt_users', array() );
        self::update_settings('wpt_investments', array() );
        self::update_settings('wpt_settings', array(
            "tiempoCobro" => 180,
            "rmin" => 30,
            "contrySelect" => ["all"]
        ) );
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
            // Obtenemos los usuarios de worpress y le aplicamos JSON.parse para convertirlo de string a object
            let $userswp = JSON.parse('<?= json_encode( get_users('role=subscriber') ); ?>')
            let $t = new WPTrader(<?=self::get('wpt_user_fields',false)?>,<?=self::get('wpt_users', false)?>,<?=self::get('wpt_investments',false)?>,<?=self::get('wpt_rates',false)?>);
            $t.setSettings(<?=self::get('wpt_settings',false)?>)
            </script>
        <?php
    }

    public static function app(){
        echo "<script type='text/javascript'>";
        include 'app.js';
        echo "</script>";
    }

}