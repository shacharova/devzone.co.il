/* global App */

// URL: /users/login_form
if ($('#users-login-form-view').length) {
    (function () {
        var $usersLoginFormView = $('#users-login-form-view');
        var $emailVerifyForm = $usersLoginFormView.find('form.email-verify');
        var $emailInput = $emailVerifyForm.find('input[name="email"]');
        var $nextButton = $emailVerifyForm.find('button.next');
        var $passwordVerifyForm = $usersLoginFormView.find('form.password-verify');
        var $passwordInput = $passwordVerifyForm.find('input[name="password"]');
        var $submitButton = $passwordVerifyForm.find('button.submit');
        var $profileImage = $usersLoginFormView.find('img.profile-image');
        var defaultImage = $profileImage.prop('src');

        function showPasswordForm(user) {
            $emailVerifyForm.fadeOut(200, function () {
                if (user.profile_image_path) {
                    $profileImage.attr('src', App.ImageSrc(user.profile_image_path));
                }
                var fullName = $.grep([user.first_name, user.last_name], Boolean).join(' ');

                $passwordVerifyForm.find('span.fullname').text(fullName);
                $passwordVerifyForm.find('span.email').text(user.email);
                $usersLoginFormView.find('button.back').show();

                $passwordVerifyForm.removeClass('displayNone');
                $passwordVerifyForm.fadeIn(200);
                $passwordVerifyForm[0].reset();
                $passwordInput.focus();
            });
        }
        function showEmailVerifyForm(callback) {
            $usersLoginFormView.find('button.back').hide();
            $profileImage.prop('src', defaultImage);
            $emailVerifyForm[0].reset();
            $emailInput.removeData('previousValue');
            $passwordVerifyForm.fadeOut(200, function () {
                $emailVerifyForm.fadeIn(200, callback);
                $emailInput.focus();
            });
        }
        function emailDataFilter(response, type) {
            if (type === 'json' || type === 'jsonp') {
                response = JSON.parse(response);
            }
            if (response instanceof Object) {
                if (typeof response.redirectURL === 'string') {
                    window.location = response.redirectURL;
                    return true;
                } else if (response.isSuccess === true && response.user instanceof Object) {
                    showPasswordForm(response.user);
                    return true;
                } else if (!jQuery.isEmptyObject(response.messages)) {
                    return JSON.stringify(Object.valueAtIndex(response.messages, 0));
                }
            }
            return false; // Default error message
        }
        function passwordDataFilter(response, type) {
            if (type === 'json' || type === 'jsonp') {
                response = JSON.parse(response);
            }
            if (response instanceof Object) {
                if (typeof response.redirectURL === 'string') {
                    window.location = response.redirectURL;
                    return true;
                } else if (response.isSuccess === true) {
                    showPasswordForm(response.user);
                    return true;
                } else if (!jQuery.isEmptyObject(response.messages)) {
                    if (response.messages.error_email_missing) {
                        showEmailVerifyForm(function () {
                            alert(App.Lang.Line('error_session_timeout'));
                        });
                    } else if (response.messages.alert_last_attempt) {
                        return JSON.stringify(response.messages.alert_last_attempt);
                    } else if (response.messages.error_too_many_attempts) {
                        showEmailVerifyForm(function () {
                            alert(response.messages.error_too_many_attempts)
                        });
                    } else {
                        return JSON.stringify(Object.valueAtIndex(response.messages, 0));
                    }
                    return true;
                }
            }
            return false;
        }

        $emailVerifyForm.validate({
            onfocusout: false,
            onkeyup: false,
            rules: {
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: '/users/emailVerifyAjax',
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {
                            $($emailInput, $nextButton).prop('disabled', true);
                        },
                        dataFilter: emailDataFilter,
                        complete: function () {
                            $($emailInput, $nextButton).prop('disabled', false);
                        }
                    }
                }
            },
            submitHandler: function () { },
            invalidHandler: undefined
        });
        $passwordVerifyForm.validate({
            onfocusout: false,
            onkeyup: false,
            rules: {
                password: {
                    required: true,
                    rangelength: [6, 12],
                    remote: {
                        url: '/users/passwordVerifyAjax',
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {
                            $($passwordInput, $submitButton).prop('disabled', true);
                        },
                        dataFilter: passwordDataFilter,
                        complete: function () {
                            $($passwordInput, $submitButton).prop('disabled', false);
                        }
                    }
                }
            },
            submitHandler: function (form) {
            },
            invalidHandler: undefined
        });

        $usersLoginFormView.find('button.back').on('click', function () {
            showEmailVerifyForm();
        });
    })();
}

