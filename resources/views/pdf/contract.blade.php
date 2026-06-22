<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>عقد صيانة رقم {{ $contract->ref_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'sans-serif';
            direction: rtl;
            text-align: right;
            color: #333;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #0b192c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #0b192c;
            float: right;
        }
        .logo span {
            color: #00d26a;
        }
        .company-details {
            float: left;
            text-align: left;
            font-size: 9px;
            color: #666;
            line-height: 1.4;
        }
        .clear {
            clear: both;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #0b192c;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 6px;
        }
        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
            float: right;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            min-height: 110px;
            box-sizing: border-box;
        }
        .info-box-left {
            width: 48%;
            float: left;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            min-height: 110px;
            box-sizing: border-box;
        }
        .info-title {
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
            margin-bottom: 6px;
            color: #0b192c;
            font-size: 12px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td, .details-table th {
            padding: 6px 10px;
            border: 1px solid #ddd;
        }
        .details-table th {
            background-color: #0b192c;
            color: white;
            text-align: right;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            width: 45%;
            float: right;
            text-align: center;
        }
        .signature-box-left {
            width: 45%;
            float: left;
            text-align: center;
        }
        .signature-line {
            margin-top: 40px;
            border-bottom: 1px solid #333;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">
            كادي <span>CADY EST</span>
        </div>
        <div class="company-details">
            مؤسسة كادي للمولدات والصيانة<br>
            المملكة العربية السعودية، الرياض<br>
            هاتف: 0790000000 | البريد: support@cady-est.com
        </div>
        <div class="clear"></div>
    </div>

    <div class="title">عقد صيانة مولد كهربائي (Maintenance Contract)</div>

    <div class="info-section">
        <div class="info-box">
            <div class="info-title">الطرف الأول (المزود)</div>
            <strong>مؤسسة كادي للمولدات والصيانة</strong><br>
            الرياض، المملكة العربية السعودية<br>
            السجل التجاري: 1010000000<br>
            المسؤول: قسم الصيانة والتشغيل
        </div>
        <div class="info-box-left">
            <div class="info-title">الطرف الثاني (العميل)</div>
            <strong>{{ $contract->customer->company_name }}</strong><br>
            المسؤول: {{ $contract->customer->contact_person }}<br>
            الهاتف: {{ $contract->customer->phone }}<br>
            البريد: {{ $contract->customer->email }}
        </div>
        <div class="clear"></div>
    </div>

    <h3 style="color: #0b192c; border-bottom: 1px solid #eee; padding-bottom: 4px;">تفاصيل المولد المتعاقد عليه</h3>
    <table class="details-table">
        <tr>
            <td style="width: 20%; background-color: #f8f9fa; font-weight: bold;">الماركة / الموديل</td>
            <td style="width: 30%">{{ $contract->generator->brand }} / {{ $contract->generator->model }}</td>
            <td style="width: 20%; background-color: #f8f9fa; font-weight: bold;">القدرة الكهربائية</td>
            <td style="width: 30%">{{ $contract->generator->capacity_kva }} KVA</td>
        </tr>
        <tr>
            <td style="background-color: #f8f9fa; font-weight: bold;">الرقم التسلسلي (S/N)</td>
            <td style="font-mono">{{ $contract->generator->serial_number }}</td>
            <td style="background-color: #f8f9fa; font-weight: bold;">موقع المولد</td>
            <td>{{ $contract->generator->location ?? 'موقع العميل الرئيسي' }}</td>
        </tr>
    </table>

    <h3 style="color: #0b192c; border-bottom: 1px solid #eee; padding-bottom: 4px;">تفاصيل وبنود التعاقد</h3>
    <table class="details-table">
        <tr>
            <td style="width: 20%; background-color: #f8f9fa; font-weight: bold;">رقم العقد المرجعي</td>
            <td style="width: 30%; font-mono font-bold;">{{ $contract->ref_number }}</td>
            <td style="width: 20%; background-color: #f8f9fa; font-weight: bold;">حالة العقد</td>
            <td style="width: 30%; font-weight: bold;">{{ $contract->status->label() }}</td>
        </tr>
        <tr>
            <td style="background-color: #f8f9fa; font-weight: bold;">تاريخ سريان العقد</td>
            <td>{{ $contract->start_date }}</td>
            <td style="background-color: #f8f9fa; font-weight: bold;">تاريخ انتهاء العقد</td>
            <td>{{ $contract->end_date }}</td>
        </tr>
        <tr>
            <td style="background-color: #f8f9fa; font-weight: bold;">قيمة العقد الإجمالية</td>
            <td style="font-bold">{{ number_format($contract->contract_value, 2) }} ر.س</td>
            <td style="background-color: #f8f9fa; font-weight: bold;">دورة الفوترة</td>
            <td>{{ $contract->billing_cycle }}</td>
        </tr>
        <tr>
            <td style="background-color: #f8f9fa; font-weight: bold;">عدد زيارات الخدمة</td>
            <td>{{ $contract->visits_count }} زيارات مجدولة سنوية</td>
            <td style="background-color: #f8f9fa; font-weight: bold;">الزيارات المنجزة</td>
            <td>{{ $contract->visits->where('status', \App\Enums\VisitStatus::COMPLETED)->count() }} من أصل {{ $contract->visits->count() }}</td>
        </tr>
    </table>

    <div style="margin-top: 15px; font-size: 10px; color: #555;">
        <strong>شروط تنفيذ الخدمة:</strong>
        <ol style="padding-right: 15px; margin: 5px 0 0 0;">
            <li>يلتزم الطرف الأول بتنفيذ زيارات الصيانة الدورية المجدولة وفقاً للتواريخ المحددة.</li>
            <li>تشمل الصيانة فحص الفلاتر، الزيوت، أنظمة التبريد والتحكم، للتأكد من جاهزية المولد للعمل.</li>
            <li>قطع الغيار والمستهلكات الإضافية تخضع لتسعير مستقل بعد موافقة الطرف الثاني.</li>
        </ol>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <strong>توقيع وختم الطرف الأول (المزود)</strong>
            <div class="signature-line"></div>
            <span style="font-size: 9px; color: #777; margin-top: 5px; display: block;">مؤسسة كادي للمولدات والصيانة</span>
        </div>
        <div class="signature-box-left">
            <strong>توقيع وختم الطرف الثاني (العميل)</strong>
            <div class="signature-line"></div>
            <span style="font-size: 9px; color: #777; margin-top: 5px; display: block;">{{ $contract->customer->company_name }}</span>
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer">
        مؤسسة كادي للمولدات والصيانة | العقد موثق ومعتمد إلكترونياً | صفحة 1 من 1
    </div>

</body>
</html>
