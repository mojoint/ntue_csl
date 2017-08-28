/*
for admin message function
*/

(function($){
    $.unReplyMsgQry = function(){
        mojo.ajax('admin','message','noReplyMsgQry');
    }

    $.showUnReplyQuest = function(data) {
        if(Array.isArray(data)){
            if( data.length>0){
                _tpl = ['',
                '<ul style="background-color:#efe9e9">',
                  '<li>提問內容<pre style="font-weight:bold;background-color:#efe9e9"><%=this.question_content%></pre></li>',
                  '<li style="text-align:right">提問者：<%=this.ins_cname%><%=this.cname%><%=this.username%> 提問時間：<%=this.insert_date%><button msgid="<%=this.message_id%>">回覆</button></li>',
                '</ul>'
                ].join('');
                $('#message-list').jqotesub(_tpl,data);
            }
        }else{
            alert('問題列表讀取失敗');
        }
    }
    $.showReplySave = function(data){
        $('#message-save').text($('#message-save').attr('orgtxt')).prop('disabled', false);
        if(1 == parseInt(data)){
            alert('存檔成功！');
            $.unReplyMsgQry();
            $('#message-cancel').trigger('click');
        }
    }
    $('#main')
        .on('click','#message-save',function(){
            if(typeof $('#message-save').data('msgid')=='string' && $('#message-save').data('msgid') == ''){
                alert('請先選擇欲回答之項目！');
                return false;
            }
            if($.trim($('#message-form textarea').val())==''){
                alert('請輸入回答內容！');
                $('#message-form textarea').focus()
                return false;
            }
            $('#message-save').text('存檔中...').prop('disabled', true);
            mojo.ajax('admin','message','replyMsgSave',{'msgid':$(this).data('msgid'),'replyContent':$('#message-form textarea').val()});
        })
        .on('click','#message-cancel',function(){
            $('#message-form textarea').val('');
            $('#message-list button').prop('disabled',false);
            $('#message-save').data('msgid','');
        })    
        .on('click','#message-list button',function(){
            _self = $(this);
            $('#message-list button').prop('disabled',true);
            $('#message-save').data('msgid',_self.attr('msgid'));
            $('#message-form textarea').focus();
        })
        ;


    $('document').ready(function(){
        $.unReplyMsgQry();    
        $('#message-save').data('msgid','');

    });
})(jQuery);


