$(function(){

    const MSG_TEXT_MAX = '20文字以内で入力してください。';
    const MSG_EMPYT = '入力必須です。';
    const MSG_EMIL_TYPE = 'emailの形式ではありません。';
    const MSG_TEXTAREA_MAX = '100文字以内で入力してください。';

    $(".valid-text").keyup(function(){

        var form_g = $(this).closest('.form-group');

        if ($(this).val().length > 20){
            form_g.removeClass('has-success').addClass('has-error');
            form_g.find('.help-block').text(MSG_TEXT_MAX);
        }
        else {
            form_g.removeClass('has-error').addClass('has-success');
            form_g.find('.help-block').text('');
        }
    });

    $(".valid-email").keyup(function(){
        var form_g = $(this).closest('.form-group');

        if ($(this).val().length === 0){
            form_g.removeClass('has-success').addClass('has-error');
            form_g.find('.help-block').text(MSG_EMPYT);
        }
        else if ($(this).val().length > 50 || !$(this).val().match(/^([a-zA-Z0-9])+(a-zA-Z0-9\._-)*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/)) {
            form_g.removeClass('has-success').addClass('has-error');
            form_g.find('.help-block').text(MSG_EMIL_TYPE);
        }
        else {
            form_g.removeClass('has-error').addClass('has-success');
            form_g.find('.help-block').text('');
        }
    });

    $(".valid-textarea").keyup(function(){
        var form_g = $(this).closest('.form-group');

        if ($(this).val().length === 0) {
            form_g.removeClass('has-success').addClass('has-error');
            form_g.find('.help-block').text(MSG_EMPYT);
        }
        else if ($(this).val().length > 100) {
            form_g.removeClass('has-success').addClass('has-error');
            form_g.find('.help-block').text(MSG_TEXTAREA_MAX);
        }
        else {
            form_g.removeClass('has-error').addClass('has-success');
            form_g.find('.help-block').text('');
        }
    });
});