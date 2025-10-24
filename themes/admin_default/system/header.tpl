<!DOCTYPE html>
<html lang="{LANG.Content_Language|default:{NV_LANG_DATA}}">
    <head>
        <title>{NV_SITE_TITLE}</title>
        <meta name="description" content="{SITE_DESCRIPTION}">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="author" content="{NV_SITE_COPYRIGHT}">
        <meta name="generator" content="{NV_SITE_NAME}">
        <meta name="robots" content="noindex, nofollow">
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="{SITE_FAVICON}">
        <link rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/css/bootstrap.min.css">
        <link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/css/font-awesome.min.css">
        <link rel="stylesheet" href="{NV_BASE_SITEURL}themes/{NV_ADMIN_THEME}/css/style.css">
        <!-- BEGIN: css_module -->
        <link rel="stylesheet" href="{NV_CSS_MODULE_THEME}" type="text/css">
        <!-- END: css_module -->

        <script type="text/javascript">
            var  nv_base_siteurl = '{NV_BASE_SITEURL}',
                 nv_lang_data = '{NV_LANG_DATA}',
                 nv_lang_interface = '{NV_LANG_INTERFACE}',
                 nv_name_variable = '{NV_NAME_VARIABLE}',
                 nv_fc_variable = '{NV_OP_VARIABLE}',
                 nv_lang_variable = '{NV_LANG_VARIABLE}',
                 nv_module_name = '{MODULE_NAME}',
                 nv_my_ofs = {NV_SITE_TIMEZONE_OFFSET},
                 nv_my_abbr = '{NV_CURRENTTIME}',
                 nv_cookie_prefix = '{NV_COOKIE_PREFIX}',
                 nv_check_pass_mstime = '{NV_CHECK_PASS_MSTIME}',
                 nv_safemode = {NV_SAFEMODE},
                 nv_area_admin = 1,
                 XSSsanitize = {NV_XSS_SANITIZE},
                 nv_whitelisted_tags = {NV_WHITELISTED_TAGS},
                 nv_whitelisted_attr = {NV_WHITELISTED_ATTR};
        </script>
        <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/{NV_LANG_INTERFACE}.js"></script>
        <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/global.js"></script>
        <!-- BEGIN: XSSsanitize -->
        <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/DOMPurify/purify{PURIFY_VERSION}.js"></script>
        <!-- END: XSSsanitize -->
        <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/admin.js"></script>

        <!-- BEGIN: module_js -->
        <script type="text/javascript" src="{NV_JS_MODULE}"></script>
        <!-- END: module_js -->

        <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=EmulateIE8; IE=EDGE" />
        <![endif]-->

        <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/stickytableheaders/jquery.stickytableheaders.min.js"></script>
        <script type="text/javascript">
            // Accessibility runtime fixes for admin theme
            document.addEventListener('DOMContentLoaded', function () {
                try {
                    // Remove empty title attributes which trigger accessibility warnings
                    document.querySelectorAll('[title=""]').forEach(function (el) { el.removeAttribute('title'); });

                    // Ensure dropdown toggles expose aria attributes
                    document.querySelectorAll('[data-toggle="dropdown"], .dropdown-toggle').forEach(function (t) {
                        if (!t.hasAttribute('aria-haspopup')) t.setAttribute('aria-haspopup', 'true');
                        if (!t.hasAttribute('aria-expanded')) t.setAttribute('aria-expanded', 'false');
                    });

                    // Ensure dropdown menus have role=menu and children role=menuitem when not present
                    document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
                        if (!menu.hasAttribute('role')) menu.setAttribute('role', 'menu');
                        menu.querySelectorAll('a, button, li').forEach(function (it) {
                            if (!it.hasAttribute('role')) it.setAttribute('role', 'menuitem');
                        });
                    });

                    // Ensure any element that declares menuitem/menuitemradio/menuitemcheckbox has a parent role=menu (or menubar)
                    ['menuitem','menuitemradio','menuitemcheckbox'].forEach(function (roleName) {
                        document.querySelectorAll('[role="' + roleName + '"]').forEach(function (el) {
                            var p = el.parentElement;
                            var found = false;
                            while (p) {
                                var r = p.getAttribute && p.getAttribute('role');
                                if (r === 'menu' || r === 'menubar') { found = true; break; }
                                p = p.parentElement;
                            }
                            if (!found) {
                                // assign role=menu to the immediate parent if safe
                                var parent = el.parentElement;
                                if (parent && !parent.getAttribute('role')) parent.setAttribute('role', 'menu');
                            }
                        });
                    });

                    // Ensure elements with role=group are inside a menubar (assign menubar to nearest parent if missing)
                    document.querySelectorAll('[role="group"]').forEach(function (g) {
                        var p = g.parentElement;
                        var found = false;
                        while (p) {
                            var r = p.getAttribute && p.getAttribute('role');
                            if (r === 'menubar') { found = true; break; }
                            p = p.parentElement;
                        }
                        if (!found) {
                            var parent = g.parentElement;
                            if (parent && !parent.getAttribute('role')) parent.setAttribute('role', 'menubar');
                        }
                    });

                    // For icon-only links/buttons without visible text, add aria-label or visually-hidden fallback
                    document.querySelectorAll('a, button').forEach(function (el) {
                        var txt = (el.textContent || '').trim();
                        if (!txt) {
                            var label = el.getAttribute('aria-label') || el.getAttribute('title') || el.getAttribute('data-original-title') || el.getAttribute('data-bs-original-title');
                            if (label) el.setAttribute('aria-label', label);
                            else if (!el.hasAttribute('aria-label')) el.setAttribute('aria-label', 'Action');

                            // Add a visually-hidden span for screen readers if none exists
                            if (!el.querySelector('.visually-hidden')) {
                                var span = document.createElement('span');
                                span.className = 'visually-hidden';
                                span.textContent = label || 'Action';
                                el.appendChild(span);
                            }
                        }
                    });
                } catch (e) {
                    // Fail silently to avoid breaking admin UI
                    if (window.console && console.warn) console.warn('Admin ARIA fixer error', e);
                }
            });
        </script>
    </head>
    <body>
