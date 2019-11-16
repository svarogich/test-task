$(function () {
    let $form = $('form');
    $form.submit((e) => {
        e.preventDefault();
        let email = $('#email').val();
        $.ajax({
            type: "POST",
            url: '/check-email',
            data: 'email=' + email,
            success: (data) => {
                if (true === data.canBeUsed) {
                    $form[0].submit();
                } else {
                    alert('This email already in use');
                }
            },
            dataType: 'json'
        });
    })
});