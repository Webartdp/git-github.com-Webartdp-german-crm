<?php
/** @var modX $modx */

$corePath = $modx->getOption(
    'crmtime.core_path',
    null,
    $modx->getOption('core_path') . 'components/crmtime/'
);

require_once $corePath . 'model/crmtime/crmtime.class.php';

if (!isset($modx->crmtime) || !($modx->crmtime instanceof CrmTime)) {
    $modx->crmtime = new CrmTime($modx);
}

$assetsUrl = $modx->crmtime->config['assetsUrl'];

$loginUrl = $modx->getOption('loginUrl', $scriptProperties, '/login/');
$title = $modx->getOption('title', $scriptProperties, 'Mitarbeiterbereich');

$fcJsUrl = $modx->getOption(
    'fcJsUrl',
    $scriptProperties,
    $assetsUrl . 'vendor/fullcalendar/index.global.min.js'
);

$fcJsPath = MODX_BASE_PATH . ltrim(parse_url($fcJsUrl, PHP_URL_PATH), '/');
$hasCalendarAssets = file_exists($fcJsPath);

if (!$modx->user || !$modx->user->isAuthenticated('web')) {
    return '
        <div class="container-fluid py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h2 class="h3 mb-3">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>
                            <p class="mb-3">Um den Mitarbeiterbereich zu öffnen, müssen Sie sich auf der Website anmelden.</p>
                            <a class="btn btn-primary" href="' . htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') . '">Anmelden</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
}

$userId = (int)$modx->user->get('id');
$profile = $modx->getObject('modUserProfile', array(
    'internalKey' => $userId,
));

$fullname = $profile ? trim((string)$profile->get('fullname')) : '';
$username = (string)$modx->user->get('username');

if ($hasCalendarAssets) {
    $modx->regClientStartupScript($fcJsUrl);
}

$modx->regClientCSS($assetsUrl . 'css/web/cabinet.css');
$modx->regClientStartupScript(
    '<script>window.crmTimeCabinetConfig = ' . json_encode(array(
        'connector_url' => $assetsUrl . 'web/connector.php',
        'user_id' => $userId,
        'username' => $username,
        'fullname' => $fullname,
        'calendar_enabled' => $hasCalendarAssets ? 1 : 0,
    )) . ';</script>'
);
$modx->regClientStartupScript($assetsUrl . 'js/web/cabinet.js');
$modx->regClientStartupScript($assetsUrl . 'js/web/cabinet-hide-money.js');

$calendarNotice = '';
if (!$hasCalendarAssets) {
    $calendarNotice = '
        <div class="alert alert-warning mb-3">
            Kalender ist vorübergehend nicht verbunden. Fügen Sie die FullCalendar-Datei hinzu:
            <code>assets/components/crmtime/vendor/fullcalendar/index.global.min.js</code>
        </div>
    ';
}

$userLabel = $fullname !== '' ? $fullname . ' (' . $username . ')' : $username;

