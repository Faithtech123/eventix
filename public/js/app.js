// Small helpers shared across pages. No build step needed.

document.addEventListener('DOMContentLoaded', function () {
    setupNav();
    setupAlerts();
    setupConfirm();
    setupCheckoutTotal();
    setupTicketRows();
    setupTableFilter();
    setupAutosubmit();
});

function naira(amount) {
    if (amount === 0) return '₦0';
    return '₦' + amount.toLocaleString('en-NG', {
        maximumFractionDigits: 2
    });
}

function setupNav() {
    var toggle = document.querySelector('[data-nav-toggle]');
    var nav = document.querySelector('[data-nav]');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', function () {
        nav.classList.toggle('open');
    });
}

function setupAlerts() {
    document.querySelectorAll('[data-dismiss]').forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 300);
        }, 5000);
    });
}

function setupConfirm() {
    document.querySelectorAll('form[data-confirm]').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            if (!confirm(f.dataset.confirm)) e.preventDefault();
        });
    });
}

// Live total on the event page
function setupCheckoutTotal() {
    var form = document.querySelector('[data-checkout]');
    if (!form) return;

    var selects = form.querySelectorAll('select[data-price]');
    var totalEl = form.querySelector('[data-total]');
    var button = form.querySelector('[data-checkout-btn]');

    function update() {
        var total = 0;
        var count = 0;

        selects.forEach(function (s) {
            var qty = parseInt(s.value, 10) || 0;
            total += qty * parseFloat(s.dataset.price);
            count += qty;
        });

        totalEl.textContent = naira(total);

        if (button) {
            button.disabled = count === 0;
            button.textContent = count === 0
                ? 'Get tickets'
                : 'Get ' + count + ' ticket' + (count > 1 ? 's' : '');
        }
    }

    selects.forEach(function (s) {
        s.addEventListener('change', update);
    });

    form.addEventListener('submit', function () {
        if (button) {
            button.disabled = true;
            button.textContent = 'Processing...';
        }
    });

    update();
}

// Add / remove ticket type rows on the event form
function setupTicketRows() {
    var wrap = document.querySelector('[data-ticket-rows]');
    var tpl = document.querySelector('[data-ticket-template]');
    var addBtn = document.querySelector('[data-add-ticket]');
    if (!wrap || !tpl || !addBtn) return;

    var next = Date.now();

    addBtn.addEventListener('click', function () {
        var html = tpl.innerHTML.replace(/__INDEX__/g, 'n' + next++);
        wrap.insertAdjacentHTML('beforeend', html);
    });

    wrap.addEventListener('click', function (e) {
        if (!e.target.matches('[data-remove-ticket]')) return;

        var rows = wrap.querySelectorAll('[data-ticket-row]');
        if (rows.length === 1) {
            alert('An event needs at least one ticket type.');
            return;
        }

        e.target.closest('[data-ticket-row]').remove();
    });
}

function setupTableFilter() {
    document.querySelectorAll('[data-table-filter]').forEach(function (inp) {
        var table = document.querySelector(inp.dataset.tableFilter);
        if (!table) return;

        inp.addEventListener('input', function () {
            var term = inp.value.toLowerCase();

            table.querySelectorAll('tbody tr').forEach(function (tr) {
                var match = tr.textContent.toLowerCase().includes(term);
                tr.style.display = match ? '' : 'none';
            });
        });
    });
}

function setupAutosubmit() {
    document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
        el.addEventListener('change', function () {
            el.form.submit();
        });
    });
}
