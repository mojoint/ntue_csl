/*
for admin postman function
*/

(function($){
    $.postmanSend = function(result){
        if(0 == parseInt(result)){
            alert('寄出失敗');
        }else{
            alert('已經寄出' + result + ' 封電子郵件！');
        }
        $('#email-send').text($('#email-send').attr('orgtxt')).prop('disabled', false);
    }
    $('#main')
        .on('click','#email-send',function(){
            if($.trim($('#email-subject').val())==''){
                alert('請輸入信件主旨！');
                $('#email-subject').focus()
                return false;
            }
            if($.trim($('#email-body textarea').val())==''){
                alert('請輸入信件內容！');
                $('#mail-body textarea').focus()
                return false;
            }
            $('#email-send').text('發送中...').prop('disabled', true);
            mojo.ajax('admin','postman','emailSend',{'emailRcptTo':$('#email-rcptto').val(),'emailSubject':$('#email-subject').val(),'emailBody':$('#email-body textarea').val()});
        })
        ;


    $('document').ready(function(){

    });
})(jQuery);


