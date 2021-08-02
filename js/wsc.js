jQuery(document).ready(init);

function init(){

    //Obtemos la data de los estados/municipios
    var estados = JSON.parse( wscajax.estados );

    //Añadimos los estados
    wscAddEstados( estados );

    jQuery('#estados').change(function(){
        var estado = this.value;
        WscAddMunicipios( estados, estado );
    });

    jQuery('#municipios').change(function(){

       jQuery.ajax({
            url: wscajax.url,
            type: 'POST',
            data: {
                action: wscajax.handler,
                callback: 'wsc_consultar_clima',
                data: {
                    nes: this.value,
                    nmun: jQuery(this).find(":selected").text()
                }
            },
            success: function( response ){
                jQuery('.response').html( response );
            }
        });       
    });

}


function wscAddEstados( estados ){
    for( estado in estados){
        jQuery('#estados').append( new Option(estado) );
    }
}

function WscAddMunicipios( estados, estado ){
    jQuery('#municipios').empty();
    estados[estado].forEach( municipio => {
        jQuery('#municipios').append( new Option( municipio, estado ) );
    });
}