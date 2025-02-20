const formReg = (function () {

    const behaviorFormRegBtn = () => {
        const formRegBtn = $('#submit-register');
        formRegBtn.on('click', function (e) {
            e.preventDefault();
            let infoPage = window.pageRegHelper;
            Swal.fire({
                title: infoPage.warn_txt,
                text: infoPage.event_txt,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: infoPage.swal_ok,
                cancelButtonText: infoPage.swal_cancel,
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).prop('disabled', false);
                    $(this).text('Loading...');
                    $(this).closest('form').submit();
                } else {
                    $(this).prop('disabled', false);
                    $(this).text(infoPage.submit_txt);
                }
            });
        });
    }

    return {
        init: () => {
            behaviorFormRegBtn();
            behaviorMembershipCheck();
        },

    }
})();

document.addEventListener('DOMContentLoaded', function () {
    const scriptTag = document.getElementById('front_script');
    if (scriptTag) {
        let dataPage = scriptTag.getAttribute('data-page');
        if (dataPage === 'form_reg') {
            window.formReg = formReg;
            formReg.init();
        }
    }
});