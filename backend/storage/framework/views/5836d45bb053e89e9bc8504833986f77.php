// Image preview
$('#f_image').on('change', function() {
    let file = this.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = e => $('#imagePreview').html(`<img src="${e.target.result}" style="width:110px;height:75px;object-fit:cover;border-radius:10px;border:2px solid #e0e3eb;box-shadow:0 2px 8px rgba(0,0,0,0.08);">`);
        reader.readAsDataURL(file);
    }
});

// Submit (Create / Update)
$('#submitBtn').on('click', function() {
    let id = $('#formModal').data('id');
    let btn = $(this);
    let originalText = btn.html();
    btn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Saving...').prop('disabled', true);

    let fd = new FormData();
    fd.append('_token', '<?php echo e(csrf_token()); ?>');
    if (id) fd.append('_method', 'PUT');

    fields.forEach(f => {
        let el = $('#f_' + f);
        if (!el.length) return;
        if (el.attr('type') === 'checkbox') {
            fd.append(f, el.is(':checked') ? 1 : 0);
        } else {
            fd.append(f, el.val() || '');
        }
    });

    // Handle includes/highlights textarea
    if ($('#f_includes_text').length) fd.set('includes_text', $('#f_includes_text').val());
    if ($('#f_highlights_text').length) fd.set('highlights_text', $('#f_highlights_text').val());

    // Image
    if ($('#f_image')[0] && $('#f_image')[0].files[0]) {
        fd.append('image_file', $('#f_image')[0].files[0]);
    }

    let url = id ? updateUrl.replace(':id', id) : storeUrl;

    $.ajax({
        url: url, type: 'POST', data: fd, processData: false, contentType: false,
        success: function(res) {
            success_noti(res.message);
            $('#formModal').modal('hide');
            resetModal();
            $('#dataTable').DataTable().ajax.reload(null, false);
            btn.html(originalText).prop('disabled', false);
        },
        error: function(xhr) {
            error_noti(xhr.responseJSON?.message ?? 'Failed to save');
            btn.html(originalText).prop('disabled', false);
        }
    });
});

// Edit
$(document).on('click', '.' + editClass, function() {
    let id = $(this).data('id');
    $.ajax({
        url: showUrl.replace(':id', id), type: 'GET',
        success: function(data) {
            fields.forEach(f => {
                let el = $('#f_' + f);
                if (!el.length) return;
                if (el.attr('type') === 'checkbox') {
                    el.prop('checked', !!data[f]);
                } else if (f === 'price' || f === 'per_night') {
                    el.val(data[f] ?? (data[f + '_cents'] ? data[f + '_cents'] / 100 : ''));
                } else if (f === 'includes_text') {
                    el.val(data.includes ? data.includes.join('\n') : '');
                } else if (f === 'highlights_text') {
                    el.val(data.highlights ? data.highlights.join('\n') : '');
                } else {
                    el.val(data[f] ?? '');
                }
            });

            // Update star rating UI if exists
            if ($('.star-btn').length && data.rating) {
                $('.star-btn').each(function() {
                    $(this).toggleClass('active', $(this).data('val') <= data.rating);
                });
            }

            // Update gradient preview if exists
            if ($('#gradientPreview').length && data.gradient_from) {
                $('#gradientPreview').css('background', `linear-gradient(135deg, ${data.gradient_from}, ${data.gradient_to})`);
            }

            if (data.image_url || data.image) {
                let imgUrl = data.image_url || data.image;
                if (imgUrl) $('#imagePreview').html(`<img src="${imgUrl}" style="width:110px;height:75px;object-fit:cover;border-radius:10px;border:2px solid #e0e3eb;box-shadow:0 2px 8px rgba(0,0,0,0.08);">`);
            }

            $('#formModal').data('id', data.id).modal('show');
            $('#modalTitle').html('<i class="bx bx-edit-alt me-2"></i>Edit ' + modalTitle);
        },
        error: () => error_noti('Unable to load record')
    });
});

// Delete
$(document).on('click', '.' + deleteClass, function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: 'This record will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bx bx-trash me-1"></i>Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: deleteUrl.replace(':id', id), type: 'POST',
            data: { _token: '<?php echo e(csrf_token()); ?>', _method: 'DELETE' },
            success: function(res) {
                Swal.fire({ title: 'Deleted!', text: res.message, icon: 'success', timer: 1500, showConfirmButton: false });
                $('#dataTable').DataTable().ajax.reload(null, false);
            },
            error: xhr => Swal.fire('Error!', xhr.responseJSON?.message ?? 'Failed to delete', 'error')
        });
    });
});

function resetModal() {
    fields.forEach(f => {
        let el = $('#f_' + f);
        if (!el.length) return;
        if (el.attr('type') === 'checkbox') {
            el.prop('checked', f === 'is_active' || f === 'is_published');
        } else if (el.is('select')) {
            el.val(el.find('option:first').val());
        } else {
            el.val(f === 'sort_order' ? '0' : (f === 'price_label' ? 'per person' : (f === 'read_time_min' ? '5' : (f === 'rating' ? '5' : ''))));
        }
    });
    $('#f_image').val('');
    $('#imagePreview').empty();
    $('#formModal').removeData('id');
    $('#modalTitle').html('<i class="bx bx-plus-circle me-2"></i>Add ' + modalTitle);

    // Reset star rating
    if ($('.star-btn').length) {
        $('.star-btn').addClass('active');
    }

    // Reset gradient preview
    if ($('#gradientPreview').length) {
        $('#f_gradient_from').val('#ff6b35');
        $('#f_gradient_to').val('#f7c948');
        $('#gradientPreview').css('background', 'linear-gradient(135deg, #ff6b35, #f7c948)');
    }
}

$('#formModal').on('hidden.bs.modal', resetModal);
<?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/master-data/_shared_js.blade.php ENDPATH**/ ?>