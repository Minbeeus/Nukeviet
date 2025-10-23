(function () {
    'use strict';

    // Helper: send POST request (fetch) with CSRF token if available
    function postForm(url, data) {
        var formBody = [];
        for (var prop in data) {
            var encodedKey = encodeURIComponent(prop);
            var encodedValue = encodeURIComponent(data[prop]);
            formBody.push(encodedKey + "=" + encodedValue);
        }
        formBody = formBody.join("&");
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            credentials: 'same-origin',
            body: formBody
        }).then(function (res) {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        });
    }

    // Quick status change via AJAX
    function initStatusChange() {
        document.querySelectorAll('[data-change-status]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var url = el.getAttribute('href');
                var toolRow = el.closest('tr');
                if (!url || !toolRow) return;

                // Optionally show a confirmation
                if (!confirm('Xác nhận thay đổi trạng thái?')) return;

                // Convert URL params into an object
                var params = {};
                var q = url.split('?')[1] || '';
                q.split('&').forEach(function (pair) {
                    if (!pair) return;
                    var parts = pair.split('=');
                    params[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || '');
                });

                // Post to a small endpoint (we'll reuse the same URL but as POST)
                postForm(url, params).then(function (json) {
                    if (json && json.success) {
                        // Update the status badge in-row if server returned updated_status
                        var badge = toolRow.querySelector('.tool-status-badge');
                        if (badge && json.updated_status_text) {
                            badge.textContent = json.updated_status_text;
                            badge.className = 'badge bg-' + (json.updated_status_class || 'secondary');
                        }
                    } else {
                        alert('Không thể thay đổi trạng thái');
                    }
                }).catch(function (err) {
                    alert('Lỗi mạng: ' + err.message);
                });
            });
        });
    }

    // Search form handling (move from inline script)
    function initSearch() {
        var btn = document.getElementById('search-btn') || document.querySelector('button[type="submit"]') || document.querySelector('.btn-search');
        if (!btn) {
            console.log('Search button not found');
            return;
        }
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var q = document.getElementById('search-q') || document.querySelector('input[name="q"]');
            q = q ? q.value : '';
            var category_id = document.getElementById('search-category') || document.querySelector('select[name="category_id"]');
            category_id = category_id ? category_id.value : 0;
            var status = document.getElementById('search-status') || document.querySelector('select[name="status"]');
            status = status ? status.value : '';
            var base = btn.getAttribute('data-base') || (window.location.pathname + window.location.search);
            // base already contains the core NV params when set from template
            var url = base;
            if (q) url += '&q=' + encodeURIComponent(q);
            if (category_id > 0) url += '&category_id=' + encodeURIComponent(category_id);
            if (status) url += '&status=' + encodeURIComponent(status);
            window.location.href = url;
        });
    }

    // Initialize when DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Admin JS loaded');
        initStatusChange();
        initSearch();
    });
})();
