/**
 * Created by hashimoto on 6/1/2017.
 */
$=require('jquery');
$(function($) {
    var $form = $('#form');
    $form.submit(function(event) {
        console.log('called');
        event.preventDefault();
        $.ajax({
            url: 'newUser',
            type: 'GET',
            data: {"UID":$('#input').val()},
            complete: function(xhr, status){
                console.log(xhr);
                console.log(status);
            },
            error: function(xhr,status,err){
                console.log(err);
            }
        });
    });

});