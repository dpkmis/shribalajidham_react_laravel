$(function() {
	"use strict";
	// new PerfectScrollbar(".app-container"),
	// new PerfectScrollbar(".header-message-list"),
	// new PerfectScrollbar(".header-notifications-list"),


	    $(".mobile-search-icon").on("click", function() {
			$(".search-bar").addClass("full-search-bar")
		}),

		$(".search-close").on("click", function() {
			$(".search-bar").removeClass("full-search-bar")
		}),

		$(".mobile-toggle-menu").on("click", function() {
			$(".wrapper").addClass("toggled")
		}),
		



		$(".dark-mode").on("click", function() {

			if($(".dark-mode-icon i").attr("class") == 'bx bx-sun') {
				$(".dark-mode-icon i").attr("class", "bx bx-moon");
				$("html").attr("class", "light-theme")
			} else {
				$(".dark-mode-icon i").attr("class", "bx bx-sun");
				$("html").attr("class", "dark-theme")
			}

		}), 

		
		
        $(".toggle-icon").click(function() {
			$(".wrapper").hasClass("toggled") ? ($(".wrapper").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($(".wrapper").addClass("toggled"), $(".sidebar-wrapper").hover(function() {
				$(".wrapper").addClass("sidebar-hovered")
			}, function() {
				$(".wrapper").removeClass("sidebar-hovered")
			}))
		}),
        
		$(document).ready(function() {
			$(window).on("scroll", function() {
				$(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
			}), $(".back-to-top").on("click", function() {
				return $("html, body").animate({
					scrollTop: 0
				}, 600), !1
			})
		}),
		
		$(function() {
			for (var e = window.location, o = $(".metismenu li a").filter(function() {
					return this.href == e
				}).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
		}),
		
		
		$(function() {
			$("#menu").metisMenu()
		}), 
		
		$(".chat-toggle-btn").on("click", function() {
			$(".chat-wrapper").toggleClass("chat-toggled")
		}), $(".chat-toggle-btn-mobile").on("click", function() {
			$(".chat-wrapper").removeClass("chat-toggled")
		}),


		$(".email-toggle-btn").on("click", function() {
			$(".email-wrapper").toggleClass("email-toggled")
		}), $(".email-toggle-btn-mobile").on("click", function() {
			$(".email-wrapper").removeClass("email-toggled")
		}), $(".compose-mail-btn").on("click", function() {
			$(".compose-mail-popup").show()
		}), $(".compose-mail-close").on("click", function() {
			$(".compose-mail-popup").hide()
		}), 
		
		
		$(".switcher-btn").on("click", function() {
			$(".switcher-wrapper").toggleClass("switcher-toggled")
		}), $(".close-switcher").on("click", function() {
			$(".switcher-wrapper").removeClass("switcher-toggled")
		}), $("#lightmode").on("click", function() {
			$("html").attr("class", "light-theme")
		}), $("#darkmode").on("click", function() {
			$("html").attr("class", "dark-theme")
		}), $("#semidark").on("click", function() {
			$("html").attr("class", "semi-dark")
		}), $("#minimaltheme").on("click", function() {
			$("html").attr("class", "minimal-theme")
		}), $("#headercolor1").on("click", function() {
			$("html").addClass("color-header headercolor1"), $("html").removeClass("headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
		}), $("#headercolor2").on("click", function() {
			$("html").addClass("color-header headercolor2"), $("html").removeClass("headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
		}), $("#headercolor3").on("click", function() {
			$("html").addClass("color-header headercolor3"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
		}), $("#headercolor4").on("click", function() {
			$("html").addClass("color-header headercolor4"), $("html").removeClass("headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8")
		}), $("#headercolor5").on("click", function() {
			$("html").addClass("color-header headercolor5"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor3 headercolor6 headercolor7 headercolor8")
		}), $("#headercolor6").on("click", function() {
			$("html").addClass("color-header headercolor6"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor3 headercolor7 headercolor8")
		}), $("#headercolor7").on("click", function() {
			$("html").addClass("color-header headercolor7"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor3 headercolor8")
		}), $("#headercolor8").on("click", function() {
			$("html").addClass("color-header headercolor8"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor3")
		})
		
	
});


// ✅ Top-level function
function getTableFilters(table) {
    let params = {};
    table.columns().every(function(index) {
        const searchValue = this.search();
        if (searchValue) {
            params[`columns[${index}][search][value]`] = searchValue;
        }
    });
    return params;
}




// datatable-helper.js
function initDataTable(config) {
    // Build buttons array
    let buttonsArr = [];

    // Add button only if modalSelector exists
    if (config.modalSelector) {
        buttonsArr.push({
            text: (config.moduleName || 'Record'),
            className: 'btn btn-primary',
            action: function() {
                // Clear modal form before showing
                const $modal = $(config.modalSelector);
                $modal.find('input[type="text"], textarea').val('');
                $modal.find('input[type="checkbox"]').prop('checked', false);
                
                $modal.find('button[type="button"]').each(function() {
                    const btnText = $(this).text().trim().toLowerCase();
                    if (btnText === 'update' || btnText === 'save' || btnText === 'submit') {
                        $(this).text('Submit');
                    }
                });
                
                $modal.removeData('id');
                $modal.find('.modal-title').text('Add Language');
                
                // Reset dirty flag
                if (typeof isFormDirty !== 'undefined') {
                    isFormDirty = false;
                }

                // Show modal
                $modal.modal('show');
            }
        });
    }

    buttonsArr.push({
        text: '<i class="fas fa-filter"></i> Filter',
        className: 'btn btn-outline-secondary',
        action: function(e, dt, node) {
            setupFilterRow(config, dt, node);
        }
    });
    // Always add Filter, Excel, Print buttons
    if (config.enableCSV !== false) {   // default true
        buttonsArr.push({
            text: '<i class="fa-solid fa-file-csv"></i> CSV',
            className: 'btn btn-outline-secondary',
            action: function (e, dt, node) {
                let params = getTableFilters(dt);
                let query = $.param(params);
                window.location = config.ajaxUrl + '?export=csv&' + query;
            }
        });
    }

    // Conditionally add Excel
    if (config.enableExcel === true) {  // default false
        buttonsArr.push({
            extend: 'excel',
            text: '<i class="fa-solid fa-table"></i> Excel',
            title: config.fristrow,
            filename: config.filename || 'export',
            sheetName: config.fristrow || 'Export',
            exportOptions: {
                columns: ':not(:last-child)',
                modifier: { search: 'applied', order: 'applied' },
                format: {
                    body: function(data, row, column, node) {
                        if ($(node).find('span.badge').length) {
                            return $(node).text().trim();
                        }
                        return $('<div>').html(data).text();
                    }
                }
            }
        });
    }


    return $(config.selector).DataTable({
        processing: false,
        serverSide: true,
        autoWidth: false,
        ajax: config.ajaxUrl,
        columns: config.columns,
        columnDefs: config.columnDefs || [],
        createdRow: config.createdRow || function(){},
        dom: "<'d-flex justify-content-end'B>rtip",
        buttons: buttonsArr,
        drawCallback: function() {
            $(config.selector + ' tbody tr.loader-row').remove();
            $(config.selector + ' tbody tr').show();
        }
    });
}

// Helper function to setup filter row
function setupFilterRow(config, table, node) {
    let $btn = $(node);
    if ($(config.selector + " thead tr.filter-row").length) {
        $(config.selector + " thead tr.filter-row").toggle();
        $btn.toggleClass("btn-secondary active btn-outline-secondary");
        return;
    }

    let filterRow = $('<tr class="filter-row"></tr>');
    config.columns.forEach(col => {
        if (col.filter === 'none') {
            filterRow.append('<th></th>');
        } else if (col.filter === 'select') {
            let options = col.options.map(opt => `<option value="${opt.value}">${opt.label}</option>`);
            filterRow.append(`<th><select class="form-select form-select-sm">${options}</select></th>`);
        } else if (col.filter === 'date') {
            filterRow.append('<th><input type="text" class="form-control form-control-sm dateFilter" autocomplete="off" /></th>');
        } else {
            filterRow.append('<th><input type="text" class="form-control form-control-sm" placeholder="Search" /></th>');
        }
    });
    $(config.selector + ' thead').append(filterRow);

    // Initialize select2 for select filters
    $(config.selector + ' .filter-row select').select2({ theme: "bootstrap-5", width: '100%' });
        // Default "All Time" range
        let defaultStart = moment('01-10-2025'); // very old date
        let defaultEnd = moment();               // today

        // Initialize daterangepicker for date inputs
        $(config.selector + ' .dateFilter').daterangepicker({
            autoUpdateInput: true,           // input shows default range
            startDate: defaultStart,
            endDate: defaultEnd,
            opens: 'right',
            locale: { 
                format: 'DD-MM-YYYY', 
                cancelLabel: 'Clear' 
            },
            ranges: {
                'All Time': [moment('01-10-2025'), moment()],
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Week': [moment().startOf('week'), moment().endOf('week')],
                'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            }
        });

        // Set input to default "All Time" range
        $(config.selector + ' .dateFilter').val(
            defaultStart.format('DD-MM-YYYY') + ' - ' + defaultEnd.format('DD-MM-YYYY')
        );

        // Update input when user applies a range
        // $(config.selector + ' .dateFilter').on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        //     $(this).trigger('change');
        // });

        // // Clear input when canceled
        // $(config.selector + ' .dateFilter').on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        //     $(this).trigger('change');
        // });

    // Apply change/enter for filters
    $(config.selector + ' .filter-row input, ' + config.selector + ' .filter-row select').on('change keypress', function(e){
        // alert("here");
        if(e.type === 'change' || e.which === 13){
            let tbody = $(config.selector + ' tbody');
            tbody.find('tr').hide();
            if(!tbody.find('tr.loader-row').length){
                tbody.append('<tr class="loader-row"><td colspan="'+config.columns.length+'" class="text-center py-5">Loading...</td></tr>');
            }
            let colIndex = $(this).closest('th').index();
            table.column(colIndex).search(this.value).draw();
        }
    });

    
}

