<div class="crmtime-shell">
    <div class="crmtime-scroll" id="crmtime-scroll">
        <div class="crmtime-page">
            <h1>crmTime</h1>
            <p>Рабочая панель CRM учета времени.</p>

            <div class="crmtime-tabs-nav">
                <button type="button" class="crmtime-tab-btn is-active" data-tab="overview">Обзор</button>
                <button type="button" class="crmtime-tab-btn" data-tab="directories">Справочники</button>
                <button type="button" class="crmtime-tab-btn" data-tab="employees">Сотрудники</button>
                <button type="button" class="crmtime-tab-btn" data-tab="timesheets">Учёт времени</button>
                <button type="button" class="crmtime-tab-btn" data-tab="calendar">Календарь</button>
                <button type="button" class="crmtime-tab-btn" data-tab="reports">Отчёты</button>
                <button type="button" class="crmtime-tab-btn" data-tab="documents">Документы</button>
            </div>

            <div id="crmtime-global-message" class="crmtime-message" style="display:none;"></div>

            <div class="crmtime-tab-panel is-active" data-tab-panel="overview">
                <div class="crmtime-stats">
                    <div class="crmtime-stat-card">
                        <div class="crmtime-stat-label">Очередь согласования</div>
                        <div class="crmtime-stat-value" id="crmtime-stat-approvals">0</div>
                    </div>

                    <div class="crmtime-stat-card">
                        <div class="crmtime-stat-label">Нарушения 11 часов</div>
                        <div class="crmtime-stat-value" id="crmtime-stat-violations">0</div>
                    </div>

                    <div class="crmtime-stat-card">
                        <div class="crmtime-stat-label">Записи времени</div>
                        <div class="crmtime-stat-value" id="crmtime-stat-timesheets">0</div>
                    </div>
                </div>

                <div class="crmtime-card">
                    <h2>Проверка connector</h2>
                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-primary" id="crmtime-ping-btn">Проверить AJAX</button>
                    </div>
                    <div id="crmtime-ping-message" class="crmtime-message" style="display:none;"></div>
                </div>

                <div class="crmtime-card">
                    <h2>Установка таблиц</h2>
                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-primary" id="crmtime-install-btn">Установить / проверить таблицы</button>
                    </div>
                    <div id="crmtime-install-message" class="crmtime-message" style="display:none;"></div>
                </div>
            </div>

            <div class="crmtime-tab-panel" data-tab-panel="directories">
                <div class="crmtime-card">
                    <h2 id="crmtime-customer-form-title">Заказчики</h2>

                    <input type="hidden" id="crmtime-customer-id" value="">

                    <div class="crmtime-form-row">
                        <input type="text" id="crmtime-customer-name" placeholder="Название заказчика">
                    </div>

                    <div class="crmtime-form-row">
                        <input type="text" id="crmtime-customer-code" placeholder="Код">
                    </div>

                    <div class="crmtime-form-row">
                        <textarea id="crmtime-customer-description" placeholder="Описание"></textarea>
                    </div>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-success" id="crmtime-save-customer-btn">Сохранить заказчика</button>
                        <button type="button" class="btn" id="crmtime-cancel-customer-btn" style="display:none;">Отменить редактирование</button>
                    </div>

                    <div id="crmtime-customer-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-customers-list"></div>
                </div>

                <div class="crmtime-card">
                    <h2 id="crmtime-workplace-form-title">Места работы</h2>

                    <input type="hidden" id="crmtime-workplace-id" value="">

                    <div class="crmtime-form-row">
                        <label for="crmtime-workplace-customer-id">ID заказчика</label>
                        <input type="number" id="crmtime-workplace-customer-id" placeholder="Например: 1">
                    </div>

                    <div class="crmtime-form-row">
                        <input type="text" id="crmtime-workplace-name" placeholder="Название места работы">
                    </div>

                    <div class="crmtime-form-row">
                        <textarea id="crmtime-workplace-address" placeholder="Адрес"></textarea>
                    </div>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-success" id="crmtime-save-workplace-btn">Сохранить место работы</button>
                        <button type="button" class="btn" id="crmtime-cancel-workplace-btn" style="display:none;">Отменить редактирование</button>
                    </div>

                    <div id="crmtime-workplace-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-workplaces-list"></div>
                </div>

                <div class="crmtime-card">
                    <h2 id="crmtime-assignment-form-title">Назначения</h2>

                    <input type="hidden" id="crmtime-assignment-id" value="">

                    <div class="crmtime-form-row">
                        <label for="crmtime-assignment-user-id">Сотрудник</label>
                        <select id="crmtime-assignment-user-id">
                            <option value="">Выберите сотрудника</option>
                        </select>
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-assignment-customer-id">Заказчик</label>
                        <select id="crmtime-assignment-customer-id">
                            <option value="">Выберите заказчика</option>
                        </select>
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-assignment-workplace-id">Место работы</label>
                        <select id="crmtime-assignment-workplace-id">
                            <option value="">Выберите место работы</option>
                        </select>
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-assignment-start-date">Дата начала</label>
                        <input type="date" id="crmtime-assignment-start-date">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-assignment-end-date">Дата окончания</label>
                        <input type="date" id="crmtime-assignment-end-date">
                    </div>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-success" id="crmtime-save-assignment-btn">Сохранить назначение</button>
                        <button type="button" class="btn" id="crmtime-cancel-assignment-btn" style="display:none;">Отменить редактирование</button>
                    </div>

                    <div id="crmtime-users-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-assignment-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-assignments-list"></div>
                </div>
            </div>

            <div class="crmtime-tab-panel" data-tab-panel="employees">
                <div class="crmtime-card">
                    <h2 id="crmtime-employee-form-title">Сотрудники CRM</h2>

                    <input type="hidden" id="crmtime-employee-id" value="">

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-username">Логин</label>
                        <input type="text" id="crmtime-employee-username" readonly>
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-fullname">Имя</label>
                        <input type="text" id="crmtime-employee-fullname" readonly>
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-email">Email</label>
                        <input type="text" id="crmtime-employee-email" readonly>
                    </div>

                    <div class="crmtime-form-row">
                        <label>
                            <input type="checkbox" id="crmtime-employee-active" checked>
                            Активен в CRM
                        </label>
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-color">Цвет в календаре</label>
                        <input type="color" id="crmtime-employee-color" value="#3788d8">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-code">Код сотрудника</label>
                        <input type="text" id="crmtime-employee-code" placeholder="Например: EMP-001">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-note">Заметка CRM</label>
                        <textarea id="crmtime-employee-note" placeholder="Внутренняя заметка по сотруднику"></textarea>
                    </div>

                    <hr>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-standard-rate">СТАНДАРТНАЯ ОПЛАТА</label>
                        <input type="number" step="0.01" min="0" id="crmtime-employee-standard-rate" placeholder="Например: 25.00">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-night-coeff">НОЧНОЙ ТАРИФ (коэффициент)</label>
                        <input type="number" step="0.01" min="0" id="crmtime-employee-night-coeff" placeholder="Например: 1.20">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-sunday-coeff">Воскресный (коэффициент)</label>
                        <input type="number" step="0.01" min="0" id="crmtime-employee-sunday-coeff" placeholder="Например: 1.50">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-holiday-coeff">Праздничный (коэффициент)</label>
                        <input type="number" step="0.01" min="0" id="crmtime-employee-holiday-coeff" placeholder="Например: 2.00">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-employee-home-address">Место жительства сотрудника</label>
                        <textarea id="crmtime-employee-home-address" placeholder="Адрес проживания сотрудника"></textarea>
                    </div>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-success" id="crmtime-save-employee-btn">Сохранить CRM-настройки</button>
                        <button type="button" class="btn" id="crmtime-cancel-employee-btn" style="display:none;">Отменить редактирование</button>
                    </div>

                    <div id="crmtime-employee-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-employees-list"></div>
                </div>
            </div>

            <div class="crmtime-tab-panel" data-tab-panel="timesheets">
                <div class="crmtime-card">
                    <h2 id="crmtime-timesheet-form-title">Фактическое время</h2>

                    <input type="hidden" id="crmtime-timesheet-id" value="">

                    <div class="crmtime-form-row">
                        <label for="crmtime-timesheet-assignment-id">Назначение</label>
                        <select id="crmtime-timesheet-assignment-id">
                            <option value="">Выберите назначение</option>
                        </select>
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-timesheet-work-date">Дата</label>
                        <input type="date" id="crmtime-timesheet-work-date">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-timesheet-start-time">Время начала</label>
                        <input type="time" id="crmtime-timesheet-start-time">
                    </div>

                    <div class="crmtime-form-row">
                        <label for="crmtime-timesheet-end-time">Время окончания</label>
                        <input type="time" id="crmtime-timesheet-end-time">
                    </div>

                    <div class="crmtime-form-row">
                        <label>
                            <input type="checkbox" id="crmtime-timesheet-is-night">
                            Ночь
                        </label>
                    </div>

                    <div class="crmtime-form-row">
                        <label>
                            <input type="checkbox" id="crmtime-timesheet-is-sunday">
                            Воскресенье
                        </label>
                    </div>

                    <div class="crmtime-form-row">
                        <label>
                            <input type="checkbox" id="crmtime-timesheet-is-holiday">
                            Праздник
                        </label>
                    </div>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-success" id="crmtime-save-timesheet-btn">Сохранить запись времени</button>
                        <button type="button" class="btn" id="crmtime-cancel-timesheet-btn" style="display:none;">Отменить редактирование</button>
                    </div>

                    <div id="crmtime-timesheet-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-timesheets-list"></div>
                </div>

                <div class="crmtime-card">
                    <h2>Нарушения 11 часов</h2>
                    <div id="crmtime-violation-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-violations-list"></div>
                </div>

                <div class="crmtime-card">
                    <h2>Очередь согласования</h2>
                    <div id="crmtime-approval-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-approvals-list"></div>
                </div>
            </div>

            <div class="crmtime-tab-panel" data-tab-panel="calendar">
                <div class="crmtime-card">
                    <h2>Календарь админа</h2>

                    <div class="crmtime-filters">
                        <div class="crmtime-filter">
                            <label for="crmtime-calendar-user-id">Сотрудник</label>
                            <select id="crmtime-calendar-user-id">
                                <option value="">Все сотрудники</option>
                            </select>
                        </div>

                        <div class="crmtime-filter">
                            <label for="crmtime-calendar-customer-id">Заказчик</label>
                            <select id="crmtime-calendar-customer-id">
                                <option value="">Все заказчики</option>
                            </select>
                        </div>

                        <div class="crmtime-filter">
                            <label for="crmtime-calendar-workplace-id">Место работы</label>
                            <select id="crmtime-calendar-workplace-id">
                                <option value="">Все места работы</option>
                            </select>
                        </div>

                        <div class="crmtime-filter">
                            <label for="crmtime-calendar-status">Статус</label>
                            <select id="crmtime-calendar-status">
                                <option value="">Все статусы</option>
                                <option value="draft">draft</option>
                                <option value="submitted">submitted</option>
                                <option value="approved">approved</option>
                                <option value="rejected">rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-primary" id="crmtime-calendar-apply-btn">Применить фильтры</button>
                        <button type="button" class="btn" id="crmtime-calendar-reset-btn">Сбросить фильтры</button>
                    </div>

                    <div id="crmtime-calendar-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-manager-calendar"></div>
                </div>

                <div class="crmtime-card">
                    <h2>Кто и где работает по дню</h2>
                    <p id="crmtime-daydetails-title">Выберите день в календаре.</p>
                    <div id="crmtime-daydetails-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-daydetails-list"></div>
                </div>
            </div>

            <div class="crmtime-tab-panel" data-tab-panel="reports">
                <div class="crmtime-card">
                    <h2>Отчёты</h2>

                    <div class="crmtime-filters">
                        <div class="crmtime-filter">
                            <label for="crmtime-report-date-from">Дата с</label>
                            <input type="date" id="crmtime-report-date-from">
                        </div>

                        <div class="crmtime-filter">
                            <label for="crmtime-report-date-to">Дата по</label>
                            <input type="date" id="crmtime-report-date-to">
                        </div>

                        <div class="crmtime-filter">
                            <label for="crmtime-report-customer-id">Заказчик</label>
                            <select id="crmtime-report-customer-id">
                                <option value="">Все заказчики</option>
                            </select>
                        </div>

                        <div class="crmtime-filter">
                            <label for="crmtime-report-user-id">Сотрудник</label>
                            <select id="crmtime-report-user-id">
                                <option value="">Все сотрудники</option>
                            </select>
                        </div>
                    </div>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn btn-primary" id="crmtime-report-run-btn">Построить отчёт</button>
                        <button type="button" class="btn btn-success" id="crmtime-report-pdf-btn">Скачать PDF и сохранить</button>
                    </div>

                    <div id="crmtime-report-message" class="crmtime-message" style="display:none;"></div>

                    <div class="crmtime-report-stats" id="crmtime-report-stats"></div>

                    <div class="crmtime-card crmtime-card-inner">
                        <h3>По сотрудникам</h3>
                        <div id="crmtime-report-employees"></div>
                    </div>

                    <div class="crmtime-card crmtime-card-inner">
                        <h3>Детализация</h3>
                        <div id="crmtime-report-rows"></div>
                    </div>
                </div>
            </div>

            <div class="crmtime-tab-panel" data-tab-panel="documents">
                <div class="crmtime-card">
                    <h2>Архив документов</h2>
                    <p>Здесь сохраняются PDF, которые были сформированы из вкладки «Отчёты».</p>

                    <div class="crmtime-form-row">
                        <button type="button" class="btn" id="crmtime-documents-refresh-btn">Обновить список</button>
                    </div>

                    <div id="crmtime-documents-message" class="crmtime-message" style="display:none;"></div>
                    <div id="crmtime-documents-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>
