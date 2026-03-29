/* Default Notifications */
function default_noti(message) {
	Lobibox.notify('default', {
		pauseDelayOnHover: true,
		size: 'mini',
		continueDelayOnInactiveTab: false,
		position: 'bottom right',
		msg: message
	});
}

function info_noti(message) {
	Lobibox.notify('info', {
		pauseDelayOnHover: true,
		size: 'mini',
		continueDelayOnInactiveTab: false,
		position: 'bottom right',
		icon: 'bx bx-info-circle',
		msg: message
	});
}

function warning_noti(message) {
	Lobibox.notify('warning', {
		pauseDelayOnHover: true,
		size: 'mini',
		continueDelayOnInactiveTab: false,
		position: 'bottom right',
		icon: 'bx bx-error',
		msg: message
	});
}

function error_noti(message) {
	Lobibox.notify('error', {
		pauseDelayOnHover: true,
		size: 'mini',
		continueDelayOnInactiveTab: false,
		position: 'bottom right',
		icon: 'bx bx-x-circle',
		msg: message
	});
}

function success_noti(message) {
	Lobibox.notify('success', {
		pauseDelayOnHover: true,
		size: 'mini',
		continueDelayOnInactiveTab: false,
		position: 'bottom right',
		icon: 'bx bx-check-circle',
		msg: message
	});
}

function alert_box(type,message){
	if(type == 'success'){
		var icon = 'bx bx-check-circle';
	}else if(type == 'error'){
		var icon = 'bx bx-x-circle';
	}else if(type == 'info'){
		var icon = 'bx bx-info-circle';
	}else{
		var icon = 'bx bx-error';
	}
	Lobibox.notify(type, {
		pauseDelayOnHover: true,
		size: 'mini',
		continueDelayOnInactiveTab: false,
		position: 'bottom right',
		icon: icon,
		msg: message
	});
}

function custom_swal(text, type, html = '') {
    return Swal.fire({
        title: "Are you sure?",
        text: text,
        html: html,
        icon: type,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, proceed",
        cancelButtonText: "No, cancel",
        didOpen: () => {
            const confirmBtn = Swal.getConfirmButton();
            const popup = Swal.getPopup();

            // Find checkbox only if present
            const checkbox = popup ? popup.querySelector('#confirmCheckbox') : null;

            // If checkbox exists → bind logic
            if (checkbox) {
                confirmBtn.disabled = true;

                checkbox.addEventListener('change', () => {
                    confirmBtn.disabled = !checkbox.checked;
                });
            }
        }
    }).then((result) => {
        return result.isConfirmed;
    });
}

