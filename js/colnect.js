// For change desirable information on
// main page without reloading
$(document).ready(function(){

    $('#check_site').click( function() {

        var url = $('#url').val() ;
        var tag  = $('#tag').val() ;
        var action = $(this).attr('id') ;

        if ( !checkUrl(url) ) return false ;
        if ( !checkEl(tag) ) return false ;

        $.ajax({
            url: '/colnect/lib/ajax.php',
            type: 'post',
            datatype: 'text',
            data:{
                    url: url,
                    tag: tag,
                    action: action,
                 },
            success: function(response){
                $('#info').html(response) ;
            },
            error: function(response, exeption){
                if (response) {
                    if (response.status === 0) {
                        alert('No connection is available.\nCheck yor network settings.');
                    } else if (response.status == 404) {
                        alert('Requested page "/ajax.php" not found. Error: 404');
                    } else if (response.status == 500) {
                        alert('Internal Server Error: 500.');
                    } else {
                        alert('Uncaught Error.\n' + response.responseText);
                    }
                }else
                    alert('An error occured on PDOStatement. Check the error.log file and Colnect.class.php module.') ;
            }
        });

    })
// Validation of URL field value
    function checkUrl(url) {
        var pattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/ ;

        if (!url) {
            alert('Field "Site" cannot be empty!') ;
            return false ;
        }

        if ( !pattern.test(url) ) {
            alert('URL entered is incorrect.') ;
            return false ;
        }
        return true ;
    }
// Validation of Element field value
    function checkEl(el) {
        var pattern = /^[a-zA-Z1-6]{1,10}$/ ;

        if (!el) {
            alert('Field "Element" cannot be empty!') ;
            return false ;
        }

        if ( !pattern.test(el) ) {
            alert('Element "'+el+'" does not exist.') ;
            return false ;
        }
        return true ;
    }

});