/* mojo admin javascript */
(function($) {
  $(function() {
console.log('!!!');

    var mojoint = { 
      ver: '1.0.1',
      mojo: ($('body').attr('data-mojo') != "")? Base64.decode($('body').attr('data-mojo').substr(1, $('body').attr('data-mojo').length)) : "",
      dialog: $('#dialog'),
      errcode: $('body').attr('data-error'),
      errmsg: '',
      reg: {
        'username': /^[a-zA-Z0-9]{4,50}$/,
        'userpass': /^[a-zA-Z0-9!@#$%^&*`~\-_=+\\|;:'",<.>\/?\[{\]}]{4,80}$/
      }   
    };  
console.log(mojo);
console.log(mojoint);

    (mojo.watch_admin = function() {
consol.log('???');
      $('#btn-admin').on('click', function(e) {
        console.log('...');
      });
    })();
console.log(mojo.ver);

  });
})(jQuery);