// URL: /users/select_logged_in
if ($('#users-select-logged-in-view').length) {
    (function () {
        var $usersSelectLoggedInView = $('#users-select-logged-in-view');
        var $redirectForm = $usersSelectLoggedInView.find('form.redirect');
        $usersSelectLoggedInView.on('click', '.user', function () {
            var email = $(this).find('.info .email').text();
            $redirectForm.find('[name="email"]').val(email);
            $redirectForm.submit();
        });
        $usersSelectLoggedInView.on('click', '.actions .logout', function (e) {
            e.stopPropagation(); // Prevent from parent element to handle click event
            var isConfirm = confirm(App.Lang.Line('confirm_unstopable_action'));
            if (isConfirm === true) {
                var $user = $(this).parent().parent();
                var email = $user.find('.info .email').text();
                var $button = $(this);
                $.ajax({
                    url: '/users/logoutAjax',
                    method: 'POST',
                    data: {'email': email}, // Email to logout
                    dataType: 'json',
                    beforeSend: function () {
                        $button.prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.isSuccess === true) {
                            $user.fadeOut(200, function () {
                                $(this).remove();
                            });
                        }
                    },
                    error: undefined,
                    complete: function () {
                        $button.prop('disabled', true);
                    }
                });
            }
        });
    })();
}

// URL: /users/signup_form
if ($('#users-signup-form-view').length) {
    (function () {
        var $signupFormView = $('#users-signup-form-view');
        var $signupForm = $signupFormView.find('form.signup');
        var $emailInput = $signupForm.find('input[name="email"]');
        var $emailConfirmInput = $signupForm.find('input[name="email_confirm"]');
        var $passwordInput = $signupForm.find('input[name="password"]');
        var $passwordConfirmInput = $signupForm.find('input[name="password_confirm"]');
        var $termsInput = $signupForm.find('input[name="terms"]');
        var $submitButton = $signupForm.find('button.submit');
        function setFormDisabled(isDisabled) {
            $emailInput.prop('disabled', isDisabled);
            $emailConfirmInput.prop('disabled', isDisabled);
            $passwordInput.prop('disabled', isDisabled);
            $passwordConfirmInput.prop('disabled', isDisabled);
            $termsInput.prop('disabled', isDisabled);
            $submitButton.prop('disabled', isDisabled);
        }
        function signupAjax() {
            var data = $signupForm.serializeObject();
            $.ajax({
                url: '/users/signupAjax',
                method: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    setFormDisabled(true);
                },
                success: function (response) {
                    if (response.isSuccess === true && response.redirectURL) {
                        window.location = response.redirectURL;
                    } else if (response.messages instanceof Object) {
                        var validator = $signupForm.data('validator');
                        var errors = {};

                        if (response.messages.email_already_registered) {
                            errors['email'] = response.messages.email_already_registered;
                        } else if (response.messages.error_password_missing) {
                            errors['password'] = response.messages.error_password_missing;
                        } else if (response.messages.error_login_failed) {
                            errors['terms'] = response.messages.error_password_missing;
                        }

                        validator.showErrors(errors);
                    }
                },
                error: function (jqXHR, textStatus) {
                    var validator = $signupForm.data('validator');
                    validator.showErrors({terms: App.Lang.Line('error_unknown')});
                    console.error(jqXHR);
                    console.error(textStatus);
                },
                complete: function () {
                    setFormDisabled(false);
                }
            });
        }

        $passwordInput.tooltip({
            tooltipClass: 'signup-password',
            disabled: false,
            position: {
                tooltipClass: 'tooltip',
                my: 'left top', at: 'left bottom'
            },
            close: function (event, ui) {
                if ($passwordInput.is(':focus')) {
                    $passwordInput.tooltip('open');
                }
            }
        }).on('keyup', function (e) {
            var passwordStrength = $(this).val().passwordStrength(), content;
            var $tooltip = $('.signup-password');
            if (passwordStrength <= 0) {
                content = '';
            } else if (passwordStrength <= 0.2) {
                content = App.Lang.Line('very_f_week');
                $passwordInput.tooltip('option', 'tooltipClass', 'signup-password very-week');
            } else if (passwordStrength <= 0.4) {
                content = App.Lang.Line('f_week');
                $passwordInput.tooltip('option', 'tooltipClass', 'signup-password week');
            } else if (passwordStrength <= 0.6) {
                content = App.Lang.Line('f_medium');
                $passwordInput.tooltip('option', 'tooltipClass', 'signup-password medium');
            } else if (passwordStrength <= 0.8) {
                content = App.Lang.Line('f_strong');
                $passwordInput.tooltip('option', 'tooltipClass', 'signup-password strong');
            } else {
                content = App.Lang.Line('very_f_strong');
                $passwordInput.tooltip('option', 'tooltipClass', 'signup-password very-strong');
            }

            $passwordInput.attr('title', content);
            $passwordInput.tooltip('option', 'content', content);
            if (content && (!$tooltip.length || !$tooltip.is(':visible'))) {
                $passwordInput.tooltip('open');
            } else {
                $passwordInput.tooltip('close');
            }
        });

        $signupForm.validate({
            onfocusout: false,
            onkeyup: false,
            errorPlacement: function ($error, $element) {
                if ($element.is('[name="terms"]')) {
                    $error.insertAfter($('label[for="terms"]'));
                } else {
                    $error.insertAfter($element);
                }
            },
            rules: {
                email: {
                    required: true,
                    email: true
                },
                email_confirm: {
                    equalToI: '#' + $emailInput.attr('id')
                },
                password: {
                    required: true,
                    rangelength: [6, 12]
                },
                password_confirm: {
                    equalTo: '#' + $passwordInput.attr('id')
                },
                terms: {
                    required: true
                }
            }
        });

        $submitButton.on('click', function () {
            $('form.signup').data('validator').resetForm();

            $passwordInput.tooltip('disable');

            var isValid = $emailInput.valid() && $emailConfirmInput.valid()
                    && $passwordInput.valid() && $passwordConfirmInput.valid() && $termsInput.valid();

            $passwordInput.tooltip('enable');
            if (isValid) {
                signupAjax();
            }
        });
    })();
}

