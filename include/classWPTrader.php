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
        'wpt_investments' => [],
        'wpt_users' => [],
        'wpt_actions' => [],
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
            'wpt_actions' => [],
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
        add_action( 'wp_ajax_wpt_save_data_with_wp', array('WP_Trader', 'wpt_save_data_with_wp') );
        add_action( 'wp_ajax_wpt_delete_data', array('WP_Trader', 'wpt_delete_data') );
        add_action( 'wp_ajax_wpt_get_data_for_ajax', array('WP_Trader', 'wpt_get_data_for_ajax') );
        //add_action( 'wp_ajax_wpt_edit_data', array('WP_Trader', 'wpt_edit_data') );
        add_shortcode( 'wpt_user_name', array('WP_Trader', 'shortcode_user_name' ) );
        add_shortcode( 'wpt_info', array('WP_Trader', 'shortcode_info' ) );
        add_shortcode( 'wpt_count_down', array('WP_Trader', 'shortcode_count_down' ) );
        add_shortcode( 'wpt_get_data', array('WP_Trader', 'shortcode_get_data' ) );
        
        self::$settings['wpt_users'] = get_option('wpt_users');
        self::$settings['wpt_actions'] = get_option('wpt_actions');
        self::$settings['wpt_investments'] = get_option('wpt_investments');
        self::$settings['wpt_settings'] = get_option('wpt_settings');
    }

    public static function shortcode_info( $atts, $content ) {
        if( ! isset( $atts['info'] ) ) return "";

        if( $atts['info'] == 'actions' ) {
            $actions = json_decode( get_option('wpt_actions'), true);
            ob_start();
            ?>            
                <ul>
                    <?php foreach( $actions as $action ): ?>
                        <li>
                            <?php if($action['foot']==-1): ?>
                                Menor a <?=$action['head']?>: <b><?=$action['precio']?>$</b>
                            <?php elseif($action['head']==-1): ?>
                                Mayor a <?=$action['foot']?>: <b><?=$action['precio']?>$</b>
                            <?php else: ?>
                                Desde <?=$action['foot']?> hasta <?=$action['head']?>: <b><?=$action['precio']?>$</b>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php
            
            $list = ob_get_contents();
            ob_end_clean();            

            if( $content != '' ) {
                $output = $content;
                str_replace("{{lista}}",$list, $output );
            }else{
                $output = $list;
            }
            return $output;
        }elseif( $atts['info'] == 'rates' ) {
            $invesments = json_decode( get_option('wpt_rates'), true);
            ob_start();
            ?>            
                <ul>
                    <?php foreach( $invesments as $invesment ): ?>
                        <li>Desde <?=$invesment['investmin']?>$ hasta <?=$invesment['investmax']?>$: <b><?=$invesment['rate']?>%</b></li>
                    <?php endforeach; ?>
                </ul>
            <?php
            
            $list = ob_get_contents();
            ob_end_clean();            

            if( $content != '' ) {
                $output = $content;
                str_replace("{{lista}}",$list, $output );
            }else{
                $output = $list;
            }
            return $output;
        }
        else return "";
    }


    public static function shortcode_get_data( $atts, $content ) {
        $id = isset($atts['id'])?isset($atts['id']):self::get_id( get_current_user_id() );
        
        if ( !isset( $atts['field'] ) ) {
            return "campo no especificado";
        };
        
        $field = $atts['field'];

        if( in_array($field, ["cobro", "saldo", "inversion", "recibos", "acciones"]) ) {
            switch( $field ) {
                case "cobro":
                    return self::get_time($id)->days;
                    break;
                case "saldo":
                    return self::get_total_avalaible($id);
                    break;
                case "inversion":
                    return self::get_total_investment($id);
                    break;
                case "recibidos":
                    return self::get_total_released($id);
                    break;
                case "acciones":
                    return 10;
                    break;
            }

        }


        $users = json_decode( get_option('wpt_users'), true);
        
        foreach( $users as $user ) {
            if( $user[ 'id' ] == $id ) {
                if ( !isset($user[ $field ]) ) {
                    return "campo no existente";
                }
                else if( $field == 'actions' ){
                    $details = isset( $atts['details'] )?$atts['details']:'numero';
                    $value = 0;
                    foreach( $user[ $field ] as $action ) {
                        if( $details == 'numero' ){
                            $value += $action['cantidad'];
                        }elseif( $details == 'valor' ){
                            $value += $action['cantidad']*$action['precio'];
                        }
                    }
                    return $value;
                }
                else{
                    return $user[ $field ];
                }
            }
        }
        return 'usuario no existente';
    }

    public static function get_id( $wpid ) {
        $users = json_decode( get_option('wpt_users'), true);
        foreach( $users as $user ) {
            if( $user['wpid'] == $wpid ) {
                return $user['id'];
            }
        }
    }

    public static function  calculate_gain( $mount ) {
        $rates = json_decode( get_option('wpt_rates'), true );
        foreach( $rates as $rate ){
            if( ( $mount > $rate['investmin'] ) && ( $mount <= $rate['investmax'] ) ) {
                return $rate['rate']*$mount;
            }
        }
        return 0;
    }
    
    public static function get_total_avalaible($id){
        $investments = json_decode( get_option('wpt_investments'), true );
        $settings = json_decode( get_option('wpt_settings'), true );
        $total = 0;
        foreach( $investments as $investment ) {
            if( $investment['usuario'] == $id ) {
                if( !!! $investment['released']  ) {
                    if( self::get_time($id, $investment['id'])->days <= ($settings[0]['tiempoCobro']-$settings[0]['rmin']) ) {
                        $days = $settings[0]['tiempoCobro'] - self::get_time($id)->days;
                        $days = $days>0?$days:0;
                        $total += $days * self::calculate_gain( (float)$investment['monto'] );
                    }
                }
            }
        }
        if( $total >= $settings[0]['rmin'] ) {
            return $total;
        }
        return 0;
    }

    public static function get_total_released($id){
        $investments = json_decode( get_option('wpt_investments'), true );
        $total = 0;
        foreach( $investments as $investment ) {
            if( $investment['usuario'] == $id ) {
                if( !! $investment['released']  ) {
                    $total += (float) $investment['monto'];
                }
            }
        }
        return $total;
    }

    public static function get_total_investment($id){
        $investments = json_decode( get_option('wpt_investments'), true );
        $total = 0;
        foreach( $investments as $investment ) {
            if( $investment['usuario'] == $id ) {
                $total += (float) $investment['monto'];
            }
        }
        return $total;
    }

    public static function get_time($id, $id_investment = null ) {
        $times = [];
        $max = null;
        $investments = json_decode( get_option('wpt_investments'), true );
        foreach( $investments as $investment ) {
            if( $investment['usuario'] == $id ) {
                $end = date_create( $investment['fecha'] );
                $end->add( new DateInterval('P180D') );
                $times[ $investment['id'] ] = date_diff( $end, date_create() );
                $max = $times[ $investment['id'] ];
            }
        }
        if( $id_investment ) {
            return $times[ $id_investment ];
        }
        
        
        foreach( $times as $t ) {
            if( $t->days > $max->days ) {
                $max = $t;
            }
            elseif( $t->days == $max->days ) {
                if( $t->h > $max->h ) {
                    $max = $t;
                }
                elseif( $t->h == $max->h ) {
                    if( $t->i > $max->i ) {
                        $max = $t;
                    }
                    elseif( $t->i == $max->i ) {
                        if( $t->s > $max->s ) {
                            $max = $t;
                        }
                    }
                }
            }
        }
        return $max;
    }

    public static function shortcode_count_down($atts,$content ){
        
        $users = json_decode( get_option('wpt_users'), true);
        
        $displayStr = isset( $atts['display'] )?$atts['display']:'d:h:m:s';

        $day = 0;
        $hour = 0;
        $minute = 0;
        $second = 0;
        $id = isset( $atts['id'] )?$atts['id']:self::get_id(get_current_user_id());
        
        foreach( $users as $user ) {
            if( $user['id'] == $id ) {
                $diff = self::get_time($user['id']);
                $day = $diff->days;
                $hour = $diff->h;
                $minute = $diff->i;
                $second = $diff->s;
            }
        }
        

        ob_start();
        ?>            
            <count-down
                data-day="<?=$day?>"
                data-hour="<?=$hour?>"
                data-minute="<?=$minute?>"
                data-second="<?=$second?>"
                data-message="Su suscripcion ha expirado"
                data-display="<?=$displayStr?>"
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

    public static function wpt_get_data_for_ajax(){
        $f = $_POST['f'];
        $data = json_decode( str_replace("\\","",$_POST['data']), true );
        $id = $data['id'];
        switch( $f ) {
            case "get_time": 
                $id_investment = isset( $data['id_investment'] )?$data['id_investment']:null;
                echo self::get_time( $id, $id_investment )->days;
                break;
            case "get_total_avalaible":
                echo self::get_total_avalaible($id);
                break;
            case "get_total_investment":
                echo self::get_total_investment($id);
                break;
            case "get_total_released":
                echo self::get_total_released($id);
                break;
            case "count_down":
                echo self::shortcode_count_down(["id"=>$data['id']], []);
                break;
        }
    }

    public static function wpt_save_data_with_wp(){
        $id = $_POST['id'];
        $subscribers = get_users('role=subscriber');
        $users = json_decode( get_option( 'wpt_users' ), true );
        $none = true;
        $max = 0;
        foreach( $users as $user ) {
            if( $user['wpid'] == $id  ) {
                $none = false;
            }
            if( $user['id'] > $max ) {
                $max = $user['id'];
            }
        }
        if( $none ) {
            foreach( $subscribers as $subscriber ) {
                if( $subscriber->ID == $id ) {
                    $users[] = [
                        'id' => $max + 1,
                        'edit' => false,
                        'nombre' => $subscriber->display_name,
                        'apellido' => '',
                        'cedula' => '',
                        'correo' => $subscriber->email,
                        'pais' => '',
                        'postalcode' => '',
                        'telefono' => '',
                        'monto' => 0,
                        'wpid' => $id, 
                        'actions' => 0
                    ];
                    update_option('wpt_users', json_encode( $users ) );
                    echo json_encode([
                        'id' => $max + 1,
                        'edit' => false,
                        'nombre' => $subscriber->display_name,
                        'apellido' => '',
                        'cedula' => '',
                        'correo' => $subscriber->email,
                        'pais' => '',
                        'postalcode' => '',
                        'telefono' => '',
                        'monto' => 0,
                        'wpid' => $id,
                        'actions' => 0
                    ]);
                }
            }
        }

        
        wp_die();
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
            'wp-trader/admin/view/all.php',
            null,
            'https://api.iconify.design/ic/round-currency-exchange.svg?color=white',
            5
        );
    }
    public static function create_db(){
        self::update_settings('wpt_rates', array() );
        self::update_settings('wpt_users', array() );
        self::update_settings('wpt_actions', array() );
        self::update_settings('wpt_investments', array() );
        self::update_settings('wpt_settings', array(
            array(
                "id" => 0,
                "tiempoCobro" => 180,
                "rmin" => 30,
                "actionMax" => 100,
                "contrySelect" => ["all"]
            )
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
            
            <link href="https://kavavdigital.com/pluginariel/materialdesignicons.min.css" rel="stylesheet">
            <link href="https://kavavdigital.com/pluginariel/vuetify.min.css" rel="stylesheet">
            <script src="https://kavavdigital.com/pluginariel/npm/vue@2"></script>
            <script src="https://kavavdigital.com/pluginariel/axios.min.js"></script>
            <script src="https://kavavdigital.com/pluginariel/vuetify.js"></script>
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
                            this.display = this.getAttribute("data-display") || "d:h:m:s";
                            this.display = this.display.split(":");
                            this.loaded = true;
                        }
                    }

                    render() {
                        this.elements.day.style.display = this.display.indexOf('d')==-1?'none':'inline-block';
                        this.elements.day.textContent = this.day;
                        this.elements.hour.style.display = this.display.indexOf('h')==-1?'none':'inline-block';
                        this.elements.hour.textContent = this.hour;
                        this.elements.minute.style.display = this.display.indexOf('m')==-1?'none':'inline-block';
                        this.elements.minute.textContent = this.minute;
                        this.elements.second.style.display = this.display.indexOf('s')==-1?'none':'inline-block';
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
            <script type="text/javascript">
            <?php
                include "countries.js";
                include "classwpt.js";
            ?>
            // Obtenemos los usuarios de worpress y le aplicamos JSON.parse para convertirlo de string a object
            let $userswp = JSON.parse('<?= json_encode( get_users('role=subscriber') ); ?>')
            let $t = new WPTrader(
                <?=self::get('wpt_user_fields',false)?>,
                <?=self::get('wpt_users', false)?>,
                <?=self::get('wpt_actions', false)?>,
                <?=self::get('wpt_investments',false)?>,
                <?=self::get('wpt_rates',false)?>
            );
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