document.addEventListener('DOMContentLoaded', function () {
    function normalizeText(value) {
        return String(value || '').replace(/\s+/g, ' ').trim().toLowerCase();
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function injectEnhancementStyles() {
        if (document.getElementById('crmtime-cabinet-enhancement-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'crmtime-cabinet-enhancement-styles';
        style.textContent = '' +
            '.crmtime-cabinet__hero{position:relative;overflow:hidden;border-radius:1.45rem;padding:1.65rem 1.6rem;background:linear-gradient(135deg, rgba(28,41,96,0.98) 0%, rgba(55,84,196,0.96) 52%, rgba(40,175,156,0.92) 100%);box-shadow:0 24px 55px rgba(10,18,44,0.28);color:#fff;}' +
            '.crmtime-cabinet__hero::before{content:"";position:absolute;inset:auto -40px -70px auto;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.08);filter:blur(2px);}' +
            '.crmtime-cabinet__hero::after{content:"";position:absolute;top:-60px;right:18%;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.09);}' +
            '.crmtime-cabinet__hero-inner{position:relative;z-index:1;display:grid;grid-template-columns:minmax(0,1.5fr) minmax(260px,0.9fr);gap:1.25rem;align-items:center;}' +
            '.crmtime-cabinet__hero-eyebrow{display:inline-flex;align-items:center;gap:.45rem;padding:.38rem .72rem;border-radius:999px;background:rgba(255,255,255,0.14);backdrop-filter:blur(10px);font-size:.78rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;margin-bottom:.9rem;}' +
            '.crmtime-cabinet__hero-title{margin:0;font-size:clamp(1.6rem,2.2vw,2.45rem);line-height:1.05;font-weight:800;letter-spacing:-.04em;color:#fff;}' +
            '.crmtime-cabinet__hero-text{margin:.8rem 0 0;font-size:1rem;line-height:1.65;color:rgba(243,247,255,.88);max-width:640px;}' +
            '.crmtime-cabinet__hero-actions{display:flex;flex-wrap:wrap;gap:.7rem;margin-top:1.2rem;}' +
            '.crmtime-cabinet__hero-link{display:inline-flex;align-items:center;gap:.45rem;padding:.72rem 1rem;border-radius:999px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.16);color:#fff;text-decoration:none;font-weight:700;transition:transform .2s ease,background-color .2s ease;}' +
            '.crmtime-cabinet__hero-link:hover,.crmtime-cabinet__hero-link:focus{color:#fff;text-decoration:none;background:rgba(255,255,255,.2);transform:translateY(-1px);}' +
            '.crmtime-cabinet__hero-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem;}' +
            '.crmtime-cabinet__hero-stat{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.14);border-radius:1.2rem;padding:1rem .95rem;backdrop-filter:blur(10px);}' +
            '.crmtime-cabinet__hero-stat-label{font-size:.78rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:rgba(238,243,255,.72);margin-bottom:.35rem;}' +
            '.crmtime-cabinet__hero-stat-value{font-size:1.55rem;font-weight:800;letter-spacing:-.04em;color:#fff;line-height:1;}' +
            '.crmtime-cabinet__hero-stat-text{margin-top:.35rem;font-size:.84rem;color:rgba(242,246,255,.82);}' +
            '.crmtime-cabinet__sidebar .card-body{position:relative;}' +
            '.crmtime-cabinet__nav .nav-link{padding-left:1rem;}' +
            '.crmtime-cabinet__nav .nav-link::before{content:attr(data-icon);display:inline-flex;align-items:center;justify-content:center;width:1.9rem;height:1.9rem;border-radius:.8rem;background:rgba(255,255,255,.12);box-shadow:none;color:#fff;font-size:1rem;font-weight:700;}' +
            '.crmtime-cabinet__nav .nav-link:hover::before,.crmtime-cabinet__nav .nav-link:focus::before{background:rgba(255,255,255,.2);box-shadow:none;}' +
            '.crmtime-cabinet__panel{position:relative;}' +
            '.crmtime-cabinet__panel .card-body{position:relative;}' +
            '.crmtime-cabinet__panel .card-body::before{content:"";position:absolute;left:1.5rem;right:1.5rem;top:0;height:4px;border-radius:999px;background:linear-gradient(90deg, rgba(64,104,255,.95) 0%, rgba(94,122,255,.82) 45%, rgba(43,196,171,.8) 100%);}' +
            '.crmtime-cabinet__section h3.h4{display:flex;align-items:center;gap:.65rem;}' +
            '.crmtime-cabinet__section h3.h4::before{display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.8rem;background:#eef3ff;color:#345cf4;font-size:1rem;line-height:1;}' +
            '#crmtime-section-assignments h3.h4::before{content:"⌘";}' +
            '#crmtime-section-calendar h3.h4::before{content:"◷";}' +
            '#crmtime-section-form h3.h4::before{content:"✎";}' +
            '#crmtime-section-timesheets h3.h4::before{content:"☑";}' +
            '#crmtime-section-violations h3.h4::before{content:"⚠";}' +
            '.crmtime-status-badge{display:inline-flex;align-items:center;gap:.38rem;border-radius:999px;padding:.42rem .75rem;font-size:.78rem;font-weight:800;letter-spacing:.02em;line-height:1;border:1px solid transparent;white-space:nowrap;}' +
            '.crmtime-status-badge::before{content:"";width:.48rem;height:.48rem;border-radius:50%;background:currentColor;opacity:.75;}' +
            '.crmtime-status-badge--draft{background:#eef2f7;border-color:#d7deea;color:#5c6b80;}' +
            '.crmtime-status-badge--submitted{background:#e8f0ff;border-color:#c7d8ff;color:#2f63db;}' +
            '.crmtime-status-badge--approved{background:#e9faf2;border-color:#c7efd8;color:#198754;}' +
            '.crmtime-status-badge--rejected{background:#fff0f1;border-color:#f7cfd5;color:#cf3648;}' +
            '.crmtime-status-warning{display:inline-flex;align-items:center;margin-left:.45rem;padding:.32rem .55rem;border-radius:999px;background:#fff3df;border:1px solid #ffd89f;color:#a96700;font-size:.74rem;font-weight:800;line-height:1;vertical-align:middle;}' +
            '.crmtime-status-wrap{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;}' +
            '@media (max-width: 991.98px){.crmtime-cabinet__hero-inner{grid-template-columns:1fr;}.crmtime-cabinet__hero-stats{grid-template-columns:repeat(4,minmax(0,1fr));}}' +
            '@media (max-width: 767.98px){.crmtime-cabinet__hero{padding:1.25rem 1rem;}.crmtime-cabinet__hero-actions{flex-direction:column;}.crmtime-cabinet__hero-link{justify-content:center;}.crmtime-cabinet__hero-stats{grid-template-columns:repeat(2,minmax(0,1fr));}.crmtime-cabinet__section h3.h4{align-items:flex-start;}.crmtime-cabinet__panel .card-body::before{left:1rem;right:1rem;}}';
        document.head.appendChild(style);
    }

    function hideMoneyInTable(table) {
        if (!table) {
            return;
        }

        var hiddenIndexes = [];
        var headRow = table.querySelector('thead tr');

        if (headRow) {
            Array.prototype.forEach.call(headRow.children, function (cell, index) {
                var text = normalizeText(cell.textContent);
                if (text === 'satz' || text === 'betrag') {
                    hiddenIndexes.push(index);
                    cell.remove();
                }
            });
        }

        if (hiddenIndexes.length) {
            Array.prototype.forEach.call(table.querySelectorAll('tbody tr'), function (row) {
                hiddenIndexes.slice().sort(function (a, b) { return b - a; }).forEach(function (index) {
                    if (row.children[index]) {
                        row.children[index].remove();
                    }
                });
            });
        }

        Array.prototype.forEach.call(table.querySelectorAll('tbody tr'), function (row) {
            var th = row.querySelector('th');
            if (!th) {
                return;
            }

            var text = normalizeText(th.textContent);
            if (text === 'satz' || text === 'betrag') {
                row.remove();
            }
        });
    }

    function hideMoneyInContainer(root) {
        if (!root) {
            return;
        }

        Array.prototype.forEach.call(root.querySelectorAll('table'), function (table) {
            hideMoneyInTable(table);
        });
    }

    function decoratePanels() {
        Array.prototype.forEach.call(document.querySelectorAll('.crmtime-cabinet__section > .card'), function (card) {
            card.classList.add('crmtime-cabinet__panel');
        });
    }

    function decorateNav() {
        var nav = document.querySelector('.crmtime-cabinet__nav');
        if (!nav) {
            return;
        }

        var icons = {
            '#crmtime-section-assignments': '⌘',
            '#crmtime-section-calendar': '◷',
            '#crmtime-section-form': '✎',
            '#crmtime-section-timesheets': '☑',
            '#crmtime-section-violations': '⚠'
        };

        Array.prototype.forEach.call(nav.querySelectorAll('.nav-link'), function (link) {
            var href = link.getAttribute('href') || '';
            if (icons[href]) {
                link.setAttribute('data-icon', icons[href]);
            }
        });
    }

    function countBodyRows(containerId) {
        var container = document.getElementById(containerId);
        if (!container) {
            return 0;
        }
        var rows = container.querySelectorAll('tbody tr');
        return rows ? rows.length : 0;
    }

    function ensureHeroBlock() {
        var content = document.querySelector('.crmtime-cabinet__content');
        if (!content || document.getElementById('crmtime-cabinet-hero')) {
            return;
        }

        var fullname = window.crmTimeCabinetConfig && window.crmTimeCabinetConfig.fullname ? String(window.crmTimeCabinetConfig.fullname) : '';
        var username = window.crmTimeCabinetConfig && window.crmTimeCabinetConfig.username ? String(window.crmTimeCabinetConfig.username) : '';
        var displayName = fullname || username || 'Kollege';

        var html = '' +
            '<section class="crmtime-cabinet__hero" id="crmtime-cabinet-hero">' +
                '<div class="crmtime-cabinet__hero-inner">' +
                    '<div>' +
                        '<div class="crmtime-cabinet__hero-eyebrow">Mitarbeiterbereich</div>' +
                        '<h1 class="crmtime-cabinet__hero-title">Willkommen zurück, ' + escapeHtml(displayName) + '</h1>' +
                        '<p class="crmtime-cabinet__hero-text">Hier sehen Sie Ihre Einsätze, pflegen Ihre Zeiterfassung, senden unterschriebene Einträge zur Prüfung und behalten wichtige Änderungen im Blick.</p>' +
                        '<div class="crmtime-cabinet__hero-actions">' +
                            '<a class="crmtime-cabinet__hero-link" href="#crmtime-section-form">Zeiterfassung öffnen</a>' +
                            '<a class="crmtime-cabinet__hero-link" href="#crmtime-section-calendar">Kalender ansehen</a>' +
                        '</div>' +
                    '</div>' +
                    '<div class="crmtime-cabinet__hero-stats">' +
                        '<div class="crmtime-cabinet__hero-stat"><div class="crmtime-cabinet__hero-stat-label">Einsätze</div><div class="crmtime-cabinet__hero-stat-value" id="crmtime-hero-stat-assignments">0</div><div class="crmtime-cabinet__hero-stat-text">aktive Zuordnungen</div></div>' +
                        '<div class="crmtime-cabinet__hero-stat"><div class="crmtime-cabinet__hero-stat-label">Einträge</div><div class="crmtime-cabinet__hero-stat-value" id="crmtime-hero-stat-timesheets">0</div><div class="crmtime-cabinet__hero-stat-text">gespeicherte Zeiten</div></div>' +
                        '<div class="crmtime-cabinet__hero-stat"><div class="crmtime-cabinet__hero-stat-label">Prüfung</div><div class="crmtime-cabinet__hero-stat-value" id="crmtime-hero-stat-signatures">0</div><div class="crmtime-cabinet__hero-stat-text">mit Unterschrift</div></div>' +
                        '<div class="crmtime-cabinet__hero-stat"><div class="crmtime-cabinet__hero-stat-label">Warnungen</div><div class="crmtime-cabinet__hero-stat-value" id="crmtime-hero-stat-violations">0</div><div class="crmtime-cabinet__hero-stat-text">11-Stunden-Regel</div></div>' +
                    '</div>' +
                '</div>' +
            '</section>';

        content.insertAdjacentHTML('afterbegin', html);
    }

    function updateHeroStats() {
        var assignmentsValue = document.getElementById('crmtime-hero-stat-assignments');
        var timesheetsValue = document.getElementById('crmtime-hero-stat-timesheets');
        var signaturesValue = document.getElementById('crmtime-hero-stat-signatures');
        var violationsValue = document.getElementById('crmtime-hero-stat-violations');

        if (!assignmentsValue || !timesheetsValue || !signaturesValue || !violationsValue) {
            return;
        }

        assignmentsValue.textContent = String(countBodyRows('crmtime-web-assignments-list'));
        timesheetsValue.textContent = String(countBodyRows('crmtime-web-timesheets-list'));
        violationsValue.textContent = String(countBodyRows('crmtime-web-violations-list'));

        var signedCount = document.querySelectorAll('#crmtime-web-timesheets-list a.btn-outline-success').length;
        signaturesValue.textContent = String(signedCount);
    }

    function buildStatusBadgeMarkup(text) {
        var raw = String(text || '').trim();
        var normalized = normalizeText(raw.replace('⚠', '').replace('warnung', ''));
        var warning = raw.indexOf('⚠') !== -1;
        var label = raw;
        var kind = '';

        if (normalized === 'entwurf' || normalized === 'draft') {
            kind = 'draft';
            label = 'Entwurf';
        } else if (normalized === 'eingereicht' || normalized === 'submitted') {
            kind = 'submitted';
            label = 'Eingereicht';
        } else if (normalized === 'genehmigt' || normalized === 'approved') {
            kind = 'approved';
            label = 'Genehmigt';
        } else if (normalized === 'abgelehnt' || normalized === 'rejected') {
            kind = 'rejected';
            label = 'Abgelehnt';
        }

        if (!kind) {
            return '';
        }

        return '<span class="crmtime-status-wrap"><span class="crmtime-status-badge crmtime-status-badge--' + kind + '">' + escapeHtml(label) + '</span>' + (warning ? '<span class="crmtime-status-warning">⚠ Prüfung</span>' : '') + '</span>';
    }

    function enhanceStatusCells(root) {
        if (!root) {
            return;
        }

        Array.prototype.forEach.call(root.querySelectorAll('td'), function (cell) {
            if (cell.querySelector('.crmtime-status-badge')) {
                return;
            }

            var row = cell.parentElement;
            var headerText = '';
            if (row) {
                var th = row.querySelector('th');
                headerText = th ? normalizeText(th.textContent) : '';
            }

            var text = cell.textContent || '';
            var badge = buildStatusBadgeMarkup(text);

            if (headerText === 'status' && badge) {
                cell.innerHTML = badge;
                return;
            }

            if (!headerText && badge) {
                var maybeOnlyStatus = normalizeText(text.replace('⚠', ''));
                if (maybeOnlyStatus === 'entwurf' || maybeOnlyStatus === 'eingereicht' || maybeOnlyStatus === 'genehmigt' || maybeOnlyStatus === 'abgelehnt' || maybeOnlyStatus === 'draft' || maybeOnlyStatus === 'submitted' || maybeOnlyStatus === 'approved' || maybeOnlyStatus === 'rejected') {
                    cell.innerHTML = badge;
                }
            }
        });
    }

    function run() {
        injectEnhancementStyles();
        decorateNav();
        decoratePanels();
        ensureHeroBlock();
        hideMoneyInContainer(document.getElementById('crmtime-cabinet'));
        hideMoneyInContainer(document.getElementById('crmtime-calendar-details-modal'));
        hideMoneyInContainer(document.getElementById('crmtime-signature-modal'));
        enhanceStatusCells(document.getElementById('crmtime-cabinet'));
        enhanceStatusCells(document.getElementById('crmtime-calendar-details-modal'));
        enhanceStatusCells(document.getElementById('crmtime-signature-modal'));
        updateHeroStats();
    }

    run();

    var observer = new MutationObserver(function () {
        run();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
