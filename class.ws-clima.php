<?php
/*
 *Prefix functions wsc = Webservice Clima 
 */
class WSClima{
    
    public function __construct(){
        $this->wsc_consume();
        add_action('wp_enqueue_scripts', array( $this, 'wsc_enqueue_custom_scripts'));
        add_action('wp_ajax_wsc_ajax_handler', array( $this, 'wsc_ajax_handler'));
        add_action('wp_ajax_nopriv_wsc_ajax_handler', array( $this, 'wsc_ajax_handler'));
        add_shortcode('wsc-form', array( $this, 'wsc_formulario_clima'));
    }

    /*
     *Encolamos los js  
     */
    public function wsc_enqueue_custom_scripts(){
        wp_enqueue_script(
            'wsc-clima', 
            url . 'js/wsc.js', 
            array('jquery'), 
            rand(9999,999999) 
        );
        
        $states = $this->wsc_get_estados_municipios();

        if( $states ){
            wp_localize_script(
                'wsc-clima',
                'wscajax', 
                array( 
                    'url'       => admin_url('admin-ajax.php'),
                    'handler'   => 'wsc_ajax_handler',
                    'estados'    => $states
                ) 
            );
        }
    }
    
    /*
     * Creamos el ajax handler para manipular llamados ajax 
     */
    public function wsc_ajax_handler(){

        if( isset( $_POST['callback'] ) )
            $this->{ $_POST['callback']}( (object) $_POST['data'] );
        die();
    }

    /*
     *Consumimos el webservice, nos regresa un gz file y hacemos unzip, save del json
     */
    public function wsc_consume(){
    
        $unzip = gzdecode( file_get_contents('https://smn.conagua.gob.mx/webservices/index.php?method=1') );
        $wsorigen = fopen( path . 'json/wsorigen.json', 'w+');
        fwrite( $wsorigen, $unzip);
    }
    
    /*
     *Query al objecto almacenado del webservice
     * */
    public function wsc_consultar_clima( $data ){
        $origen = json_decode(file_get_contents( path . 'json/wsorigen.json'));
        foreach( $origen as $item){
            if( $item->nes == $data->nes && $item->nmun == $data->nmun){
                echo $this->wsc_print_results_query( $item );
                return;
            }
        }
    }
    /*
     *Vista para los resultados
     * */
    public function wsc_print_results_query( $data ){
        $view = file_get_contents( path . 'views/wsc-hoy.html');
        foreach( $data as $index => $value ){
            $view = str_replace('[' . $index .']', $value, $view);
        }
        return $view;
    }
    /*
     *Extraemos la data del json de estados/municipios
     */
    public function wsc_get_estados_municipios(){
        $states = file_get_contents( path . 'json/estados-municipios.json');
        if( !$states )
            return;
        return $states;
    }   
    
    /*
     *Creamos shortcode del form
     */
    public function wsc_formulario_clima(){
        return file_get_contents( path . 'views/wsc-form.html');
    }
}