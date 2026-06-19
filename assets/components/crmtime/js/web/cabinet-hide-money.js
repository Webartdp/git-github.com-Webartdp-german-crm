document.addEventListener('DOMContentLoaded', function () {
    function normalizeText(value) {
        return String(value || '').replace(/\s+/g, ' ').trim().toLowerCase();
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

    function run() {
        hideMoneyInContainer(document.getElementById('crmtime-cabinet'));
        hideMoneyInContainer(document.getElementById('crmtime-calendar-details-modal'));
        hideMoneyInContainer(document.getElementById('crmtime-signature-modal'));
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
