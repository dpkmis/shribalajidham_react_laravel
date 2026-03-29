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

// function alert_box(type,message){
// 	if(type == 'success'){
// 		var icon = 'bx bx-check-circle';
// 	}else if(type == 'error'){
// 		var icon = 'bx bx-x-circle';
// 	}else if(type == 'info'){
// 		var icon = 'bx bx-info-circle';
// 	}else{
// 		var icon = 'bx bx-error';
// 	}
// 	Lobibox.notify(type, {
// 		pauseDelayOnHover: true,
// 		size: 'mini',
// 		continueDelayOnInactiveTab: false,
// 		position: 'bottom right',
// 		icon: icon,
// 		msg: message
// 	});
// }


function alert_box(type, message) {
    let icon = {
        success: 'bx bx-check-circle',
        error: 'bx bx-x-circle',
        info: 'bx bx-info-circle'
    }[type] || 'bx bx-error';

    Lobibox.notify(type, {
        pauseDelayOnHover: true,
        size: 'mini',
        continueDelayOnInactiveTab: false,
        position: 'bottom right',
        icon: icon,
        msg: message
    });
}
