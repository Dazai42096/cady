(function () {
    const arToEn = {
        // Brand / general
        "كادي للمولدات": "CADY Generators",
        "كادي": "CADY",
        "الرئيسية": "Home",
        "من نحن": "About Us",
        "الخدمات": "Services",
        "تواصل معنا": "Contact Us",
        "طلب عرض سعر": "Request Quotation",
        "تسجيل الدخول": "Login",
        "تسجيل خروج": "Logout",
        "خروج": "Logout",
        "حفظ": "Save",
        "إلغاء": "Cancel",
        "رجوع": "Back",
        "عرض": "View",
        "تعديل": "Edit",
        "حذف": "Delete",
        "إضافة": "Add",
        "إنشاء": "Create",
        "بحث": "Search",
        "فلترة": "Filter",
        "الحالة": "Status",
        "الإجراءات": "Actions",
        "التفاصيل": "Details",
        "الاسم": "Name",
        "الاسم الكامل": "Full Name",
        "البريد الإلكتروني": "Email",
        "رقم الهاتف": "Phone Number",
        "كلمة السر": "Password",
        "تأكيد كلمة السر": "Confirm Password",
        "الدور": "Role",
        "ملاحظات": "Notes",
        "التاريخ": "Date",
        "الوقت": "Time",
        "العملة": "Currency",
        "السعر": "Price",
        "القيمة": "Value",
        "الإجمالي": "Total",
        "تحميل": "Download",
        "طباعة": "Print",
        "PDF": "PDF",

        // Dashboard / sidebar
        "لوحة الإحصائيات": "Dashboard",
        "لوحة الإحصائيات العامة": "General Dashboard",
        "الإجراءات السريعة": "Quick Actions",
        "إدارة العملاء": "Customers Management",
        "عملاء معلقون": "Pending Customers",
        "المولدات الكهربائية": "Electrical Generators",
        "عروض الأسعار": "Quotations",
        "عقود الصيانة": "Maintenance Contracts",
        "زيارات الخدمة الميدانية": "Field Service Visits",
        "طلبات الموقع العام": "Website Requests",
        "سجل العمليات": "Audit Log",
        "سجل العمليات (Audit)": "Audit Log",
        "التحكم بالتأجير": "Rental Control",
        "تقارير الخدمة": "Service Reports",
        "رسائل واتساب": "WhatsApp Messages",
        "إدارة الموظفين": "Employees Management",
        "مدير النظام": "System Admin",
        "أحدث العمليات وسجل التدقيق الفني": "Latest Operations and Technical Audit Log",
        "عرض جميع السجلات": "View All Logs",
        "المستخدم": "User",
        "العملية": "Action",
        "النوع/السجل": "Type / Record",
        "عنوان IP": "IP Address",
        "الوقت والتاريخ": "Date and Time",

        // Quick actions
        "إضافة عميل جديد": "Add New Customer",
        "إضافة مولد جديد": "Add New Generator",
        "إنشاء عرض سعر": "Create Quotation",
        "تنظيم عقد صيانة": "Create Maintenance Contract",
        "متابعة طلبات الربط مع العملاء": "Review Customer Link Requests",

        // Dashboard cards
        "العملاء النشطين": "Active Customers",
        "العملاء بانتظار التفعيل": "Pending Customers",
        "إجمالي المولدات": "Total Generators",
        "عقود الصيانة السارية": "Active Maintenance Contracts",
        "قيمة العقود النشطة": "Active Contract Value",
        "المولدات المتوفرة": "Available Generators",
        "إجمالي عروض الأسعار": "Total Quotations",
        "الزيارات القادمة المجدولة": "Upcoming Scheduled Visits",

        // Customers
        "العملاء": "Customers",
        "إضافة عميل": "Add Customer",
        "اسم العميل": "Customer Name",
        "اسم الشركة": "Company Name",
        "الشركة": "Company",
        "الشخص المسؤول": "Contact Person",
        "العنوان": "Address",
        "المدينة": "City",
        "الرقم الضريبي": "Tax Number",
        "عميل نشط": "Active Customer",
        "عميل معلق": "Pending Customer",
        "تفعيل العميل": "Activate Customer",
        "تعطيل العميل": "Deactivate Customer",

        // Employees
        "الموظفين": "Employees",
        "الموظفون": "Employees",
        "إضافة موظف جديد": "Add New Employee",
        "المدير هو المسؤول عن إضافة موظفي المبيعات والدعم الفني.": "Admin is responsible for adding Sales and Technical Support employees.",
        "المدير فقط يستطيع إنشاء حسابات موظفي المبيعات والدعم الفني.": "Only admin can create Sales and Technical Support accounts.",
        "الدور الوظيفي": "Employee Role",
        "مبيعات": "Sales",
        "الدعم الفني": "Technical Support",
        "إنشاء الحساب": "Create Account",
        "لا يوجد موظفين.": "No employees found.",

        // Generators
        "المولدات": "Generators",
        "المولد": "Generator",
        "إضافة مولد": "Add Generator",
        "إضافة المولد": "Add Generator",
        "إضافة مولد كهربائي جديد إلى سجل المولدات.": "Add a new generator to the generator registry.",
        "الرقم التسلسلي": "Serial Number",
        "الموديل": "Model",
        "الشركة المصنعة": "Brand",
        "القدرة KVA": "Capacity KVA",
        "الموقع": "Location",
        "متاح": "Available",
        "مؤجر": "Rented",
        "صيانة": "Maintenance",
        "غير فعال": "Inactive",

        // Rentals
        "التأجير": "Rentals",
        "إدارة التأجير": "Rental Management",
        "إنشاء تأجير": "Create Rental",
        "تعديل التأجير": "Edit Rental",
        "إنشاء عقد تأجير": "Create Rental",
        "رقم التأجير": "Rental Number",
        "اختر العميل": "Select Customer",
        "اختر المولد": "Select Generator",
        "تاريخ البداية": "Start Date",
        "تاريخ النهاية": "End Date",
        "السعر الشهري": "Monthly Rate",
        "قراءة العداد الابتدائية": "Initial Hour Meter",
        "قراءة العداد النهائية": "Final Hour Meter",
        "عدد الأيام": "Days Count",
        "المبلغ الإجمالي": "Total Amount",
        "تفعيل التأجير": "Activate Rental",
        "تمديد التأجير": "Extend Rental",
        "إنهاء التأجير": "Complete Rental",
        "إلغاء التأجير": "Cancel Rental",
        "لا توجد تأجيرات": "No rentals found",
        "Create Rental": "Create Rental",
        "Customer Name": "Customer Name",
        "Generator": "Generator",

        // Quotations
        "عرض سعر": "Quotation",
        "عرض السعر": "Quotation",
        "إضافة عرض سعر": "Add Quotation",
        "إنشاء عرض سعر جديد": "Create New Quotation",
        "رقم عرض السعر": "Quotation Number",
        "العميل": "Customer",
        "البنود": "Items",
        "الكمية": "Quantity",
        "سعر الوحدة": "Unit Price",
        "الضريبة": "Tax",
        "نسبة الضريبة": "Tax Rate",
        "المجموع قبل الضريبة": "Subtotal",
        "المجموع بعد الضريبة": "Total After Tax",
        "قبول": "Accept",
        "رفض": "Reject",
        "إرسال": "Send",

        // Contracts / visits
        "عقد صيانة": "Maintenance Contract",
        "عقد الصيانة": "Maintenance Contract",
        "إضافة عقد صيانة": "Add Maintenance Contract",
        "رقم العقد": "Contract Number",
        "تاريخ بداية العقد": "Contract Start Date",
        "تاريخ نهاية العقد": "Contract End Date",
        "قيمة العقد": "Contract Value",
        "زيارة": "Visit",
        "زيارات": "Visits",
        "زيارة ميدانية": "Field Visit",
        "زيارات الخدمة": "Service Visits",
        "الزيارات المجدولة": "Scheduled Visits",
        "تاريخ الزيارة": "Visit Date",
        "الفني": "Technician",
        "مكتملة": "Completed",
        "مجدولة": "Scheduled",
        "ملغاة": "Cancelled",

        // Service reports
        "تقرير خدمة": "Service Report",
        "تقارير الصيانة": "Service Reports",
        "إنشاء تقرير خدمة": "Create Service Report",
        "تقرير جديد": "New Report",
        "نوع التقرير": "Report Type",
        "تاريخ الخدمة": "Service Date",
        "اسم الفني": "Technician Name",
        "وصف العطل": "Fault Description",
        "التشخيص": "Diagnosis",
        "الأعمال الميكانيكية": "Mechanical Work",
        "الأعمال الكهربائية": "Electrical Work",
        "قطع الغيار": "Spare Parts",
        "ملاحظات الفني": "Technician Notes",
        "المتابعة المقترحة": "Recommended Follow Up",
        "تحميل PDF": "Download PDF",
        "إظهار في بوابة العميل": "Show in Customer Portal",
        "اعتماد": "Approve",
        "إرسال التقرير": "Submit Report",

        // WhatsApp
        "واتساب": "WhatsApp",
        "رسالة واتساب": "WhatsApp Message",
        "إنشاء رسالة واتساب": "Create WhatsApp Message",
        "رقم واتساب": "WhatsApp Number",
        "نص الرسالة": "Message Body",
        "فتح واتساب": "Open WhatsApp",
        "تم الإرسال": "Mark Sent",

        // Backups / compliance
        "Backups": "Backups",
        "Compliance": "Compliance",
        "النسخ الاحتياطي": "Backups",
        "إنشاء نسخة احتياطية": "Generate Backup",
        "تحميل النسخة": "Download Backup",
        "لوحة الامتثال": "Compliance Dashboard",
        "مكتمل": "Complete",
        "جزئي": "Partial",
        "مفقود": "Missing",

        // Auth / portal
        "بوابة العميل": "Customer Portal",
        "حسابي": "My Account",
        "ملفي الشخصي": "My Profile",
        "عقودي": "My Contracts",
        "عروض أسعاري": "My Quotations",
        "تأجيراتي": "My Rentals",
        "تقاريري": "My Reports",
        "تذكرني": "Remember Me",
        "نسيت كلمة السر؟": "Forgot Password?",
        "ليس لديك حساب؟": "Do not have an account?",
        "إنشاء حساب": "Register",
        "تسجيل": "Register"
    };

    const enToAr = {};
    Object.keys(arToEn).forEach((ar) => {
        enToAr[arToEn[ar]] = ar;
    });

    function getLocale() {
        return localStorage.getItem("cady_locale") || document.documentElement.lang || "ar";
    }

    function setLocale(locale) {
        localStorage.setItem("cady_locale", locale);
        document.documentElement.lang = locale;
        document.documentElement.dir = locale === "ar" ? "rtl" : "ltr";
        document.body.dir = locale === "ar" ? "rtl" : "ltr";
    }

    function preserveWhitespace(original, translated) {
        const start = original.match(/^\s*/)?.[0] || "";
        const end = original.match(/\s*$/)?.[0] || "";
        return start + translated + end;
    }

    function translateString(value, locale) {
        if (!value || !value.trim()) return value;

        const trimmed = value.trim();
        const map = locale === "en" ? arToEn : enToAr;

        if (map[trimmed]) {
            return preserveWhitespace(value, map[trimmed]);
        }

        let result = value;
        const keys = Object.keys(map).sort((a, b) => b.length - a.length);

        for (const key of keys) {
            if (key.length < 3) continue;
            result = result.split(key).join(map[key]);
        }

        return result;
    }

    function shouldSkipNode(node) {
        const parent = node.parentElement;
        if (!parent) return true;

        const tag = parent.tagName ? parent.tagName.toLowerCase() : "";
        return ["script", "style", "textarea", "code", "pre"].includes(tag);
    }

    function translateTextNodes(root, locale) {
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                return shouldSkipNode(node) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
            }
        });

        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        nodes.forEach((node) => {
            node.nodeValue = translateString(node.nodeValue, locale);
        });
    }

    function translateAttributes(locale) {
        const attrs = ["placeholder", "title", "aria-label", "value"];

        document.querySelectorAll("input, textarea, button, a, option").forEach((el) => {
            attrs.forEach((attr) => {
                if (!el.hasAttribute(attr)) return;

                if (attr === "value") {
                    const type = (el.getAttribute("type") || "").toLowerCase();
                    if (!["submit", "button", "reset"].includes(type)) return;
                }

                el.setAttribute(attr, translateString(el.getAttribute(attr), locale));
            });
        });
    }

    function translatePage() {
        const locale = getLocale();
        setLocale(locale);
        translateTextNodes(document.body, locale);
        translateAttributes(locale);
        updateButton();
    }

    function updateButton() {
        const button = document.getElementById("cady-language-switch-button");
        if (!button) return;

        const locale = getLocale();
        button.innerHTML = locale === "ar" ? "🌐 English" : "🌐 العربية";
        button.setAttribute("aria-label", locale === "ar" ? "Switch to English" : "Switch to Arabic");
    }

    function addButton() {
        if (document.getElementById("cady-language-switcher")) return;

        const wrapper = document.createElement("div");
        wrapper.id = "cady-language-switcher";
        wrapper.style.position = "fixed";
        wrapper.style.bottom = "22px";
        wrapper.style.left = "22px";
        wrapper.style.zIndex = "999999";

        const button = document.createElement("button");
        button.id = "cady-language-switch-button";
        button.type = "button";
        button.style.display = "inline-flex";
        button.style.alignItems = "center";
        button.style.gap = "8px";
        button.style.background = "#10b981";
        button.style.color = "white";
        button.style.padding = "10px 16px";
        button.style.borderRadius = "999px";
        button.style.fontWeight = "800";
        button.style.border = "0";
        button.style.cursor = "pointer";
        button.style.boxShadow = "0 10px 25px rgba(0,0,0,.18)";

        button.addEventListener("click", function () {
            const next = getLocale() === "ar" ? "en" : "ar";
            setLocale(next);
            location.reload();
        });

        wrapper.appendChild(button);
        document.body.appendChild(wrapper);
        updateButton();
    }

    document.addEventListener("DOMContentLoaded", function () {
        setLocale(getLocale());
        addButton();
        translatePage();
    });
})();