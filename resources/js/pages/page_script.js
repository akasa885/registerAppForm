const { default: axios } = require("axios");

const memberListPageMain = (function () {
    let showenModal = null;

    const memberLog = (memberId) => {
        showenModal.modal('show');

        let bodyContent = showenModal.find('.modal-body');

        bodyContent.html('Loading...');

        let url = window.routeMemberDetail.event_log;

        url = url.replace(':id', memberId);

        axios.get(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            }
        }).then((response) => {
            let respData = response.data;
            bodyContent.html(respData.data);
        }).catch((error) => {
            bodyContent.html('Error loading member log');
        });
    }

    return {
        init: () => {
            //
        },
        getMemberEventLog: (memberId) => {
            showenModal = $('#ModalLogEvent');
            memberLog(memberId);
        },
    };
})();

document.addEventListener('DOMContentLoaded', function () {
    const scriptTag = document.getElementById('page_script');
    if (scriptTag) {
        let dataPage = scriptTag.getAttribute('data-page');
        if (dataPage === 'member_list') {
            window.memberListPageMain = memberListPageMain;
        }
    }
});
