(function () {
    'use strict';

    // Helper: send POST request (XMLHttpRequest for better compatibility)
    function postForm(url, data) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.withCredentials = true; // Gửi cookie
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(xhr.responseText);
                } else {
                    reject(new Error('Network error: ' + xhr.status + ' ' + xhr.statusText + ' | ' + xhr.responseText));
                }
            };

            xhr.onerror = function() {
                reject(new Error('Network error'));
            };

            // Kiểm tra nếu data là FormData
            if (data instanceof FormData) {
                xhr.send(data);
            } else {
                // Nếu data là object
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                var formBody = [];
                for (var prop in data) {
                    var encodedKey = encodeURIComponent(prop);
                    var encodedValue = encodeURIComponent(data[prop]);
                    formBody.push(encodedKey + "=" + encodedValue);
                }
                xhr.send(formBody.join("&"));
            }
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

         var submitUrl = '/nukeviet/admin/index.php?nv=tool-slip-management&op=tools';
          console.log('Slip form submitting to URL:', submitUrl);

          postForm(submitUrl, formData).then(function (text) {
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

            fetch(studentCheckUrl + "&ajax=1&code=" + encodeURIComponent(code), {
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Server error: ' + res.status);
                    return res.json();
                })
                .then(data => {
                    if (data.success && data.student) {
                        studentInfoDiv.innerHTML = `<strong>${langStudent}:</strong> ${data.student.full_name} (${data.student.student_code})<br>
                        <strong>Lớp:</strong> ${data.student.class || 'N/A'}<br>
                        <strong>Số điện thoại:</strong> <input type="text" id="student-phone-input" value="${data.student.phone_number || ''}" placeholder="Nhập số điện thoại">`;
                        studentInfoDiv.classList.add("success");
                        studentIdHidden.value = data.student.id;
                        // Set hidden class if needed
                        var classHidden = document.getElementById('student-class-hidden');
                        if (classHidden) classHidden.value = data.student.class || '';
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
            var url;
            if (typeof TSM_ADD_SLIP_URL !== 'undefined' && TSM_ADD_SLIP_URL) {
                url = TSM_ADD_SLIP_URL;
            } else {
                // Fallback nếu biến global không tồn tại
                console.warn('TSM_ADD_SLIP_URL is not defined. Using fallback URL logic (may be incorrect).');
                var baseUrl = window.location.href.split('?')[0];
                var params = new URLSearchParams(window.location.search);
                params.set('action', 'add');
                params.set('ajax', '1');
                params.delete('id'); // Xóa id nếu có
                url = baseUrl + '?' + params.toString();
            }

            console.log('Fetching Add Slip Form URL:', url);
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.withCredentials = true;
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var html = xhr.responseText;
                    var modalEl = document.getElementById('tsmActionModal');
                    if (!modalEl) { console.error('Modal #tsmActionModal not found!'); return; }
                    var body = modalEl.querySelector('.modal-body');
                    var title = modalEl.querySelector('.modal-title');
                    if (body) {
                         body.innerHTML = html;
                         console.log('Modal content updated.');
                    } else { console.error('Modal body not found!'); }
                    if (title) title.textContent = 'Thêm phiếu mượn';
                    showModalById('tsmActionModal');
                    initAddSlipFormLogic();
                } else {
                    console.error('XHR error:', xhr.status, xhr.statusText);
                    alert('Lỗi khi tải form thêm phiếu mượn: ' + xhr.status + ' ' + xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('XHR network error');
                alert('Lỗi mạng khi tải form thêm phiếu mượn');
            };
            xhr.send();
        });
        console.log('Event listener attached to #btn-add-slip');
    }

    // Submit edit tool form
    window.submitEditForm = function() {
        var form = document.getElementById('edit-tool-form');
        if (!form) { alert('Lỗi: Không tìm thấy form sửa công cụ.'); return; }
        var formData = new FormData(form);
        formData.append('submit', 'submit');

        // Validation cơ bản
        if (!formData.get('tool_code')) { alert('Vui lòng nhập mã công cụ.'); return; }
        if (!formData.get('name')) { alert('Vui lòng nhập tên công cụ.'); return; }
        if (!formData.get('category_id') || formData.get('category_id') == 0) { alert('Vui lòng chọn danh mục.'); return; }

        if (!confirm('Bạn có chắc muốn cập nhật công cụ này?')) return;

        var submitUrl = '/nukeviet/admin/index.php?nv=tool-slip-management&op=tools';
        console.log('Edit form submitting to URL:', submitUrl);
        console.log('Edit form data:', Object.fromEntries(formData));

        postForm(submitUrl, formData).then(text => {
        try {
        var json = JSON.parse(text);
        if (json && json.success) {
        alert(json.message || 'Cập nhật thành công!');
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
            alert('Lỗi mạng khi cập nhật công cụ: ' + err.message);
        });
    };

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

        var submitUrl = '/nukeviet/admin/index.php?nv=tool-slip-management&op=tools';
        console.log('Add form submitting to URL:', submitUrl);

        postForm(submitUrl, formData).then(text => {
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

        var url = form.getAttribute('action'); // Use getAttribute to avoid conflict with input name="action"
        var formData = new FormData(form);

        // Validation Client-side
        var studentId = formData.get('student_id');
        if (!studentId || studentId == 0) {
            alert('Vui lòng tìm và xác nhận một sinh viên hợp lệ.');
            var studentInput = document.getElementById('student-code-input');
            if(studentInput) studentInput.focus();
            return;
        }
        // Get phone from input
        var phoneInput = document.getElementById('student-phone-input');
        if (phoneInput) {
            formData.append('student_phone', phoneInput.value.trim());
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
        postForm(url, formData).then(function (text) {
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


    // Tool search in borrowing form - filter select options based on search input
    function initToolSearch() {
        var searchInput = document.getElementById('tool-search');
        var toolSelect = document.getElementById('tool-select');

        if (!searchInput || !toolSelect) {
            // Only log if we're actually on a page that might need this (borrowing related)
            if (window.location.href.indexOf('borrowing') > -1) {
                console.log('Tool search elements not found for client-side filtering.');
            }
            return;
        }

        function filterTools() {
            var query = searchInput.value.toLowerCase().trim();
            var options = toolSelect.querySelectorAll('option');

            options.forEach(function(option) {
                if (option.value === '') return; // Skip placeholder
                var text = option.textContent.toLowerCase();
                var show = text.includes(query);
                option.style.display = show ? '' : 'none';
            });

            // Reset selection if filtered options don't include selected ones
            var selectedOptions = Array.from(toolSelect.selectedOptions);
            selectedOptions.forEach(function(opt) {
                if (opt.style.display === 'none') {
                    opt.selected = false;
                }
            });
        }

        searchInput.addEventListener('input', debounce(filterTools, 300));
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterTools();
            }
        });
    }

    // Real-time form validation for borrowing form
    function initFormValidation() {
        var form = document.querySelector('form[action=""]'); // Borrowing form
        if (!form) return;

        var studentSelect = form.querySelector('select[name="student_id"]');
        var borrowDateInput = form.querySelector('input[name="borrow_date"]');
        var dueDateInput = form.querySelector('input[name="due_date"]');
        var toolSelect = form.querySelector('select[name="tool_ids[]"]');

        function validateField(field, condition, message) {
            var feedback = field.parentNode.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                field.parentNode.appendChild(feedback);
            }

            if (!condition) {
                field.classList.add('is-invalid');
                feedback.textContent = message;
                return false;
            } else {
                field.classList.remove('is-invalid');
                feedback.textContent = '';
                return true;
            }
        }

        function validateDates() {
            if (!borrowDateInput.value || !dueDateInput.value) return;
            var borrowDate = new Date(borrowDateInput.value);
            var dueDate = new Date(dueDateInput.value);
            validateField(dueDateInput, dueDate >= borrowDate, 'Ngày hẹn trả phải sau hoặc bằng ngày mượn.');
        }

        if (studentSelect) {
            studentSelect.addEventListener('change', function() {
                validateField(studentSelect, this.value !== '0', 'Vui lòng chọn sinh viên.');
            });
        }

        if (borrowDateInput) {
            borrowDateInput.addEventListener('change', validateDates);
        }

        if (dueDateInput) {
            dueDateInput.addEventListener('change', validateDates);
        }

        if (toolSelect) {
            toolSelect.addEventListener('change', function() {
                validateField(toolSelect, this.selectedOptions.length > 0, 'Vui lòng chọn ít nhất một công cụ.');
            });
        }

        // Validate on form submit
        form.addEventListener('submit', function(e) {
            var isValid = true;
            if (studentSelect) isValid &= validateField(studentSelect, studentSelect.value !== '0', 'Vui lòng chọn sinh viên.');
            if (borrowDateInput) isValid &= validateField(borrowDateInput, borrowDateInput.value, 'Vui lòng nhập ngày mượn.');
            if (dueDateInput) {
                isValid &= validateField(dueDateInput, dueDateInput.value, 'Vui lòng nhập ngày hẹn trả.');
                validateDates();
            }
            if (toolSelect) isValid &= validateField(toolSelect, toolSelect.selectedOptions.length > 0, 'Vui lòng chọn ít nhất một công cụ.');

            if (!isValid) {
                e.preventDefault();
                // Scroll to first invalid field
                var firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    // Ajax action links: open returned HTML inside a generic modal
    function initAjaxActions() {
        document.querySelectorAll('a.action-ajax').forEach(function (el) { // Select specific links
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var url = el.getAttribute('href');
                var modalId = el.dataset.modalTarget || 'tsmActionModal'; // Allow specifying target modal
                var modalTitle = el.dataset.modalTitle || ''; // Allow specifying title

                if (!url) return;

                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.withCredentials = true;
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        var html = xhr.responseText;
                        var modalEl = document.getElementById(modalId);
                        if (!modalEl) { console.error(`Modal #${modalId} not found!`); return; }
                        var body = modalEl.querySelector('.modal-body');
                        var titleEl = modalEl.querySelector('.modal-title');

                        if (body) body.innerHTML = html;
                        if (titleEl) titleEl.textContent = modalTitle;

                        showModalById(modalId);
                    } else {
                        console.error('XHR error:', xhr.status, xhr.statusText);
                        alert('Lỗi khi tải nội dung: ' + xhr.status + ' ' + xhr.statusText);
                    }
                };
                xhr.onerror = function() {
                    console.error('XHR network error');
                    alert('Lỗi mạng khi tải nội dung');
                };
                xhr.send();
            });
        });
    }



    

    // Real-time search for borrowing list
    function initBorrowingSearch() {
        var searchInput = document.getElementById('search-input');
        var table = document.getElementById('borrowing-table');

        if (!searchInput || !table) return;

        var tbody = table.querySelector('tbody');
        if (!tbody) return;

        var rows = Array.from(tbody.querySelectorAll('tr'));

        function filterRows() {
            var query = searchInput.value.toLowerCase().trim();

            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td');
                if (cells.length < 4) return;

                var slipId = (cells[0]?.textContent || '').toLowerCase();
                var studentName = (cells[1]?.textContent || '').toLowerCase();
                var status = (cells[5]?.textContent || '').toLowerCase();

                var match = !query ||
                    slipId.includes(query) ||
                    studentName.includes(query) ||
                    status.includes(query);

                row.style.display = match ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', debounce(filterRows, 300));
    }

    // Initialize when DOM ready. Use readyState check so script works even if loaded after event.
    function initAll() {
        try {
        initStatusChange();
        initSearch();
        initAddTool();
        initAddSlip();
        initToolSearch();
        initFormValidation();
        initBorrowingSearch();
        initAjaxActions();
        fixDropdownAria();
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
