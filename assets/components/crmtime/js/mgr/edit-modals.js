document.addEventListener('DOMContentLoaded', function () {
    if (!window.crmTimeConfig || !window.crmTimeConfig.connector_url) {
        return;
    }

    var storageKey = 'crmtime-active-tab';
    var modalState = {
        entity: '',
        id: '',
        mode: 'edit',
        related: {}
    };

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
                modAuth: modAuth
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

    function getActiveTabName() {
        var active = document.querySelector('.crmtime-tab-btn.is-active');
        return active ? active.getAttribute('data-tab') : 'overview';
    }

    function saveActiveTabName() {
        try {
            sessionStorage.setItem(storageKey, getActiveTabName());
        } catch (e) {}
    }

    function restoreActiveTabName() {
        var tabName = '';
        try {
            tabName = sessionStorage.getItem(storageKey) || '';
        } catch (e) {}

        if (!tabName) {
            return;
        }

        window.setTimeout(function () {
            var btn = document.querySelector('.crmtime-tab-btn[data-tab="' + tabName + '"]');
            if (btn) {
                btn.click();
            }
        }, 0);
    }

    document.addEventListener('click', function (e) {
        var tabBtn = e.target.closest('.crmtime-tab-btn');
        if (tabBtn) {
            try {
                sessionStorage.setItem(storageKey, tabBtn.getAttribute('data-tab') || 'overview');
            } catch (err) {}
        }
    }, true);

    function injectStyles() {
        if (document.getElementById('crmtime-edit-modal-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'crmtime-edit-modal-styles';
        style.textContent = '' +
            '.crmtime-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;align-items:center;justify-content:center;z-index:9999;padding:24px;}' +
            '.crmtime-modal-backdrop.is-open{display:flex;}' +
            '.crmtime-modal{background:#fff;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,.35);width:min(920px,100%);max-height:calc(100vh - 48px);display:flex;flex-direction:column;overflow:hidden;}' +
            '.crmtime-modal__header{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid #e5e7eb;gap:16px;}' +
            '.crmtime-modal__title{margin:0;font-size:22px;line-height:1.2;}' +
            '.crmtime-modal__close{border:none;background:transparent;font-size:28px;line-height:1;cursor:pointer;padding:0 4px;color:#374151;}' +
            '.crmtime-modal__body{padding:20px 22px;overflow:auto;}' +
            '.crmtime-modal__footer{padding:16px 22px;border-top:1px solid #e5e7eb;display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;}' +
            '.crmtime-modal-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 16px;}' +
            '.crmtime-modal-field{display:flex;flex-direction:column;gap:6px;}' +
            '.crmtime-modal-field--full{grid-column:1 / -1;}' +
            '.crmtime-modal-field label{font-weight:600;font-size:13px;color:#374151;}' +
            '.crmtime-modal-field input,.crmtime-modal-field select,.crmtime-modal-field textarea{width:100%;padding:10px 12px;border:1px solid #cfd4dc;border-radius:10px;background:#fff;}' +
            '.crmtime-modal-field textarea{min-height:110px;resize:vertical;}' +
            '.crmtime-modal-checkboxes{display:flex;gap:18px;flex-wrap:wrap;padding-top:8px;}' +
            '.crmtime-modal-checkbox{display:flex;align-items:center;gap:8px;}' +
            '.crmtime-modal-note{padding:10px 12px;border-radius:10px;background:#f8fafc;color:#334155;font-size:13px;}' +
            '.crmtime-modal-message{display:none;margin-top:14px;}' +
            '.crmtime-modal-message.is-visible{display:block;}' +
            '.crmtime-modal-add-row{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;margin-bottom:14px;}' +
            '@media (max-width: 768px){.crmtime-modal-grid{grid-template-columns:1fr;}.crmtime-modal{width:100%;}}';
        document.head.appendChild(style);
    }

    function createModalRoot() {
        if (document.getElementById('crmtime-edit-modal-backdrop')) {
            return;
        }

        var html = '' +
            '<div class="crmtime-modal-backdrop" id="crmtime-edit-modal-backdrop">' +
                '<div class="crmtime-modal" role="dialog" aria-modal="true" aria-labelledby="crmtime-edit-modal-title">' +
                    '<div class="crmtime-modal__header">' +
                        '<h3 class="crmtime-modal__title" id="crmtime-edit-modal-title">Редактирование</h3>' +
                        '<button type="button" class="crmtime-modal__close" id="crmtime-edit-modal-close" aria-label="Закрыть">×</button>' +
                    '</div>' +
                    '<div class="crmtime-modal__body">' +
                        '<div id="crmtime-edit-modal-body"></div>' +
                        '<div id="crmtime-edit-modal-message" class="crmtime-message crmtime-modal-message"></div>' +
                    '</div>' +
                    '<div class="crmtime-modal__footer">' +
                        '<button type="button" class="btn" id="crmtime-edit-modal-cancel">Отмена</button>' +
                        '<button type="button" class="btn btn-success" id="crmtime-edit-modal-save">Сохранить</button>' +
                    '</div>' +
                '</div>' +
            '</div>';

        document.body.insertAdjacentHTML('beforeend', html);

        var backdrop = document.getElementById('crmtime-edit-modal-backdrop');
        var closeBtn = document.getElementById('crmtime-edit-modal-close');
        var cancelBtn = document.getElementById('crmtime-edit-modal-cancel');

        function closeModal() {
            backdrop.classList.remove('is-open');
            modalState.entity = '';
            modalState.id = '';
            modalState.mode = 'edit';
            modalState.related = {};
            setModalMessage('', '');
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && backdrop.classList.contains('is-open')) {
                closeModal();
            }
        });

        window.crmTimeEditModalClose = closeModal;
    }

    function openModal(title, html) {
        var backdrop = document.getElementById('crmtime-edit-modal-backdrop');
        var titleEl = document.getElementById('crmtime-edit-modal-title');
        var bodyEl = document.getElementById('crmtime-edit-modal-body');
        titleEl.textContent = title;
        bodyEl.innerHTML = html;
        setModalMessage('', '');
        backdrop.classList.add('is-open');
    }

    function setModalMessage(type, text) {
        var messageEl = document.getElementById('crmtime-edit-modal-message');
        if (!messageEl) {
            return;
        }
        if (!text) {
            messageEl.className = 'crmtime-message crmtime-modal-message';
            messageEl.textContent = '';
            return;
        }
        messageEl.className = 'crmtime-message crmtime-modal-message is-visible crmtime-message-' + type;
        messageEl.textContent = text;
    }

    function buildOptions(items, valueKey, labelBuilder, selectedValue) {
        var html = '';
        (items || []).forEach(function (item) {
            var value = String(item[valueKey]);
            var selected = String(selectedValue) === value ? ' selected' : '';
            html += '<option value="' + escapeHtml(value) + '"' + selected + '>' + escapeHtml(labelBuilder(item)) + '</option>';
        });
        return html;
    }

    function findById(items, id) {
        return (items || []).find(function (item) {
            return String(item.id) === String(id);
        }) || null;
    }

    function fetchResults(action) {
        return request(action).then(function (data) {
            if (!data.success) {
                throw new Error(data.message || 'Ошибка загрузки данных');
            }
            return data.object && data.object.results ? data.object.results : [];
        });
    }

    function renderCustomerModal(item, mode) {
        modalState.entity = 'customer';
        modalState.id = item.id || '';
        modalState.mode = mode || 'edit';
        openModal((modalState.mode === 'create' ? 'Добавить заказчика' : 'Редактирование заказчика #' + item.id), '' +
            '<div class="crmtime-modal-grid">' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label for="crm-modal-customer-name">Название</label>' +
                    '<input type="text" id="crm-modal-customer-name" value="' + escapeHtml(item.name || '') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-customer-code">Код</label>' +
                    '<input type="text" id="crm-modal-customer-code" value="' + escapeHtml(item.code || '') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label for="crm-modal-customer-description">Описание</label>' +
                    '<textarea id="crm-modal-customer-description">' + escapeHtml(item.description || '') + '</textarea>' +
                '</div>' +
            '</div>'
        );
    }

    function renderWorkplaceModal(item, customers, mode) {
        modalState.entity = 'workplace';
        modalState.id = item.id || '';
        modalState.mode = mode || 'edit';
        modalState.related.customers = customers || [];
        openModal((modalState.mode === 'create' ? 'Добавить место работы' : 'Редактирование места работы #' + item.id), '' +
            '<div class="crmtime-modal-grid">' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-workplace-customer">Заказчик</label>' +
                    '<select id="crm-modal-workplace-customer">' +
                        '<option value="">Выберите заказчика</option>' +
                        buildOptions(customers, 'id', function (row) { return row.id + ' — ' + row.name; }, item.customer_id) +
                    '</select>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-workplace-name">Название</label>' +
                    '<input type="text" id="crm-modal-workplace-name" value="' + escapeHtml(item.name || '') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label for="crm-modal-workplace-address">Адрес</label>' +
                    '<textarea id="crm-modal-workplace-address">' + escapeHtml(item.address || '') + '</textarea>' +
                '</div>' +
            '</div>'
        );
    }

    function renderAssignmentModal(item, users, customers, workplaces, mode) {
        modalState.entity = 'assignment';
        modalState.id = item.id || '';
        modalState.mode = mode || 'edit';
        modalState.related.users = users || [];
        modalState.related.customers = customers || [];
        modalState.related.workplaces = workplaces || [];

        openModal((modalState.mode === 'create' ? 'Добавить назначение' : 'Редактирование назначения #' + item.id), '' +
            '<div class="crmtime-modal-grid">' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-assignment-user">Сотрудник</label>' +
                    '<select id="crm-modal-assignment-user">' +
                        '<option value="">Выберите сотрудника</option>' +
                        buildOptions(users, 'id', function (row) {
                            var text = row.username || '';
                            if (row.fullname) {
                                text += ' — ' + row.fullname;
                            }
                            return row.id + ' — ' + text;
                        }, item.user_id) +
                    '</select>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-assignment-customer">Заказчик</label>' +
                    '<select id="crm-modal-assignment-customer">' +
                        '<option value="">Выберите заказчика</option>' +
                        buildOptions(customers, 'id', function (row) { return row.id + ' — ' + row.name; }, item.customer_id) +
                    '</select>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-assignment-workplace">Место работы</label>' +
                    '<select id="crm-modal-assignment-workplace"></select>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-assignment-start-date">Дата начала</label>' +
                    '<input type="date" id="crm-modal-assignment-start-date" value="' + escapeHtml(item.start_date || '') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-assignment-end-date">Дата окончания</label>' +
                    '<input type="date" id="crm-modal-assignment-end-date" value="' + escapeHtml(item.end_date || '') + '">' +
                '</div>' +
            '</div>'
        );

        function syncAssignmentWorkplaces(selectedWorkplaceId) {
            var customerId = document.getElementById('crm-modal-assignment-customer').value;
            var workplaceSelect = document.getElementById('crm-modal-assignment-workplace');
            var filtered = (modalState.related.workplaces || []).filter(function (row) {
                return !customerId || String(row.customer_id) === String(customerId);
            });
            workplaceSelect.innerHTML = '<option value="">Выберите место работы</option>' + buildOptions(filtered, 'id', function (row) {
                return row.id + ' — ' + row.name + ' (' + row.customer_name + ')';
            }, selectedWorkplaceId || item.workplace_id);
        }

        syncAssignmentWorkplaces(item.workplace_id);
        document.getElementById('crm-modal-assignment-customer').addEventListener('change', function () {
            syncAssignmentWorkplaces('');
        });
    }

    function renderEmployeeModal(item, users, mode) {
        modalState.entity = 'employee';
        modalState.id = item.id || '';
        modalState.mode = mode || 'edit';
        modalState.related.users = users || [];

        var userPicker = '';
        if (modalState.mode === 'create') {
            userPicker = '' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label for="crm-modal-employee-user-id">MODX пользователь</label>' +
                    '<select id="crm-modal-employee-user-id">' +
                        '<option value="">Выберите пользователя</option>' +
                        buildOptions(users, 'id', function (row) {
                            var text = row.username || '';
                            if (row.fullname) {
                                text += ' — ' + row.fullname;
                            }
                            return row.id + ' — ' + text;
                        }, item.id || '') +
                    '</select>' +
                '</div>';
        }

        openModal((modalState.mode === 'create' ? 'Добавить сотрудника в CRM' : 'Настройка сотрудника #' + item.id), '' +
            '<div class="crmtime-modal-grid">' +
                userPicker +
                '<div class="crmtime-modal-field">' +
                    '<label>Логин</label>' +
                    '<input type="text" value="' + escapeHtml(item.username || '') + '" readonly>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label>Имя</label>' +
                    '<input type="text" value="' + escapeHtml(item.fullname || '') + '" readonly>' +
                '</div>' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label>Email</label>' +
                    '<input type="text" value="' + escapeHtml(item.email || '') + '" readonly>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-employee-color">Цвет в календаре</label>' +
                    '<input type="color" id="crm-modal-employee-color" value="' + escapeHtml(item.color || '#3788d8') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label class="crmtime-modal-checkbox"><input type="checkbox" id="crm-modal-employee-active"' + (parseInt(item.crm_active || 1, 10) === 1 ? ' checked' : '') + '> Активен в CRM</label>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-employee-code">Код сотрудника</label>' +
                    '<input type="text" id="crm-modal-employee-code" value="' + escapeHtml(item.crm_code || '') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label for="crm-modal-employee-note">Заметка CRM</label>' +
                    '<textarea id="crm-modal-employee-note">' + escapeHtml(item.crm_note || '') + '</textarea>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-employee-standard-rate">Стандартная оплата</label>' +
                    '<input type="number" step="0.01" min="0" id="crm-modal-employee-standard-rate" value="' + escapeHtml(item.standard_rate || '0.00') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-employee-night-coeff">Ночной коэффициент</label>' +
                    '<input type="number" step="0.01" min="0" id="crm-modal-employee-night-coeff" value="' + escapeHtml(item.night_coeff || '1.00') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-employee-sunday-coeff">Воскресный коэффициент</label>' +
                    '<input type="number" step="0.01" min="0" id="crm-modal-employee-sunday-coeff" value="' + escapeHtml(item.sunday_coeff || '1.00') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-employee-holiday-coeff">Праздничный коэффициент</label>' +
                    '<input type="number" step="0.01" min="0" id="crm-modal-employee-holiday-coeff" value="' + escapeHtml(item.holiday_coeff || '1.00') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label for="crm-modal-employee-home-address">Место жительства сотрудника</label>' +
                    '<textarea id="crm-modal-employee-home-address">' + escapeHtml(item.home_address || '') + '</textarea>' +
                '</div>' +
            '</div>'
        );
    }

    function renderTimesheetModal(item, assignments, mode) {
        modalState.entity = 'timesheet';
        modalState.id = item.id || '';
        modalState.mode = mode || 'edit';
        modalState.related.assignments = assignments || [];
        openModal((modalState.mode === 'create' ? 'Добавить запись времени' : 'Редактирование записи времени #' + item.id), '' +
            '<div class="crmtime-modal-grid">' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<label for="crm-modal-timesheet-assignment">Назначение</label>' +
                    '<select id="crm-modal-timesheet-assignment">' +
                        '<option value="">Выберите назначение</option>' +
                        buildOptions(assignments, 'id', function (row) {
                            var text = row.fullname ? row.fullname : row.username;
                            return row.id + ' — ' + text + ' / ' + row.customer_name + ' / ' + row.workplace_name;
                        }, item.assignment_id) +
                    '</select>' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-timesheet-date">Дата</label>' +
                    '<input type="date" id="crm-modal-timesheet-date" value="' + escapeHtml(item.work_date || '') + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-timesheet-start">Время начала</label>' +
                    '<input type="time" id="crm-modal-timesheet-start" value="' + escapeHtml(normalizeTime(item.start_time)) + '">' +
                '</div>' +
                '<div class="crmtime-modal-field">' +
                    '<label for="crm-modal-timesheet-end">Время окончания</label>' +
                    '<input type="time" id="crm-modal-timesheet-end" value="' + escapeHtml(normalizeTime(item.end_time)) + '">' +
                '</div>' +
                '<div class="crmtime-modal-field crmtime-modal-field--full">' +
                    '<div class="crmtime-modal-checkboxes">' +
                        '<label class="crmtime-modal-checkbox"><input type="checkbox" id="crm-modal-timesheet-night"' + (parseInt(item.is_night, 10) === 1 ? ' checked' : '') + '> Ночь</label>' +
                        '<label class="crmtime-modal-checkbox"><input type="checkbox" id="crm-modal-timesheet-sunday"' + (parseInt(item.is_sunday, 10) === 1 ? ' checked' : '') + '> Воскресенье</label>' +
                        '<label class="crmtime-modal-checkbox"><input type="checkbox" id="crm-modal-timesheet-holiday"' + (parseInt(item.is_holiday, 10) === 1 ? ' checked' : '') + '> Праздник</label>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function loadCustomerEdit(id) {
        fetchResults('mgr/customer/getlist').then(function (items) {
            var item = findById(items, id);
            if (!item) {
                throw new Error('Заказчик не найден');
            }
            renderCustomerModal(item, 'edit');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function openCreateCustomer() {
        renderCustomerModal({ id: '', name: '', code: '', description: '' }, 'create');
    }

    function loadWorkplaceEdit(id) {
        Promise.all([
            fetchResults('mgr/workplace/getlist'),
            fetchResults('mgr/customer/getlist')
        ]).then(function (result) {
            var item = findById(result[0], id);
            if (!item) {
                throw new Error('Место работы не найдено');
            }
            renderWorkplaceModal(item, result[1], 'edit');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function openCreateWorkplace() {
        fetchResults('mgr/customer/getlist').then(function (customers) {
            renderWorkplaceModal({ id: '', customer_id: '', name: '', address: '' }, customers, 'create');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function loadAssignmentEdit(id) {
        Promise.all([
            fetchResults('mgr/assignment/getlist'),
            fetchResults('mgr/user/getlist'),
            fetchResults('mgr/customer/getlist'),
            fetchResults('mgr/workplace/getlist')
        ]).then(function (result) {
            var item = findById(result[0], id);
            if (!item) {
                throw new Error('Назначение не найдено');
            }
            renderAssignmentModal(item, result[1], result[2], result[3], 'edit');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function openCreateAssignment() {
        Promise.all([
            fetchResults('mgr/user/getlist'),
            fetchResults('mgr/customer/getlist'),
            fetchResults('mgr/workplace/getlist')
        ]).then(function (result) {
            renderAssignmentModal({ id: '', user_id: '', customer_id: '', workplace_id: '', start_date: '', end_date: '' }, result[0], result[1], result[2], 'create');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function loadEmployeeEdit(id) {
        fetchResults('mgr/user/getlist').then(function (items) {
            var item = findById(items, id);
            if (!item) {
                throw new Error('Сотрудник не найден');
            }
            renderEmployeeModal(item, items, 'edit');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function openCreateEmployee() {
        fetchResults('mgr/user/getlist').then(function (items) {
            renderEmployeeModal({
                id: '', username: '', fullname: '', email: '', crm_active: 1, color: '#3788d8', crm_code: '', crm_note: '',
                standard_rate: '0.00', night_coeff: '1.00', sunday_coeff: '1.00', holiday_coeff: '1.00', home_address: ''
            }, items, 'create');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function loadTimesheetEdit(id) {
        Promise.all([
            fetchResults('mgr/timesheet/getlist'),
            fetchResults('mgr/assignment/getlist')
        ]).then(function (result) {
            var item = findById(result[0], id);
            if (!item) {
                throw new Error('Запись времени не найдена');
            }
            renderTimesheetModal(item, result[1], 'edit');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function openCreateTimesheet() {
        fetchResults('mgr/assignment/getlist').then(function (assignments) {
            renderTimesheetModal({ id: '', assignment_id: '', work_date: '', start_time: '', end_time: '', is_night: 0, is_sunday: 0, is_holiday: 0 }, assignments, 'create');
        }).catch(function (error) {
            alert(error.message || error);
        });
    }

    function saveModal() {
        var action = '';
        var payload = {};

        if (modalState.entity === 'customer') {
            action = modalState.mode === 'create' ? 'mgr/customer/create' : 'mgr/customer/update';
            payload.id = modalState.id;
            payload.name = document.getElementById('crm-modal-customer-name').value;
            payload.code = document.getElementById('crm-modal-customer-code').value;
            payload.description = document.getElementById('crm-modal-customer-description').value;
        } else if (modalState.entity === 'workplace') {
            action = modalState.mode === 'create' ? 'mgr/workplace/create' : 'mgr/workplace/update';
            payload.id = modalState.id;
            payload.customer_id = document.getElementById('crm-modal-workplace-customer').value;
            payload.name = document.getElementById('crm-modal-workplace-name').value;
            payload.address = document.getElementById('crm-modal-workplace-address').value;
        } else if (modalState.entity === 'assignment') {
            action = modalState.mode === 'create' ? 'mgr/assignment/create' : 'mgr/assignment/update';
            payload.id = modalState.id;
            payload.user_id = document.getElementById('crm-modal-assignment-user').value;
            payload.customer_id = document.getElementById('crm-modal-assignment-customer').value;
            payload.workplace_id = document.getElementById('crm-modal-assignment-workplace').value;
            payload.start_date = document.getElementById('crm-modal-assignment-start-date').value;
            payload.end_date = document.getElementById('crm-modal-assignment-end-date').value;
        } else if (modalState.entity === 'employee') {
            action = 'mgr/employee/update';
            payload.id = modalState.mode === 'create' ? document.getElementById('crm-modal-employee-user-id').value : modalState.id;
            payload.crm_active = document.getElementById('crm-modal-employee-active').checked ? 1 : 0;
            payload.color = document.getElementById('crm-modal-employee-color').value;
            payload.crm_code = document.getElementById('crm-modal-employee-code').value;
            payload.crm_note = document.getElementById('crm-modal-employee-note').value;
            payload.standard_rate = document.getElementById('crm-modal-employee-standard-rate').value;
            payload.night_coeff = document.getElementById('crm-modal-employee-night-coeff').value;
            payload.sunday_coeff = document.getElementById('crm-modal-employee-sunday-coeff').value;
            payload.holiday_coeff = document.getElementById('crm-modal-employee-holiday-coeff').value;
            payload.home_address = document.getElementById('crm-modal-employee-home-address').value;
        } else if (modalState.entity === 'timesheet') {
            action = modalState.mode === 'create' ? 'mgr/timesheet/create' : 'mgr/timesheet/update';
            payload.id = modalState.id;
            payload.assignment_id = document.getElementById('crm-modal-timesheet-assignment').value;
            payload.work_date = document.getElementById('crm-modal-timesheet-date').value;
            payload.start_time = document.getElementById('crm-modal-timesheet-start').value;
            payload.end_time = document.getElementById('crm-modal-timesheet-end').value;
            payload.is_night = document.getElementById('crm-modal-timesheet-night').checked ? 1 : 0;
            payload.is_sunday = document.getElementById('crm-modal-timesheet-sunday').checked ? 1 : 0;
            payload.is_holiday = document.getElementById('crm-modal-timesheet-holiday').checked ? 1 : 0;
        }

        if (!action) {
            return;
        }

        request(action, payload).then(function (data) {
            if (!data.success) {
                setModalMessage('warning', data.message || 'Не удалось сохранить изменения');
                return;
            }

            setModalMessage('success', data.message || 'Изменения сохранены');
            saveActiveTabName();
            window.setTimeout(function () {
                window.location.reload();
            }, 250);
        }).catch(function (error) {
            setModalMessage('error', 'AJAX error: ' + error);
        });
    }

    function onEditClick(e) {
        var btn = e.target.closest('.crmtime-entity-action[data-action="edit"]');
        if (!btn) {
            return;
        }

        var entity = btn.getAttribute('data-entity');
        var id = btn.getAttribute('data-id');
        if (!entity || !id) {
            return;
        }

        if (['customer', 'workplace', 'assignment', 'employee', 'timesheet'].indexOf(entity) === -1) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        saveActiveTabName();

        if (entity === 'customer') {
            loadCustomerEdit(id);
        } else if (entity === 'workplace') {
            loadWorkplaceEdit(id);
        } else if (entity === 'assignment') {
            loadAssignmentEdit(id);
        } else if (entity === 'employee') {
            loadEmployeeEdit(id);
        } else if (entity === 'timesheet') {
            loadTimesheetEdit(id);
        }
    }

    function hideInlineControl(id) {
        var el = document.getElementById(id);
        if (!el) {
            return;
        }
        var row = el.closest('.crmtime-form-row');
        if (row) {
            row.style.display = 'none';
            return;
        }
        el.style.display = 'none';
    }

    function hideInlineForms() {
        [
            'crmtime-customer-id','crmtime-customer-name','crmtime-customer-code','crmtime-customer-description','crmtime-save-customer-btn','crmtime-cancel-customer-btn',
            'crmtime-workplace-id','crmtime-workplace-customer-id','crmtime-workplace-name','crmtime-workplace-address','crmtime-save-workplace-btn','crmtime-cancel-workplace-btn',
            'crmtime-assignment-id','crmtime-assignment-user-id','crmtime-assignment-customer-id','crmtime-assignment-workplace-id','crmtime-assignment-start-date','crmtime-assignment-end-date','crmtime-save-assignment-btn','crmtime-cancel-assignment-btn',
            'crmtime-employee-id','crmtime-employee-username','crmtime-employee-fullname','crmtime-employee-email','crmtime-employee-active','crmtime-employee-color','crmtime-employee-code','crmtime-employee-note','crmtime-employee-standard-rate','crmtime-employee-night-coeff','crmtime-employee-sunday-coeff','crmtime-employee-holiday-coeff','crmtime-employee-home-address','crmtime-save-employee-btn','crmtime-cancel-employee-btn',
            'crmtime-timesheet-id','crmtime-timesheet-assignment-id','crmtime-timesheet-work-date','crmtime-timesheet-start-time','crmtime-timesheet-end-time','crmtime-timesheet-is-night','crmtime-timesheet-is-sunday','crmtime-timesheet-is-holiday','crmtime-save-timesheet-btn','crmtime-cancel-timesheet-btn'
        ].forEach(hideInlineControl);
    }

    function insertAddButtonBefore(listId, buttonId, text, handler) {
        var listEl = document.getElementById(listId);
        if (!listEl || document.getElementById(buttonId)) {
            return;
        }

        var row = document.createElement('div');
        row.className = 'crmtime-modal-add-row';
        row.id = buttonId + '-row';
        row.innerHTML = '<button type="button" class="btn btn-primary" id="' + buttonId + '">' + escapeHtml(text) + '</button>';

        listEl.parentNode.insertBefore(row, listEl);
        document.getElementById(buttonId).addEventListener('click', handler);
    }

    injectStyles();
    createModalRoot();
    restoreActiveTabName();
    hideInlineForms();

    insertAddButtonBefore('crmtime-customers-list', 'crmtime-add-customer-modal-btn', 'Добавить заказчика', openCreateCustomer);
    insertAddButtonBefore('crmtime-workplaces-list', 'crmtime-add-workplace-modal-btn', 'Добавить место работы', openCreateWorkplace);
    insertAddButtonBefore('crmtime-assignments-list', 'crmtime-add-assignment-modal-btn', 'Добавить назначение', openCreateAssignment);
    insertAddButtonBefore('crmtime-employees-list', 'crmtime-add-employee-modal-btn', 'Добавить сотрудника в CRM', openCreateEmployee);
    insertAddButtonBefore('crmtime-timesheets-list', 'crmtime-add-timesheet-modal-btn', 'Добавить запись времени', openCreateTimesheet);

    document.addEventListener('click', onEditClick, true);
    document.getElementById('crmtime-edit-modal-save').addEventListener('click', saveModal);
});
