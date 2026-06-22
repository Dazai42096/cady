<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>عرض سعر رقم {{ $quotation->ref_number }}</title>
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
            min-height: 90px;
        }
        .info-box-left {
            width: 48%;
            float: left;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            min-height: 90px;
        }
        .info-title {
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
            margin-bottom: 6px;
            color: #0b192c;
            font-size: 12px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0b192c;
            color: white;
            font-weight: bold;
            padding: 6px;
            border: 1px solid #0b192c;
            text-align: center;
            font-size: 10px;
        }
        .items-table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary-table {
            width: 250px;
            float: left;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .summary-table .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #0b192c;
        }
        .terms {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .terms-title {
            font-weight: bold;
            color: #0b192c;
            margin-bottom: 5px;
            font-size: 11px;
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
            هاتف: 0790000000 | البريد: sales@cady-est.com
        </div>
        <div class="clear"></div>
    </div>

    <div class="title">عرض سعر (Quotation)</div>

    <div class="info-section">
        <div class="info-box">
            <div class="info-title">معلومات العميل</div>
            <strong>{{ $quotation->customer->company_name }}</strong><br>
            جهة الاتصال: {{ $quotation->customer->contact_person }}<br>
            الهاتف: {{ $quotation->customer->phone }}<br>
            البريد: {{ $quotation->customer->email }}
        </div>
        <div class="info-box-left">
            <div class="info-title">تفاصيل عرض السعر</div>
            رقم العرض: {{ $quotation->ref_number }}<br>
            التاريخ: {{ $quotation->date }}<br>
            صالح حتى: {{ $quotation->valid_until }}<br>
            الحالة: {{ $quotation->status->label() }}
        </div>
        <div class="clear"></div>
    </div>

    <div style="margin-bottom: 12px;">
        <strong>الموضوع:</strong> {{ $quotation->subject }}
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 45%; text-align: right;">البيان (الوصف)</th>
                <th style="width: 15%">سعر الوحدة</th>
                <th style="width: 10%">الكمية</th>
                <th style="width: 25%">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right;">{{ $item->description }}</td>
                    <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->total_price, 2) }} ر.s</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="info-section">
        <div style="width: 50%; float: right;">
            &nbsp;
        </div>
        <div style="width: 45%; float: left;">
            <table class="summary-table" style="width: 100%;">
                <tr>
                    <td>المبلغ الخاضع للضريبة</td>
                    <td style="text-align: left; font-weight: bold;">{{ number_format($quotation->subtotal, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td>ضريبة القيمة المضافة (15%)</td>
                    <td style="text-align: left; font-weight: bold;">{{ number_format($quotation->vat_amount, 2) }} ر.س</td>
                </tr>
                <tr class="total-row">
                    <td>الإجمالي الشامل للضريبة</td>
                    <td style="text-align: left; font-weight: bold;">{{ number_format($quotation->total_amount, 2) }} ر.س</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <div class="terms">
        <div class="terms-title">الشروط والأحكام:</div>
        <ul style="padding-right: 15px; margin: 0; font-size: 9px; color: #555;">
            <li>هذا العرض خاضع لشروط التوريد والتركيب القياسية لمؤسسة كادي.</li>
            <li>الأسعار تشمل ضريبة القيمة المضافة بنسبة 15%.</li>
            <li>طريقة الدفع: حسب الاتفاق المعتمد في أمر الشراء.</li>
        </ul>
    </div>

    <div class="footer">
        مؤسسة كادي للمولدات والصيانة | الرقم الضريبي: 300000000000003 | صفحة 1 من 1
    </div>

</body>
</html>
