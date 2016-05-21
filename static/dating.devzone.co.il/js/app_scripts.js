/* global Function, HTMLFormElement, HTMLInputElement */
// Here write code which common to all of the application!! (Common scripts)

Function.isFunction = Function.isFunction || function (arg) {
    return arg instanceof Function;
};

Object.valueAtIndex = function (obj, index) {
    var i = -1;
    for (var k in obj) {
        if (++i === index) {
            return obj[k];
        }
    }
    return undefined;
};
Object.keys = Object.keys || (function () {
    var hasOwnProperty = Object.prototype.hasOwnProperty,
            hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
            dontEnums = ['toString', 'toLocaleString', 'valueOf', 'hasOwnProperty', 'isPrototypeOf',
                'propertyIsEnumerable', 'constructor'],
            dontEnumsLength = dontEnums.length;

    return function (obj) {
        if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
            throw new TypeError('Object.keys called on non-object');
        }
        var result = [], prop, i;
        for (prop in obj) {
            if (hasOwnProperty.call(obj, prop)) {
                result.push(prop);
            }
        }
        if (hasDontEnumBug) {
            for (i = 0; i < dontEnumsLength; i++) {
                if (hasOwnProperty.call(obj, dontEnums[i])) {
                    result.push(dontEnums[i]);
                }
            }
        }
        return result;
    };
}());

Array.isArray = Array.isArray || function (arg) {
    return Object.prototype.toString.call(arg) === '[object Array]';
};

Number.isInteger = Number.isInteger || function (value) {
    return typeof value === "number" && isFinite(value) && Math.floor(value) === value;
};
Number.parseIntOrDefault = Number.parseIntOrDefault || function (obj, defaultValue) {
    obj = parseInt(obj);
    if (isNaN(obj)) {
        return (defaultValue instanceof Function ? defaultValue() : defaultValue);
    }
    return obj;
};
Number.parseUIntOrDefault = Number.parseUIntOrDefault || function (obj, defaultValue) {
    obj = parseInt(obj);
    if (obj >= 0) {
        return obj;
    }
    return (defaultValue instanceof Function ? defaultValue() : defaultValue);
};

String.isString = String.isString || function (arg) {
    return typeof (arg) === "string";
};
String.prototype.passwordStrength = function () {
    var strength = 0;
    if (this !== null && this.length >= 1) {
        // Calculate strength by password length
        strength += (this.length >= 8 ? 40 : (this.length / 8) * 40);
        // If contain at least 1 lowercase alphabetical character
        strength += (new RegExp("(?=.*[a-z])").test(this) ? 14 : 0);
        // If contain at least 1 uppercase alphabetical character
        strength += (new RegExp("(?=.*[A-Z])").test(this) ? 14 : 0);
        // If contain at least 1 numeric character
        strength += (new RegExp("(?=.*[0-9])").test(this) ? 8 : 0);
        // If special character (within ASCII)
        strength += (new RegExp("(?=.*[\u0020-\u002F\u003A-\u0040\u005B-\u0060\u007B-\u007F])").test(this) ? 16 : 0);
        // If at least one NON ASCII character
        strength += (new RegExp("(?=.*[^\u0000-\u00FF])").test(this) ? 8 : 0);

        strength /= 100; // To percent
    }
    return strength;
};
Math.roundReal = Math.roundReal || function (number, decimals) {
    return Number(Math.round(number + 'e' + decimals) + 'e-' + decimals);
};

Date.exists = Date.exists || function (year, month, day) {
    if (year > 0 && month >= 0 && day > 0) {
        var date = new Date(year, month, day);
        return date instanceof Date && date.getDate() === day;
    }
    return false;
};

