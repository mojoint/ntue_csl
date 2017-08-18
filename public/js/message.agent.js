/*
for agent message function
*/

(function($){
    $.histMsgQry = function(){
        rdata = mojo.ajax('agent','message','histMsgQry');
    }

    $.showHisQuest = function(data) {
        if(Array.isArray(data)){
            if( data.length>0){
                _tpl = ['',
                '<ul style="background-color:#efe9e9">',
                  '<li>提問內容<pre style="font-weight:bold;background-color:#efe9e9"><%=this.question_content%></pre></li>',
                  '<li style="text-align:right">提問時間：<%=this.insert_date%></li>',
                  '<%if(this.reply_yn=="1"){%>',
                  '<li>管理員回覆<pre style="font-weight:bold;background-color:#e62929"><%=this.reply_content%></pre></li>',
                  '<li style="text-align:right">回覆時間：<%=this.reply_date%></li>',
                  '<%}%>',
                '</ul>'
                ].join('');
                $('#message-list').jqotesub(_tpl,data);
            }
        }else{
            alert('過往提問讀取失敗');
        }
    }
    $.showQuesSave = function(data){
        $('#message-save').text($('#message-save').attr('orgTxt')).prop('disabled', false);
        if(parseInt(data)){
            alert('存檔成功！');
            $.histMsgQry();
            $('#message-cancel').trigger('click');
        }
    }
    $('#main')
        .on('click','#message-save',function(){
            if($.trim($('#message-form textarea').val())==''){
                
                alert('想告訴管理員什麼事情呢？');
                $('#message-form textarea').focus();
                return false;
            }
            $('#message-save').text('存檔中...').prop('disabled', true);
            mojo.ajax('agent','message','quesSave',{'questionContent':$('#message-form textarea').val()});
        })
        .on('click','#message-cancel',function(){
            $('#message-form textarea').val('');
        })    
        ;


    $('document').ready(function(){
        console.log('document ready');
        $.histMsgQry();    

    });
})(jQuery);


