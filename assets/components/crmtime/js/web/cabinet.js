document.addEventListener('DOMContentLoaded', function () {
    if (!window.crmTimeCabinetConfig) {
        return;
    }

    var assignmentResult = document.getElementById('crmtime-web-assignment-result');
    var assignmentsList = document.getElementById('crmtime-web-assignments-list');
    var assignmentSelect = document.getElementById('crmtime-web-assignment-id');

    var calendarResult = document.getElementById('crmtime-web-calendar-result');
    var calendarEl = document.getElementById('crmtime-web-calendar');
    var calendarInstance = null;
    var calendarDetailsModalInstance = null;

    var formTitle = document.getElementById('crmtime-web-form-title');
    var timesheetIdField = document.getElementById('crmtime-web-timesheet-id');
    var saveBtn = document.getElementById('crmtime-web-save-btn');
    var cancelEditBtn = document.getElementById('crmtime-web-cancel-edit-btn');
    var reloadBtn = document.getElementById('crmtime-web-reload-btn');

    var timesheetResult = document.getElementById('crmtime-web-timesheet-result');
    var timesheetsList = document.getElementById('crmtime-web-timesheets-list');

    var workDate = document.getElementById('crmtime-web-work-date');
    var startTime = document.getElementById('crmtime-web-start-time');
    var endTime = document.getElementById('crmtime-web-end-time');
    var isNight = document.getElementById('crmtime-web-is-night');
    var isSunday = document.getElementById('crmtime-web-is-sunday');
    var isHoliday = document.getElementById('crmtime-web-is-holiday');

    var violationResult = document.getElementById('crmtime-web-violation-result');
    var violationsList = document.getElementById('crmtime-web-violations-list');

    var signatureModalEl = document.getElementById('crmtime-signature-modal');
    var signatureModalTitle = document.getElementById('crmtime-signature-modal-title');
    var signatureTimesheetId = document.getElementById('crmtime-signature-timesheet-id');
    var signatureMessage = document.getElementById('crmtime-signature-message');
    var signatureSummary = document.getElementById('crmtime-signature-summary');
    var signatureCanvas = document.getElementById('crmtime-signature-canvas');
    var signatureClearBtn = document.getElementById('crmtime-signature-clear-btn');
    var signatureFileInput = document.getElementById('crmtime-signature-file');
    var signaturePreviewWrap = document.getElementById('crmtime-signature-preview-wrap');
    var signaturePreview = document.getElementById('crmtime-signature-preview');
    var signatureApplyBtn = document.getElementById('crmtime-signature-apply-btn');
    var signatureModalInstance = null;

    var currentAssignments = [];
    var currentTimesheets = [];

    var signatureCtx = null;
    var signatureDrawing = false;
    var signatureHasInk = false;
    var signatureSubmitAfterSave = false;

    function request(action, payload) {
        payload = payload || {};

        var formData = new FormData();
        formData.append('action', action);

        Object.keys(payload).forEach(function (key) {
            formData.append(key, payload[key]);
        });

        return fetch(window.crmTimeCabinetConfig.connector_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (text) {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    return {
                        success: false,
                        message: text,
                        object: {}
                    };
                }
            });
    }

    function showDebug(el, data) {
        if (!el) {
            return;
        }

        if (typeof data === 'string') {
            el.textContent = data;
            return;
        }

        try {
            el.textContent = JSON.stringify(data, null, 2);
        } catch (e) {
            el.textContent = String(data);
        }
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

    function translateStatus(status) {
        switch (String(status || '')) {
            case 'draft':
                return 'Entwurf';
            case 'submitted':
                return 'Eingereicht';
            case 'approved':
                return 'Genehmigt';
            case 'rejected':
                return 'Abgelehnt';
            default:
                return status || '';
        }
    }

    function getTimesheetById(id) {
        return currentTimesheets.find(function (row) {
            return String(row.id) === String(id);
        }) || null;
    }

    function setTimesheetMessage(text, type) {
        type = type || 'info';

        if (!timesheetResult) {
            return;
        }

        if (!text) {
            timesheetResult.textContent = '';
            return;
        }

        if (type === 'warning') {
            timesheetResult.textContent = '[WARNUNG] ' + text;
            return;
        }

        if (type === 'error') {
            timesheetResult.textContent = '[FEHLER] ' + text;
            return;
        }

        timesheetResult.textContent = text;
    }

    function getTariffText(item) {
        var flags = [];

        if (parseInt(item.is_night, 10) === 1) {
            flags.push('Nacht');
        }

        if (parseInt(item.is_sunday, 10) === 1) {
            flags.push('Sonntag');
        }

        if (parseInt(item.is_holiday, 10) === 1) {
            flags.push('Feiertag');
        }

        return flags.length ? flags.join(', ') : 'Standard';
    }

    function resetForm() {
        timesheetIdField.value = '';
        assignmentSelect.value = '';
        workDate.value = '';
        startTime.value = '';
        endTime.value = '';
        isNight.checked = false;
        isSunday.checked = false;
        isHoliday.checked = false;
        formTitle.textContent = 'Zeiteintrag hinzufügen';
        saveBtn.textContent = 'Als Entwurf speichern';
        cancelEditBtn.style.display = 'none';
    }

    function setEditMode(item) {
        timesheetIdField.value = item.id;
        assignmentSelect.value = item.assignment_id;
        workDate.value = item.work_date || '';
        startTime.value = normalizeTime(item.start_time);
        endTime.value = normalizeTime(item.end_time);
        isNight.checked = parseInt(item.is_night, 10) === 1;
        isSunday.checked = parseInt(item.is_sunday, 10) === 1;
        isHoliday.checked = parseInt(item.is_holiday, 10) === 1;
        formTitle.textContent = 'Zeiteintrag bearbeiten';
        saveBtn.textContent = 'Änderungen speichern';
        cancelEditBtn.style.display = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function fillAssignments(items) {
        var html = '<option value="">Einsatz auswählen</option>';

        (items || []).forEach(function (item) {
            html += '<option value="' + escapeHtml(item.id) + '">';
            html += escapeHtml(item.id + ' — ' + item.customer_name + ' / ' + item.workplace_name);
            html += '</option>';
        });

        assignmentSelect.innerHTML = html;
    }

    function renderAssignments(items) {
        currentAssignments = items || [];

        if (!items || !items.length) {
            assignmentsList.innerHTML = '<p class="mb-0">Noch keine Einsätze vorhanden.</p>';
            fillAssignments([]);
            return;
        }

        var html = '<div class="table-responsive">';
        html += '<table class="table table-striped align-middle mb-0">';
        html += '<thead><tr><th>ID</th><th>Kunde</th><th>Arbeitsort</th><th>Adresse</th><th>Von</th><th>Bis</th></tr></thead><tbody>';

        items.forEach(function (item) {
            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name) + '</td>';
            html += '<td>' + escapeHtml(item.workplace_name) + '</td>';
            html += '<td>' + escapeHtml(item.workplace_address || '') + '</td>';
            html += '<td>' + escapeHtml(item.start_date) + '</td>';
            html += '<td>' + escapeHtml(item.end_date || '—') + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        html += '</div>';

        assignmentsList.innerHTML = html;
        fillAssignments(items);
    }

    function renderTimesheets(items) {
        currentTimesheets = items || [];

        if (!items || !items.length) {
            timesheetsList.innerHTML = '<p class="mb-0">Noch keine Zeiteinträge vorhanden.</p>';
            return;
        }

        var html = '<div class="table-responsive">';
        html += '<table class="table table-striped align-middle mb-0">';
        html += '<thead><tr><th>ID</th><th>Kunde</th><th>Arbeitsort</th><th>Datum</th><th>Beginn</th><th>Ende</th><th>Tarif</th><th>Satz</th><th>Betrag</th><th>Status</th><th>Kommentar</th><th>Signatur</th><th>Aktionen</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var statusLabel = translateStatus(item.status);
            var actions = '';
            var signLabel = (parseInt(item.is_signed, 10) === 1 && item.signature_url)
                ? '<a href="' + escapeHtml(item.signature_url) + '" target="_blank" class="btn btn-sm btn-outline-success">Ansehen</a>'
                : '<span class="badge text-bg-secondary">Fehlt</span>';

            if (parseInt(item.has_violation, 10) > 0) {
                statusLabel += ' ⚠';
            }

            if (item.status === 'draft' || item.status === 'rejected') {
                actions += '<button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1 crmtime-web-action-btn" data-action="sign" data-id="' + escapeHtml(item.id) + '">Unterschrift</button>';
                actions += '<button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1 crmtime-web-action-btn" data-action="edit" data-id="' + escapeHtml(item.id) + '">Bearbeiten</button>';
                actions += '<button type="button" class="btn btn-sm btn-primary me-1 mb-1 crmtime-web-action-btn" data-action="submit" data-id="' + escapeHtml(item.id) + '">Einreichen mit Unterschrift</button>';
            }

            if (item.status === 'draft') {
                actions += '<button type="button" class="btn btn-sm btn-outline-danger mb-1 crmtime-web-action-btn" data-action="remove" data-id="' + escapeHtml(item.id) + '">Löschen</button>';
            }

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(item.customer_name) + '</td>';
            html += '<td>' + escapeHtml(item.workplace_name) + '</td>';
            html += '<td>' + escapeHtml(item.work_date) + '</td>';
            html += '<td>' + escapeHtml(item.start_time) + '</td>';
            html += '<td>' + escapeHtml(item.end_time) + '</td>';
            html += '<td>' + escapeHtml(item.tariff_text || getTariffText(item)) + '</td>';
            html += '<td>' + escapeHtml(item.rate || '0.00') + '</td>';
            html += '<td>' + escapeHtml(item.amount || '0.00') + '</td>';
            html += '<td>' + escapeHtml(statusLabel) + '</td>';
            html += '<td>' + escapeHtml(item.admin_comment || '') + '</td>';
            html += '<td>' + signLabel + '</td>';
            html += '<td>' + actions + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        html += '</div>';

        timesheetsList.innerHTML = html;
    }

    function renderViolations(items) {
        if (!items || !items.length) {
            violationsList.innerHTML = '<p class="mb-0">Noch keine Verstöße vorhanden.</p>';
            return;
        }

        var html = '<div class="table-responsive">';
        html += '<table class="table table-striped align-middle mb-0">';
        html += '<thead><tr><th>ID</th><th>Aktueller Eintrag</th><th>Zugehöriger Eintrag</th><th>Richtung</th><th>Ruhezeit, Std.</th><th>Vorgabe, Std.</th><th>Meldung</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var currentText = '#' + item.timesheet_id + ' / ' + item.timesheet_date + ' / ' + item.timesheet_start + '–' + item.timesheet_end;
            var relatedText = '#' + item.related_timesheet_id + ' / ' + item.related_date + ' / ' + item.related_start + '–' + item.related_end;

            html += '<tr>';
            html += '<td>' + escapeHtml(item.id) + '</td>';
            html += '<td>' + escapeHtml(currentText) + '</td>';
            html += '<td>' + escapeHtml(relatedText) + '</td>';
            html += '<td>' + escapeHtml(item.direction) + '</td>';
            html += '<td>' + escapeHtml(item.rest_hours) + '</td>';
            html += '<td>' + escapeHtml(item.required_hours) + '</td>';
            html += '<td>' + escapeHtml(item.message) + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        html += '</div>';

        violationsList.innerHTML = html;
    }

    function getEventColors(status, hasViolation) {
        if (hasViolation) {
            return {
                backgroundColor: '#fd7e14',
                borderColor: '#fd7e14'
            };
        }

        if (status === 'draft') {
            return {
                backgroundColor: '#6c757d',
                borderColor: '#6c757d'
            };
        }

        if (status === 'submitted') {
            return {
                backgroundColor: '#0d6efd',
                borderColor: '#0d6efd'
            };
        }

        if (status === 'approved') {
            return {
                backgroundColor: '#198754',
                borderColor: '#198754'
            };
        }

        if (status === 'rejected') {
            return {
                backgroundColor: '#dc3545',
                borderColor: '#dc3545'
            };
        }

        return {
            backgroundColor: '#6c757d',
            borderColor: '#6c757d'
        };
    }

    function setSignatureMessage(type, text) {
        if (!signatureMessage) {
            return;
        }

        if (!text) {
            signatureMessage.className = 'alert d-none';
            signatureMessage.innerHTML = '';
            return;
        }

        var cls = 'alert ';
        if (type === 'success') {
            cls += 'alert-success';
        } else if (type === 'warning') {
            cls += 'alert-warning';
        } else {
            cls += 'alert-info';
        }

        signatureMessage.className = cls;
        signatureMessage.innerHTML = text;
    }

    function resizeSignatureCanvas() {
        if (!signatureCanvas) {
            return;
        }

        var ratio = window.devicePixelRatio || 1;
        var rect = signatureCanvas.getBoundingClientRect();

        if (!rect.width || !rect.height) {
            return;
        }

        signatureCanvas.width = Math.round(rect.width * ratio);
        signatureCanvas.height = Math.round(rect.height * ratio);

        signatureCtx = signatureCanvas.getContext('2d');
        signatureCtx.setTransform(1, 0, 0, 1, 0, 0);
        signatureCtx.scale(ratio, ratio);
        signatureCtx.lineWidth = 2;
        signatureCtx.lineCap = 'round';
        signatureCtx.lineJoin = 'round';
        signatureCtx.strokeStyle = '#111111';
        signatureCtx.fillStyle = '#ffffff';
        signatureCtx.fillRect(0, 0, rect.width, rect.height);
    }

    function clearSignatureCanvas() {
        if (!signatureCanvas || !signatureCtx) {
            return;
        }

        var rect = signatureCanvas.getBoundingClientRect();
        signatureCtx.fillStyle = '#ffffff';
        signatureCtx.fillRect(0, 0, rect.width, rect.height);
        signatureCtx.strokeStyle = '#111111';
        signatureHasInk = false;
    }

    function getCanvasPoint(event) {
        var rect = signatureCanvas.getBoundingClientRect();
        return {
            x: event.clientX - rect.left,
            y: event.clientY - rect.top
        };
    }

    function initSignatureCanvas() {
        if (!signatureCanvas) {
            return;
        }

        resizeSignatureCanvas();
        signatureCanvas.style.touchAction = 'none';

        signatureCanvas.addEventListener('pointerdown', function (event) {
            if (!signatureCtx) {
                return;
            }

            var point = getCanvasPoint(event);
            signatureDrawing = true;
            signatureHasInk = true;
            signatureCtx.beginPath();
            signatureCtx.moveTo(point.x, point.y);
        });

        signatureCanvas.addEventListener('pointermove', function (event) {
            if (!signatureDrawing || !signatureCtx) {
                return;
            }

            var point = getCanvasPoint(event);
            signatureCtx.lineTo(point.x, point.y);
            signatureCtx.stroke();
        });

        function stopSignatureDrawing() {
            signatureDrawing = false;
        }

        signatureCanvas.addEventListener('pointerup', stopSignatureDrawing);
        signatureCanvas.addEventListener('pointerleave', stopSignatureDrawing);
        signatureCanvas.addEventListener('pointercancel', stopSignatureDrawing);
    }

    function readFileAsDataUrl(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();

            reader.onload = function (e) {
                resolve(e.target.result);
            };

            reader.onerror = function () {
                reject('Datei konnte nicht gelesen werden.');
            };

            reader.readAsDataURL(file);
        });
    }

    function showSignaturePreview(dataUrl) {
        if (!signaturePreviewWrap || !signaturePreview) {
            return;
        }

        if (!dataUrl) {
            signaturePreviewWrap.style.display = 'none';
            signaturePreview.src = '';
            return;
        }

        signaturePreview.src = dataUrl;
        signaturePreviewWrap.style.display = '';
    }

    function buildSignatureSummary(item) {
        var statusLabel = translateStatus(item.status || '');
        if (parseInt(item.has_violation, 10) > 0) {
            statusLabel += ' ⚠';
        }

        var html = '<div class="table-responsive">';
        html += '<table class="table table-striped align-middle mb-0">';
        html += '<tbody>';
        html += '<tr><th style="width:220px;">ID</th><td>' + escapeHtml(item.id || '') + '</td></tr>';
        html += '<tr><th>Kunde</th><td>' + escapeHtml(item.customer_name || '') + '</td></tr>';
        html += '<tr><th>Arbeitsort</th><td>' + escapeHtml(item.workplace_name || '') + '</td></tr>';
        html += '<tr><th>Adresse</th><td>' + escapeHtml(item.workplace_address || '—') + '</td></tr>';
        html += '<tr><th>Datum</th><td>' + escapeHtml(item.work_date || '') + '</td></tr>';
        html += '<tr><th>Beginn</th><td>' + escapeHtml(item.start_time || '') + '</td></tr>';
        html += '<tr><th>Ende</th><td>' + escapeHtml(item.end_time || '') + '</td></tr>';
        html += '<tr><th>Tarif</th><td>' + escapeHtml(item.tariff_text || getTariffText(item)) + '</td></tr>';
        html += '<tr><th>Satz</th><td>' + escapeHtml(item.rate || '0.00') + '</td></tr>';
        html += '<tr><th>Status</th><td>' + escapeHtml(statusLabel) + '</td></tr>';
        html += '<tr><th>Kommentar</th><td>' + escapeHtml(item.admin_comment || '—') + '</td></tr>';
        html += '<tr><th>Signiert</th><td>' + (parseInt(item.is_signed, 10) === 1 ? 'Ja' : 'Nein') + '</td></tr>';
        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        return html;
    }

    function openSignatureModal(item, submitAfterSave) {
        if (!signatureModalEl || !window.bootstrap || !bootstrap.Modal) {
            alert('Bootstrap Modal wurde auf der Seite nicht gefunden.');
            return;
        }

        signatureSubmitAfterSave = !!submitAfterSave;
        signatureTimesheetId.value = item.id || '';
        signatureModalTitle.textContent = 'Unterschrift für Eintrag #' + (item.id || '');
        signatureSummary.innerHTML = buildSignatureSummary(item);
        signatureApplyBtn.textContent = signatureSubmitAfterSave ? 'Signatur speichern und einreichen' : 'Signatur speichern';

        setSignatureMessage('', '');

        if (signatureFileInput) {
            signatureFileInput.value = '';
        }

        resizeSignatureCanvas();
        clearSignatureCanvas();

        if (parseInt(item.is_signed, 10) === 1 && item.signature_url) {
            showSignaturePreview(item.signature_url);
            setSignatureMessage('info', 'Für diesen Eintrag ist bereits eine gespeicherte Signatur vorhanden. Sie können sie überschreiben.');
        } else {
            showSignaturePreview('');
        }

        signatureModalInstance = bootstrap.Modal.getOrCreateInstance(signatureModalEl);
        signatureModalInstance.show();
    }

    function ensureCalendarDetailsModal() {
        var existing = document.getElementById('crmtime-calendar-details-modal');
        if (existing) {
            return existing;
        }

        var html = '';
        html += '<div class="modal fade" id="crmtime-calendar-details-modal" tabindex="-1" aria-hidden="true">';
        html += '  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">';
        html += '      <div class="modal-content">';
        html += '          <div class="modal-header">';
        html += '              <h5 class="modal-title" id="crmtime-calendar-details-title">Eintragsdetails</h5>';
        html += '              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>';
        html += '          </div>';
        html += '          <div class="modal-body" id="crmtime-calendar-details-body"></div>';
        html += '          <div class="modal-footer" id="crmtime-calendar-details-footer">';
        html += '              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Schließen</button>';
        html += '          </div>';
        html += '      </div>';
        html += '  </div>';
        html += '</div>';

        document.body.insertAdjacentHTML('beforeend', html);
        return document.getElementById('crmtime-calendar-details-modal');
    }

    function openCalendarDetailsModal(item) {
        if (!window.bootstrap || !bootstrap.Modal) {
            alert('Bootstrap Modal wurde auf der Seite nicht gefunden.');
            return;
        }

        var modalEl = ensureCalendarDetailsModal();
        var modalTitle = document.getElementById('crmtime-calendar-details-title');
        var modalBody = document.getElementById('crmtime-calendar-details-body');
        var modalFooter = document.getElementById('crmtime-calendar-details-footer');

        var statusLabel = translateStatus(item.status || '');
        if (parseInt(item.has_violation, 10) > 0) {
            statusLabel += ' ⚠';
        }

        modalTitle.textContent = 'Eintragsdetails #' + escapeHtml(item.id || '');

        var html = '<div class="table-responsive">';
        html += '<table class="table table-striped align-middle mb-0">';
        html += '<tbody>';
        html += '<tr><th style="width:220px;">ID</th><td>' + escapeHtml(item.id || '') + '</td></tr>';
        html += '<tr><th>Einsatz-ID</th><td>' + escapeHtml(item.assignment_id || '') + '</td></tr>';
        html += '<tr><th>Kunde</th><td>' + escapeHtml(item.customer_name || '') + '</td></tr>';
        html += '<tr><th>Arbeitsort</th><td>' + escapeHtml(item.workplace_name || '') + '</td></tr>';
        html += '<tr><th>Adresse</th><td>' + escapeHtml(item.workplace_address || '—') + '</td></tr>';
        html += '<tr><th>Satz</th><td>' + escapeHtml(item.rate || '0.00') + '</td></tr>';
        html += '<tr><th>Tarif</th><td>' + escapeHtml(item.tariff_text || getTariffText(item)) + '</td></tr>';
        html += '<tr><th>Datum</th><td>' + escapeHtml(item.work_date || '') + '</td></tr>';
        html += '<tr><th>Beginn</th><td>' + escapeHtml(item.start_time || '') + '</td></tr>';
        html += '<tr><th>Ende</th><td>' + escapeHtml(item.end_time || '') + '</td></tr>';
        html += '<tr><th>Status</th><td>' + escapeHtml(statusLabel) + '</td></tr>';
        html += '<tr><th>Kommentar</th><td>' + escapeHtml(item.admin_comment || '—') + '</td></tr>';
        html += '<tr><th>Signatur</th><td>' + (parseInt(item.is_signed, 10) === 1 ? 'Vorhanden' : 'Fehlt') + '</td></tr>';
        html += '<tr><th>Signiert von</th><td>' + escapeHtml(item.signed_name || '—') + '</td></tr>';
        html += '<tr><th>Signiert am</th><td>' + escapeHtml(item.signed_on || '—') + '</td></tr>';
        html += '<tr><th>Verstoß 11 Std.</th><td>' + (parseInt(item.has_violation, 10) > 0 ? 'Ja' : 'Nein') + '</td></tr>';

        if (item.signature_url) {
            html += '<tr><th>Bild</th><td><a href="' + escapeHtml(item.signature_url) + '" target="_blank" class="btn btn-sm btn-outline-success">Signatur öffnen</a></td></tr>';
        }

        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        modalBody.innerHTML = html;

        var footerHtml = '<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Schließen</button>';

        if (item.status === 'draft' || item.status === 'rejected') {
            footerHtml =
                '<button type="button" class="btn btn-outline-secondary" id="crmtime-calendar-sign-btn">Unterschrift</button>' +
                '<button type="button" class="btn btn-outline-primary" id="crmtime-calendar-edit-btn">Bearbeiten</button>' +
                '<button type="button" class="btn btn-primary" id="crmtime-calendar-submit-btn">Einreichen</button>' +
                footerHtml;
        }

        modalFooter.innerHTML = footerHtml;

        calendarDetailsModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

        var signBtn = document.getElementById('crmtime-calendar-sign-btn');
        var editBtn = document.getElementById('crmtime-calendar-edit-btn');
        var submitBtn = document.getElementById('crmtime-calendar-submit-btn');

        if (signBtn) {
            signBtn.addEventListener('click', function () {
                calendarDetailsModalInstance.hide();
                openSignatureModal(item, false);
            });
        }

        if (editBtn) {
            editBtn.addEventListener('click', function () {
                calendarDetailsModalInstance.hide();
                setEditMode(item);
            });
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                calendarDetailsModalInstance.hide();
                submitTimesheet(item.id);
            });
        }

        calendarDetailsModalInstance.show();
    }

    function initCalendar() {
        if (!window.crmTimeCabinetConfig.calendar_enabled) {
            return;
        }

        if (!calendarEl || typeof FullCalendar === 'undefined' || !FullCalendar.Calendar) {
            calendarResult.textContent = 'FullCalendar wurde auf der Seite nicht gefunden';
            return;
        }

        calendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'de',
            firstDay: 1,
            height: 'auto',
            events: [],
            eventClick: function (info) {
                var eventId = info.event.id;
                var item = getTimesheetById(eventId);

                if (!item && info.event.extendedProps) {
                    item = {
                        id: info.event.id,
                        assignment_id: info.event.extendedProps.assignment_id || '',
                        customer_name: info.event.extendedProps.customer_name || '',
                        workplace_name: info.event.extendedProps.workplace_name || '',
                        workplace_address: info.event.extendedProps.workplace_address || '',
                        rate: info.event.extendedProps.rate || '',
                        work_date: info.event.extendedProps.work_date || '',
                        start_time: info.event.extendedProps.start_time || '',
                        end_time: info.event.extendedProps.end_time || '',
                        status: info.event.extendedProps.status || '',
                        admin_comment: info.event.extendedProps.admin_comment || '',
                        is_night: info.event.extendedProps.is_night || 0,
                        is_sunday: info.event.extendedProps.is_sunday || 0,
                        is_holiday: info.event.extendedProps.is_holiday || 0,
                        tariff_text: info.event.extendedProps.tariff_text || '',
                        has_violation: info.event.extendedProps.has_violation || 0,
                        createdon: info.event.extendedProps.createdon || '',
                        is_signed: info.event.extendedProps.is_signed || 0,
                        signed_on: info.event.extendedProps.signed_on || '',
                        signed_name: info.event.extendedProps.signed_name || '',
                        signature_url: info.event.extendedProps.signature_url || ''
                    };
                }

                if (item) {
                    openCalendarDetailsModal(item);
                }
            }
        });

        calendarInstance.render();
    }

    function loadCalendar() {
        return request('timesheet/calendar')
            .then(function (data) {
                showDebug(calendarResult, data);

                if (data.success && data.object && data.object.results && calendarInstance) {
                    var events = (data.object.results || []).map(function (item) {
                        var colors = getEventColors(
                            item.extendedProps ? item.extendedProps.status : '',
                            item.extendedProps ? item.extendedProps.has_violation : 0
                        );

                        item.backgroundColor = colors.backgroundColor;
                        item.borderColor = colors.borderColor;

                        return item;
                    });

                    calendarInstance.removeAllEvents();
                    events.forEach(function (eventItem) {
                        calendarInstance.addEvent(eventItem);
                    });
                }
            })
            .catch(function (error) {
                showDebug(calendarResult, 'AJAX-Fehler: ' + error);
            });
    }

    function loadAssignments() {
        return request('assignment/mylist')
            .then(function (data) {
                showDebug(assignmentResult, data);

                if (data.success && data.object && data.object.results) {
                    renderAssignments(data.object.results);
                } else {
                    assignmentsList.innerHTML = '<p class="mb-0">Einsätze konnten nicht geladen werden.</p>';
                }
            })
            .catch(function (error) {
                showDebug(assignmentResult, 'AJAX-Fehler: ' + error);
            });
    }

    function loadTimesheets() {
        return request('timesheet/mylist')
            .then(function (data) {
                showDebug(timesheetResult, data);

                if (data.success && data.object && data.object.results) {
                    renderTimesheets(data.object.results);
                } else {
                    timesheetsList.innerHTML = '<p class="mb-0">Zeiteinträge konnten nicht geladen werden.</p>';
                }
            })
            .catch(function (error) {
                showDebug(timesheetResult, 'AJAX-Fehler: ' + error);
            });
    }

    function loadViolations() {
        return request('violation/mylist')
            .then(function (data) {
                showDebug(violationResult, data);

                if (data.success && data.object && data.object.results) {
                    renderViolations(data.object.results);
                } else {
                    violationsList.innerHTML = '<p class="mb-0">Verstöße konnten nicht geladen werden.</p>';
                }
            })
            .catch(function (error) {
                showDebug(violationResult, 'AJAX-Fehler: ' + error);
            });
    }

    function refreshAll() {
        loadAssignments();
        loadTimesheets().then(loadCalendar);
        loadViolations();
    }

    function createTimesheet() {
        request('timesheet/create', {
            assignment_id: assignmentSelect.value,
            work_date: workDate.value,
            start_time: startTime.value,
            end_time: endTime.value,
            is_night: isNight.checked ? 1 : 0,
            is_sunday: isSunday.checked ? 1 : 0,
            is_holiday: isHoliday.checked ? 1 : 0
        })
            .then(function (data) {
                showDebug(timesheetResult, data);
                if (data.success) {
                    resetForm();
                    refreshAll();
                }
            })
            .catch(function (error) {
                showDebug(timesheetResult, 'AJAX-Fehler: ' + error);
            });
    }

    function updateTimesheet() {
        request('timesheet/update', {
            id: timesheetIdField.value,
            assignment_id: assignmentSelect.value,
            work_date: workDate.value,
            start_time: startTime.value,
            end_time: endTime.value,
            is_night: isNight.checked ? 1 : 0,
            is_sunday: isSunday.checked ? 1 : 0,
            is_holiday: isHoliday.checked ? 1 : 0
        })
            .then(function (data) {
                showDebug(timesheetResult, data);
                if (data.success) {
                    resetForm();
                    refreshAll();
                }
            })
            .catch(function (error) {
                showDebug(timesheetResult, 'AJAX-Fehler: ' + error);
            });
    }

    function actuallySubmitTimesheet(id) {
        request('timesheet/submitwithsignature', { id: id })
            .then(function (data) {
                showDebug(timesheetResult, data);
                refreshAll();
            })
            .catch(function (error) {
                showDebug(timesheetResult, 'AJAX-Fehler: ' + error);
            });
    }

    function submitTimesheet(id) {
        var item = getTimesheetById(id);

        if (!item) {
            setTimesheetMessage('Eintrag wurde nicht gefunden.', 'warning');
            return;
        }

        if (parseInt(item.is_signed, 10) !== 1 || !item.signature_url) {
            setTimesheetMessage('Dieser Eintrag kann nicht ohne gespeicherte Unterschrift eingereicht werden.', 'warning');
            openSignatureModal(item, true);
            return;
        }

        actuallySubmitTimesheet(id);
    }

    function removeTimesheet(id) {
        if (!window.confirm('Diesen Zeiteintrag löschen?')) {
            return;
        }

        request('timesheet/remove', { id: id })
            .then(function (data) {
                showDebug(timesheetResult, data);
                if (data.success) {
                    if (String(timesheetIdField.value) === String(id)) {
                        resetForm();
                    }
                    refreshAll();
                }
            })
            .catch(function (error) {
                showDebug(timesheetResult, 'AJAX-Fehler: ' + error);
            });
    }

    function saveSignatureToServer() {
        var id = signatureTimesheetId.value;
        if (!id) {
            setSignatureMessage('warning', 'Eintrag-ID wurde nicht gefunden.');
            return;
        }

        if (signatureFileInput && signatureFileInput.files && signatureFileInput.files[0]) {
            request('timesheet/sign', {
                id: id,
                signature_type: 'upload',
                signature_upload: signatureFileInput.files[0]
            })
                .then(function (data) {
                    showDebug(timesheetResult, data);

                    if (!data.success) {
                        setSignatureMessage('warning', data.message || 'Signatur konnte nicht gespeichert werden.');
                        return;
                    }

                    showSignaturePreview(data.object && data.object.signature_url ? data.object.signature_url : '');
                    setSignatureMessage('success', data.message || 'Signatur wurde gespeichert.');
                    refreshAll();

                    if (signatureSubmitAfterSave) {
                        signatureSubmitAfterSave = false;
                        if (signatureModalInstance) {
                            signatureModalInstance.hide();
                        }
                        actuallySubmitTimesheet(id);
                    }
                })
                .catch(function (error) {
                    showDebug(timesheetResult, 'AJAX-Fehler: ' + error);
                    setSignatureMessage('warning', 'AJAX-Fehler: ' + error);
                });

            return;
        }

        if (signatureHasInk && signatureCanvas) {
            request('timesheet/sign', {
                id: id,
                signature_type: 'draw',
                signature_data: signatureCanvas.toDataURL('image/png')
            })
                .then(function (data) {
                    showDebug(timesheetResult, data);

                    if (!data.success) {
                        setSignatureMessage('warning', data.message || 'Signatur konnte nicht gespeichert werden.');
                        return;
                    }

                    showSignaturePreview(data.object && data.object.signature_url ? data.object.signature_url : '');
                    setSignatureMessage('success', data.message || 'Signatur wurde gespeichert.');
                    refreshAll();

                    if (signatureSubmitAfterSave) {
                        signatureSubmitAfterSave = false;
                        if (signatureModalInstance) {
                            signatureModalInstance.hide();
                        }
                        actuallySubmitTimesheet(id);
                    }
                })
                .catch(function (error) {
                    showDebug(timesheetResult, 'AJAX-Fehler: ' + error);
                    setSignatureMessage('warning', 'AJAX-Fehler: ' + error);
                });

            return;
        }

        setSignatureMessage('warning', 'Bitte zeichnen Sie zuerst eine Signatur oder laden Sie eine Datei hoch.');
    }

    if (signatureClearBtn) {
        signatureClearBtn.addEventListener('click', function () {
            clearSignatureCanvas();
            showSignaturePreview('');
            if (signatureFileInput) {
                signatureFileInput.value = '';
            }
            setSignatureMessage('', '');
        });
    }

    if (signatureFileInput) {
        signatureFileInput.addEventListener('change', function () {
            setSignatureMessage('', '');
            clearSignatureCanvas();

            if (!signatureFileInput.files || !signatureFileInput.files[0]) {
                return;
            }

            readFileAsDataUrl(signatureFileInput.files[0])
                .then(function (dataUrl) {
                    showSignaturePreview(dataUrl);
                })
                .catch(function (error) {
                    setSignatureMessage('warning', error);
                });
        });
    }

    if (signatureApplyBtn) {
        signatureApplyBtn.addEventListener('click', function () {
            saveSignatureToServer();
        });
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            if (timesheetIdField.value) {
                updateTimesheet();
            } else {
                createTimesheet();
            }
        });
    }

    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function () {
            resetForm();
        });
    }

    if (reloadBtn) {
        reloadBtn.addEventListener('click', function () {
            refreshAll();
        });
    }

    if (timesheetsList) {
        timesheetsList.addEventListener('click', function (e) {
            var btn = e.target.closest('.crmtime-web-action-btn');
            if (!btn) {
                return;
            }

            var action = btn.getAttribute('data-action');
            var id = btn.getAttribute('data-id');

            if (action === 'edit') {
                var editItem = getTimesheetById(id);
                if (editItem) {
                    setEditMode(editItem);
                }
                return;
            }

            if (action === 'sign') {
                var signItem = getTimesheetById(id);
                if (signItem) {
                    openSignatureModal(signItem, false);
                }
                return;
            }

            if (action === 'submit') {
                submitTimesheet(id);
                return;
            }

            if (action === 'remove') {
                removeTimesheet(id);
            }
        });
    }

    window.addEventListener('resize', function () {
        if (signatureModalEl && signatureModalEl.classList.contains('show')) {
            resizeSignatureCanvas();
        }
    });

    if (signatureModalEl) {
        signatureModalEl.addEventListener('shown.bs.modal', function () {
            resizeSignatureCanvas();
        });
    }

    resetForm();
    initSignatureCanvas();
    initCalendar();
    refreshAll();
});