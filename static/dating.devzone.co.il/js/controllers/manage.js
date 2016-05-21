/* global App */

// URL: /manage/roles
if ($("#RolesGrid").length) {
    (function () {
        var RolesGrid = {
            Validation: {
                ServerError: undefined
            },
            OnChange: function (e) {
            },
            OnDataBound: function (e) {
            },
            OnDataBinding: function (e) {
            },
            OnEdit: function (e) {
                if (e.model.isNew()) { // Create
                    e.container.find("td.id").text("?");
                } else {
                    //e.container.find("input[name='name']").prop("disabled", true)
                }
            },
            OnSave: function (e) {
                // e.sender.dataSource.read();
                // $("#RolesGrid").data("kendoGrid").dataSource.read()
            },
            OnRead: function (options) {
                $.ajax({
                    url: "/roles/getManyAjax",
                    method: "POST",
                    data: options.data,
                    dataType: "json",
                    success: options.success,
                    error: options.error
                });
            },
            OnUpdate: function (options) {
                $.ajax({
                    url: "/roles/updateOneAjax",
                    method: "POST",
                    dataType: "json",
                    data: options.data,
                    success: function (response) {
                        if (!response) {
                            options.error(response, App.Lang.Line('error_unknown'));
                        } else if (response.isSuccess === true) {
                            response.data = options.data;
                            options.success(response);
                        } else if (Array.isArray(response.errors) && response.errors.length > 0) {
                            options.error(response, response.errors[0]);
                            RolesGrid.Validation.ServerError = response.errors[0];
                            $("#RolesGrid").data("kendoGrid").editable.validatable.validate();
                        } else {
                            options.error(response, App.Lang.Line('error_unknown'));
                        }
                    },
                    error: options.error
                });
            },
            OnCreate: function (options) {
                $.ajax({
                    url: "/roles/createOneAjax",
                    method: "POST",
                    dataType: "json",
                    data: {name: options.data.name, description: options.data.description},
                    success: function (response) {
                        if (response && response.id) {
                            options.data.id = parseInt(response.id);
                            response.data = options.data;
                            options.success(response); // notify the data source that the request succeeded
                        } else {
                            options.error(response); // notify the data source that the request failed
                        }
                    },
                    error: options.error
                });
            },
            OnDestroy: function (options) {
                $.ajax({
                    url: "/roles/deleteOneAjax",
                    method: "POST",
                    dataType: "json",
                    data: {id: options.data.id},
                    success: function (response) {
                        var $kendoGrid = $("#RolesGrid").data("kendoGrid");
                        $kendoGrid.dataSource.read();
                        $kendoGrid.refresh();
                        options.success(response);
                    },
                    error: options.error
                });
            }
        };

        $("#RolesGrid").kendoGrid({
            //height: 550,
            autoBind: true,
            columnResizeHandleWidth: 400,
            allowCopy: {delimeter: " "},
            toolbar: [{name: "create", text: App.Lang.Line('create_role')}],
            groupable: false,
            filterable: false,
            sortable: false,
            selectable: "multiple", // "multiple cell"
            pageable: {
                messages: App.Kendo.Grid.Pageable.DefaultMessages,
                refresh: true
            },
            editable: {
                mode: 'inline', // modes: 'incell', 'inline', 'popup'
                update: true,
                window: {title: App.Lang.Line('edit')},
                confirmation: App.Lang.Line('confirm_unstopable_action')
            },
            dataSource: {
                transport: {
                    read: RolesGrid.OnRead,
                    update: RolesGrid.OnUpdate,
                    create: RolesGrid.OnCreate,
                    destroy: RolesGrid.OnDestroy
                },
                schema: {
                    data: "data",
                    total: "total",
                    model: {
                        id: "id",
                        fields: {
                            id: {
                                type: "number",
                                validation: {required: {message: App.Lang.Line('error_field_missing')}},
                                editable: false
                            },
                            name: {
                                type: "string",
                                validation: {
                                    required: {message: App.Lang.Line('error_field_missing')},
                                    custom: function (input) {
                                        var isValid = false;
                                        if (input.is("[name='name']") && input.val().length > 32) {
                                            input.attr("data-custom-msg", App.Lang.Line('alert_max_32_chars'));
                                        } else if (input.is("[name='name']") && !App.Regex.ASCII.test(input.val())) {
                                            input.attr("data-custom-msg", App.Lang.Line('error_only_ascii_html'));
                                        } else if (input.is("[name='description']") && input.val().length > 255) {
                                            input.attr("data-custom-msg", App.Lang.Line('alert_max_255_chars'));
                                        } else if (RolesGrid.Validation.ServerError) {
                                            input.attr("data-custom-msg", RolesGrid.Validation.ServerError);
                                        } else {
                                            isValid = true; // True for other fields
                                        }

                                        if (!isValid && input.parent().parent().is("tr:last")) {
                                            var gridContent = input.parents().find(".k-grid-content");
                                            var toScroll = gridContent.prop("scrollHeight") - gridContent.outerHeight();
                                            gridContent.animate({scrollTop: toScroll});
                                        }

                                        RolesGrid.Validation.ServerError = undefined;
                                        return isValid;
                                    }
                                }
                            },
                            description: {type: "string"}
                        }
                    }
                },
                pageSize: 20,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            change: RolesGrid.OnChange,
            dataBound: RolesGrid.OnDataBound,
            dataBinding: RolesGrid.OnDataBinding,
            edit: RolesGrid.OnEdit,
            save: RolesGrid.OnSave,
            error: function (e) {
            },
            //saveChanges: RolesGrid.OnSaveChanges, // on ToolBar Save button click
            columns: [
                {field: "id", title: "ID", filterable: false, width: 50, attributes: {"class": "id"}},
                {field: "name", title: App.Lang.Line('name'), width: 200},
                {field: "description", title: App.Lang.Line('description')},
                {
                    title: App.Lang.Line('actions'),
                    width: "180px",
                    attributes: {"class": "actions"},
                    command: [
                        {name: "edit", text: {edit: App.Lang.Line('edit'), cancel: App.Lang.Line('cancel'), update: App.Lang.Line('save')}},
                        {name: "destroy", text: App.Lang.Line('delete')}
                    ]
                }
            ]
        });
    })();
}

// URL: /manage/capabilities
if ($("#CapabilitiesGrid").length) {
    (function () {
        var CapabilitiesGrid = {
            Validation: {
                ServerError: undefined
            },
            OnRead: function (options) {
                $.ajax({
                    url: "/capabilities/getManyAjax",
                    method: "POST",
                    data: options.data,
                    dataType: "json",
                    success: options.success,
                    error: options.error
                });
            },
            OnUpdate: function (options) {
                $.ajax({
                    url: "/capabilities/updateOneAjax",
                    method: "POST",
                    data: options.data,
                    dataType: "json",
                    success: function (response) {
                        if (!response) {
                            options.error(response, App.Lang.Line('error_unknown'));
                        } else if (response.isSuccess === true) {
                            response.data = options.data;
                            options.success(response);
                        } else if (Array.isArray(response.errors) && response.errors.length > 0) {
                            options.error(response, response.errors[0]);
                            CapabilitiesGrid.Validation.ServerError = response.errors[0];
                            $("#CapabilitiesGrid").data("kendoGrid").editable.validatable.validate();
                        } else {
                            options.error(response, App.Lang.Line('error_unknown'));
                        }
                    },
                    error: options.error
                });
            },
            OnCreate: function (options) {
                $.ajax({
                    url: "/capabilities/createOneAjax",
                    method: "POST",
                    dataType: "json",
                    data: {name: options.data.name, description: options.data.description},
                    success: function (response) {
                        if (response && response.id) {
                            options.data.id = parseInt(response.id);
                            response.data = options.data;
                            options.success(response); // notify the data source that the request succeeded
                        } else {
                            options.error(response); // notify the data source that the request failed
                        }
                    },
                    error: options.error
                });
            },
            OnDestroy: function (options) {
                $.ajax({
                    url: "/capabilities/deleteOneAjax",
                    method: "POST",
                    dataType: "json",
                    data: {id: options.data.id},
                    success: function (response) {
                        var $kendoGrid = $("#CapabilitiesGrid").data("kendoGrid");
                        $kendoGrid.dataSource.read();
                        $kendoGrid.refresh();
                        options.success(response);
                    },
                    error: options.error
                });
            },
            OnEdit: function (e) {
                if (e.model.isNew()) { // Create
                    e.container.find("td.id").text("?");
                } else {
                    //e.container.find("input[name='name']").prop("disabled", true)
                }
            },
        };

        $("#CapabilitiesGrid").kendoGrid({
            //height: 550
            autoBind: true,
            columnResizeHandleWidth: 400,
            allowCopy: {delimeter: " "},
            toolbar: [{name: "create", text: App.Lang.Line('create_capability')}],
            groupable: false,
            filterable: false,
            sortable: false,
            pageable: {
                messages: App.Kendo.Grid.Pageable.DefaultMessages,
                refresh: true
            },
            editable: {
                mode: 'inline', // modes: 'incell', 'inline', 'popup'
                update: true,
                window: {title: App.Lang.Line('edit')},
                confirmation: App.Lang.Line('confirm_unstopable_action')
            },
            selectable: "multiple", // "multiple cell"
            dataSource: {
                transport: {
                    read: CapabilitiesGrid.OnRead,
                    update: CapabilitiesGrid.OnUpdate,
                    create: CapabilitiesGrid.OnCreate,
                    destroy: CapabilitiesGrid.OnDestroy
                },
                schema: {
                    data: "data",
                    total: "total",
                    model: {
                        id: "id",
                        fields: {
                            id: {
                                type: "number",
                                validation: {required: {message: App.Lang.Line('error_field_missing')}},
                                editable: false
                            },
                            name: {
                                type: "string",
                                validation: {
                                    required: {message: App.Lang.Line('error_field_missing')},
                                    custom: function (input) {
                                        var isValid = false;
                                        if (input.is("[name='name']") && input.val().length > 32) {
                                            input.attr("data-custom-msg", App.Lang.Line('alert_max_32_chars'));
                                        } else if (input.is("[name='name']") && !App.Regex.ASCII.test(input.val())) {
                                            input.attr("data-custom-msg", App.Lang.Line('error_only_ascii_html'));
                                        } else if (input.is("[name='description']") && input.val().length > 255) {
                                            input.attr("data-custom-msg", App.Lang.Line('alert_max_255_chars'));
                                        } else if (CapabilitiesGrid.Validation.ServerError) {
                                            input.attr("data-custom-msg", CapabilitiesGrid.Validation.ServerError);
                                        } else {
                                            isValid = true; // True for other fields
                                        }

                                        if (!isValid && input.parent().parent().is("tr:last")) {
                                            var gridContent = input.parents().find(".k-grid-content");
                                            gridContent.animate({scrollTop: gridContent.prop("scrollHeight")});
                                        }

                                        CapabilitiesGrid.Validation.ServerError = undefined;
                                        return isValid;
                                    }
                                }
                            },
                            description: {type: "string"}
                        }
                    }
                },
                pageSize: 20,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            edit: CapabilitiesGrid.OnEdit,
            columns: [
                {field: "id", title: "ID", filterable: false, width: 50, attributes: {"class": "id"}},
                {field: "name", title: App.Lang.Line('name'), width: 200},
                {field: "description", title: App.Lang.Line('description')},
                {
                    title: App.Lang.Line('actions'),
                    width: "180px",
                    attributes: {"class": "actions"},
                    command: [
                        {name: "edit", text: {edit: App.Lang.Line('edit'), cancel: App.Lang.Line('cancel'), update: App.Lang.Line('save')}},
                        {name: "destroy", text: App.Lang.Line('delete')}
                    ]
                }
            ]
        });
    });
}

// URL: /manage/menu
if ($("#ManageMenu").length) {
    (function () {
        var Menu = {
            OnSelect: function (e, b) {
                debugger;
            }
        };

        $("#ManageMenu").kendoMenu({
            select: Menu.OnSelect,
            dataSource: [
                {
                    text: App.Lang.Line('users_management'),
                    spriteCssClass: "",
                    cssClass: "",
                    url: "/manage/users",
                    items: false // 'items' array
                },
                {
                    text: App.Lang.Line('messages_management'),
                    spriteCssClass: "",
                    cssClass: "",
                    url: "/manage/messages",
                    items: false
                },
                {
                    text: App.Lang.Line('pictures_management'),
                    spriteCssClass: "",
                    cssClass: "",
                    url: "/manage/images",
                    items: false // 'items' array
                },
                {
                    text: App.Lang.Line('albums_management'),
                    spriteCssClass: "",
                    cssClass: "",
                    url: "/manage/albums",
                    items: false // 'items' array
                },
                {
                    text: App.Lang.Line('events_management'),
                    spriteCssClass: "",
                    cssClass: "",
                    url: "/manage/events",
                    items: false // 'items' array
                },
                {
                    text: App.Lang.Line('privileges_management'),
                    spriteCssClass: "",
                    cssClass: "",
                    url: "/manage/privileges",
                    items: [
                        {
                            text: App.Lang.Line('roles'),
                            url: "/manage/roles",
                        },
                        {
                            text: App.Lang.Line('capabilities'),
                            url: "/manage/capabilities"
                        }
                    ]
                }
            ]
        });

        // TODO: Pass capabilities + add menu
    })();
}