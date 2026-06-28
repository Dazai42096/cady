$report = ".\cady_spec_role_audit.txt"
Remove-Item $report -ErrorAction SilentlyContinue

function Add-Line($text = "") {
    Add-Content -Path $report -Value $text -Encoding UTF8
}

function Check($name, $ok, $note = "") {
    $status = if ($ok) { "OK" } else { "MISSING / REVIEW" }
    Add-Line ("[{0}] {1} {2}" -f $status, $name, $note)
}

Add-Line "CADY EST PLATFORM - SPEC + ROLE AUDIT"
Add-Line ("Generated: " + (Get-Date))
Add-Line "============================================================"
Add-Line ""

$routes = (php artisan route:list 2>$null) -join "`n"

$codeFiles = Get-ChildItem "app","routes","resources","database","config" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\vendor\\|\\node_modules\\" }

$allText = ""
foreach ($file in $codeFiles) {
    try {
        $allText += [System.IO.File]::ReadAllText($file.FullName) + "`n"
    } catch {}
}

Add-Line "1) MODULE CHECKS"
Add-Line "------------------------------------------------------------"

Check "Public Website: /, /about, /services, /contact, /quote-request" (
    $routes -match "\s/\s" -and
    $routes -match "about" -and
    $routes -match "services" -and
    $routes -match "contact" -and
    $routes -match "quote-request"
)

Check "Authentication: login, register, forgot/reset password, logout" (
    $routes -match "login" -and
    $routes -match "register" -and
    $routes -match "forgot-password" -and
    $routes -match "reset-password" -and
    $routes -match "logout"
)

Check "Customers module" ($routes -match "dashboard/customers")
Check "Pending customer approvals" ($routes -match "customers-pending" -and $allText -match "approve")
Check "Generator registry" ($routes -match "dashboard/generators")
Check "Quotations module" ($routes -match "dashboard/quotations")
Check "Quotation items / quantity" ($allText -match "quotation_items|items\[\$|quantity")
Check "Quotation PDF" ($routes -match "quotations/\{quotation\}/pdf" -or $allText -match "Pdf::loadView")
Check "Maintenance contracts" ($routes -match "dashboard/contracts")
Check "Rental control" ($routes -match "dashboard/rentals")
Check "Maintenance visits / service visits" ($routes -match "dashboard/visits")
Check "Customer portal" ($routes -match "portal")
Check "Management dashboard" ($routes -match "dashboard")
Check "Audit logs" ($routes -match "audit-logs" -and $allText -match "audit")
Check "WhatsApp integration" ($allText -match "whatsapp|WhatsApp|Meta")
Check "PDF generation exists" ($allText -match "Pdf::loadView|dompdf|puppeteer|headless")

Add-Line ""
Add-Line "2) SPEC TECHNOLOGY CHECKS"
Add-Line "------------------------------------------------------------"

Check "Supabase Auth present" ($allText -match "supabase") "Master spec says Supabase Auth."
Check "Postgres RLS policies present" ($allText -match "CREATE POLICY|ROW LEVEL SECURITY|RLS") "Role matrix says RLS + app code."
Check "2FA / TOTP present" ($allText -match "TOTP|2FA|two-factor|authenticator")
Check "Puppeteer PDF engine present" ($allText -match "puppeteer|headless chrome") "Current app may use DomPDF instead."
Check "Backup logic documented/implemented" ($allText -match "backup|pg_dump|Backblaze")
Check "WhatsApp messages table/API present" ($allText -match "whatsapp_messages|WhatsApp Business|Meta WhatsApp")

Add-Line ""
Add-Line "3) ROLE PERMISSION CHECKS"
Add-Line "------------------------------------------------------------"

Check "Role middleware exists" ($allText -match "role:")
Check "Admin role checks exist" ($allText -match "admin|isAdmin")
Check "Sales access exists" ($allText -match "sales")
Check "Support access exists" ($allText -match "support")
Check "Customer role / portal isolation exists" ($allText -match "customer.portal|customer")
Check "Policies exist" ((Test-Path "app\Policies") -and ((Get-ChildItem "app\Policies" -Filter "*.php" -ErrorAction SilentlyContinue).Count -gt 0))
Check "Admin-only audit log route" ($routes -match "audit-logs" -and $allText -match "role:admin")
Check "Admin/Sales quotation permissions" ($routes -match "quotations" -and $allText -match "role:admin,sales")
Check "Customer portal routes separated from dashboard" ($routes -match "portal" -and $routes -match "dashboard")

Add-Line ""
Add-Line "4) FINAL FEEDBACK REGRESSION CHECKS"
Add-Line "------------------------------------------------------------"

$mojibake = Select-String -Path "resources\views\*.blade.php","resources\views\**\*.blade.php","resources\views\**\**\*.blade.php" -Pattern "Ø|Ù|Ã|Â|ð|â|�" -List -ErrorAction SilentlyContinue
Check "No corrupted Arabic in Blade files" (-not $mojibake)

if ($mojibake) {
    Add-Line "Corrupted Arabic found in:"
    foreach ($m in $mojibake) {
        Add-Line (" - " + $m.Path)
    }
}

$pdfBad = Select-String -Path "resources\views\pdf\quotation.blade.php" -Pattern "ر\.س|ضريبة القيمة المضافة \(15%\)|بنسبة 15%" -List -ErrorAction SilentlyContinue
Check "Quotation PDF does not use Saudi Riyal or fixed 15%" (-not $pdfBad)

Check "JOD exists in quotation files" ($allText -match "JOD")
Check "Tax 16 / 8 / 0 exists" ($allText -match "16" -and $allText -match "8" -and $allText -match "0")
Check "Pending customers route exists" ($routes -match "customers-pending")
Check "Rental route exists" ($routes -match "dashboard/rentals")

Add-Line ""
Add-Line "5) ROUTES SNAPSHOT"
Add-Line "------------------------------------------------------------"
Add-Line $routes

Write-Host "Audit finished. Open cady_spec_role_audit.txt" -ForegroundColor Green
