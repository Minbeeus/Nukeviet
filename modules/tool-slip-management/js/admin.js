(function () {
    'use strict';
    console.log('Script admin.js loaded');

    // Helper: send POST request (fetch)
    function postForm(url, data) {
        let fetchOptions = {
            method: 'POST',
            credentials: 'include', // Gửi cookie (quan trọng cho session NukeViet)
        };

        // Kiểm tra nếu data là FormData (cho form submit)
        if (data instanceof FormData) {
            fetchOptions.body = data;
            // KHÔNG set 'Content-Type', trình duyệt sẽ tự làm với boundary đúng
        } else {
            // Nếu data là object (cho các action khác)
            var formBody = [];
            for (var prop in data) {
                var encodedKey = encodeURIComponent(prop);
                var encodedValue = encodeURIComponent(data[prop]);
                formBody.push(encodedKey + "=" + encodedValue);
            }
            fetchOptions.body = formBody.join("&");
            fetchOptions.headers = {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest' // Đánh dấu là AJAX
            };
        }

        return fetch(url, fetchOptions).then(function (res) {
            if (!res.ok) {
                console.error('Network response was not ok. Status:', res.status, res.statusText);
                return res.text().then(text => { // Cố gắng đọc lỗi từ server
                    throw new Error('Network error: ' + res.status + ' ' + res.statusText + ' | ' + text);
                });
            }
            return res.text(); // Trả về text để hàm gọi xử lý JSON
        });
    }


    // Generic modal show/hide helpers
    function showModalById(elId) {
        console.log('showModalById called for', elId);
        var el = document.getElementById(elId);
        console.log('Modal element found:', !!el);
        if (!el) return null;
        if (el.classList.contains('show')) {
            // Hide first if already shown, prevents issues with Bootstrap instances
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var existingModal = bootstrap.Modal.getInstance(el);
                    if (existingModal) existingModal.hide();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                     $(el).modal('hide');
                }
            } catch (e) { console.warn('Error hiding existing modal instance:', e); }
        }

        // Use a slight delay to ensure the DOM is ready after potential hide
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log('Using Bootstrap modal');
                // Ensure options allow reuse: { backdrop: false } might interfere, use default or specific needs
                var m = bootstrap.Modal.getOrCreateInstance(el);
                m.show();
                return m;
            }
            if (typeof $ !== 'undefined' && $.fn.modal) {
                console.log('Using jQuery modal');
                $(el).modal({ backdrop: 'static', keyboard: false, show: true }); // Use static backdrop & disable keyboard to prevent accidental close
                return null;
            }
            // fallback (less robust)
            console.log('Using fallback modal');
            el.classList.add('show');
            el.style.display = 'block';
            el.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
            return null;
        }, 150); // Delay may need adjustment
    }


    function hideModalById(elId, modalInstance) {
        var el = document.getElementById(elId);
        if (!el) return;
        if (modalInstance && typeof modalInstance.hide === 'function') {
            modalInstance.hide();
            return;
        }
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var m2 = bootstrap.Modal.getInstance(el);
                if (m2) m2.hide();
                // Ensure backdrop is removed if modal leaves one behind
                 const backdrop = document.querySelector('.modal-backdrop');
                 if (backdrop) backdrop.remove();
                return;
            }
        } catch (err) {}
        if (typeof $ !== 'undefined' && $.fn.modal) {
            $(el).modal('hide');
            return;
        }
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        var bd = document.getElementById('tsm-backdrop'); // Check if your fallback uses this ID
        if (bd) bd.parentNode.removeChild(bd);
    }

    // Expose functions globally
    window.hideModalById = hideModalById;

    // Submit edit form (Assuming for tools)
    window.submitEditForm = function() {
        var form = document.getElementById('edit-tool-form'); // Make sure this ID exists in your edit tool form HTML
        if (!form) return;
        var formData = new FormData(form);
        formData.append('submit', 'submit'); // Add submit flag if needed by backend

        // Client-side Validation (Example)
        if (!formData.get('tool_code')) { alert('Vui lòng nhập mã công cụ.'); return; }
        if (!formData.get('name')) { alert('Vui lòng nhập tên công cụ.'); return; }
        if (!formData.get('category_id') || formData.get('category_id') == 0) { alert('Vui lòng chọn danh mục.'); return; }

        if (!confirm('Bạn có chắc muốn cập nhật công cụ này?')) return;

        // Determine URL correctly. Avoid hardcoding if possible. Maybe get from form action or data attribute.
        var url = form.action || '/nukeviet/admin/index.php?language=vi&nv=tool-slip-management&op=tools'; // Fallback URL

        postForm(url, formData).then(function (text) {
            try {
                var json = JSON.parse(text);
                if (json && json.success) {
                    alert(json.message || 'Cập nhật thành công!');
                    hideModalById('tsmActionModal', null);
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + (json && json.message ? json.message : 'Lỗi không xác định'));
                }
            } catch (e) {
                 console.error("JSON Parse Error:", e);
                 console.log("Response Text:", text);
                alert('Lỗi: Phản hồi từ server không hợp lệ.');
            }
        }).catch(function (err) {
             console.error('Fetch error:', err);
            alert('Lỗi mạng: ' + err.message);
        });
    };


    // Submit slip form (maintenance/disposal)
    window.submitSlipForm = function() {
         var form = document.getElementById('slip-form'); // Ensure this ID exists
         if (!form) return;
         var formData = new FormData(form);

         if (!formData.get('reason')) { alert('Vui lòng nhập lý do.'); return; }

         var actionText = formData.get('action') === 'maintenance' ? 'bảo trì' : 'huỷ';
         if (!confirm('Bạn có chắc muốn tạo phiếu ' + actionText + ' cho công cụ này?')) return;

         postForm(form.action || window.location.href, formData).then(function (text) {
             try {
                 var json = JSON.parse(text);
                 if (json && json.success) {
                     alert(json.message || 'Thao tác thành công!');
                     hideModalById('tsmActionModal', null);
                     window.location.reload();
                 } else {
                     alert('Lỗi: ' + (json && json.message ? json.message : 'Lỗi không xác định'));
                 }
             } catch (e) {
                 console.error("JSON Parse Error:", e);
                 console.log("Response Text:", text);
                 alert('Lỗi: Phản hồi từ server không hợp lệ.');
             }
         }).catch(function (err) {
             console.error('Fetch error:', err);
             alert('Lỗi mạng: ' + err.message);
         });
    };


    // Quick status change via AJAX
    function initStatusChange() {
        document.querySelectorAll('a[data-change-status]').forEach(function (el) { // Select using data attribute for clarity
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var url = el.getAttribute('href');
                var toolRow = el.closest('tr');
                if (!url || !toolRow) return;

                if (!confirm('Xác nhận thay đổi trạng thái?')) return;

                // Extract params from URL for POST data
                var params = {};
                try {
                    var urlParams = new URLSearchParams(new URL(url).search);
                    urlParams.forEach((value, key) => {
                        params[key] = value;
                    });
                } catch(e) { // Fallback for browsers not supporting URLSearchParams or relative URLs
                     var q = url.split('?')[1] || '';
                     q.split('&').forEach(function (pair) {
                         if (!pair) return;
                         var parts = pair.split('=');
                         params[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || '');
                     });
                }


                postForm(url.split('?')[0], params).then(function (text) { // Send params as POST body
                    try {
                        var json = JSON.parse(text);
                        if (json && json.success) {
                            var badge = toolRow.querySelector('.tool-status-badge'); // Make sure your status element has this class
                            if (badge && json.updated_status_text) {
                                badge.textContent = json.updated_status_text;
                                badge.className = 'tool-status-badge badge bg-' + (json.updated_status_class || 'secondary'); // Ensure base class remains
                            }
                             // Optionally update action links if status change affects them
                        } else {
                            alert('Không thể thay đổi trạng thái: ' + (json.message || 'Lỗi không rõ'));
                        }
                    } catch (e) {
                        console.error("JSON Parse Error:", e);
                        console.log("Response Text:", text);
                        alert('Lỗi: Phản hồi từ server không hợp lệ khi đổi trạng thái.');
                    }
                }).catch(function (err) {
                    console.error('Fetch error:', err);
                    alert('Lỗi mạng khi đổi trạng thái: ' + err.message);
                });
            });
        });
    }

    // Debounce helper
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Search and filter handling (client-side for table #dataTable)
    function initSearch() {
        var dataTable = document.getElementById('dataTable'); // Ensure your table has this ID
        if (!dataTable) {
            // console.log('Table #dataTable not found for client-side search.');
            return;
        }
        var tableBody = dataTable.querySelector('tbody');
        if (!tableBody) return;

        var searchInput = document.getElementById('search-q');
        var categorySelect = document.getElementById('search-category');
        var statusSelect = document.getElementById('search-status');
        var rows = Array.from(tableBody.querySelectorAll('tr')); // Cache rows initially

        const filterRows = debounce(function() {
            var q = searchInput ? searchInput.value.trim().toLowerCase() : '';
            var category_id = categorySelect ? categorySelect.value : '0';
            var status = statusSelect ? statusSelect.value : ''; // Assuming value directly corresponds or needs mapping

            rows.forEach(function (row) {
                var cells = row.querySelectorAll('td');
                if (cells.length < 4) return; // Basic check

                var code = (cells[0]?.textContent || '').trim().toLowerCase();
                var name = (cells[1]?.textContent || '').trim().toLowerCase();
                // Assumes category/status text is directly comparable or needs data attributes for reliable filtering
                var categoryText = (cells[2]?.textContent || '').trim().toLowerCase();
                var statusBadge = cells[3]?.querySelector('.badge');
                var statusText = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';

                var show = true;

                if (q && !code.includes(q) && !name.includes(q)) {
                    show = false;
                }

                if (show && category_id !== '0') {
                    // This comparison might be unreliable if based on text only.
                    // Consider adding data-category-id to the row or cell.
                    let selectedCatText = categorySelect.options[categorySelect.selectedIndex]?.text.toLowerCase().replace('-- ', '').replace(' --', '');
                    if (!selectedCatText || !categoryText.includes(selectedCatText)) {
                         show = false;
                    }
                }

                if (show && status) {
                    // Similar reliability issue. Consider data-status attribute.
                    let selectedStatusText = statusSelect.options[statusSelect.selectedIndex]?.text.toLowerCase();
                     if (!selectedStatusText || !statusText.includes(selectedStatusText)) {
                         show = false;
                     }
                }

                row.style.display = show ? '' : 'none';
            });
        }, 300); // Debounce filtering

        if (searchInput) searchInput.addEventListener('input', filterRows);
        if (categorySelect) categorySelect.addEventListener('change', filterRows);
        if (statusSelect) statusSelect.addEventListener('change', filterRows);
    }

    // Add tool modal handling
    function initAddTool() {
        var btn = document.getElementById('btn-add-tool');
        if (!btn) return;
        btn.addEventListener('click', function () {
            // Construct URL carefully, avoid potential issues with existing params
            var baseUrl = window.location.href.split('?')[0];
            var params = new URLSearchParams(window.location.search);
            params.set('action', 'add');
            params.set('ajax', '1');
            // Remove params that might interfere if present (e.g., id for editing)
            params.delete('id');
            var url = baseUrl + '?' + params.toString();

            fetch(url, { credentials: 'include', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(html => {
                var modalEl = document.getElementById('tsmActionModal');
                if (!modalEl) return;
                var body = modalEl.querySelector('.modal-body');
                var title = modalEl.querySelector('.modal-title');
                if (body) body.innerHTML = html;
                if (title) title.textContent = 'Thêm công cụ mới'; // Consider getting title from lang file via data attr
                showModalById('tsmActionModal');
                // You might need to initialize JS specific to the loaded form here
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Lỗi khi tải form thêm công cụ: ' + err.message);
            });
        });
    }

    // =================================================================
    // LOGIC JS CHO FORM THÊM PHIẾU MƯỢN (Đã tách ra)
    // =================================================================
    function initAddSlipFormLogic() {
        console.log('TSM: Initializing Add Slip Form Logic');
        var form = document.getElementById('add-slip-form');
        if (!form) {
            console.error('TSM: add-slip-form not found! Cannot initialize logic.');
            return; // Dừng nếu không tìm thấy form
        }

        // Đọc các biến từ data attributes một cách an toàn
        var studentCheckUrl = form.dataset.studentCheckUrl;
        var langStudentCodeEmpty = form.dataset.langStudentCodeEmpty || 'Vui lòng nhập mã sinh viên.';
        var langSearching = form.dataset.langSearching || 'Đang tìm kiếm';
        var langStudent = form.dataset.langStudent || 'Sinh viên';
        var langRemove = form.dataset.langRemove || 'Xóa';

        // Lấy các element cần thiết
        var studentInput = document.getElementById("student-code-input");
        var studentCheckBtn = document.getElementById("btn-check-student");
        var studentInfoDiv = document.getElementById("student-info-display");
        var studentIdHidden = document.getElementById("student-id-hidden");
        var toolContainer = document.getElementById("tool-inputs-container");
        var addToolBtn = document.getElementById("btn-add-tool-row");

        // Kiểm tra tất cả element trước khi gắn listener
        if (!studentInput || !studentCheckBtn || !studentInfoDiv || !studentIdHidden || !toolContainer || !addToolBtn) {
             console.error('TSM: Missing one or more required elements in add slip form. Check IDs: student-code-input, btn-check-student, student-info-display, student-id-hidden, tool-inputs-container, btn-add-tool-row.');
             return; // Dừng nếu thiếu element
        }

        // --- Xử lý tra cứu Sinh Viên ---
        function checkStudent() {
            var code = studentInput.value.trim();
            studentIdHidden.value = "0"; // Reset ID ẩn trước khi tìm
            studentInfoDiv.className = ""; // Reset class style
            if (!code) {
                studentInfoDiv.innerHTML = langStudentCodeEmpty;
                studentInfoDiv.classList.add("error"); // Sử dụng classList
                studentInfoDiv.style.display = "block";
                return;
            }
            studentInfoDiv.innerHTML = langSearching + '...';
            studentInfoDiv.style.display = "block";

            fetch(studentCheckUrl + "&code=" + encodeURIComponent(code), {
                credentials: 'include',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Server error: ' + res.status);
                    return res.json();
                })
                .then(data => {
                    if (data.success && data.student) {
                        studentInfoDiv.innerHTML = `<strong>${langStudent}:</strong> ${data.student.full_name} (${data.student.student_code})`;
                        studentInfoDiv.classList.add("success");
                        studentIdHidden.value = data.student.id;
                    } else {
                        studentInfoDiv.innerHTML = data.message || 'Không tìm thấy sinh viên.';
                        studentInfoDiv.classList.add("error");
                    }
                })
                .catch(err => {
                    console.error('Fetch error during student check:', err);
                    studentInfoDiv.innerHTML = "Lỗi kết nối hoặc server: " + err.message;
                    studentInfoDiv.classList.add("error");
                });
        }
        studentCheckBtn.addEventListener("click", checkStudent);
        studentInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                checkStudent();
            }
        });

        // --- Xử lý thêm/xóa Công Cụ ---
        function updateToolOptions() {
            var selectedValues = new Set(); // Dùng Set để kiểm tra nhanh hơn
            var selects = toolContainer.querySelectorAll(".tool-select");
            selects.forEach(function(s) {
                if (s.value !== "0") {
                    selectedValues.add(s.value);
                }
            });

            selects.forEach(function(s) {
                var currentVal = s.value;
                Array.from(s.options).forEach(function(opt) {
                    // Disable nếu giá trị đã được chọn ở select khác
                    opt.disabled = selectedValues.has(opt.value) && opt.value !== currentVal;
                });
            });

             // Hiển thị/Ẩn nút xóa cho hàng đầu tiên
             const firstRow = toolContainer.querySelector('.tool-input-row');
             if (firstRow) {
                 const firstRemoveBtn = firstRow.querySelector('.btn-remove-tool');
                 if (firstRemoveBtn) {
                     firstRemoveBtn.style.display = selects.length > 1 ? '' : 'none';
                 }
             }
        }

        addToolBtn.addEventListener("click", function() {
            var firstRow = toolContainer.querySelector(".tool-input-row");
            if (!firstRow) return; // Không có hàng mẫu để clone

            var newRow = firstRow.cloneNode(true); // Clone sâu
            var newSelect = newRow.querySelector('.tool-select');
            var removeBtn = newRow.querySelector('.btn-remove-tool');

            if (newSelect) newSelect.value = "0"; // Reset select về mặc định
            if (removeBtn) removeBtn.style.display = ''; // Luôn hiện nút xóa cho hàng mới

            toolContainer.appendChild(newRow);
            updateToolOptions();
        });

        toolContainer.addEventListener("click", function(e) {
            var removeBtn = e.target.closest(".btn-remove-tool");
            if (removeBtn) {
                 // Chỉ xóa nếu có nhiều hơn 1 hàng
                 if (toolContainer.querySelectorAll('.tool-input-row').length > 1) {
                    removeBtn.closest('.tool-input-row').remove();
                    updateToolOptions();
                 }
            }
        });

        toolContainer.addEventListener("change", function(e) {
            if (e.target.classList.contains("tool-select")) {
                updateToolOptions();
            }
        });

        // Khởi chạy lần đầu để cập nhật option và ẩn nút xóa nếu chỉ có 1 hàng
        updateToolOptions();
        console.log('TSM: Add Slip Form Logic Initialized.');
    }
    // =================================================================
    // KẾT THÚC KHỐI LOGIC JS MỚI
    // =================================================================

    // Add slip modal handling (Sửa đổi để gọi initAddSlipFormLogic)
    function initAddSlip() {
        var btn = document.getElementById('btn-add-slip');
        if (!btn) {
             // console.log('Button #btn-add-slip not found.');
             return;
        }
        btn.addEventListener('click', function () {
            var baseUrl = window.location.href.split('?')[0];
            var params = new URLSearchParams(window.location.search);
            params.set('action', 'add');
            params.set('ajax', '1');
            params.delete('id'); // Xóa id nếu có
            var url = baseUrl + '?' + params.toString();

            console.log('Fetching Add Slip Form URL:', url);
            fetch(url, { credentials: 'include', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => {
                if (!res.ok) throw new Error(`Network response was not ok (${res.status})`);
                return res.text();
            })
            .then(html => {
                var modalEl = document.getElementById('tsmActionModal');
                if (!modalEl) { console.error('Modal #tsmActionModal not found!'); return; }
                var body = modalEl.querySelector('.modal-body');
                var title = modalEl.querySelector('.modal-title');
                if (body) {
                     body.innerHTML = html; // Chèn HTML form vào modal
                     console.log('Modal content updated.');
                } else { console.error('Modal body not found!'); }

                if (title) title.textContent = 'Thêm phiếu mượn'; // Lấy từ data-attribute sẽ tốt hơn

                showModalById('tsmActionModal'); // Hiển thị modal

                // Quan trọng: Gọi hàm khởi tạo logic SAU KHI HTML được chèn vào DOM
                initAddSlipFormLogic();

            }).catch(err => {
                console.error('Fetch error loading add slip form:', err);
                alert('Lỗi khi tải form thêm phiếu mượn: ' + err.message);
            });
        });
        console.log('Event listener attached to #btn-add-slip');
    }

    // Submit add tool form (Cần ID form cụ thể)
    window.submitAddToolForm = function() {
        var form = document.getElementById('add-tool-form'); // Đảm bảo form thêm công cụ có ID này
        if (!form) { alert('Lỗi: Không tìm thấy form thêm công cụ.'); return; }
        var formData = new FormData(form);
        formData.append('submit', 'submit'); // Thêm cờ submit nếu backend cần

        // Validation cơ bản
        if (!formData.get('tool_code')) { alert('Vui lòng nhập mã công cụ.'); return; }
        if (!formData.get('name')) { alert('Vui lòng nhập tên công cụ.'); return; }
        if (!formData.get('category_id') || formData.get('category_id') == 0) { alert('Vui lòng chọn danh mục.'); return; }

        if (!confirm('Bạn có chắc muốn thêm công cụ này?')) return;

        postForm(form.action || window.location.href, formData).then(text => {
            try {
                var json = JSON.parse(text);
                if (json && json.success) {
                    alert(json.message || 'Thêm thành công!');
                    hideModalById('tsmActionModal', null);
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + (json && json.message ? json.message : 'Lỗi không xác định từ server.'));
                }
            } catch (e) {
                console.error("JSON Parse Error:", e);
                console.log("Response Text:", text);
                alert('Lỗi: Phản hồi không hợp lệ từ server.');
            }
        }).catch(err => {
            console.error('Fetch error:', err);
            alert('Lỗi mạng khi thêm công cụ: ' + err.message);
        });
    };


    // Submit add slip form (Đã sửa)
    window.submitAddSlipForm = function() {
        var form = document.getElementById('add-slip-form');
        if (!form) { alert('Lỗi: Không tìm thấy form thêm phiếu mượn.'); return; }

        var formData = new FormData(form);

        // Validation Client-side
        var studentId = formData.get('student_id');
        if (!studentId || studentId == 0) {
            alert('Vui lòng tìm và xác nhận một sinh viên hợp lệ.');
            var studentInput = document.getElementById('student-code-input');
            if(studentInput) studentInput.focus();
            return;
        }
        // Lọc các tool_ids hợp lệ (khác "0")
        var toolIds = formData.getAll('tool_ids[]').filter(id => id && id !== "0");
         if (toolIds.length === 0) {
             alert('Vui lòng chọn ít nhất một công cụ.');
             // Focus vào select đầu tiên chưa chọn
             var firstInvalidSelect = Array.from(form.querySelectorAll('.tool-select')).find(s => s.value === '0');
             if(firstInvalidSelect) firstInvalidSelect.focus();
             return;
         }
        // Cập nhật lại tool_ids trong formData sau khi lọc
        formData.delete('tool_ids[]'); // Xóa hết key cũ
        toolIds.forEach(id => formData.append('tool_ids[]', id)); // Thêm lại các key hợp lệ

        if (!formData.get('due_date')) {
             alert('Vui lòng nhập ngày hẹn trả.');
             var dueDateInput = form.querySelector('input[name="due_date"]');
             if(dueDateInput) dueDateInput.focus();
            return;
        }

        if (!confirm('Bạn có chắc muốn thêm phiếu mượn này?')) {
            return;
        }

        // Submit dùng FormData
        postForm(form.action, formData).then(function (text) {
            try {
                var json = JSON.parse(text);
                if (json && json.success) {
                    alert(json.message || 'Thêm phiếu mượn thành công!');
                    hideModalById('tsmActionModal', null);
                    window.location.reload();
                } else {
                    alert('Lỗi khi lưu phiếu mượn: ' + (json && json.message ? json.message : 'Lỗi không xác định'));
                }
            } catch (e) {
                console.error("JSON Parse Error:", e);
                console.log("Response Text:", text);
                alert('Lỗi: Phản hồi từ server không hợp lệ sau khi lưu.');
            }
        }).catch(function (err) {
            console.error('Fetch error:', err);
            alert('Lỗi mạng khi lưu phiếu mượn: ' + err.message);
        });
    };

    // Accessibility helper
    function fixDropdownAria() {
        try {
            document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
                if (!menu.getAttribute('role')) menu.setAttribute('role', 'menu');
                menu.querySelectorAll('a, button, li').forEach(function (item) {
                     // Check if item already has a role or is purely decorative
                     if (!item.getAttribute('role') && !item.classList.contains('divider') && item.tagName !== 'HR') {
                         item.setAttribute('role', 'menuitem');
                     }
                 });
            });
        } catch (e) { console.warn('ARIA fixer error', e); }
    }

    // Change status via select (Cần kiểm tra lại cấu trúc HTML nếu dùng select)
    window.changeStatus = function(select, id) {
        var status = select.value;
        var data = {id: id, status: status, action: 'change_status'}; // Cần endpoint PHP xử lý action này

        postForm(window.location.href, data).then(text => { // URL cần endpoint đúng
            try {
                var json = JSON.parse(text);
                if (json && json.success) {
                    var badge = select.closest('tr')?.querySelector('.tool-status-badge');
                    if (badge && json.updated_status_text) {
                        badge.textContent = json.updated_status_text;
                        badge.className = 'tool-status-badge badge bg-' + (json.updated_status_class || 'secondary');
                    }
                } else {
                    alert('Không thể thay đổi trạng thái: ' + (json.message || 'Lỗi không rõ'));
                    select.value = select.dataset.originalStatus || ''; // Revert select on error
                }
            } catch (e) {
                console.error("JSON Parse Error:", e);
                console.log("Response Text:", text);
                 alert('Lỗi: Phản hồi không hợp lệ từ server khi đổi trạng thái.');
                 select.value = select.dataset.originalStatus || ''; // Revert select on error
            }
        }).catch(err => {
            console.error('Fetch error:', err);
            alert('Lỗi mạng khi đổi trạng thái: ' + err.message);
            select.value = select.dataset.originalStatus || ''; // Revert select on error
        });
        // Store original status in case of error
        if (!select.dataset.originalStatus) {
            select.dataset.originalStatus = select.value;
        }
    };


    // Tool search in borrowing form (Đã bị thay thế bởi logic trong initAddSlipFormLogic)
    // function initToolSearch() { ... }

    // Ajax action links: open returned HTML inside a generic modal
    function initAjaxActions() {
        document.querySelectorAll('a.action-ajax').forEach(function (el) { // Select specific links
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var url = el.getAttribute('href');
                var modalId = el.dataset.modalTarget || 'tsmActionModal'; // Allow specifying target modal
                var modalTitle = el.dataset.modalTitle || ''; // Allow specifying title

                if (!url) return;

                fetch(url, { credentials: 'include', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.text();
                })
                .then(html => {
                    var modalEl = document.getElementById(modalId);
                    if (!modalEl) { console.error(`Modal #${modalId} not found!`); return; }
                    var body = modalEl.querySelector('.modal-body');
                    var titleEl = modalEl.querySelector('.modal-title');

                    if (body) body.innerHTML = html;
                    if (titleEl) titleEl.textContent = modalTitle; // Use data-attribute title

                    showModalById(modalId);
                    // Add hook or event for JS initialization if needed after loading content
                    // Example: document.dispatchEvent(new CustomEvent('tsm:modalContentLoaded', { detail: { modalId: modalId } }));
                })
                .catch(err => {
                     console.error('Fetch error loading AJAX content:', err);
                    alert('Lỗi khi tải nội dung: ' + err.message);
                });
            });
        });
    }



    // Initialize when DOM ready. Use readyState check so script works even if loaded after event.
    function initAll() {
        try {
            console.log('Admin JS initAll called');
            initStatusChange();
            initSearch();
            initAddTool();
            initAddSlip(); // Sẽ gắn listener cho nút #btn-add-slip
            // initToolSearch(); // Không cần nữa vì logic đã chuyển vào initAddSlipFormLogic
            initAjaxActions();
            fixDropdownAria();
            console.log('Admin JS Initialized Successfully.');
        } catch (e) {
            console.error('Error initializing admin.js components:', e);
        }
    }

    // Khởi tạo ngay khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        // DOM đã sẵn sàng
        initAll();
    }
})();
