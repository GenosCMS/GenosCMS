$Form = {
    //
    prefix: '<span class="help-inline">',
    suffix: '</span>',
    //
    error: function(errors)
    {
        $.each(errors, function(field, error){
            var obj = $('#field_' + field);

            if (obj.attr('type') == 'radio' || obj.attr('type') == 'checkbox')
            {
                obj = obj.parent().parent();
            }
            else
            {
                obj = obj.parent();
            }

            obj.find('.help-inline').remove();

            if (error != null)
            {
                obj.parent().addClass('error');
                obj.append($Form.prefix + error + $Form.suffix)
            }
            else
            {
                obj.parent().removeClass('error');
            }
        })
    }
}