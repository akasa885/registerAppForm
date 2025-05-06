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

    const behaviorMembershipCheck = () => {
        const membershipNumberInput = $('#input-1');
        const membershipEmailInput = $('#input-2');
        const membershipCheckButton = $('#submit_check_member');
        const textStatusMember = $('#status-member');
        const inputNik = $('#input-nik-1');

        if (membershipCheckButton.length === 0) {
            return;
        }

        if (membershipNumberInput.length === 0 || membershipEmailInput.length === 0) {
            return;
        }

        membershipCheckButton.on('click', function (e) {
            e.preventDefault();
            let infoPage = window.pageRegHelper;
            let endSite = infoPage.end_site;
            endPoint = endSite + '/api/v1/member/verification/status'

            let membershipNumber = membershipNumberInput.val();
            let membershipEmail = membershipEmailInput.val();

            if (membershipNumber === '' || membershipEmail === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: infoPage.err_filled,
                });
                return;
            }

            axios.post(endPoint, {
                no_registration_member: membershipNumber,
                email: membershipEmail
            }).then((response) => {
                let data = response.data;
                if (data.success === true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                    });

                    textStatusMember.text(data.data.memberStatus);

                    if (data.data.identification_number !== null) {
                        inputNik.val(data.data.identification_number);
                        inputNik.prop('disabled', false);
                        inputNik.prop('readonly', true);

                    } else {
                        inputNik.val('');//clear value
                        inputNik.prop('disabled', false);
                        inputNik.attr('placeholder', 'Input NIK');
                        inputNik.removeClass('bg-gray-200');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                    });
                }
            }).catch((error) => {
                let data = error.response.data;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message,
                });
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