$(document).ready(function (){
    // Chosen
	$('select').chosen({
		disable_search_threshold : 10,
		allow_single_deselect : true
	});
    // Frase autocomplete
    $('.phrase').keyup(function (){
         var value = $(this).val();
         var id = $(this).attr('id');
         
         $('.phrase_' + id).val('setting_' + value); 
    });
    // Frase group
    $('.phrase_group').keyup(function (){
         var value = $(this).val();
         var id = $(this).attr('id');
         
         $('.phrase_' + id).val('setting_group_' + value); 
    });
    
    // Frase group
    $('.phrase_menu').keyup(function (){
         var value = $(this).val();
         var id = $(this).attr('id');
         
         $('.phrase_' + id).val(value); 
    });
    
    $('.rewrite').keyup(function (){
        var module = $('#module').val();
        var value = $(this).val();
        var id = $(this).attr('id');
        
        value = (id == 'replacement' && value == '') ? 'index' : value;
        
        $('#rewrite_' + id).text(module + '/' + value)
    })
    
    $('.rewrite').keyup();
});


function showDiv(id)
{
    $('#' + id).toggle();
}