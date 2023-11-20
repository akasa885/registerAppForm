// ajax header setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function authSession () {
    // check after idle time, if user is still logged in
    var idleTime = 0;
    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

    $(document).on('mousemove keydown scroll', function () {
        idleTime = 0;
    });

    function timerIncrement() {
        idleTime = idleTime + 1;
        if (idleTime > 15) { // 15 minutes
            clearInterval(idleInterval);
            $.ajax({
                url: '/dpanel/check-session',
                type: 'POST',
                data: { session: 'auth'},
                success: function (data) {
                    if (data.status === 'success') {
                        idleInterval = setInterval(timerIncrement, 60000);
                    } else {
                        window.location.href = data.redirect;
                    }
                }
            });
        }
    }
}

// Path: public/js/session.js
// run authSession function
authSession();