jQuery.fn.extend({
    serializeObject: function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    },
    allowNumeric: function (isLocale, decimal) {
        var validIsLocale = (isLocale === true),
                validDecimal = Number.parseUIntOrDefault(decimal, 0), // Make sure decimal is valid or set default (zero value)
                numericRegEx = new RegExp("^(\s)*[1-9]{1}[0-9]" + (validIsLocale ? "{0,2}([\,]{0,1}[0-9]{3})*" : "*") + (validDecimal >= 1 ? "([\.]{1}[0-9]{0," + validDecimal + "}){0,1}$" : "$"));

        return this.each(function (index, element) {
            element.lastValue = $(element).val();

            if (validIsLocale === true) {
                $(element).bind('focusin.allowNumeric', function (e) {
                    $(e.target).val($(e.target).val().replace(/\,/g, ""));
                });
                $(element).bind('focusout.allowNumeric', function (e) {
                    $(e.target).val($(e.target).val().toNumericFormat(true, validDecimal));
                });
            }

            $(element).bind('change.allowNumeric', function (e) {
                if ($(e.target).val().length === 0 || numericRegEx.test($(e.target).val())) {
                    e.target.lastValue = $(e.target).val();
                } else {
                    $(e.target).val(e.target.lastValue);
                }
            });
            $(element).bind('keydown.allowNumeric', function (e) {
                console.log("e.which = " + e.which);
                var isValid = e.which === 8 // Backspace
                        || e.which === 13 // Enter
                        || (e.which >= 37 && e.which <= 40) // Arrows
                        || e.which === 46 // Delete
                        || (e.which >= 48 && e.which <= 57) // Top keyboard numbers
                        || (e.which >= 96 && e.which <= 105) // Right keyboard number
                        || (validDecimal >= 1 && (e.which === 110 || e.which === 190)) // Point
                        || (validIsLocale && e.which === 188) // Comma
                        || (e.ctrlKey && (e.which === 65 || e.which === 67 || e.which === 82 || e.which === 83 || e.which === 86 || e.which === 88)); // Ctrl + [A,C,R,S,V,X]

                if (!isValid) {
                    e.preventDefault();
                }
            });
            $(element).bind('paste.allowNumeric', function (e) {
                var self = this;
                setTimeout(function (e) {
                    $(self).val($(self).val());
                    $(self).trigger('change.allowNumeric');
                }, 0);
            });
        });
    },
    initModifyListener: function (e) {
        return this.each(function () {
            var $this = $(this).eq(0);
            var $form = $this.closest("form");

            var getModifyValue = function ($element) {
                if ($element.is("form")) {
                    return $element.serialize();
                } else if ($element.is(":checkbox,:radio")) {
                    return $element.prop("checked");
                } else if ($element.is("input")) {
                    return $element.val();
                }
                return $element.text();
            };
            var checkModify = function ($element) {
                var wasModify = $element.data("initModifyListener-wasModify");
                var originalValue = $element.data("initModifyListener-originalValue");
                var isOriginal = originalValue === getModifyValue($element);
                if (!(isOriginal ^ wasModify)) {
                    $element.data("initModifyListener-wasModify", !wasModify);
                    $element.trigger("modify", [{
                            isOriginal: isOriginal,
                            originalValue: originalValue,
                            modifiedElement: $element[0]
                        }]);
                }
            };

            $this.data("initModifyListener-wasModify", false);
            $this.data("initModifyListener-originalValue", getModifyValue($this));

            $this.unbind("change.initModifyListener");
            $this.bind("change.initModifyListener", function () {
                checkModify($this);
            });

            if (!$this.is("form,input,textarea,select,datalist")) {
                $this.unbind("focusout.initModifyListener");
                $this.bind("focusout.initModifyListener", function () {
                    if ($this.is('[contentEditable="true"]')) {
                        checkModify($this);
                    }
                });
            }

            if ($form.length > 0) {
                var identifier = $form[0].tagName + $form.index() + this.tagName + $this.index();
                $form.unbind("reset.initModifyListener-" + identifier);
                $form.bind("reset.initModifyListener-" + identifier, function () {
                    setTimeout(function () {
                        checkModify($this);
                    });
                });
            }
        });
    }
});

if (jQuery.validator) {
    jQuery.validator.addMethod('equalToI', function (value, element, param) {
        return $(param).val().toLowerCase() === value.toLowerCase();
    });
    jQuery.validator.messages.equalToI = jQuery.validator.messages.equalTo;
}


