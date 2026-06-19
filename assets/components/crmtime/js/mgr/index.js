document.addEventListener('DOMContentLoaded', function () {
    var pingBtn = document.getElementById('crmtime-ping-btn');
    var pingMessage = document.getElementById('crmtime-ping-message');

    var installBtn = document.getElementById('crmtime-install-btn');
    var installMessage = document.getElementById('crmtime-install-message');

    var globalMessage = document.getElementById('crmtime-global-message');

    var statApprovals = document.getElementById('crmtime-stat-approvals');
    var statViolations = document.getElementById('crmtime-stat-violations');
    var statTimesheets = document.getElementById('crmtime-stat-timesheets');

    var customerFormTitle = document.getElementById('crmtime-customer-form-title');
    var customerIdField = document.getElementById('crmtime-customer-id');
    var saveCustomerBtn = document.getElementById('crmtime-save-customer-btn');
    var cancelCustomerBtn = document.getElementById('crmtime-cancel-customer-btn');
    var customerMessage = document.getElementById('crmtime-customer-message');
    var customersList = document.getElementById('crmtime-customers-list');
    var customerName = document.getElementById('crmtime-customer-name');
    var customerCode = document.getElementById('crmtime-customer-code');
    var customerDescription = document.getElementById('crmtime-customer-description');

    var workplaceFormTitle = document.getElementById('crmtime-workplace-form-title');
    var workplaceIdField = document.getElementById('crmtime-workplace-id');
    var saveWorkplaceBtn = document.getElementById('crmtime-save-workplace-btn');
    var cancelWorkplaceBtn = document.getElementById('crmtime-cancel-workplace-btn');
    var workplaceMessage = document.getElementById('crmtime-workplace-message');
    var workplacesList = document.getElementById('crmtime-workplaces-list');
    var workplaceCustomerId = document.getElementById('crmtime-workplace-customer-id');
    var workplaceName = document.getElementById('crmtime-workplace-name');
    var workplaceAddress = document.getElementById('crmtime-workplace-address');

    var assignmentFormTitle = document.getElementById('crmtime-assignment-form-title');
    var assignmentIdField = document.getElementById('crmtime-assignment-id');
    var saveAssignmentBtn = document.getElementById('crmtime-save-assignment-btn');
    var cancelAssignmentBtn = document.getElementById('crmtime-cancel-assignment-btn');
    var usersMessage = document.getElementById('crmtime-users-message');
    var assignmentMessage = document.getElementById('crmtime-assignment-message');
    var assignmentsList = document.getElementById('crmtime-assignments-list');
    var assignmentUserId = document.getElementById('crmtime-assignment-user-id');
    var assignmentCustomerId = document.getElementById('crmtime-assignment-customer-id');
    var assignmentWorkplaceId = document.getElementById('crmtime-assignment-workplace-id');
    var assignmentStartDate = document.getElementById('crmtime-assignment-start-date');
    var assignmentEndDate = document.getElementById('crmtime-assignment-end-date');

    var employeeFormTitle = document.getElementById('crmtime-employee-form-title');
    var employeeIdField = document.getElementById('crmtime-employee-id');
    var employeeUsername = document.getElementById('crmtime-employee-username');
    var employeeFullname = document.getElementById('crmtime-employee-fullname');
    var employeeEmail = document.getElementById('crmtime-employee-email');
    var employeeActive = document.getElementById('crmtime-employee-active');
    var employeeColor = document.getElementById('crmtime-employee-color');
    var employeeCode = document.getElementById('crmtime-employee-code');
    var employeeNote = document.getElementById('crmtime-employee-note');
    var employeeStandardRate = document.getElementById('crmtime-employee-standard-rate');
    var employeeNightCoeff = document.getElementById('crmtime-employee-night-coeff');
    var employeeSundayCoeff = document.getElementById('crmtime-employee-sunday-coeff');
    var employeeHolidayCoeff = document.getElementById('crmtime-employee-holiday-coeff');
    var employeeHomeAddress = document.getElementById('crmtime-employee-home-address');
    var saveEmployeeBtn = document.getElementById('crmtime-save-employee-btn');
    var cancelEmployeeBtn = document.getElementById('crmtime-cancel-employee-btn');
    var employeeMessage = document.getElementById('crmtime-employee-message');
    var employeesList = document.getElementById('crmtime-employees-list');

    var timesheetFormTitle = document.getElementById('crmtime-timesheet-form-title');
    var timesheetIdField = document.getElementById('crmtime-timesheet-id');
    var saveTimesheetBtn = document.getElementById('crmtime-save-timesheet-btn');
    var cancelTimesheetBtn = document.getElementById('crmtime-cancel-timesheet-btn');
    var timesheetMessage = document.getElementById('crmtime-timesheet-message');
    var timesheetsList = document.getElementById('crmtime-timesheets-list');
    var timesheetAssignmentId = document.getElementById('crmtime-timesheet-assignment-id');
    var timesheetWorkDate = document.getElementById('crmtime-timesheet-work-date');
    var timesheetStartTime = document.getElementById('crmtime-timesheet-start-time');
    var timesheetEndTime = document.getElementById('crmtime-timesheet-end-time');
    var timesheetIsNight = document.getElementById('crmtime-timesheet-is-night');
    var timesheetIsSunday = document.getElementById('crmtime-timesheet-is-sunday');
    var timesheetIsHoliday = document.getElementById('crmtime-timesheet-is-holiday');

    var violationMessage = document.getElementById('crmtime-violation-message');
    var violationsList = document.getElementById('crmtime-violations-list');

    var calendarApplyBtn = document.getElementById('crmtime-calendar-apply-btn');
    var calendarResetBtn = document.getElementById('crmtime-calendar-reset-btn');
    var calendarMessage = document.getElementById('crmtime-calendar-message');
    var calendarEl = document.getElementById('crmtime-manager-calendar');
    var calendarFilterUserId = document.getElementById('crmtime-calendar-user-id');
    var calendarFilterCustomerId = document.getElementById('crmtime-calendar-customer-id');
    var calendarFilterWorkplaceId = document.getElementById('crmtime-calendar-workplace-id');
    var calendarFilterStatus = document.getElementById('crmtime-calendar-status');
    var daydetailsTitle = document.getElementById('crmtime-daydetails-title');
    var daydetailsMessage = document.getElementById('crmtime-daydetails-message');
    var daydetailsList = document.getElementById('crmtime-daydetails-list');
    var calendarInstance = null;
    var currentCalendarDate = '';

    var approvalMessage = document.getElementById('crmtime-approval-message');
    var approvalsList = document.getElementById('crmtime-approvals-list');

    var reportRunBtn = document.getElementById('crmtime-report-run-btn');
    var reportPdfBtn = document.getElementById('crmtime-report-pdf-btn');
    var reportDateFrom = document.getElementById('crmtime-report-date-from');
    var reportDateTo = document.getElementById('crmtime-report-date-to');
    var reportCustomerId = document.getElementById('crmtime-report-customer-id');
    var reportUserId = document.getElementById('crmtime-report-user-id');
    var reportMessage = document.getElementById('crmtime-report-message');
    var reportStats = document.getElementById('crmtime-report-stats');
    var reportEmployees = document.getElementById('crmtime-report-employees');
    var reportRows = document.getElementById('crmtime-report-rows');

    var documentsRefreshBtn = document.getElementById('crmtime-documents-refresh-btn');
    var documentsMessage = document.getElementById('crmtime-documents-message');
    var documentsList = document.getElementById('crmtime-documents-list');

    var currentCustomers = [];
    var currentWorkplaces = [];
    var currentAssignments = [];
    var currentTimesheets = [];
    var currentUsers = [];

    function initTabs() {
        var tabButtons = Array.prototype.slice.call(document.querySelectorAll('.crmtime-tab-btn'));
        var tabPanels = Array.prototype.slice.call(document.querySelectorAll('.crmtime-tab-panel'));

        function activateTab(tabName) {
            tabButtons.forEach(function (btn) {
                btn.classList.toggle('is-active', btn.getAttribute('data-tab') === tabName);
            });

            tabPanels.forEach(function (panel) {
                panel.classList.toggle('is-active', panel.getAttribute('data-tab-panel') === tabName);
            });
        }

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                activateTab(btn.getAttribute('data-tab'));
            });
        });
    }

    function getModAuth() {
        if (window.MODx) {
            if (MODx.siteId) {
                return MODx.siteId;
            }
            if (MODx.config) {
                if (MODx.config.siteId) {
                    return MODx.config.siteId;
                }
                if (MODx.config.site_id) {
                    return MODx.config.site_id;
                }
            }
        }
        return '';
    }

    function setMessage(el, type, text) {
        if (!el) {
            return;
        }

        if (!text) {
            el.style.display = 'none';
            el.className = 'crmtime-message';
            el.innerHTML = '';
            return;
        }

        el.style.display = '';
        el.className = 'crmtime-message crmtime-message-' + type;
        el.innerHTML = text;
    }

    function setStat(el, value) {
        if (el) {
            el.textContent = String(value || 0);
        }
    }

    function request(action, payload) {
        payload = payload || {};

        var modAuth = getModAuth();
        var formData = new FormData();

        formData.append('action', action);
        formData.append('HTTP_MODAUTH', modAuth);

        Object.keys(payload).forEach(function (key) {
            formData.append(key, payload[key]);
        });

        return fetch(window.crmTimeConfig.connector_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'modAuth': modAuth
            }
        }).then(function (response) {
            return response.text();
        }).then(function (text) {
            try {
                return JSON.parse(text);
            } catch (e) {
                return {
                    success: false,
                    message: text
                };
            }
        });
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalizeTime(value) {
        value = String(value || '');
        if (value.length >= 5) {
            return value.substring(0, 5);
        }
        return value;
    }

    function fillSelect(select, items, valueKey, textBuilder, placeholder, selectedValue) {
        if (!select) {
            return;
        }

        var html = '<option value="">' + escapeHtml(placeholder || 'Выберите') + '</option>';

        (items || []).forEach(function (item) {
            var value = String(item[valueKey]);
            var selected = (selectedValue !== undefined && String(selectedValue) === value) ? ' selected' : '';
            html += '<option value="' + escapeHtml(value) + '"' + selected + '>' + escapeHtml(textBuilder(item)) + '</option>';
        });

        select.innerHTML = html;
    }

    function buildTariffFlagsText(item) {
        var flags = [];

        if (parseInt(item.is_night, 10) === 1) {
            flags.push('Ночь');
        }
        if (parseInt(item.is_sunday, 10) === 1) {
            flags.push('Воскресенье');
        }
        if (parseInt(item.is_holiday, 10) === 1) {
            flags.push('Праздник');
        }

        return flags.length ? flags.join(', ') : 'Стандарт';
    }

    function formatFileSize(bytes) {
        bytes = parseFloat(bytes || 0);
        if (!bytes || bytes < 1024) {
            return bytes ? bytes.toFixed(0) + ' B' : '0 B';
        }
        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function resetCustomerForm() {
        customerIdField.value = '';
        customerName.value = '';
        customerCode.value = '';
        customerDescription.value = '';
        customerFormTitle.textContent = 'Заказчики';
        saveCustomerBtn.textContent = 'Сохранить заказчика';
        cancelCustomerBtn.style.display = 'none';
    }

    function setCustomerEditMode(item) {
        customerIdField.value = item.id;
        customerName.value = item.name || '';
        customerCode.value = item.code || '';
        customerDescription.value = item.description || '';
        customerFormTitle.textContent = 'Редактирование заказчика';
        saveCustomerBtn.textContent = 'Сохранить изменения';
        cancelCustomerBtn.style.display = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetWorkplaceForm() {
        workplaceIdField.value = '';
        workplaceCustomerId.value = '';
        workplaceName.value = '';
        workplaceAddress.value = '';
        workplaceFormTitle.textContent = 'Места работы';
        saveWorkplaceBtn.textContent = 'Сохранить место работы';
        cancelWorkplaceBtn.style.display = 'none';
    }

    function setWorkplaceEditMode(item) {
        workplaceIdField.value = item.id;
        workplaceCustomerId.value = item.customer_id || '';
        workplaceName.value = item.name || '';
        workplaceAddress.value = item.address || '';
        workplaceFormTitle.textContent = 'Редактирование места работы';
        saveWorkplaceBtn.textContent = 'Сохранить изменения';
        cancelWorkplaceBtn.style.display = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetAssignmentForm() {
        assignmentIdField.value = '';
        assignmentUserId.value = '';
        assignmentCustomerId.value = '';
        assignmentWorkplaceId.value = '';
        assignmentStartDate.value = '';
        assignmentEndDate.value = '';
        assignmentFormTitle.textContent = 'Назначения';
        saveAssignmentBtn.textContent = 'Сохранить назначение';
        cancelAssignmentBtn.style.display = 'none';
    }

    function setAssignmentEditMode(item) {
        assignmentIdField.value = item.id;
        assignmentUserId.value = item.user_id || '';
        assignmentCustomerId.value = item.customer_id || '';
        assignmentWorkplaceId.value = item.workplace_id || '';
        assignmentStartDate.value = item.start_date || '';
        assignmentEndDate.value = item.end_date || '';
        assignmentFormTitle.textContent = 'Редактирование назначения';
        saveAssignmentBtn.textContent = 'Сохранить изменения';
        cancelAssignmentBtn.style.display = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetEmployeeForm() {
        employeeIdField.value = '';
        employeeUsername.value = '';
        employeeFullname.value = '';
        employeeEmail.value = '';
        employeeActive.checked = true;
        employeeColor.value = '#3788d8';
        employeeCode.value = '';
        employeeNote.value = '';
        employeeStandardRate.value = '0.00';
        employeeNightCoeff.value = '1.00';
        employeeSundayCoeff.value = '1.00';
        employeeHolidayCoeff.value = '1.00';
        employeeHomeAddress.value = '';
        employeeFormTitle.textContent = 'Сотрудники CRM';
        saveEmployeeBtn.textContent = 'Сохранить CRM-настройки';
        cancelEmployeeBtn.style.display = 'none';
    }

    function setEmployeeEditMode(item) {
        employeeIdField.value = item.id;
        employeeUsername.value = item.username || '';
        employeeFullname.value = item.fullname || '';
        employeeEmail.value = item.email || '';
        employeeActive.checked = parseInt(item.crm_active, 10) === 1;
        employeeColor.value = item.color || '#3788d8';
        employeeCode.value = item.crm_code || '';
        employeeNote.value = item.crm_note || '';
        employeeStandardRate.value = item.standard_rate || '0.00';
        employeeNightCoeff.value = item.night_coeff || '1.00';
        employeeSundayCoeff.value = item.sunday_coeff || '1.00';
        employeeHolidayCoeff.value = item.holiday_coeff || '1.00';
        employeeHomeAddress.value = item.home_address || '';
        employeeFormTitle.textContent = 'Редактирование CRM-настроек сотрудника';
        saveEmployeeBtn.textContent = 'Сохранить изменения';
        cancelEmployeeBtn.style.display = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetTimesheetForm() {
        timesheetIdField.value = '';
        timesheetAssignmentId.value = '';
        timesheetWorkDate.value = '';
        timesheetStartTime.value = '';
        timesheetEndTime.value = '';

        if (timesheetIsNight) {
            timesheetIsNight.checked = false;
        }
        if (timesheetIsSunday) {
            timesheetIsSunday.checked = false;
        }
        if (timesheetIsHoliday) {
            timesheetIsHoliday.checked = false;
        }

        timesheetFormTitle.textContent = 'Фактическое время';
        saveTimesheetBtn.textContent = 'Сохранить запись времени';
        cancelTimesheetBtn.style.display = 'none';
    }

    function setTimesheetEditMode(item) {
        timesheetIdField.value = item.id;
        timesheetAssignmentId.value = item.assignment_id || '';
        timesheetWorkDate.value = item.work_date || '';
        timesheetStartTime.value = normalizeTime(item.start_time);
        timesheetEndTime.value = normalizeTime(item.end_time);

        if (timesheetIsNight) {
            timesheetIsNight.checked = parseInt(item.is_night, 10) === 1;
        }
        if (timesheetIsSunday) {
            timesheetIsSunday.checked = parseInt(item.is_sunday, 10) === 1;
        }
        if (timesheetIsHoliday) {
            timesheetIsHoliday.checked = parseInt(item.is_holiday, 10) === 1;
        }

        timesheetFormTitle.textContent = 'Редактирование записи времени';
        saveTimesheetBtn.textContent = 'Сохранить изменения';
        cancelTimesheetBtn.style.display = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function renderCustomers(items) {
        currentCustomers = items || [];

        var selectedAssignmentCustomer = assignmentCustomerId ? assignmentCustomerId.value : '';
        var selectedCalendarCustomer = calendarFilterCustomerId ? calendarFilterCustomerId.value : '';

        if (!items || !items.length) {
            customersList.innerHTML = '<p>Пока нет заказчиков.</p>';
            fillSelect(assignmentCustomerId, [], 'id', function () { return ''; }, 'Выберите заказчика', '');
            fillSelect(calendarFilterCustomerId, [], 'id', function () { return ''; }, 'Все заказчики', '');
            fillSelect(reportCustomerId, [], 'id', function () { return ''; }, 'Все заказчики', '');
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Название</th><th>Код</th><th>Описание</th><th>Создан</th><th>Действия</th></tr></thead><tbody>';

        items.forEach(function (item) {
            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.name) + '</td>';
            html += '<td>' + escapeHtml(item.code) + '</td>';
            html += '<td>' + escapeHtml(item.description) + '</td>';
            html += '<td>' + escapeHtml(item.createdon) + '</td>';
            html += '<td>' +
                '<button type="button" class="btn crmtime-entity-action" data-entity="customer" data-action="edit" data-id="' + escapeHtml(item.id) + '">Редактировать</button> ' +
                '<button type="button" class="btn btn-danger crmtime-entity-action" data-entity="customer" data-action="remove" data-id="' + escapeHtml(item.id) + '">Удалить</button>' +
                '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        customersList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';

        fillSelect(assignmentCustomerId, items, 'id', function (item) {
            return item.id + ' — ' + item.name;
        }, 'Выберите заказчика', selectedAssignmentCustomer);

        fillSelect(calendarFilterCustomerId, items, 'id', function (item) {
            return item.id + ' — ' + item.name;
        }, 'Все заказчики', selectedCalendarCustomer);

        fillSelect(reportCustomerId, items, 'id', function (item) {
            return item.id + ' — ' + item.name;
        }, 'Все заказчики', reportCustomerId ? reportCustomerId.value : '');
    }

    function renderWorkplaces(items) {
        currentWorkplaces = items || [];

        var selectedAssignmentWorkplace = assignmentWorkplaceId ? assignmentWorkplaceId.value : '';
        var selectedCalendarWorkplace = calendarFilterWorkplaceId ? calendarFilterWorkplaceId.value : '';

        if (!items || !items.length) {
            workplacesList.innerHTML = '<p>Пока нет мест работы.</p>';
            fillSelect(assignmentWorkplaceId, [], 'id', function () { return ''; }, 'Выберите место работы', '');
            fillSelect(calendarFilterWorkplaceId, [], 'id', function () { return ''; }, 'Все места работы', '');
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>ID заказчика</th><th>Заказчик</th><th>Название</th><th>Адрес</th><th>Создан</th><th>Действия</th></tr></thead><tbody>';

        items.forEach(function (item) {
            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.customer_id) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name) + '</td>';
            html += '<td>' + escapeHtml(item.name) + '</td>';
            html += '<td>' + escapeHtml(item.address) + '</td>';
            html += '<td>' + escapeHtml(item.createdon) + '</td>';
            html += '<td>' +
                '<button type="button" class="btn crmtime-entity-action" data-entity="workplace" data-action="edit" data-id="' + escapeHtml(item.id) + '">Редактировать</button> ' +
                '<button type="button" class="btn btn-danger crmtime-entity-action" data-entity="workplace" data-action="remove" data-id="' + escapeHtml(item.id) + '">Удалить</button>' +
                '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        workplacesList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';

        fillSelect(assignmentWorkplaceId, items, 'id', function (item) {
            return item.id + ' — ' + item.name + ' (' + item.customer_name + ')';
        }, 'Выберите место работы', selectedAssignmentWorkplace);

        fillSelect(calendarFilterWorkplaceId, items, 'id', function (item) {
            return item.id + ' — ' + item.name + ' (' + item.customer_name + ')';
        }, 'Все места работы', selectedCalendarWorkplace);
    }

    function renderEmployees(items) {
        currentUsers = items || [];

        if (!items || !items.length) {
            employeesList.innerHTML = '<p>Пока нет пользователей.</p>';
            fillSelect(assignmentUserId, [], 'id', function () { return ''; }, 'Выберите сотрудника', '');
            fillSelect(calendarFilterUserId, [], 'id', function () { return ''; }, 'Все сотрудники', '');
            fillSelect(reportUserId, [], 'id', function () { return ''; }, 'Все сотрудники', '');
            return;
        }

        var selectedAssignmentUser = assignmentUserId ? assignmentUserId.value : '';
        var selectedCalendarUser = calendarFilterUserId ? calendarFilterUserId.value : '';
        var selectedReportUser = reportUserId ? reportUserId.value : '';

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Логин</th><th>Имя</th><th>Email</th><th>CRM</th><th>Цвет</th><th>Стандарт</th><th>Ночь</th><th>Воскрес.</th><th>Праздн.</th><th>Адрес</th><th>Код</th><th>Заметка</th><th>Действия</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var activeText = parseInt(item.crm_active, 10) === 1 ? 'Да' : 'Нет';

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.username) + '</td>';
            html += '<td>' + escapeHtml(item.fullname) + '</td>';
            html += '<td>' + escapeHtml(item.email) + '</td>';
            html += '<td>' + escapeHtml(activeText) + '</td>';
            html += '<td><span class="crmtime-color-dot" style="background:' + escapeHtml(item.color || '#3788d8') + ';"></span> ' + escapeHtml(item.color || '#3788d8') + '</td>';
            html += '<td>' + escapeHtml(item.standard_rate || '0.00') + '</td>';
            html += '<td>' + escapeHtml(item.night_coeff || '1.00') + '</td>';
            html += '<td>' + escapeHtml(item.sunday_coeff || '1.00') + '</td>';
            html += '<td>' + escapeHtml(item.holiday_coeff || '1.00') + '</td>';
            html += '<td>' + escapeHtml(item.home_address || '') + '</td>';
            html += '<td>' + escapeHtml(item.crm_code) + '</td>';
            html += '<td>' + escapeHtml(item.crm_note) + '</td>';
            html += '<td><button type="button" class="btn crmtime-entity-action" data-entity="employee" data-action="edit" data-id="' + escapeHtml(item.id) + '">Настроить</button></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        employeesList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';

        fillSelect(assignmentUserId, items, 'id', function (item) {
            var text = item.username;
            if (item.fullname) {
                text += ' — ' + item.fullname;
            }
            return item.id + ' — ' + text;
        }, 'Выберите сотрудника', selectedAssignmentUser);

        fillSelect(calendarFilterUserId, items, 'id', function (item) {
            var text = item.username;
            if (item.fullname) {
                text += ' — ' + item.fullname;
            }
            return item.id + ' — ' + text;
        }, 'Все сотрудники', selectedCalendarUser);

        fillSelect(reportUserId, items, 'id', function (item) {
            var text = item.username;
            if (item.fullname) {
                text += ' — ' + item.fullname;
            }
            return item.id + ' — ' + text;
        }, 'Все сотрудники', selectedReportUser);
    }

    function renderAssignments(items) {
        currentAssignments = items || [];

        if (!items || !items.length) {
            assignmentsList.innerHTML = '<p>Пока нет назначений.</p>';
            fillSelect(timesheetAssignmentId, [], 'id', function () { return ''; }, 'Выберите назначение', '');
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Сотрудник</th><th>Заказчик</th><th>Место работы</th><th>Дата с</th><th>Дата по</th><th>Создан</th><th>Действия</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var userText = item.fullname ? (item.fullname + ' (' + item.username + ')') : item.username;

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(userText) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name) + '</td>';
            html += '<td>' + escapeHtml(item.workplace_name) + '</td>';
            html += '<td>' + escapeHtml(item.start_date) + '</td>';
            html += '<td>' + escapeHtml(item.end_date) + '</td>';
            html += '<td>' + escapeHtml(item.createdon) + '</td>';
            html += '<td>' +
                '<button type="button" class="btn crmtime-entity-action" data-entity="assignment" data-action="edit" data-id="' + escapeHtml(item.id) + '">Редактировать</button> ' +
                '<button type="button" class="btn btn-danger crmtime-entity-action" data-entity="assignment" data-action="remove" data-id="' + escapeHtml(item.id) + '">Удалить</button>' +
                '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        assignmentsList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';

        fillSelect(timesheetAssignmentId, items, 'id', function (item) {
            var userText = item.fullname ? item.fullname : item.username;
            return item.id + ' — ' + userText + ' / ' + item.customer_name + ' / ' + item.workplace_name;
        }, 'Выберите назначение', timesheetAssignmentId ? timesheetAssignmentId.value : '');
    }

    function renderTimesheets(items) {
        currentTimesheets = items || [];
        setStat(statTimesheets, items ? items.length : 0);

        if (!items || !items.length) {
            timesheetsList.innerHTML = '<p>Пока нет записей времени.</p>';
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Сотрудник</th><th>Заказчик</th><th>Место работы</th><th>Дата</th><th>Начало</th><th>Окончание</th><th>Тариф</th><th>Статус</th><th>Комментарий</th><th>Подпись</th><th>Действия</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var userText = item.fullname ? (item.fullname + ' (' + item.username + ')') : item.username;
            var actions = '';
            var statusLabel = item.status;
            var signatureHtml = '<span class="crmtime-signature-empty">Нет</span>';

            if (parseInt(item.has_violation, 10) > 0) {
                statusLabel += ' ⚠';
            }

            if (parseInt(item.is_signed, 10) === 1 && item.signature_url) {
                signatureHtml =
                    '<div class="crmtime-signature-cell">' +
                        '<a href="' + escapeHtml(item.signature_url) + '" target="_blank" class="btn btn-success btn-sm">Открыть</a>' +
                        '<div class="crmtime-signature-preview-wrap" style="margin-top:6px;">' +
                            '<img src="' + escapeHtml(item.signature_url) + '" alt="Подпись" style="max-width:90px; max-height:50px; border:1px solid #ddd; border-radius:4px; background:#fff; padding:2px;">' +
                        '</div>' +
                        '<div style="font-size:11px; margin-top:6px; color:#666;">' +
                            escapeHtml(item.signed_name || '') +
                            (item.signed_on ? '<br>' + escapeHtml(item.signed_on) : '') +
                        '</div>' +
                    '</div>';
            }

            actions += '<button type="button" class="btn crmtime-entity-action" data-entity="timesheet" data-action="edit" data-id="' + escapeHtml(item.id) + '">Редактировать</button> ';
            actions += '<button type="button" class="btn btn-danger crmtime-entity-action" data-entity="timesheet" data-action="remove" data-id="' + escapeHtml(item.id) + '">Удалить</button> ';

            if (item.status === 'draft' || item.status === 'rejected') {
                actions += '<button type="button" class="btn btn-primary crmtime-timesheet-action" data-action="submit" data-id="' + escapeHtml(item.id) + '">Отправить</button> ';
            }

            if (item.status === 'submitted') {
                actions += '<button type="button" class="btn btn-success crmtime-timesheet-action" data-action="approve" data-id="' + escapeHtml(item.id) + '">Утвердить</button> ';
                actions += '<button type="button" class="btn btn-danger crmtime-timesheet-action" data-action="reject" data-id="' + escapeHtml(item.id) + '">Отклонить</button>';
            }

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(userText) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name) + '</td>';
            html += '<td>' + escapeHtml(item.workplace_name) + '</td>';
            html += '<td>' + escapeHtml(item.work_date) + '</td>';
            html += '<td>' + escapeHtml(item.start_time) + '</td>';
            html += '<td>' + escapeHtml(item.end_time) + '</td>';
            html += '<td>' + escapeHtml(buildTariffFlagsText(item)) + '</td>';
            html += '<td><span class="crmtime-status crmtime-status-' + escapeHtml(item.status) + '">' + escapeHtml(statusLabel) + '</span></td>';
            html += '<td>' + escapeHtml(item.admin_comment) + '</td>';
            html += '<td>' + signatureHtml + '</td>';
            html += '<td>' + actions + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        timesheetsList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';
    }

    function renderViolations(items) {
        setStat(statViolations, items ? items.length : 0);

        if (!items || !items.length) {
            violationsList.innerHTML = '<p>Пока нет нарушений.</p>';
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Сотрудник</th><th>Текущая запись</th><th>Связанная запись</th><th>Направление</th><th>Отдых, ч</th><th>Норма, ч</th><th>Сообщение</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var userText = item.fullname ? (item.fullname + ' (' + item.username + ')') : item.username;
            var currentText = '#' + item.timesheet_id + ' / ' + item.timesheet_date + ' / ' + item.timesheet_start + '–' + item.timesheet_end;
            var relatedText = '#' + item.related_timesheet_id + ' / ' + item.related_date + ' / ' + item.related_start + '–' + item.related_end;

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(userText) + '</td>';
            html += '<td>' + escapeHtml(currentText) + '</td>';
            html += '<td>' + escapeHtml(relatedText) + '</td>';
            html += '<td>' + escapeHtml(item.direction) + '</td>';
            html += '<td>' + escapeHtml(item.rest_hours) + '</td>';
            html += '<td>' + escapeHtml(item.required_hours) + '</td>';
            html += '<td>' + escapeHtml(item.message) + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        violationsList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';
    }

    function renderDayDetails(items, date) {
        daydetailsTitle.textContent = date ? ('Кто и где работает: ' + date) : 'Выберите день в календаре.';

        if (!items || !items.length) {
            daydetailsList.innerHTML = '<p>На выбранный день записей нет.</p>';
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Время</th><th>Сотрудник</th><th>Заказчик</th><th>Место работы</th><th>Тариф</th><th>Статус</th><th>Комментарий</th><th>Подпись</th><th>Действия</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var actions = '';
            var statusLabel = item.status;
            var signatureHtml = '<span class="crmtime-signature-empty">Нет</span>';

            if (parseInt(item.has_violation, 10) > 0) {
                statusLabel += ' ⚠';
            }

            if (parseInt(item.is_signed, 10) === 1 && item.signature_url) {
                signatureHtml =
                    '<div class="crmtime-signature-cell">' +
                        '<a href="' + escapeHtml(item.signature_url) + '" target="_blank" class="btn btn-success btn-sm">Открыть</a>' +
                        '<div style="font-size:11px; margin-top:6px; color:#666;">' +
                            escapeHtml(item.signed_name || '') +
                            (item.signed_on ? '<br>' + escapeHtml(item.signed_on) : '') +
                        '</div>' +
                    '</div>';
            }

            actions += '<button type="button" class="btn crmtime-entity-action" data-entity="timesheet" data-action="edit" data-id="' + escapeHtml(item.id) + '">Редактировать</button> ';
            actions += '<button type="button" class="btn btn-danger crmtime-entity-action" data-entity="timesheet" data-action="remove" data-id="' + escapeHtml(item.id) + '">Удалить</button> ';

            if (item.status === 'submitted') {
                actions += '<button type="button" class="btn btn-success crmtime-timesheet-action" data-action="approve" data-id="' + escapeHtml(item.id) + '">Утвердить</button> ';
                actions += '<button type="button" class="btn btn-danger crmtime-timesheet-action" data-action="reject" data-id="' + escapeHtml(item.id) + '">Отклонить</button>';
            }

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.start_time + ' – ' + item.end_time) + '</td>';
            html += '<td>' + escapeHtml(item.user_name) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name) + '</td>';
            html += '<td>' + escapeHtml(item.workplace_name) + '</td>';
            html += '<td>' + escapeHtml(buildTariffFlagsText(item)) + '</td>';
            html += '<td><span class="crmtime-status crmtime-status-' + escapeHtml(item.status) + '">' + escapeHtml(statusLabel) + '</span></td>';
            html += '<td>' + escapeHtml(item.admin_comment) + '</td>';
            html += '<td>' + signatureHtml + '</td>';
            html += '<td>' + actions + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        daydetailsList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';
    }

    function renderApprovals(items) {
        setStat(statApprovals, items ? items.length : 0);

        if (!items || !items.length) {
            approvalsList.innerHTML = '<p>В очереди согласования нет записей.</p>';
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Дата</th><th>Время</th><th>Сотрудник</th><th>Заказчик</th><th>Место работы</th><th>Тариф</th><th>Нарушение</th><th>Подпись</th><th>Действия</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var warning = parseInt(item.has_violation, 10) > 0 ? '⚠ Да' : 'Нет';
            var signatureHtml = '<span class="crmtime-signature-empty">Нет</span>';
            var actions = '';

            if (parseInt(item.is_signed, 10) === 1 && item.signature_url) {
                signatureHtml =
                    '<div class="crmtime-signature-cell">' +
                        '<a href="' + escapeHtml(item.signature_url) + '" target="_blank" class="btn btn-success btn-sm">Открыть</a>' +
                        '<div style="font-size:11px; margin-top:6px; color:#666;">' +
                            escapeHtml(item.signed_name || '') +
                            (item.signed_on ? '<br>' + escapeHtml(item.signed_on) : '') +
                        '</div>' +
                    '</div>';
            }

            actions += '<button type="button" class="btn crmtime-entity-action" data-entity="timesheet" data-action="edit" data-id="' + escapeHtml(item.id) + '">Редактировать</button> ';
            actions += '<button type="button" class="btn btn-danger crmtime-entity-action" data-entity="timesheet" data-action="remove" data-id="' + escapeHtml(item.id) + '">Удалить</button> ';
            actions += '<button type="button" class="btn btn-success crmtime-timesheet-action" data-action="approve" data-id="' + escapeHtml(item.id) + '">Утвердить</button> ';
            actions += '<button type="button" class="btn btn-danger crmtime-timesheet-action" data-action="reject" data-id="' + escapeHtml(item.id) + '">Отклонить</button>';

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.work_date) + '</td>';
            html += '<td>' + escapeHtml(item.start_time + ' – ' + item.end_time) + '</td>';
            html += '<td>' + escapeHtml(item.user_name) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name) + '</td>';
            html += '<td>' + escapeHtml(item.workplace_name) + '</td>';
            html += '<td>' + escapeHtml(buildTariffFlagsText(item)) + '</td>';
            html += '<td>' + escapeHtml(warning) + '</td>';
            html += '<td>' + signatureHtml + '</td>';
            html += '<td>' + actions + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        approvalsList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';
    }

    function renderReportSummary(data) {
        if (!data || !data.summary) {
            reportStats.innerHTML = '';
            reportEmployees.innerHTML = '<p>Нет данных.</p>';
            reportRows.innerHTML = '<p>Нет данных.</p>';
            return;
        }

        reportStats.innerHTML =
            '<div class="crmtime-stats">' +
                '<div class="crmtime-stat-card">' +
                    '<div class="crmtime-stat-label">Записей</div>' +
                    '<div class="crmtime-stat-value">' + escapeHtml(data.summary.records) + '</div>' +
                '</div>' +
                '<div class="crmtime-stat-card">' +
                    '<div class="crmtime-stat-label">Часов</div>' +
                    '<div class="crmtime-stat-value">' + escapeHtml(data.summary.hours) + '</div>' +
                '</div>' +
                '<div class="crmtime-stat-card">' +
                    '<div class="crmtime-stat-label">Сумма</div>' +
                    '<div class="crmtime-stat-value">' + escapeHtml(data.summary.amount) + '</div>' +
                '</div>' +
            '</div>';

        if (data.employees && data.employees.length) {
            var employeesHtml = '<table class="crmtime-table">';
            employeesHtml += '<thead><tr><th>ID</th><th>Сотрудник</th><th>Записей</th><th>Часов</th><th>Сумма</th></tr></thead><tbody>';

            data.employees.forEach(function (item) {
                employeesHtml += '<tr>';
                employeesHtml += '<td>' + escapeHtml(item.user_id) + '</td>';
                employeesHtml += '<td>' + escapeHtml(item.user_name) + '</td>';
                employeesHtml += '<td>' + escapeHtml(item.records) + '</td>';
                employeesHtml += '<td>' + escapeHtml(item.hours) + '</td>';
                employeesHtml += '<td>' + escapeHtml(item.amount) + '</td>';
                employeesHtml += '</tr>';
            });

            employeesHtml += '</tbody></table>';
            reportEmployees.innerHTML = '<div class="crmtime-table-wrapper">' + employeesHtml + '</div>';
        } else {
            reportEmployees.innerHTML = '<p>Нет данных.</p>';
        }

        if (data.rows && data.rows.length) {
            var rowsHtml = '<table class="crmtime-table">';
            rowsHtml += '<thead><tr><th>ID</th><th>Дата</th><th>Время</th><th>Сотрудник</th><th>Заказчик</th><th>Место работы</th><th>Тариф</th><th>Часы</th><th>Ставка</th><th>Сумма</th></tr></thead><tbody>';

            data.rows.forEach(function (item) {
                rowsHtml += '<tr>';
                rowsHtml += '<td>' + escapeHtml(item.id) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.work_date) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.start_time + ' – ' + item.end_time) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.user_name) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.customer_name) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.workplace_name) + '</td>';
                rowsHtml += '<td>' + escapeHtml(buildTariffFlagsText(item)) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.hours) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.rate) + '</td>';
                rowsHtml += '<td>' + escapeHtml(item.amount) + '</td>';
                rowsHtml += '</tr>';
            });

            rowsHtml += '</tbody></table>';
            reportRows.innerHTML = '<div class="crmtime-table-wrapper">' + rowsHtml + '</div>';
        } else {
            reportRows.innerHTML = '<p>Нет данных.</p>';
        }
    }

    function renderDocuments(items) {
        if (!documentsList) {
            return;
        }

        if (!items || !items.length) {
            documentsList.innerHTML = '<p>Документов пока нет.</p>';
            return;
        }

        var html = '<table class="crmtime-table">';
        html += '<thead><tr><th>ID</th><th>Название</th><th>Период</th><th>Заказчик</th><th>Сотрудник</th><th>Размер</th><th>Создан</th><th>Файл</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var period = '';
            if (item.date_from || item.date_to) {
                period = (item.date_from || '—') + ' — ' + (item.date_to || '—');
            } else {
                period = '—';
            }

            var fileLink = item.file_path
                ? '<a class="btn btn-success btn-sm" href="/' + String(item.file_path || '').replace(/^\/+/, '') + '" target="_blank">Открыть</a>'
                : '<span class="crmtime-signature-empty">Нет</span>';

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.title || item.file_name || '') + '</td>';
            html += '<td>' + escapeHtml(period) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name || '') + '</td>';
            html += '<td>' + escapeHtml(item.user_name || '') + '</td>';
            html += '<td>' + escapeHtml(formatFileSize(item.file_size)) + '</td>';
            html += '<td>' + escapeHtml(item.createdon || '') + '</td>';
            html += '<td>' + fileLink + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        documentsList.innerHTML = '<div class="crmtime-table-wrapper">' + html + '</div>';
    }

    function runReport() {
        request('mgr/report/summary', {
            date_from: reportDateFrom ? reportDateFrom.value : '',
            date_to: reportDateTo ? reportDateTo.value : '',
            customer_id: reportCustomerId ? reportCustomerId.value : '',
            user_id: reportUserId ? reportUserId.value : ''
        })
            .then(function (data) {
                if (data.success && data.object) {
                    setMessage(reportMessage, '', '');
                    renderReportSummary(data.object);
                } else {
                    setMessage(reportMessage, 'warning', data.message || 'Не удалось построить отчёт');
                    renderReportSummary(null);
                }
            })
            .catch(function (error) {
                setMessage(reportMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function downloadReportPdf() {
        request('mgr/report/createpdf', {
            date_from: reportDateFrom ? reportDateFrom.value : '',
            date_to: reportDateTo ? reportDateTo.value : '',
            customer_id: reportCustomerId ? reportCustomerId.value : '',
            user_id: reportUserId ? reportUserId.value : ''
        })
            .then(function (data) {
                if (data.success) {
                    setMessage(reportMessage, 'success', data.message || 'PDF сформирован');
                    if (data.object && data.object.file_url) {
                        window.open(data.object.file_url, '_blank');
                    }
                    loadDocuments();
                } else {
                    setMessage(reportMessage, 'warning', data.message || 'Не удалось сформировать PDF');
                }
            })
            .catch(function (error) {
                setMessage(reportMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadDocuments() {
        request('mgr/document/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    setMessage(documentsMessage, '', '');
                    renderDocuments(data.object.results);
                } else {
                    setMessage(documentsMessage, 'warning', data.message || 'Не удалось загрузить документы');
                    renderDocuments([]);
                }
            })
            .catch(function (error) {
                setMessage(documentsMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function getCalendarFilters() {
        return {
            user_id: calendarFilterUserId ? calendarFilterUserId.value : '',
            customer_id: calendarFilterCustomerId ? calendarFilterCustomerId.value : '',
            workplace_id: calendarFilterWorkplaceId ? calendarFilterWorkplaceId.value : '',
            status: calendarFilterStatus ? calendarFilterStatus.value : ''
        };
    }

    function initManagerCalendar() {
        if (!window.crmTimeConfig || !window.crmTimeConfig.calendar_enabled) {
            setMessage(calendarMessage, 'warning', 'FullCalendar не подключен. Положи <code>assets/components/crmtime/vendor/fullcalendar/index.global.min.js</code>');
            return;
        }

        if (typeof FullCalendar === 'undefined' || !FullCalendar.Calendar) {
            setMessage(calendarMessage, 'error', 'FullCalendar не найден на странице');
            return;
        }

        calendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'uk',
            firstDay: 1,
            height: 'auto',
            events: function (info, successCallback, failureCallback) {
                var payload = getCalendarFilters();
                payload.start = info.startStr.substring(0, 10);
                payload.end = info.endStr.substring(0, 10);

                request('mgr/calendar/events', payload)
                    .then(function (data) {
                        if (data.success && data.object && data.object.results) {
                            setMessage(calendarMessage, '', '');
                            successCallback(data.object.results);
                        } else {
                            setMessage(calendarMessage, 'warning', data.message || 'Не удалось загрузить события календаря');
                            successCallback([]);
                        }
                    })
                    .catch(function (error) {
                        setMessage(calendarMessage, 'error', 'AJAX error: ' + error);
                        failureCallback(error);
                    });
            },
            dateClick: function (info) {
                currentCalendarDate = info.dateStr.substring(0, 10);
                loadDayDetails(currentCalendarDate);
            },
            eventClick: function (info) {
                var dateStr = '';
                if (info.event.start) {
                    dateStr = info.event.start.toISOString().substring(0, 10);
                } else if (info.event.extendedProps && info.event.extendedProps.work_date) {
                    dateStr = info.event.extendedProps.work_date;
                }

                currentCalendarDate = dateStr;
                loadDayDetails(currentCalendarDate);
            }
        });

        calendarInstance.render();
    }

    function refreshManagerCalendar() {
        if (calendarInstance) {
            calendarInstance.refetchEvents();
        }
    }

    function loadDayDetails(date) {
        if (!date) {
            daydetailsTitle.textContent = 'Выберите день в календаре.';
            setMessage(daydetailsMessage, '', '');
            daydetailsList.innerHTML = '';
            return;
        }

        var payload = getCalendarFilters();
        payload.date = date;

        request('mgr/calendar/daydetails', payload)
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    setMessage(daydetailsMessage, '', '');
                    renderDayDetails(data.object.results, date);
                } else {
                    setMessage(daydetailsMessage, 'warning', data.message || 'Не удалось загрузить данные по дню');
                    renderDayDetails([], date);
                }
            })
            .catch(function (error) {
                setMessage(daydetailsMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadUsers() {
        request('mgr/user/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    currentUsers = data.object.results;
                    renderEmployees(currentUsers);
                    setMessage(usersMessage, '', '');
                } else {
                    currentUsers = [];
                    renderEmployees([]);
                    setMessage(usersMessage, 'warning', data.message || 'Не удалось загрузить сотрудников');
                }
            })
            .catch(function (error) {
                setMessage(usersMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadCustomers() {
        request('mgr/customer/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    renderCustomers(data.object.results);
                    setMessage(customerMessage, '', '');
                } else {
                    customersList.innerHTML = '<p>Не удалось загрузить список.</p>';
                    setMessage(customerMessage, 'warning', data.message || 'Не удалось загрузить заказчиков');
                }
            })
            .catch(function (error) {
                setMessage(customerMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadWorkplaces() {
        request('mgr/workplace/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    renderWorkplaces(data.object.results);
                    setMessage(workplaceMessage, '', '');
                } else {
                    workplacesList.innerHTML = '<p>Не удалось загрузить список.</p>';
                    setMessage(workplaceMessage, 'warning', data.message || 'Не удалось загрузить места работы');
                }
            })
            .catch(function (error) {
                setMessage(workplaceMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadAssignments() {
        request('mgr/assignment/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    renderAssignments(data.object.results);
                    setMessage(assignmentMessage, '', '');
                } else {
                    assignmentsList.innerHTML = '<p>Не удалось загрузить список назначений.</p>';
                    setMessage(assignmentMessage, 'warning', data.message || 'Не удалось загрузить назначения');
                }
            })
            .catch(function (error) {
                setMessage(assignmentMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadTimesheets() {
        request('mgr/timesheet/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    renderTimesheets(data.object.results);
                    setMessage(timesheetMessage, '', '');
                } else {
                    timesheetsList.innerHTML = '<p>Не удалось загрузить список записей.</p>';
                    setMessage(timesheetMessage, 'warning', data.message || 'Не удалось загрузить записи времени');
                    setStat(statTimesheets, 0);
                }
            })
            .catch(function (error) {
                setMessage(timesheetMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadViolations() {
        request('mgr/violation/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    renderViolations(data.object.results);
                    setMessage(violationMessage, '', '');
                } else {
                    violationsList.innerHTML = '<p>Не удалось загрузить список нарушений.</p>';
                    setMessage(violationMessage, 'warning', data.message || 'Не удалось загрузить нарушения');
                    setStat(statViolations, 0);
                }
            })
            .catch(function (error) {
                setMessage(violationMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function loadApprovals() {
        request('mgr/approval/getlist')
            .then(function (data) {
                if (data.success && data.object && data.object.results) {
                    renderApprovals(data.object.results);
                    setMessage(approvalMessage, '', '');
                } else {
                    approvalsList.innerHTML = '<p>Не удалось загрузить очередь согласования.</p>';
                    setMessage(approvalMessage, 'warning', data.message || 'Не удалось загрузить очередь');
                    setStat(statApprovals, 0);
                }
            })
            .catch(function (error) {
                setMessage(approvalMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function refreshAllMainData() {
        loadUsers();
        loadCustomers();
        loadWorkplaces();
        loadAssignments();
        loadTimesheets();
        loadViolations();
        loadApprovals();
        loadDocuments();
        refreshManagerCalendar();

        if (currentCalendarDate) {
            loadDayDetails(currentCalendarDate);
        }
    }

    function saveCustomer() {
        var action = customerIdField.value ? 'mgr/customer/update' : 'mgr/customer/create';
        var payload = {
            id: customerIdField.value,
            name: customerName.value,
            code: customerCode.value,
            description: customerDescription.value
        };

        request(action, payload)
            .then(function (data) {
                if (data.success) {
                    setMessage(customerMessage, 'success', data.message || 'Заказчик сохранён');
                    resetCustomerForm();
                    refreshAllMainData();
                } else {
                    setMessage(customerMessage, 'warning', data.message || 'Не удалось сохранить заказчика');
                }
            })
            .catch(function (error) {
                setMessage(customerMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function saveWorkplace() {
        var action = workplaceIdField.value ? 'mgr/workplace/update' : 'mgr/workplace/create';
        var payload = {
            id: workplaceIdField.value,
            customer_id: workplaceCustomerId.value,
            name: workplaceName.value,
            address: workplaceAddress.value
        };

        request(action, payload)
            .then(function (data) {
                if (data.success) {
                    setMessage(workplaceMessage, 'success', data.message || 'Место работы сохранено');
                    resetWorkplaceForm();
                    refreshAllMainData();
                } else {
                    setMessage(workplaceMessage, 'warning', data.message || 'Не удалось сохранить место работы');
                }
            })
            .catch(function (error) {
                setMessage(workplaceMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function saveAssignment() {
        var action = assignmentIdField.value ? 'mgr/assignment/update' : 'mgr/assignment/create';
        var payload = {
            id: assignmentIdField.value,
            user_id: assignmentUserId.value,
            customer_id: assignmentCustomerId.value,
            workplace_id: assignmentWorkplaceId.value,
            start_date: assignmentStartDate.value,
            end_date: assignmentEndDate.value
        };

        request(action, payload)
            .then(function (data) {
                if (data.success) {
                    setMessage(assignmentMessage, 'success', data.message || 'Назначение сохранено');
                    resetAssignmentForm();
                    refreshAllMainData();
                } else {
                    setMessage(assignmentMessage, 'warning', data.message || 'Не удалось сохранить назначение');
                }
            })
            .catch(function (error) {
                setMessage(assignmentMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function saveEmployee() {
        request('mgr/employee/update', {
            id: employeeIdField.value,
            crm_active: employeeActive.checked ? 1 : 0,
            color: employeeColor.value,
            crm_code: employeeCode.value,
            crm_note: employeeNote.value,
            standard_rate: employeeStandardRate.value,
            night_coeff: employeeNightCoeff.value,
            sunday_coeff: employeeSundayCoeff.value,
            holiday_coeff: employeeHolidayCoeff.value,
            home_address: employeeHomeAddress.value
        })
            .then(function (data) {
                if (data.success) {
                    setMessage(employeeMessage, 'success', data.message || 'CRM-настройки сотрудника сохранены');
                    refreshAllMainData();
                } else {
                    setMessage(employeeMessage, 'warning', data.message || 'Не удалось сохранить CRM-настройки сотрудника');
                }
            })
            .catch(function (error) {
                setMessage(employeeMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function saveTimesheet() {
        var action = timesheetIdField.value ? 'mgr/timesheet/update' : 'mgr/timesheet/create';
        var payload = {
            id: timesheetIdField.value,
            assignment_id: timesheetAssignmentId.value,
            work_date: timesheetWorkDate.value,
            start_time: timesheetStartTime.value,
            end_time: timesheetEndTime.value,
            is_night: timesheetIsNight && timesheetIsNight.checked ? 1 : 0,
            is_sunday: timesheetIsSunday && timesheetIsSunday.checked ? 1 : 0,
            is_holiday: timesheetIsHoliday && timesheetIsHoliday.checked ? 1 : 0
        };

        request(action, payload)
            .then(function (data) {
                if (data.success) {
                    setMessage(timesheetMessage, 'success', data.message || 'Запись времени сохранена');
                    resetTimesheetForm();
                    refreshAllMainData();
                } else {
                    setMessage(timesheetMessage, 'warning', data.message || 'Не удалось сохранить запись времени');
                }
            })
            .catch(function (error) {
                setMessage(timesheetMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function removeEntity(entity, id) {
        if (!window.confirm('Удалить запись?')) {
            return;
        }

        request('mgr/' + entity + '/remove', { id: id })
            .then(function (data) {
                var messageEl = globalMessage;

                if (entity === 'customer') {
                    messageEl = customerMessage;
                    if (String(customerIdField.value) === String(id)) {
                        resetCustomerForm();
                    }
                } else if (entity === 'workplace') {
                    messageEl = workplaceMessage;
                    if (String(workplaceIdField.value) === String(id)) {
                        resetWorkplaceForm();
                    }
                } else if (entity === 'assignment') {
                    messageEl = assignmentMessage;
                    if (String(assignmentIdField.value) === String(id)) {
                        resetAssignmentForm();
                    }
                } else if (entity === 'timesheet') {
                    messageEl = timesheetMessage;
                    if (String(timesheetIdField.value) === String(id)) {
                        resetTimesheetForm();
                    }
                }

                if (data.success) {
                    setMessage(messageEl, 'success', data.message || 'Запись удалена');
                    refreshAllMainData();
                } else {
                    setMessage(messageEl, 'warning', data.message || 'Не удалось удалить запись');
                }
            })
            .catch(function (error) {
                setMessage(globalMessage, 'error', 'AJAX error: ' + error);
            });
    }

    function handleTimesheetWorkflow(action, id) {
        if (!action || !id) {
            return;
        }

        if (action === 'submit') {
            request('mgr/timesheet/submit', { id: id })
                .then(function (data) {
                    if (data.success) {
                        setMessage(timesheetMessage, 'success', data.message || 'Запись отправлена');
                    } else {
                        setMessage(timesheetMessage, 'warning', data.message || 'Не удалось отправить запись');
                    }
                    refreshAllMainData();
                })
                .catch(function (error) {
                    setMessage(timesheetMessage, 'error', 'AJAX error: ' + error);
                });
            return;
        }

        if (action === 'approve') {
            request('mgr/timesheet/approve', { id: id })
                .then(function (data) {
                    if (data.success) {
                        setMessage(globalMessage, 'success', data.message || 'Запись утверждена');
                    } else {
                        setMessage(globalMessage, 'warning', data.message || 'Не удалось утвердить запись');
                    }
                    refreshAllMainData();
                })
                .catch(function (error) {
                    setMessage(globalMessage, 'error', 'AJAX error: ' + error);
                });
            return;
        }

        if (action === 'reject') {
            var comment = window.prompt('Введите комментарий для отклонения:');
            if (comment === null) {
                return;
            }

            request('mgr/timesheet/reject', {
                id: id,
                comment: comment
            })
                .then(function (data) {
                    if (data.success) {
                        setMessage(globalMessage, 'success', data.message || 'Запись отклонена');
                    } else {
                        setMessage(globalMessage, 'warning', data.message || 'Не удалось отклонить запись');
                    }
                    refreshAllMainData();
                })
                .catch(function (error) {
                    setMessage(globalMessage, 'error', 'AJAX error: ' + error);
                });
        }
    }

    if (pingBtn) {
        pingBtn.addEventListener('click', function () {
            request('mgr/ping')
                .then(function (data) {
                    if (data.success) {
                        setMessage(pingMessage, 'success', data.message || 'Connector работает');
                    } else {
                        setMessage(pingMessage, 'warning', data.message || 'Проверка connector завершилась с ошибкой');
                    }
                })
                .catch(function (error) {
                    setMessage(pingMessage, 'error', 'AJAX error: ' + error);
                });
        });
    }

    if (installBtn) {
        installBtn.addEventListener('click', function () {
            request('mgr/setup/install')
                .then(function (data) {
                    if (data.success) {
                        setMessage(installMessage, 'success', data.message || 'Таблицы проверены');
                    } else {
                        setMessage(installMessage, 'warning', data.message || 'Не удалось проверить таблицы');
                    }
                })
                .catch(function (error) {
                    setMessage(installMessage, 'error', 'AJAX error: ' + error);
                });
        });
    }

    if (saveCustomerBtn) {
        saveCustomerBtn.addEventListener('click', saveCustomer);
    }
    if (cancelCustomerBtn) {
        cancelCustomerBtn.addEventListener('click', resetCustomerForm);
    }

    if (saveWorkplaceBtn) {
        saveWorkplaceBtn.addEventListener('click', saveWorkplace);
    }
    if (cancelWorkplaceBtn) {
        cancelWorkplaceBtn.addEventListener('click', resetWorkplaceForm);
    }

    if (saveAssignmentBtn) {
        saveAssignmentBtn.addEventListener('click', saveAssignment);
    }
    if (cancelAssignmentBtn) {
        cancelAssignmentBtn.addEventListener('click', resetAssignmentForm);
    }

    if (saveEmployeeBtn) {
        saveEmployeeBtn.addEventListener('click', saveEmployee);
    }
    if (cancelEmployeeBtn) {
        cancelEmployeeBtn.addEventListener('click', resetEmployeeForm);
    }

    if (saveTimesheetBtn) {
        saveTimesheetBtn.addEventListener('click', saveTimesheet);
    }
    if (cancelTimesheetBtn) {
        cancelTimesheetBtn.addEventListener('click', resetTimesheetForm);
    }

    if (reportRunBtn) {
        reportRunBtn.addEventListener('click', runReport);
    }
    if (reportPdfBtn) {
        reportPdfBtn.addEventListener('click', downloadReportPdf);
    }
    if (documentsRefreshBtn) {
        documentsRefreshBtn.addEventListener('click', loadDocuments);
    }

    if (calendarApplyBtn) {
        calendarApplyBtn.addEventListener('click', function () {
            refreshManagerCalendar();

            if (currentCalendarDate) {
                loadDayDetails(currentCalendarDate);
            }
        });
    }

    if (calendarResetBtn) {
        calendarResetBtn.addEventListener('click', function () {
            if (calendarFilterUserId) {
                calendarFilterUserId.value = '';
            }
            if (calendarFilterCustomerId) {
                calendarFilterCustomerId.value = '';
            }
            if (calendarFilterWorkplaceId) {
                calendarFilterWorkplaceId.value = '';
            }
            if (calendarFilterStatus) {
                calendarFilterStatus.value = '';
            }

            refreshManagerCalendar();

            if (currentCalendarDate) {
                loadDayDetails(currentCalendarDate);
            } else {
                daydetailsTitle.textContent = 'Выберите день в календаре.';
                setMessage(daydetailsMessage, '', '');
                daydetailsList.innerHTML = '';
            }
        });
    }

    if (customersList) {
        customersList.addEventListener('click', function (e) {
            var btn = e.target.closest('.crmtime-entity-action');
            if (!btn) return;

            var id = btn.getAttribute('data-id');
            var action = btn.getAttribute('data-action');

            if (action === 'edit') {
                var item = currentCustomers.find(function (row) {
                    return String(row.id) === String(id);
                });
                if (item) {
                    setCustomerEditMode(item);
                }
            } else if (action === 'remove') {
                removeEntity('customer', id);
            }
        });
    }

    if (workplacesList) {
        workplacesList.addEventListener('click', function (e) {
            var btn = e.target.closest('.crmtime-entity-action');
            if (!btn) return;

            var id = btn.getAttribute('data-id');
            var action = btn.getAttribute('data-action');

            if (action === 'edit') {
                var item = currentWorkplaces.find(function (row) {
                    return String(row.id) === String(id);
                });
                if (item) {
                    setWorkplaceEditMode(item);
                }
            } else if (action === 'remove') {
                removeEntity('workplace', id);
            }
        });
    }

    if (assignmentsList) {
        assignmentsList.addEventListener('click', function (e) {
            var btn = e.target.closest('.crmtime-entity-action');
            if (!btn) return;

            var id = btn.getAttribute('data-id');
            var action = btn.getAttribute('data-action');

            if (action === 'edit') {
                var item = currentAssignments.find(function (row) {
                    return String(row.id) === String(id);
                });
                if (item) {
                    setAssignmentEditMode(item);
                }
            } else if (action === 'remove') {
                removeEntity('assignment', id);
            }
        });
    }

    if (employeesList) {
        employeesList.addEventListener('click', function (e) {
            var btn = e.target.closest('.crmtime-entity-action');
            if (!btn) return;

            if (btn.getAttribute('data-entity') !== 'employee') {
                return;
            }

            var id = btn.getAttribute('data-id');
            var item = currentUsers.find(function (row) {
                return String(row.id) === String(id);
            });

            if (item) {
                setEmployeeEditMode(item);
            }
        });
    }

    function handleTimesheetEntityClick(e) {
        var entityBtn = e.target.closest('.crmtime-entity-action');
        if (entityBtn && entityBtn.getAttribute('data-entity') === 'timesheet') {
            var id = entityBtn.getAttribute('data-id');
            var action = entityBtn.getAttribute('data-action');

            if (action === 'edit') {
                var item = currentTimesheets.find(function (row) {
                    return String(row.id) === String(id);
                });
                if (item) {
                    setTimesheetEditMode(item);
                }
            } else if (action === 'remove') {
                removeEntity('timesheet', id);
            }
            return;
        }

        var btn = e.target.closest('.crmtime-timesheet-action');
        if (!btn) return;
        handleTimesheetWorkflow(btn.getAttribute('data-action'), btn.getAttribute('data-id'));
    }

    if (timesheetsList) {
        timesheetsList.addEventListener('click', handleTimesheetEntityClick);
    }
    if (daydetailsList) {
        daydetailsList.addEventListener('click', handleTimesheetEntityClick);
    }
    if (approvalsList) {
        approvalsList.addEventListener('click', handleTimesheetEntityClick);
    }

    function resizeCrmTimeScroll() {
        var shell = document.querySelector('.crmtime-shell');
        if (!shell) {
            return;
        }

        var rect = shell.getBoundingClientRect();
        var bottomGap = 20;
        var height = window.innerHeight - rect.top - bottomGap;

        if (height < 300) {
            height = 300;
        }

        shell.style.height = height + 'px';
    }

    function startCrmTimeWhenReady(attempt) {
        attempt = attempt || 0;

        var modAuth = getModAuth();

        if (!modAuth) {
            if (attempt < 20) {
                window.setTimeout(function () {
                    startCrmTimeWhenReady(attempt + 1);
                }, 250);
            } else {
                setMessage(globalMessage, 'error', 'Не удалось получить auth-токен manager. Обнови страницу с Ctrl+F5.');
            }
            return;
        }

        resetCustomerForm();
        resetWorkplaceForm();
        resetAssignmentForm();
        resetEmployeeForm();
        resetTimesheetForm();

        refreshAllMainData();
        initManagerCalendar();
    }

    initTabs();
    window.addEventListener('resize', resizeCrmTimeScroll);
    resizeCrmTimeScroll();
    startCrmTimeWhenReady();
});
