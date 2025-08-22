/**
 * Overrides carrier shipping with Table Rate Shipping
 *
 * Table Rate Shipping by Kahanit(https://www.kahanit.com/) is licensed under a
 * Creative Creative Commons Attribution-NoDerivatives 4.0 International License.
 * Based on a work at https://www.kahanit.com/.
 * Permissions beyond the scope of this license may be available at https://www.kahanit.com/.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/4.0/.
 *
 * @author    Amit Sidhpura <amit@kahanit.com>
 * @copyright 2016 Kahanit
 * @license   http://creativecommons.org/licenses/by-nd/4.0/
 */

(function ($) {
    var url = '';

    var trscontent = '';

    var trstableproducts = '';

    var trszones = '';

    var modalimportexportcsv = '';

    var modaladdeditcarrier = '';

    var modaladdeditzone = '';

    $.fn.AdminTableRateShipping = function (options) {
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editableform.buttons = '';

        // variable initializtion
        url = options.url;
        trscontent = $('#trs-content');
        trstableproducts = $('#trs-table-products');
        trszones = $('#trs-zones');
        modalimportexportcsv = $("#modalimportexportcsv");
        modaladdeditcarrier = $("#modaladdeditcarrier");
        modaladdeditzone = $("#modaladdeditzone");

        // initialize zone tabs
        trszones.tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
        trszones.find('> ul > li').removeClass("ui-corner-top").addClass("ui-corner-left");

        // products datatable initailization
        trstableproducts.dataTable({
            "ajax": {
                "url": url + '&method=getProducts',
                "data": function (data) {
                    data.id_carrier = trscontent.find('#trs-select-carrier').val();
                }
            },
            "processing": true,
            "serverSide": true,
            "pageLength": 25,
            "order": [[0, "ASC"]],
            "columns": [
                {"data": "id_product", "className": "text-center"},                             // 0
                {"data": "name"},                                                               // 1
                {"data": "reference"},                                                          // 2
                {"data": "active", "className": "text-center"},                                 // 3
                {"data": "hascarrier", "className": "text-center"}                              // 4
            ],
            "columnDefs": [{
                render: function (data, type, row) {
                    return ((!data)
                        ? '<a class="trs-enable list-action-enable action-disabled" href="javascript:void(0);" data-id="' +
                        row.id_product + '"><i class="glyphicon glyphicon-remove"></i></a>'
                        : '<a class="trs-disable list-action-enable action-enabled" href="javascript:void(0);" data-id="' +
                        row.id_product + '"><i class="glyphicon glyphicon-ok"></i></a>');
                },
                targets: 3
            }, {
                render: function (data, type, row) {
                    return ((!data)
                        ? '<a class="trs-select list-action-enable action-disabled" href="javascript:void(0);" data-id="' +
                        row.id_product + '"><i class="glyphicon glyphicon-remove"></i></a>'
                        : '<a class="trs-deselect list-action-enable action-enabled" href="javascript:void(0);" data-id="' +
                        row.id_product + '"><i class="glyphicon glyphicon-ok"></i></a>');
                },
                targets: 4,
                orderable: false
            }],
            "initComplete": function (settings, json) {
            },
            "rowCallback": function (row, data, index) {
            },
            "drawCallback": function (settings) {
            }
        });

        // event attachment
        // trscontent.find('#trs-add-carrier').on('click', onAddCarrierClick);
        // trscontent.find('#trs-edit-carrier').on('click', onEditCarrierClick);
        // trscontent.find('#trs-delete-carrier').on('click', onDeleteCarrierClick);
        trscontent.find('#trs-select-carrier').on('change', onSelectCarrierChange);
        trscontent.find('.alert-close').on('click blur', onAlertClose);
        // trscontent.find('#trs-add-zone').on('click', onAddZoneClick);
        trscontent.find('#trs-importcsv').on('click', onImportCSVClick);
        trscontent.find('#trs-exportcsv').on('click', onExportCSVClick);
        trscontent.find('#trs-selectproducts').on('click', onSelectProductsClick);
        trscontent.find('#trs-reload').on('click', onReloadClick);
        trstableproducts.on('click', '.trs-select', onSelectClick);
        trstableproducts.on('click', '.trs-deselect', onDeselectClick);
        modalimportexportcsv.on('click', '.trs-importexportcsv-form-submit', onImportExportCSVSubmitClick);
        // modaladdeditcarrier.on('click', '.trs-addeditcarrier-form-submit', onAddEditCarrierSubmitClick);
        // modaladdeditzone.on('click', '.trs-addeditzone-form-submit', onAddEditZoneSubmitClick);

        // attach zone events
        trszones.find('.ui-tabs-panel').each(function () {
            attachZoneEvents($(this).attr('data-zone'));
        });

        // set focus to select carrier
        trscontent.find('#trs-select-carrier').focus();

        // jquery file upload
        modalimportexportcsv.find('#fileupload').fileupload({
            url: url + '&method=processAttachment',
            acceptFileTypes: /(\.|\/)(csv)$/i
        });
        modalimportexportcsv.find('#fileupload').addClass('fileupload-processing');
        $.ajax({
            method: 'get',
            url: modalimportexportcsv.find('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: modalimportexportcsv.find('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
        });

        // set zones, rulegroups and rules sortable
        trszones.find('.ui-tabs-nav').attr('data-entity', 'zone');
        trszones.find('.ui-tabs-nav').sortable({axis: 'y', delay: 150, update: onUpdateOrder});
    };

    var attachZoneEvents = function ($id_zone) {
        var zone_tab = $('#zone-' + $id_zone),
            zone_panel = $('#tabs-' + $id_zone);

        // zone table rates datatable initialization
        zone_panel.find('.trs-table-rates').dataTable({
            "ajax": {
                "url": url + '&method=getRuleGroups',
                "data": function (data, settings) {
                    data.id_carrier = trscontent.find('#trs-select-carrier').val();
                    data.id_zone = $(settings.nTable).attr('data-zone');
                }
            },
            "processing": true,
            "serverSide": true,
            "pageLength": 20,
            "ordering": false,
            "columns": [
                {"data": "id_country"}, 											    // 0
                {"data": "id_state"}, 								                    // 1
                {"data": "dest_city"}, 												    // 2
                {"data": "dest_zip"}, 												    // 3
                {"data": "id_country", "class": "text-center"}                          // 4
            ],
            "columnDefs": [{
                render: function (data, type, row) {
                    return '<a href="#" class="x-editable-country" data-pk="0" ' +
                        'data-value="' + row.id_country + '" ' +
                        'data-group="' + row.id_group + '" ' +
                        'data-state="' + row.id_state + '" ' +
                        'data-city="' + row.dest_city.replace(/"/g, '&quot;') + '" ' +
                        'data-zip="' + row.dest_zip.replace(/"/g, '&quot;') + '">'
                        + ((row.country != '*') ? row.country + ' (' + row.c_iso_code + ')' : '*') + '</a>';
                },
                targets: 0
            }, {
                render: function (data, type, row) {
                    return '<a href="#" class="x-editable-state" data-pk="0" ' +
                        'data-value="' + row.id_state + '" ' +
                        'data-group="' + row.id_group + '" ' +
                        'data-country="' + row.id_country + '" ' +
                        'data-city="' + row.dest_city.replace(/"/g, '&quot;') + '" ' +
                        'data-zip="' + row.dest_zip.replace(/"/g, '&quot;') + '">'
                        + ((row.state != '*') ? row.state + ' (' + row.s_iso_code + ')' : '*') + '</a>';
                },
                targets: 1
            }, {
                render: function (data, type, row) {
                    return '<a href="#" class="x-editable-city" data-pk="0" ' +
                        'data-value="' + row.dest_city.replace(/"/g, '&quot;') + '" ' +
                        'data-group="' + row.id_group + '" ' +
                        'data-country="' + row.id_country + '" ' +
                        'data-state="' + row.id_state + '" ' +
                        'data-zip="' + row.dest_zip.replace(/"/g, '&quot;') + '">'
                        + data + '</a>';
                },
                targets: 2
            }, {
                render: function (data, type, row) {
                    return '<a href="#" class="x-editable-zip" data-pk="0" ' +
                        'data-value="' + row.dest_zip.replace(/"/g, '&quot;') + '" ' +
                        'data-group="' + row.id_group + '" ' +
                        'data-country="' + row.id_country + '" ' +
                        'data-state="' + row.id_state + '" ' +
                        'data-city="' + row.dest_city.replace(/"/g, '&quot;') + '">'
                        + data.split(',').join('<br/>') + '</a>';
                },
                targets: 3
            }, {
                render: function (data, type, row) {
                    return '<div class="btn-group">' +
                        '   <button class="btn btn-danger trs-delete" data-type="single" data-entity="rulegroup" ' +
                        '       data-zone="' + row.id_zone + '"' +
                        '       data-group="' + row.id_group + '" ' +
                        '       data-country="' + row.id_country + '" ' +
                        '       data-state="' + row.id_state + '"' +
                        '       data-city="' + row.dest_city.replace(/"/g, '&quot;') + '"' +
                        '       data-zip="' + row.dest_zip.replace(/"/g, '&quot;') + '">' +
                        '       <i class="glyphicon glyphicon-remove"></i>' +
                        '   </button>' +
                        '   <button class="btn btn-default trs-show-rules"' +
                        '       data-zone="' + row.id_zone + '"' +
                        '       data-group="' + row.id_group + '" ' +
                        '       data-country="' + row.id_country + '" ' +
                        '       data-state="' + row.id_state + '"' +
                        '       data-city="' + row.dest_city.replace(/"/g, '&quot;') + '"' +
                        '       data-zip="' + row.dest_zip.replace(/"/g, '&quot;') + '">' +
                        '       <i class="glyphicon glyphicon-chevron-down"></i>' +
                        '   </button>' +
                        '</div>';
                },
                targets: 4
            }],
            "initComplete": function (settings) {
                $(settings.nTable).find('> tbody').attr('data-entity', 'rulegroup');
                $(settings.nTable).find('> tbody').sortable({
                    items: 'tr:not(.trs-table-rates-tr)',
                    axis: 'y',
                    delay: 150,
                    start: onSortStart,
                    update: onUpdateOrder
                });
                $(settings.nTable).closest('.dataTables_wrapper').find('.dataTables_info').before('<div class="btn-group">\
                    <a class="btn btn-default trs-delete" href="javascript:void(0);" data-type="all" data-entity="rulegroup"\
                        data-zone="' + $(settings.nTable).attr('data-zone') + '">\
                        <i class="glyphicon glyphicon-remove"></i> <span>Delete all</span>\
                    </a>\
                    <a class="btn btn-default trs-reload" href="javascript:void(0);">\
                        <i class="glyphicon glyphicon-refresh"></i> <span>Reload</span>\
                    </a>\
                </div>');
            },
            "rowCallback": function (row, data, index) {
                $(row).attr('id', data.id_group);
                $(row).find('.trs-show-rules').on('click', onShowRulesClick);
            },
            "drawCallback": function (settings) {
                var api = new $.fn.dataTable.Api(settings);

                // make country, state, city and zip editable
                $(settings.nTable).find('.x-editable-country').editable({
                    type: 'select',
                    source: [],
                    url: url,
                    onblur: 'submit',
                    params: function (params) {
                        params.method = 'updateCountryStateCity';
                        params.update = 'id_country';
                        params.id_carrier = trscontent.find('#trs-select-carrier').val();
                        params.id_zone = $(settings.nTable).attr('data-zone');
                        params.id_group = $(this).attr('data-group');
                        params.id_country = $(this).attr('data-value');
                        params.id_state = $(this).attr('data-state');
                        params.dest_city = $(this).attr('data-city');
                        params.dest_zip = $(this).attr('data-zip');
                        return params;
                    },
                    success: updateRuleGroupData,
                    display: function (value, sourceData, response) {
                        return false;
                    }
                });
                $(settings.nTable).find('.x-editable-state').editable({
                    type: 'select',
                    source: [],
                    url: url,
                    onblur: 'submit',
                    params: function (params) {
                        params.method = 'updateCountryStateCity';
                        params.update = 'id_state';
                        params.id_carrier = trscontent.find('#trs-select-carrier').val();
                        params.id_zone = $(settings.nTable).attr('data-zone');
                        params.id_group = $(this).attr('data-group');
                        params.id_country = $(this).attr('data-country');
                        params.id_state = $(this).attr('data-value');
                        params.dest_city = $(this).attr('data-city');
                        params.dest_zip = $(this).attr('data-zip');
                        return params;
                    },
                    success: updateRuleGroupData,
                    display: function (value, sourceData, response) {
                        return false;
                    }
                });
                $(settings.nTable).find('.x-editable-city').editable({
                    type: 'text',
                    url: url,
                    onblur: 'submit',
                    params: function (params) {
                        params.method = 'updateCountryStateCity';
                        params.update = 'dest_city';
                        params.id_carrier = trscontent.find('#trs-select-carrier').val();
                        params.id_zone = $(settings.nTable).attr('data-zone');
                        params.id_group = $(this).attr('data-group');
                        params.id_country = $(this).attr('data-country');
                        params.id_state = $(this).attr('data-state');
                        params.dest_city = $(this).attr('data-value');
                        params.dest_zip = $(this).attr('data-zip');
                        return params;
                    },
                    success: updateRuleGroupData,
                    display: function (value, sourceData, response) {
                        return false;
                    }
                });
                $(settings.nTable).find('.x-editable-zip').editable({
                    type: 'textarea',
                    url: url,
                    onblur: 'submit',
                    params: function (params) {
                        params.method = 'saveRules';
                        params.action = 'edit';
                        params.id_carrier = trscontent.find('#trs-select-carrier').val();
                        params.records = [{
                            'id_zone': $(settings.nTable).attr('data-zone'),
                            'id_group': $(this).attr('data-group'),
                            'id_country': $(this).attr('data-country'),
                            'id_state': $(this).attr('data-state'),
                            'dest_city': $(this).attr('data-city'),
                            'dest_zip_before': $(this).attr('data-value'),
                            'dest_zip': params.value,
                            'price': 0,
                            'comment': '{{dummy}}'
                        }];
                        return params;
                    },
                    success: function (response, newValue) {
                        showAjaxRequestMessage(response.status, response.message);
                        if (response.status == 'success') {
                            newValue = (newValue == '') ? '*' : newValue;
                            newValue = newValue.split('\n').join(',');
                            $(this).attr('data-value', newValue);
                            $(this).html(newValue.split(',').join('<br/>'));
                            $(this).closest('tr')
                                .find('.x-editable-country, .x-editable-state, .x-editable-city, .x-editable-zip, .trs-delete, .trs-show-rules')
                                .attr('data-zip', newValue);
                            if ($(this).closest('tr').next().hasClass('trs-table-rates-tr')) {
                                $(this).closest('tr').find('.trs-show-rules i').addClass('glyphicon-chevron-down');
                                $(this).closest('tr').find('.trs-show-rules i').removeClass('glyphicon-chevron-up');
                                $(this).closest('tr').next().remove();
                            }
                        }
                        return {
                            success: response.status == 'success',
                            pk: 0,
                            newValue: $(this).attr('data-value')
                        };
                    },
                    display: function (value, sourceData, response) {
                        return false;
                    }
                });

                // country and state events
                $(settings.nTable).find('.x-editable-country').on('shown', function (e, editable) {
                    makeSelect2Country(
                        editable.input.$input,
                        [{
                            id: $(this).attr('data-value'),
                            text: $(this).text()
                        }]
                    );
                });
                $(settings.nTable).find('.x-editable-state').on('shown', function (e, editable) {
                    makeSelect2State(
                        editable.input.$input,
                        [{
                            id: $(this).attr('data-value'),
                            text: $(this).text()
                        }],
                        $(this).closest('tr').find('.x-editable-country').attr('data-value')
                    );
                });
                $(settings.nTable).find('.x-editable-zip').on('shown', function (e, editable) {
                    editable.input.$input.val(editable.input.$input.val().split(',').join('\n'));
                });
                $(settings.nTable).find('.x-editable-country,.x-editable-state,.x-editable-city,.x-editable-zip').on('focus', function () {
                    $(this).trigger('click');
                });

                // remove if form exists
                if ($(settings.nTable).next().hasClass('trs-table-rate-add-form')) {
                    $(settings.nTable).next().remove();
                }
                var template_tr = '<tr data-index="{{index}}">\
                            <td class="td-id_country"><select class="form-control select2-country" name="add-form-data[{{index}}][id_country]"></select></td>\
                            <td class="td-id_state"><select class="form-control select2-state" name="add-form-data[{{index}}][id_state]"></select></td>\
                            <td class="td-dest_city"><input class="form-control" type="text" name="add-form-data[{{index}}][dest_city]" placeholder="City"></td>\
                            <td class="td-dest_zip"><textarea class="form-control" name="add-form-data[{{index}}][dest_zip]" placeholder="Zip/Post Code"></textarea></td>\
                            <td class="td-actions text-center">\
                                <div class="btn-group">\
                                    <button type="button" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Save All</button>\
                                    <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>\
                                </div>\
                            </td>\
                        </tr>',
                    template = '<form class="trs-table-rate-add-form" name="trs-table-rate-add-form">\
                            <table class="trs-table-rate-add table table-striped table-responsive-row clearfix" width="100%">\
                                <tbody>' + template_tr.replace(/\{\{index\}\}/g, '0') + '</tbody>\
                            </table>\
                        </form>';
                template = $(template);
                makeSelect2Country(template.find('.select2-country'));
                makeSelect2State(template.find('.select2-state'), '', template.find('.select2-country'));
                template.find('.btn-danger').remove();
                template.on('change', 'tr:last-child select, tr:last-child input, tr:last-child textarea', function () {
                    var template_tr_temp = template_tr;
                    template_tr_temp = template_tr_temp.replace(/\{\{index\}\}/g, (parseInt($(this).closest('tr').attr('data-index')) + 1).toString());
                    template_tr_temp = $(template_tr_temp);
                    makeSelect2Country(template_tr_temp.find('.select2-country'));
                    makeSelect2State(template_tr_temp.find('.select2-state'), '', template_tr_temp.find('.select2-country'));
                    template_tr_temp.find('.btn-success').remove();
                    $(this).closest('tr').after(template_tr_temp);
                });
                template.on('click', '.btn-danger', function () {
                    $(this).closest('tr').remove();
                });
                template.on('click', '.btn-success', function () {
                    var form_data = $(this).closest('form').serializeJSON();
                    $.each(form_data['add-form-data'], function (index, value) {
                        if ((typeof value['id_country'] == 'undefined' || value['id_country'] == '')
                            && (typeof value['id_state'] == 'undefined' || value['id_state'] == '')
                            && (typeof value['dest_city'] == 'undefined' || value['dest_city'] == '')
                            && (typeof value['dest_zip'] == 'undefined' || value['dest_zip'] == '')) {
                            delete form_data['add-form-data'][index];
                        } else {
                            value.id_zone = $(settings.nTable).attr('data-zone');
                            value.id_group = 0;
                            value.dest_zip_before = '*';
                            value.price = 0;
                            value.comment = '{{dummy}}';
                        }
                    });
                    var length = $.map(form_data['add-form-data'], function (k, v) {
                        return v;
                    }).length;
                    if (typeof  form_data['add-form-data'] != 'undefined' && length > 0) {
                        ajaxRequest({
                            'method': 'saveRules',
                            'action': 'add',
                            'id_carrier': trscontent.find('#trs-select-carrier').val(),
                            'records': form_data['add-form-data']
                        }, $(this)).success(function (response) {
                            showAjaxRequestMessage(response.status, response.message);
                            if (response.status == 'success') {
                                api.page(api.page.info().page).draw(false);
                            }
                        });
                    }
                });
                $(settings.nTable).after(template);
            }
        });

        // zone events
        // zone_tab.find('.trs-edit-zone').on('click', onEditZoneClick);
        // zone_tab.find('.trs-delete-zone').on('click', onDeleteZoneClick);
        zone_panel.on('click', '.trs-delete', onDeleteClick);
        zone_panel.on('click', '.trs-reload', function () {
            var table = $('#' + $(this).closest('.dataTables_wrapper').find('table').attr('id')).dataTable();
            table.api().page(table.api().page.info().page).draw(false);
        });
        zone_panel.on('click', '.trs-enable', onEnableClick);
        zone_panel.on('click', '.trs-disable', onDisableClick);
    };

    var updateRuleGroupData = function (response) {
        showAjaxRequestMessage(response.status, response.message);
        if (response.status == 'success') {
            $(this).closest('tr')
                .find('.x-editable-country, .x-editable-state, .x-editable-city, .x-editable-zip, .trs-delete, .trs-show-rules')
                .attr('data-country', response.id_country)
                .attr('data-state', response.id_state)
                .attr('data-city', response.dest_city)
                .attr('data-zip', response.dest_zip);
            $(this).closest('tr').find('.x-editable-country').attr('data-value', response.id_country);
            $(this).closest('tr').find('.x-editable-country').text(response.country);
            $(this).closest('tr').find('.x-editable-state').attr('data-value', response.id_state);
            $(this).closest('tr').find('.x-editable-state').text(response.state);
            $(this).closest('tr').find('.x-editable-city').attr('data-value', response.dest_city);
            $(this).closest('tr').find('.x-editable-city').text(response.dest_city);
            $(this).closest('tr').find('.x-editable-zip').attr('data-value', response.dest_zip);
            $(this).closest('tr').find('.x-editable-zip').html(response.dest_zip.split(',').join('<br/>'));
            if ($(this).closest('tr').next().hasClass('trs-table-rates-tr')) {
                $(this).closest('tr').find('.trs-show-rules i').addClass('glyphicon-chevron-down');
                $(this).closest('tr').find('.trs-show-rules i').removeClass('glyphicon-chevron-up');
                $(this).closest('tr').next().remove();
            }
        }
        return {
            success: response.status == 'success',
            pk: 0,
            newValue: $(this).attr('data-value')
        };
    };

    var onSelectCarrierChange = function () {
        if ($(this).val() == '') {
            trscontent.find('#trs-panel-table-rates').hide();
        } else {
            trscontent.find('#trs-panel-table-rates').show();
            onReloadClick();
        }
    };

    var onReloadClick = function () {
        var trstablerates = $('.trs-table-rates').dataTable();

        ajaxRequest({
            'method': 'getZonesOrder',
            'id_carrier': trscontent.find('#trs-select-carrier').val()
        }, $('#trs-table-loader')).success(function (response) {
            if ($.isArray(response)) {
                $.each(response, function (index, value) {
                    if (index == 0) {
                        trszones.find('.ui-tabs-nav').prepend(trszones.find('.ui-tabs-nav > #zone-' + value));
                    } else {
                        trszones.find('.ui-tabs-nav > li:nth-child(' + index + ')').after(trszones.find('.ui-tabs-nav #zone-' + value));
                    }
                });
                trszones.find('.ui-tabs-nav > li:nth-child(1) > a').trigger('click');
            }
        });
        trstablerates.api().page(trstablerates.api().page.info().page).draw(false);
        trstableproducts.api().page(trstableproducts.api().page.info().page).draw(false);
    };

    var onSelectProductsClick = function () {
        trscontent.find('#modalselectproducts').modal('show');
    };

    var onSortStart = function () {
        var entity = $(this).attr('data-entity');

        if (entity == 'rulegroup') {
            $(this).find('.trs-table-rates-tr').remove();
        }
    };

    var onUpdateOrder = function () {
        ajaxRequest({
            'method': 'updateOrder',
            'entity': $(this).attr('data-entity'),
            'id_carrier': trscontent.find('#trs-select-carrier').val(),
            'id_zone': trscontent.find('#trs-zones .ui-tabs-panel:visible .trs-table-rates').attr('data-zone'),
            'order': $(this).sortable('toArray')
        }, $('#trs-table-loader'));
    };

    var onShowRulesClick = function () {
        var button = $(this),
            tr = $(this).closest('tr'),
            template = '<tr class="trs-table-rates-tr">\
                <td colspan="5">\
                    <table class="trs-table-rules table table-striped table-responsive-row clearfix" width="100%">\
                    <thead>\
                        <tr>\
                            <th colspan="5" class="td-condition_limits">\
                                <span class="title_box">Limits</span>\
                            </th>\
                            <th colspan="4" class="td-condition_limits_right">\
                                <span class="title_box"></span>\
                            </th>\
                        </tr>\
                        <tr>\
                            <th class="td-condition_weight">\
                                <span class="title_box">Weight</span>\
                            </th>\
                            <th class="td-condition_price">\
                                <span class="title_box">Price</span>\
                            </th>\
                            <th class="td-condition_ptprice">\
                                <span class="title_box">Price(pretax)</span>\
                            </th>\
                            <th class="td-condition_quantity">\
                                <span class="title_box">Quantity</span>\
                            </th>\
                            <th class="td-condition_volume">\
                                <span class="title_box">Volume</span>\
                            </th>\
                            <th class="td-price">\
                                <span class="title_box">Price</span>\
                            </th>\
                            <th class="td-comment">\
                                <span class="title_box">Comment</span>\
                            </th>\
                            <th class="td-status">\
                                <span class="title_box">Status</span>\
                            </th>\
                            <th class="td-actions">\
                                <span class="title_box">Actions</span>\
                            </th>\
                        </tr>\
                    </thead>\
                    </table>\
                </td>\
            </tr>';

        if (!tr.next().hasClass('trs-table-rates-tr')) {
            tr.after(template);
            tr.next().find('.trs-table-rules').dataTable({
                "ajax": {
                    "url": url + '&method=getRules',
                    "data": function (data, settings) {
                        data.id_carrier = trscontent.find('#trs-select-carrier').val();
                        data.id_zone = button.attr('data-zone');
                        data.id_group = button.attr('data-group');
                        data.id_country = button.attr('data-country');
                        data.id_state = button.attr('data-state');
                        data.dest_city = button.attr('data-city');
                        data.dest_zip = button.attr('data-zip');
                    }
                },
                "processing": true,
                "serverSide": true,
                "pageLength": 20,
                "ordering": false,
                "columns": [
                    {"data": "condition_weight"}, 								        // 0
                    {"data": "condition_price"}, 								        // 1
                    {"data": "condition_ptprice"}, 								        // 2
                    {"data": "condition_quantity"}, 								    // 3
                    {"data": "condition_volume"},                                       // 4
                    {"data": "price"}, 												    // 5
                    {"data": "comment"}, 												// 6
                    {"data": "active", "class": "text-center"},                         // 7
                    {"data": "id_carrier_table_rate", "class": "text-center"}           // 8
                ],
                "columnDefs": [{
                    render: function (data, type, row) {
                        var weight;
                        if (row.condition_weight_from == '*' && row.condition_weight_to == '*') {
                            weight = row.condition_weight_from;
                        } else if (row.condition_weight_from == row.condition_weight_to) {
                            weight = parseFloat(row.condition_weight_from).toFixed(2);
                        } else {
                            weight = parseFloat(row.condition_weight_from).toFixed(2) + '-' + parseFloat(row.condition_weight_to).toFixed(2);
                        }

                        return '<a href="#" class="x-editable-condition_price_comment x-editable-condition_weight" data-update="condition_weight" data-pk="0" data-value="' + weight + '" data-id="' + row.id_carrier_table_rate + '">' +
                            weight + '</a>';
                    },
                    targets: 0
                }, {
                    render: function (data, type, row) {
                        var price;

                        if (row.condition_price_from == '*' && row.condition_price_to == '*') {
                            price = row.condition_price_from;
                        } else if (row.condition_price_from == row.condition_price_to) {
                            price = parseFloat(row.condition_price_from).toFixed(2);
                        } else {
                            price = parseFloat(row.condition_price_from).toFixed(2) + '-' + parseFloat(row.condition_price_to).toFixed(2);
                        }

                        return '<a href="#" class="x-editable-condition_price_comment x-editable-condition_price" data-update="condition_price" data-pk="0" data-value="' + price + '" data-id="' + row.id_carrier_table_rate + '">' +
                            price + '</a>';
                    },
                    targets: 1
                }, {
                    render: function (data, type, row) {
                        var ptprice;
                        if (row.condition_ptprice_from == '*' && row.condition_ptprice_to == '*') {
                            ptprice = row.condition_ptprice_from;
                        } else if (row.condition_ptprice_from == row.condition_ptprice_to) {
                            ptprice = parseFloat(row.condition_ptprice_from).toFixed(2);
                        } else {
                            ptprice = parseFloat(row.condition_ptprice_from).toFixed(2) + '-' + parseFloat(row.condition_ptprice_to).toFixed(2);
                        }

                        return '<a href="#" class="x-editable-condition_price_comment x-editable-condition_ptprice" data-update="condition_ptprice" data-pk="0" data-value="' + ptprice + '" data-id="' + row.id_carrier_table_rate + '">' +
                            ptprice + '</a>';
                    },
                    targets: 2
                }, {
                    render: function (data, type, row) {
                        var quantity;
                        if (row.condition_quantity_from == '*' && row.condition_quantity_to == '*') {
                            quantity = row.condition_quantity_from;
                        } else if (row.condition_quantity_from == row.condition_quantity_to) {
                            quantity = parseFloat(row.condition_quantity_from).toFixed(2);
                        } else {
                            quantity = parseFloat(row.condition_quantity_from).toFixed(2) + '-' + parseFloat(row.condition_quantity_to).toFixed(2);
                        }

                        return '<a href="#" class="x-editable-condition_price_comment x-editable-condition_quantity" data-update="condition_quantity" data-pk="0" data-value="' + quantity + '" data-id="' + row.id_carrier_table_rate + '">' +
                            quantity + '</a>';
                    },
                    targets: 3
                }, {
                    render: function (data, type, row) {
                        var volume;
                        if (row.condition_volume_from == '*' && row.condition_volume_to == '*') {
                            volume = row.condition_volume_from;
                        } else if (row.condition_volume_from == row.condition_volume_to) {
                            volume = parseFloat(row.condition_volume_from).toFixed(2);
                        } else {
                            volume = parseFloat(row.condition_volume_from).toFixed(2) + '-' + parseFloat(row.condition_volume_to).toFixed(2);
                        }

                        return '<a href="#" class="x-editable-condition_price_comment x-editable-condition_volume" data-update="condition_volume" data-pk="0" data-value="' + volume + '" data-id="' + row.id_carrier_table_rate + '">' +
                            volume + '</a>';
                    },
                    targets: 4
                }, {
                    render: function (data, type, row) {
                        return '<a href="#" class="x-editable-condition_price_comment x-editable-price" data-update="price" data-pk="0" data-value="' + data + '" data-id="' + row.id_carrier_table_rate + '">' +
                            data + '</a>';
                    },
                    targets: 5
                }, {
                    render: function (data, type, row) {
                        var comment = data.substring(0, 7) + '...';
                        return '<a href="#" class="x-editable-condition_price_comment x-editable-comment" data-update="comment" data-pk="0" data-value="' + data.replace(/"/g, '&quot;') + '" data-id="' + row.id_carrier_table_rate + '">' +
                            comment + '</span>';
                    },
                    targets: 6
                }, {
                    render: function (data, type, row) {
                        return ((data == '0')
                            ? '<a class="trs-enable list-action-enable action-disabled" href="javascript:void(0);" data-id="' + row.id_carrier_table_rate + '"><i class="glyphicon glyphicon-remove"></i></a>'
                            : '<a class="trs-disable list-action-enable action-enabled" href="javascript:void(0);" data-id="' + row.id_carrier_table_rate + '"><i class="glyphicon glyphicon-ok"></i></a>');
                    },
                    targets: 7
                }, {
                    render: function (data, type, row) {
                        return '<div class="btn-group">\
                            <a class="trs-delete btn btn-danger" href="javascript:void(0);" data-type="single" data-entity="rule" data-id="' + data + '">\
                                <i class="glyphicon glyphicon-remove"></i>\
                            </a>\
                        </div>';
                    },
                    targets: 8
                }],
                "initComplete": function (settings, json) {
                    $(settings.nTable).find('> tbody').attr('data-entity', 'rule');
                    $(settings.nTable).find('> tbody').sortable({axis: 'y', delay: 150, update: onUpdateOrder});
                    $(settings.nTable).closest('.dataTables_wrapper').find('.dataTables_info').before('<div class="btn-group">\
                        <a class="btn btn-default trs-reload" href="javascript:void(0);">\
                            <i class="glyphicon glyphicon-refresh"></i> <span>Reload</span>\
                        </a>\
                    </div>');
                },
                "rowCallback": function (row, data, index) {
                    $(row).attr('id', data.id_carrier_table_rate);
                },
                "drawCallback": function (settings) {
                    var api = new $.fn.dataTable.Api(settings);

                    // make condition from, condition to, price and comment editable
                    $(settings.nTable).find('.x-editable-condition_price_comment').editable({
                        type: 'text',
                        url: url,
                        onblur: 'submit',
                        params: function (params) {
                            params.method = 'updateConditionPriceComment';
                            params.update = $(this).attr('data-update');
                            params.id_carrier_table_rate = $(this).attr('data-id');
                            return params;
                        },
                        success: function (response) {
                            showAjaxRequestMessage(response.status, response.message);
                            if (response.status == 'success') {
                                if ($(this).attr('data-update') == 'comment') {
                                    $(this).attr('data-value', response[$(this).attr('data-update')]);
                                    $(this).text(response[$(this).attr('data-update')].substring(0, 7) + '...');
                                } else if ($(this).attr('data-update') == 'price') {
                                    $(this).attr('data-value', response[$(this).attr('data-update')]);
                                    $(this).text(response[$(this).attr('data-update')]);
                                } else {
                                    var condition_from = response[$(this).attr('data-update') + '_from'],
                                        condition_to = response[$(this).attr('data-update') + '_to'];
                                    if (condition_from == '*' && condition_to == '*') {
                                        $(this).attr('data-value', condition_from);
                                        $(this).text(condition_from);
                                    } else if (condition_from == condition_to) {
                                        $(this).attr('data-value', parseFloat(condition_from).toFixed(2));
                                        $(this).text(parseFloat(condition_from).toFixed(2));
                                    } else {
                                        $(this).attr('data-value', parseFloat(condition_from).toFixed(2) + '-' + parseFloat(condition_to).toFixed(2));
                                        $(this).text(parseFloat(condition_from).toFixed(2) + '-' + parseFloat(condition_to).toFixed(2));
                                    }
                                }
                            }
                            return {
                                success: response.status == 'success',
                                pk: 0,
                                newValue: $(this).attr('data-value')
                            };
                        },
                        display: function (value, sourceData, response) {
                            return false;
                        }
                    });
                    $(settings.nTable).find('.x-editable-condition_price_comment').on('focus', function () {
                        $(this).trigger('click');
                    });

                    // remove if form exists
                    if ($(settings.nTable).next().hasClass('trs-table-rule-add-form')) {
                        $(settings.nTable).next().remove();
                    }
                    var template_tr = '<tr data-index="{{index}}">\
                            <td class="td-condition_weight"><input class="form-control" type="text" name="add-form-data[{{index}}][condition_weight]" placeholder="Weight Limit"></td>\
                            <td class="td-condition_price"><input class="form-control" type="text" name="add-form-data[{{index}}][condition_price]" placeholder="Price Limit"></td>\
                            <td class="td-condition_ptprice"><input class="form-control" type="text" name="add-form-data[{{index}}][condition_ptprice]" placeholder="Price(pretax) Limit"></td>\
                            <td class="td-condition_quantity"><input class="form-control" type="text" name="add-form-data[{{index}}][condition_quantity]" placeholder="Quantity Limit"></td>\
                            <td class="td-condition_volume"><input class="form-control" type="text" name="add-form-data[{{index}}][condition_volume]" placeholder="Volume Limit"></td>\
                            <td class="td-price"><input class="form-control" type="text" name="add-form-data[{{index}}][price]" placeholder="Price"></td>\
                            <td class="td-comment"><input class="form-control" type="text" name="add-form-data[{{index}}][comment]" placeholder="Comment"></td>\
                            <td class="td-status text-center"><input type="checkbox" checked value="1" name="add-form-data[{{index}}][active]"></td>\
                            <td class="td-actions text-center">\
                                <div class="btn-group">\
                                    <button type="button" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Save All</button>\
                                    <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>\
                                </div>\
                            </td>\
                        </tr>',
                        template = '<form class="trs-table-rule-add-form" name="trs-table-rule-add-form">\
                            <table class="trs-table-rule-add table table-striped table-responsive-row clearfix" width="100%">\
                                <tbody>' + template_tr.replace(/\{\{index\}\}/g, '0') + '</tbody>\
                            </table>\
                        </form>';
                    template = $(template);
                    template.find('.btn-danger').remove();
                    template.on('change', 'tr:last-child input, tr:last-child textarea', function () {
                        var template_tr_temp = template_tr;
                        template_tr_temp = template_tr_temp.replace(/\{\{index\}\}/g, (parseInt($(this).closest('tr').attr('data-index')) + 1).toString());
                        template_tr_temp = $(template_tr_temp);
                        template_tr_temp.find('.btn-success').remove();
                        $(this).closest('tr').after(template_tr_temp);
                    });
                    template.on('click', '.btn-danger', function () {
                        $(this).closest('tr').remove();
                    });
                    template.on('click', '.btn-success', function () {
                        var form_data = $(this).closest('form').serializeJSON();
                        $.each(form_data['add-form-data'], function (index, value) {
                            if ((typeof value['condition_weight'] == 'undefined' || value['condition_weight'] == '')
                                && (typeof value['condition_price'] == 'undefined' || value['condition_price'] == '')
                                && (typeof value['condition_ptprice'] == 'undefined' || value['condition_ptprice'] == '')
                                && (typeof value['condition_quantity'] == 'undefined' || value['condition_quantity'] == '')
                                && (typeof value['condition_volume'] == 'undefined' || value['condition_volume'] == '')
                                && (typeof value['price'] == 'undefined' || value['price'] == '')
                                && (typeof value['comment'] == 'undefined' || value['comment'] == '')
                                && (typeof value['active'] != 'undefined' && value['active'] == '1')) {
                                delete form_data['add-form-data'][index];
                            } else {
                                value.id_zone = button.attr('data-zone');
                                value.id_group = button.attr('data-group');
                                value.id_country = button.attr('data-country');
                                value.id_state = button.attr('data-state');
                                value.dest_city = button.attr('data-city');
                                value.dest_zip_before = button.attr('data-zip');
                                value.dest_zip = button.attr('data-zip');
                            }
                        });
                        var length = $.map(form_data['add-form-data'], function (k, v) {
                            return v;
                        }).length;
                        if (typeof form_data['add-form-data'] != 'undefined' && length > 0) {
                            ajaxRequest({
                                'method': 'saveRules',
                                'action': 'add',
                                'id_carrier': trscontent.find('#trs-select-carrier').val(),
                                'records': form_data['add-form-data']
                            }, $(this)).success(function (response) {
                                showAjaxRequestMessage(response.status, response.message);
                                if (response.status == 'success') {
                                    api.page(api.page.info().page).draw(false);
                                }
                            });
                        }
                    });
                    $(settings.nTable).after(template);
                }
            });
        } else {
            tr.next().toggle();
        }

        if (tr.next().is(':visible')) {
            $(this).find('i').addClass('glyphicon-chevron-up');
            $(this).find('i').removeClass('glyphicon-chevron-down');
        } else {
            $(this).find('i').addClass('glyphicon-chevron-down');
            $(this).find('i').removeClass('glyphicon-chevron-up');
        }
    };

    var onSelectClick = function () {
        var id_product = $(this).attr('data-id'),
            id_carrier = trscontent.find('#trs-select-carrier').val(),
            thisbutton = $(this);

        ajaxRequest({
            'method': 'updateProductSelected',
            'id_product': id_product,
            'id_carrier': id_carrier,
            'select': 1
        }, thisbutton).success(function (response) {
            if (response.status == 'success' && response.id_product == id_product &&
                response.id_carrier == id_carrier && response.select == 1) {
                thisbutton.removeClass('trs-select action-disabled').addClass('trs-deselect action-enabled');
                thisbutton.find('i').data('iconclass', 'glyphicon glyphicon-ok');
            }
        });
    };

    var onDeselectClick = function () {
        var id_product = $(this).attr('data-id'),
            id_carrier = trscontent.find('#trs-select-carrier').val(),
            thisbutton = $(this);

        ajaxRequest({
            'method': 'updateProductSelected',
            'id_product': id_product,
            'id_carrier': id_carrier,
            'select': 0
        }, thisbutton).success(function (response) {
            if (response.status == 'success' && response.id_product == id_product &&
                response.id_carrier == id_carrier && response.select == 0) {
                thisbutton.removeClass('trs-deselect action-enabled').addClass('trs-select action-disabled');
                thisbutton.find('i').data('iconclass', 'glyphicon glyphicon-remove');
            }
        });
    };

    var onEnableClick = function () {
        var id_carrier_table_rate = $(this).attr('data-id'),
            thisbutton = $(this);

        ajaxRequest({
            'method': 'updateStatus',
            'id_carrier_table_rate': id_carrier_table_rate,
            'active': 1
        }, thisbutton).success(function (response) {
            if (response.status == 'success' && response.id_carrier_table_rate == id_carrier_table_rate
                && response.active == 1) {
                thisbutton.removeClass('trs-enable action-disabled').addClass('trs-disable action-enabled');
                thisbutton.find('i').data('iconclass', 'glyphicon glyphicon-ok');
            }
        });
    };

    var onDisableClick = function () {
        var id_carrier_table_rate = $(this).attr('data-id'),
            thisbutton = $(this);

        ajaxRequest({
            'method': 'updateStatus',
            'id_carrier_table_rate': id_carrier_table_rate,
            'active': 0
        }, thisbutton).success(function (response) {
            if (response.status == 'success' && response.id_carrier_table_rate == id_carrier_table_rate
                && response.active == 0) {
                thisbutton.removeClass('trs-disable action-enabled').addClass('trs-enable action-disabled');
                thisbutton.find('i').data('iconclass', 'glyphicon glyphicon-remove');
            }
        });
    };

    var onDeleteClick = function (e) {
        e.stopPropagation();

        if (confirm('Are you sure?')) {
            var button = $(this),
                type = button.attr('data-type'),
                entity = button.attr('data-entity'),
                params = {
                    'method': 'deleteRules',
                    'type': type,
                    'entity': entity,
                    'id_carrier': trscontent.find('#trs-select-carrier').val()
                },
                trstablerates = $('.trs-table-rates');

            switch (type) {
                case 'single':
                    params['id_carrier_table_rate'] = button.attr('data-id');
                    params['data'] = [];
                    params['data'][0] = {
                        'id_zone': button.attr('data-zone'),
                        'id_group': button.attr('data-group')
                    };
                    break;
                case 'selected':
                    params['id_carrier_table_rate'] = [];
                    trstablerates.find('tr.active').each(function (index, value) {
                        params['id_carrier_table_rate'].push(button.attr('data-id'));
                    });
                    break;
                case 'all':
                    params['id_zone'] = button.attr('data-zone');
                    break;
            }

            ajaxRequest(params, button).success(function (response) {
                if (response.status == 'success') {
                    var datatable;

                    if (type == 'all') {
                        datatable = $('#' + button.closest('.dataTables_wrapper').find('.dataTable:first').attr('id')).dataTable();
                    } else {
                        datatable = $('#' + button.closest('.dataTable').attr('id')).dataTable();
                    }

                    datatable.api().page(datatable.api().page.info().page).draw(false);
                }
            });
        }
    };

    var onAlertClose = function () {
        $(this).closest('.alert').fadeOut('slow');
    };

    var onImportCSVClick = function () {
        resetImportExportModal('import-csv');
        trscontent.find('#modalimportexportcsv').modal('show');
        trscontent.find("#csv_file").focus();
    };

    var onExportCSVClick = function () {
        resetImportExportModal('export-csv');
        trscontent.find('#modalimportexportcsv').modal('show');
        trscontent.find("#csv_file").focus();
    };

    var resetImportExportModal = function (resetfor) {
        modalimportexportcsv.find('#csv-file-form-group').show();
        modalimportexportcsv.find('#csv-separator-form-group').show();
        modalimportexportcsv.find('.panel-footer').show();

        modalimportexportcsv.find("#trs-importexportcsv-action").val(resetfor);

        switch (resetfor) {
            case 'import-csv':
                modalimportexportcsv.find(".modal-title").html('<i class="glyphicon glyphicon-import"></i> <span>Import CSV</span>');
                modalimportexportcsv.find('.panel-footer').hide();
                break;
            case 'export-csv':
                modalimportexportcsv.find(".modal-title").html('<i class="glyphicon glyphicon-export"></i> <span>Export CSV</span>');
                modalimportexportcsv.find('#csv-file-form-group').hide();
                break;
        }
    };

    var onImportExportCSVSubmitClick = function () {
        var id_carrier = trscontent.find('#trs-select-carrier').val(),
            csv_separator = $.trim(modalimportexportcsv.find("#csv_separator").val()),
            action = modalimportexportcsv.find("#trs-importexportcsv-action").val();

        if (csv_separator == '') {
            alert('CSV separator is required');
            return;
        }

        if (id_carrier == '') {
            alert('Please select carrier');
            return;
        }

        switch (action) {
            case 'import-csv':
                var importstatustimeout,
                    sendimportstatusrequest = true,
                    me = $(this);

                ajaxRequest({
                    'method': 'import',
                    'file': $(this).attr('data-file'),
                    'csv_separator': csv_separator,
                    'id_carrier': id_carrier,
                    'beforeSend': function () {
                        me.find('.glyphicon').after(' <span class="import-status">(0)</span>');
                        importstatustimeout = setInterval(function () {
                            if (sendimportstatusrequest) {
                                ajaxRequest({
                                    'method': 'getImportStatus',
                                    'beforeSend': function () {
                                        sendimportstatusrequest = false;
                                    }
                                }, $('#trs-table-loader'))
                                    .success(function (response) {
                                        me.find('.import-status').text('(' + response.rowImported + ')');
                                        sendimportstatusrequest = true;
                                    });
                            }
                        }, 3000);
                    }
                }, me)
                    .success(function (response) {
                        if (response.status == 'success' || response.status == 'warning') {
                            onReloadClick();
                        }
                    })
                    .complete(function () {
                        me.find('.import-status').remove();
                        clearInterval(importstatustimeout);
                    });

                break;
            case 'export-csv':
                window.open(url + '&method=export&header=csv&id_carrier=' + id_carrier + '&csv_separator=' + csv_separator, '_blank');

                break;
        }
    };

    var makeSelect2Country = function (field, value) {
        var options = {
            placeholder: 'Country',
            allowClear: true,
            width: '100%',
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        method: 'getCountries',
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 0
        };

        if (typeof value != 'undefined' && value != '')
            options.data = value;

        field.select2(options);
    };

    var makeSelect2State = function (field, value, field_country) {
        var options = {
            placeholder: 'State',
            allowClear: true,
            width: '100%',
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        method: 'getStates',
                        c: (typeof field_country.val !== 'function') ? field_country : field_country.val(),
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 0
        };

        if (typeof value != 'undefined' && value != '')
            options.data = value;

        field.select2(options);
    };

    var ajaxRequest = function (data, button) {
        if (!button.find('i').hasClass('icon-spinner')) {
            button.find('i').data('iconclass', button.find('i').attr('class'));
        }

        return $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            beforeSend: function () {
                button.find('i').removeClass(button.find('i').data('iconclass')).addClass('icon-spinner');
            },
            complete: function () {
                button.find('i').removeClass('icon-spinner').addClass(button.find('i').data('iconclass'));
            },
            success: function (response) {
                showAjaxRequestMessage(response.status, response.message);
            }
        });
    };

    var showAjaxRequestMessage = function (status, message) {
        if (typeof status != 'undefined' && status != '' && typeof message != 'undefined' && message != '') {
            trscontent.find('.alert').removeClass('alert-success');
            trscontent.find('.alert').removeClass('alert-danger');
            trscontent.find('.alert').removeClass('alert-warning');
            trscontent.find('.alert-message').text(message);
            trscontent.find('.alert').addClass('alert-' + status).fadeIn('slow');
            trscontent.find('.alert-close').focus();
        }
    };
})(jQuery);

$(function () {
    $.extend({
        getUrlVars: function () {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        },
        getUrlVar: function (name) {
            return $.getUrlVars()[name];
        }
    });

    $(document).AdminTableRateShipping({
        'url': 'index.php?controller=AdminTableRateShipping&token=' + $.getUrlVar('token') + '&ajax=1'
    });
});