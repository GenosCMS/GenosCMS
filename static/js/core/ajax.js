/* ===========================================================
 * core/ajax.js v1.0.0
 * {@link}
 * Author: Ivan Molina Pavana
 * ========================================================== */
! function ($) {

    $ajaxCall = function (e) {
        // Nuestras variables
        var $this = $(e.target)
        , call = $this.data('ajax')
        , isForm = ($this.data('form') ? true : false)
        
        // Form Ajax
        if ( ! call && isForm)
        {
            call = $this.attr('action')
            call = call && call.replace(/.*(?=#[^\s]*$)/, '') // ie7 :/
        }
        
        // Tipo de solocitud
        var type = (!$this.data('type')) ? 'POST' : $this.data('type');
        
        // Parámetros
        var params = (isForm ? $getForm($this.get(0)) : new FormData());
        
        // Añadir parámetros extra.
        if ($this.data('extra'))
        {
            var extra = $this.data('extra').split('&');
            $.each(extra, function (i, val){
                val = val.split('=');
                params.append(val[0], val[1]);
            })
        }
        
        // Realizar solicitud
        $.ajax({
            type: type,
            url: call,
            data: params,
            dataType: 'script',
            cache: false,
            contentType: false,
            processData: false
        });
        
        // No seguir el enlace
        return false;
    }
    
    // Obtener parámetros de un formulario
    $getForm = function (objForm) {
        // Recojemos los campos tipo Input
        var formData = new FormData();
        $.each($(objForm).find("input[type='file']"), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        
        // Demás campos..
        var params = $(objForm).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });

        return formData;
    }
    
    // Data API
    $(document).on('click.ajax.data-api', '[data-ajax]', $ajaxCall);
    
    $(document).on('submit.ajax.data-api', '[data-form]', $ajaxCall);
     
}(window.jQuery);