var App = {};
App.Config = {
    image_url: $('meta[app_config=image_url]').attr("content")
};
App.Lang = {
    _Dictionary: (function () {
        var result = {};
        switch ($("html").first().attr("lang")) {
            case 'english': case 'eng': case 'en':
                result.very_f_week = "Very week";
                result.f_week = "Week";
                result.f_medium = "Medium";
                result.f_strong = "Strong";
                result.very_f_strong = "Very strong";
                result.otherwise_start_over = "Otherwise, will be required to start over";
                result.edit = "Edit";
                result.save = "Save";
                result.name = "Name";
                result.login = "Login";
                result['delete'] = "Delete";
                result.cancel = "Cancel";
                result.actions = "Actions";
                result.change_website_language = "Change website language";
                result.users_management = "Users management";
                result.messages_management = "Messages management";
                result.pictures_management = "Pictures management";
                result.albums_management = "Albums management";
                result.events_management = "Events management";
                result.privileges_management = "Privileges management";
                result.roles = "Roles";
                result.less = "Less";
                result.more = "More";
                result.description = "Description";
                result.create_role = "Create role";
                result.capabilities = "Capabilities";
                result.create_capability = "Create capability";
                result.confirm_unstopable_action = "Are you sure? This action can not be undone.";
                result.do_you_want_to_continue = "Do you want to continue?";
                result.alert_max_32_chars = "Maximum of 32 characters";
                result.alert_max_255_chars = "Maximum of 255 characters";
                result.error_session_timeout = "You session timeout is expired";
                result.error_unknown = "Unknown error";
                result.error_field_missing = "Required field is missing";
                result.error_only_ascii_html = 'ASCII characters not allowed.<br /><a class="help" href="https://he.wikipedia.org/wiki/ASCII" target="_blank" title="Explanation of the characters">Help</a>';
                break;
            case 'hebrew': case 'heb': case 'he':
            default:
                result.very_f_week = "חלשה מאוד";
                result.f_week = "חלשה";
                result.f_medium = "בינונית";
                result.f_strong = "חזקה";
                result.very_f_strong = "חזקה מאוד";
                result.otherwise_start_over = "אחרת, יידרש להתחיל מחדש";
                result.close = "סגירה";
                result.edit = "עריכה";
                result.save = "שמירה";
                result.name = "שם";
                result.login = "התחברות";
                result['delete'] = "מחיקה";
                result.cancel = "ביטול";
                result.ok = "אישור";
                result.actions = "פעולות";
                result.change_website_language = "שינוי שפת האתר";
                result.users_management = "ניהול משתמשים";
                result.messages_management = "ניהול הודעות";
                result.pictures_management = "ניהול תמונות";
                result.albums_management = "ניהול אלבומים";
                result.events_management = "ניהול אירועים";
                result.privileges_management = "ניהול הרשאות";
                result.roles = "חוקים";
                result.less = "פחות";
                result.more = "יותר";
                result.description = "תיאור";
                result.create_role = "יצירת חוק";
                result.capabilities = "יכולות";
                result.create_capability = "יצירת יכולת";
                result.confirm_unstopable_action = "האם להמשיך? פעולה זו לא ניתנת לביטול.";
                result.do_you_want_to_continue = "האם ברצונך להמשיך?";
                result.alert_max_32_chars = "מקסימום 32 תווים";
                result.alert_max_255_chars = "מקסימום 255 תווים";
                result.error_session_timeout = "פג תוקף זמן הפעילות שלך";
                result.error_unknown = "שגיאה לא ידועה";
                result.error_field_missing = "שדה חובה חסר";
                result.error_only_ascii_html = 'אסור תווים שאינם ASCII.<br /><a class="help" href="https://he.wikipedia.org/wiki/ASCII" target="_blank" title="הסבר על התווים">עזרה</a>';
                result.kendo_grid_pageable_display = "פריטים {0} - {1} מתוך {2}";
                result.kendo_grid_pageable_empty = "אין פריטים להציג";
                result.kendo_grid_pageable_page = "עמוד";
                result.kendo_grid_pageable_of = "מתוך {0}";
                result.kendo_grid_pageable_itemsPerPage = "פריטים בעמוד";
                result.kendo_grid_pageable_first = "עבור לעמוד הראשון";
                result.kendo_grid_pageable_previous = "עבור לעמוד הקודם";
                result.kendo_grid_pageable_next = "עבור לעמוד הבא";
                result.kendo_grid_pageable_last = "עבור לעמוד האחרון";
                result.kendo_grid_pageable_refresh = "רענון";
                result.israel = "ישראל";
                break;
        }
        return result;
    })(),
    Line: function (key) {
        var translation = App.Lang._Dictionary[key.toLowerCase()];
        return (!translation ? key : translation);
    }
};
App.Regex = {
    ASCII: /^[\x00-\x7F]*$/
};
App.Kendo = {
    Grid: {
        Pageable: {
            DefaultMessages: {
                "display": App.Lang.Line('kendo_grid_pageable_display'),
                "empty": App.Lang.Line('kendo_grid_pageable_empty'),
                "page": App.Lang.Line('kendo_grid_pageable_page'),
                "of": App.Lang.Line('kendo_grid_pageable_of'),
                "itemsPerPage": App.Lang.Line('kendo_grid_pageable_itemsPerPage'),
                "first": App.Lang.Line('kendo_grid_pageable_first'),
                "previous": App.Lang.Line('kendo_grid_pageable_previous'),
                "next": App.Lang.Line('kendo_grid_pageable_next'),
                "last": App.Lang.Line('kendo_grid_pageable_last'),
                "refresh": App.Lang.Line('kendo_grid_pageable_refresh')
            }
        }
    }
};
App.ImageSrc = function (path) {
    return App.Config.image_url + path;
};
App.Utils = {
    toStringOrDefault: function (arg, defaultValue) {
        var tempTypeof = typeof arg;
        if (tempTypeof === 'function') {
            arg = arg();
        } else if (tempTypeof === 'object' && typeof arg.toString === 'function') {
            arg = arg.toString();
        }
        if (typeof arg !== 'string' || arg === '[object Object]') {
            arg = defaultValue;
        }
        return arg;
    },
    confirm: function (options) {
        var isConfirm = false;
        var tempTypeof = typeof options;

        if (tempTypeof === 'string') {
            options = {message: options};
        } else if (tempTypeof === 'function') {
            options = {message: options()};
        } else if (tempTypeof !== 'object') {
            options = {};
        }

        options.message = App.Utils.toStringOrDefault(options.message, App.Lang.Line('do_you_want_to_continue'));

        function done() {
            if (isConfirm === true && typeof options.confirm === 'function') {
                options.confirm();
            } else if (isConfirm !== true && typeof options.cancel === 'function') {
                options.cancel();
            }
        }

        if (typeof jQuery.ui !== 'object') {
            setTimeout(function () {
                isConfirm = confirm(options.message);
                done();
            }, 0);
        } else {
            options.confirmText = App.Utils.toStringOrDefault(options.confirmText, App.Lang.Line("OK"));
            options.cancelText = App.Utils.toStringOrDefault(options.cancelText, App.Lang.Line("Cancel"));
            options.closeText = App.Utils.toStringOrDefault(options.closeText, App.Lang.Line("Close"));
            options.dialogClass = App.Utils.toStringOrDefault(options.dialogClass, "confirm-dialog");

            options.modal = (typeof options.modal === 'boolean' ? options.modal : true);
            options.buttons = [
                {
                    text: options.confirmText,
                    class: 'dialog-confirm',
                    click: function () {
                        isConfirm = true;
                        $(this).dialog('close');
                        done();
                    }
                }, {
                    text: options.cancelText,
                    class: 'dialog-cancel',
                    click: function () {
                        $(this).dialog('close');
                        done();
                    }
                }
            ];
            $("<div>").text(options.message).dialog(options);
        }
    },
    changeLanguage: function (options) {
        if (typeof options !== 'object') {
            options = {};
        }
        options.url = '/languages/changeAjax';
        options.method = options.method || 'POST';
        options.dataType = options.dataType || 'json';
        $.ajax(options);
    }
};

// .language-selection
if ($('.language-selection').length) {
    (function () {
        var $languageSelection = $('.language-selection');
        var $languageSelect = $languageSelection.find('select');

        $languageSelect.on('change', function (e) {
            
            App.Utils.confirm({
                title: App.Lang.Line("change_website_language"),
                confirm: function () {
                    var options = {
                        data: {language_id: $(e.target).val()},
                        success: function (response) {
                            location.reload();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            debugger;
                        }
                    };
                    App.Utils.changeLanguage(options);
                }
            });
        });
    })();
}