return '
<div class="crmtime-cabinet container-fluid py-4" id="crmtime-cabinet">
    <div class="row g-4">
        <div class="col-lg-3">
            <aside class="crmtime-cabinet__sidebar card shadow-sm border-0">
                <div class="card-body">
                    <div class="mb-4">
                        <h2 class="h4 mb-2">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>
                        <p class="text-muted small mb-0">
                            Benutzer: <strong>' . htmlspecialchars($userLabel, ENT_QUOTES, 'UTF-8') . '</strong>
                        </p>
                    </div>

                    <nav class="nav flex-column crmtime-cabinet__nav">
                        <a class="nav-link" href="#crmtime-section-assignments">Meine Einsätze</a>
                        <a class="nav-link" href="#crmtime-section-calendar">Mein Kalender</a>
                        <a class="nav-link" href="#crmtime-section-form">Zeiterfassung</a>
                        <a class="nav-link" href="#crmtime-section-timesheets">Meine Zeiteinträge</a>
                        <a class="nav-link" href="#crmtime-section-violations">11-Stunden-Verstöße</a>
                    </nav>
                </div>
            </aside>
        </div>

        <div class="col-lg-9">
            <div class="crmtime-cabinet__content">

                <section id="crmtime-section-assignments" class="crmtime-cabinet__section mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <h3 class="h4 mb-0">Meine Einsätze</h3>
                            </div>

                            <div id="crmtime-web-assignment-result" class="d-none"></div>
                            <div id="crmtime-web-assignments-list"></div>
                        </div>
                    </div>
                </section>

                <section id="crmtime-section-calendar" class="crmtime-cabinet__section mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h4 mb-3">Mein Kalender</h3>
                            ' . $calendarNotice . '
                            <div id="crmtime-web-calendar-result" class="d-none"></div>
                            <div id="crmtime-web-calendar"></div>
                        </div>
                    </div>
                </section>

                <section id="crmtime-section-form" class="crmtime-cabinet__section mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h3 id="crmtime-web-form-title" class="h4 mb-4">Zeiteintrag hinzufügen</h3>

                            <input type="hidden" id="crmtime-web-timesheet-id" value="">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="crmtime-web-assignment-id" class="form-label">Einsatz</label>
                                    <select id="crmtime-web-assignment-id" class="form-select">
                                        <option value="">Einsatz auswählen</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="crmtime-web-work-date" class="form-label">Datum</label>
                                    <input type="date" id="crmtime-web-work-date" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label for="crmtime-web-start-time" class="form-label">Startzeit</label>
                                    <input type="time" id="crmtime-web-start-time" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label for="crmtime-web-end-time" class="form-label">Endzeit</label>
                                    <input type="time" id="crmtime-web-end-time" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="crmtime-web-is-night">
                                        <label class="form-check-label" for="crmtime-web-is-night">Nachtzuschlag</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="crmtime-web-is-sunday">
                                        <label class="form-check-label" for="crmtime-web-is-sunday">Sonntag / frei</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="crmtime-web-is-holiday">
                                        <label class="form-check-label" for="crmtime-web-is-holiday">Feiertag</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="button" id="crmtime-web-save-btn" class="btn btn-primary">Als Entwurf speichern</button>
                                <button type="button" id="crmtime-web-cancel-edit-btn" class="btn btn-outline-secondary" style="display:none;">Bearbeitung abbrechen</button>
                                <button type="button" id="crmtime-web-reload-btn" class="btn btn-outline-secondary">Aktualisieren</button>
                            </div>

                            <div id="crmtime-web-timesheet-result" class="d-none"></div>
                        </div>
                    </div>
                </section>

                <section id="crmtime-section-timesheets" class="crmtime-cabinet__section mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h4 mb-3">Meine Zeiteinträge</h3>
                            <div id="crmtime-web-timesheets-list"></div>
                        </div>
                    </div>
                </section>

                <section id="crmtime-section-violations" class="crmtime-cabinet__section">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h4 mb-3">Meine 11-Stunden-Verstöße</h3>
                            <div id="crmtime-web-violation-result" class="d-none"></div>
                            <div id="crmtime-web-violations-list"></div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="crmtime-signature-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crmtime-signature-modal-title">Unterschrift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="crmtime-signature-timesheet-id" value="">

                <div id="crmtime-signature-message" class="alert d-none"></div>

                <div id="crmtime-signature-summary" class="mb-3"></div>

                <div class="mb-3">
                    <label class="form-label">Unterschrift zeichnen</label>
                    <div class="crmtime-signature-canvas-wrap">
                        <canvas id="crmtime-signature-canvas"></canvas>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="crmtime-signature-clear-btn">Leeren</button>
                </div>

                <hr>

                <div class="mb-3">
                    <label for="crmtime-signature-file" class="form-label">Oder Bilddatei hochladen</label>
                    <input type="file" id="crmtime-signature-file" class="form-control" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                </div>

                <div id="crmtime-signature-preview-wrap" style="display:none;">
                    <label class="form-label">Vorschau</label>
                    <div>
                        <img id="crmtime-signature-preview" src="" alt="Signatur-Vorschau" class="img-fluid border rounded p-2 bg-white">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="crmtime-signature-apply-btn">Signatur speichern</button>
            </div>
        </div>
    </div>
</div>
';