// URL: /users/personal_details_form
if ($('#users-personal-details-form-view').length) {
    (function () {
        var $personalDetailsFormView = $('#users-personal-details-form-view');
        var $genderInputs = $personalDetailsFormView.find('input[name="gender"]');
        var $calenderIcon = $personalDetailsFormView.find('.birthday-field .icon .fa-calendar');
        var $birthdayInput = $personalDetailsFormView.find('input[name="birthday"]');
        var $yearInput = $personalDetailsFormView.find('input.year');
        var $monthInput = $personalDetailsFormView.find('input.month');
        var $dayInput = $personalDetailsFormView.find('input.day');
        var $countryInput = $personalDetailsFormView.find('input.country');
        var $countryIdInput = $personalDetailsFormView.find('input[name="countryId"]');
        var $localityInput = $personalDetailsFormView.find('input.locality');
        var $localityIdInput = $personalDetailsFormView.find('input[name="localityId"]');
        var maxAutocompleteResults = 6;
        var countries = {};
        var localities = [];
        var defaultCountryId = parseInt($countryInput.data('value'));
        
        function setDatePickerDate() {
            var year = parseInt($yearInput.val());
            var month = parseInt($monthInput.val());
            var day = parseInt($dayInput.val());
            if (Date.exists(year, month - 1, day)) {
                $birthdayInput.datepicker('setDate', day + '/' + month + '/' + year);
            } else {
                // TODO: Handle NOT exists date
            }
        }
        function selectCountry(label, value) {
            $countryInput.val(label);
            $countryIdInput.val(value);
        }
        function selectLocality(label, value) {
            $localityInput.val(label);
            $localityIdInput.val(value);
        }
        function updateLocalitiesByCountry(countryId) {
            $.ajax({
                dataType: 'json',
                type: 'POST',
                data: {countryId: countryId},
                url: '/localities/getByCountryAjax',
                beforeSend: function () {
                    selectLocality('', '');
                    $localityInput.prop('disabled', true);
                },
                success: function (data) {
                    localities = data;
                    $localityInput.prop('disabled', !localities || !localities.length);
                }
            });
        }
        function searchStartWithThenContains(data, term, limit, labelPropName, isCaseSensitive) {
            var startsWith = [], contains = [];
            for (var i = 0; i < data.length; ++i) {
                var value = data[i][labelPropName];
                var matchIndex = (isCaseSensitive ? value.indexOf(term) : value.toLowerCase().indexOf(term.toLowerCase()));
                if (matchIndex !== 0) {
                    var translated_value = App.Lang.Line(value);
                    if (translated_value !== value) {
                        var transMatchIndex = (isCaseSensitive ? translated_value.indexOf(term) : translated_value.toLowerCase().indexOf(term.toLowerCase()));
                        if (transMatchIndex === 0) {
                            matchIndex = 0;
                        } else if (matchIndex < 0) {
                            matchIndex = transMatchIndex;
                        }
                    }
                }
                if (matchIndex === 0) {
                    startsWith.push(data[i]);
                    if (startsWith.length === limit) {
                        return startsWith;
                    }
                } else if (matchIndex >= 1 && contains.length < limit) {
                    contains.push(data[i]);
                }
            }
            return startsWith.concat(contains.slice(0, limit - startsWith.length));
        }
        function translateAutocompleteLabel(label) {
            var translated_label = App.Lang.Line(label);
            return (translated_label === label ? label : translated_label + ' (' + label + ')');
        }
        
        $birthdayInput.datepicker({
            onSelect: function (date, datepicker) {
                $yearInput.val(datepicker.selectedYear);
                $monthInput.val(datepicker.selectedMonth + 1);
                $dayInput.val(datepicker.selectedDay);
            }
        });
        $countryInput.autocomplete({
            minLength: 0,
            autoFocus: false,
            delay: 300,
            focus: function (event, ui) {
                return false;
            },
            select: function (e, ui) {
                e.preventDefault();
                selectCountry(ui.item.label, ui.item.value);
                updateLocalitiesByCountry(ui.item.value);
            },
            source: function (request, response) {
                var searchResult = searchStartWithThenContains(countries, request.term, maxAutocompleteResults, 'name');
                response(searchResult.map(function (country) {
                    return {label: translateAutocompleteLabel(country.name), value: country.id};
                }));
            }
        });
        $countryInput.on('focusin', function () {
            $countryInput.autocomplete('instance').search('');
        });
        $localityInput.autocomplete({
            minLength: 0,
            autoFocus: false,
            delay: 300,
            focus: function (event, ui) {
                return false;
            },
            select: function (e, ui) {
                e.preventDefault();
                selectLocality(ui.item.label, ui.item.value);
            },
            source: function (request, response) {
                var searchResult = searchStartWithThenContains(localities, request.term, maxAutocompleteResults, 'name');
                response(searchResult.map(function (locality) {
                    return {
                        label: translateAutocompleteLabel(locality.name),
                        value: locality.location_id // TODO: Replace location_id with id (locality_id)
                    };
                }));
            }
        });
        $localityInput.on('focusin', function () {
            $localityInput.autocomplete('instance').search('');
        });

        $calenderIcon.on('click', function () {
            $birthdayInput.datepicker('show');
        });
        $yearInput.on('change', setDatePickerDate);
        $monthInput.on('change', setDatePickerDate);
        $dayInput.on('change', setDatePickerDate);
        $genderInputs.on('change', function() {
            debugger;
        });
        updateLocalitiesByCountry(defaultCountryId);
        
        // Initialize all countries (AJAX):
        $.ajax({
            dataType: 'json',
            type: 'POST',
            async: true,
            url: '/countries/getAll',
            beforeSend: function () {
                $countryInput.prop('disabled', true);
            },
            success: function (data) {
                countries = data;
                var countryIdString = defaultCountryId.toString();
                for(var k in countries) {
                    if(countries[k].id === countryIdString) {
                        var translatedLabel = translateAutocompleteLabel(countries[k].name);
                        selectCountry(translatedLabel, countries[k].id);
                        $countryInput.autocomplete('instance').selectedItem = {label: translatedLabel, value: countries[k].id};
                    }
                }
                
                if (countries.length > 0) {
                    $countryInput.prop('disabled', false);
                }
            }
        });
    })();
}
