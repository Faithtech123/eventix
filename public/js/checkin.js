// Gate check-in: verify by typed code or by scanning the QR.

(function () {
    var form = document.querySelector('[data-checkin-form]');
    if (!form) return;

    var input = form.querySelector('input[name="code"]');
    var result = document.querySelector('[data-result]');
    var countEl = document.querySelector('[data-count]');
    var scanBtn = document.querySelector('[data-start-scan]');
    var token = document.querySelector('meta[name="csrf-token"]').content;

    var scanner = null;
    var busy = false;

    function esc(text) {
        var d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function show(data) {
        var labels = {
            ok: 'Valid ticket',
            used: 'Already used',
            invalid: 'Invalid'
        };

        var html = '<strong>' + labels[data.status] + '</strong>';
        html += '<p>' + esc(data.message) + '</p>';

        if (data.ticket) {
            html += '<p class="small">' + esc(data.ticket.holder)
                + ' &middot; ' + esc(data.ticket.type)
                + ' &middot; <span class="mono">' + esc(data.ticket.code)
                + '</span></p>';
        }

        result.className = 'checkin-result result-' + data.status;
        result.innerHTML = html;
        result.hidden = false;

        if (data.status === 'ok' && countEl) {
            countEl.textContent = parseInt(countEl.textContent, 10) + 1;
        }

        if (navigator.vibrate) {
            navigator.vibrate(data.status === 'ok' ? 80 : [80, 60, 80]);
        }
    }

    function verify(code) {
        if (busy || !code) return;
        busy = true;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ code: code })
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(show)
            .catch(function () {
                show({
                    status: 'invalid',
                    message: 'Could not reach the server. Try again.'
                });
            })
            .finally(function () {
                // short pause so the same QR isn't read twice
                setTimeout(function () { busy = false; }, 1500);
            });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        verify(input.value.trim());
        input.value = '';
        input.focus();
    });

    scanBtn.addEventListener('click', function () {
        if (typeof Html5Qrcode === 'undefined') {
            alert('Scanner failed to load. Type the code instead.');
            return;
        }

        var reader = document.getElementById('reader');

        if (scanner) {
            scanner.stop().then(function () {
                reader.hidden = true;
                scanner = null;
                scanBtn.textContent = 'Scan QR with camera';
            });
            return;
        }

        reader.hidden = false;
        scanner = new Html5Qrcode('reader');

        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 240 },
            function (text) { verify(text); },
            function () { /* no QR in frame, ignore */ }
        ).then(function () {
            scanBtn.textContent = 'Stop camera';
        }).catch(function () {
            reader.hidden = true;
            scanner = null;
            alert('Camera not available. Allow camera access or type the code.');
        });
    });
})();
