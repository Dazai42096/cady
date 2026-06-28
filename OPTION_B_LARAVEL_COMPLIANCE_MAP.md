# CADY EST — Option B Laravel Master Spec Compliance Map

## Decision

We will keep the existing Laravel + PostgreSQL + Render platform and implement Laravel-equivalent compliance for the approved CADY EST Master Spec v2.0.

## Original Master Spec Architecture

- Next.js 14
- Supabase Auth
- Supabase PostgreSQL RLS
- Supabase Storage
- Puppeteer PDF
- WhatsApp Business API
- Supabase backups

## Option B Laravel Equivalent

| Master Spec Requirement | Laravel Equivalent |
|---|---|
| Supabase Auth | Laravel Auth with secure password policy, lockout, audit logging |
| Optional TOTP 2FA | Laravel 2FA using pragmarx/google2fa-laravel |
| Postgres RLS | Laravel middleware + policies + scoped queries + negative tests |
| Supabase Storage | Laravel Storage, local/Render disk first, S3-compatible later |
| Puppeteer PDF | Laravel Browsershot/Puppeteer or keep DomPDF where acceptable |
| WhatsApp Business API | Laravel WhatsApp service using Meta Cloud API |
| whatsapp_messages table | Laravel migration + model + logs |
| Daily/weekly backups | Render PostgreSQL backup + pg_dump script/GitHub Action |
| Customer data isolation | Customer portal middleware + policies + query scoping |
| Audit log | Existing audit log expanded to auth, exports, sensitive views |
| Service reports | Laravel module: reports, checklists, photos, signatures, PDF |
| Rental calculations | Laravel service for inclusive day counting and monthly-rate split |

## Priority Order

1. Security/auth hardening
2. Role permission tests
3. Rental full calculation + Excel export
4. Service reports module
5. WhatsApp integration
6. PDF improvements
7. Backup/restore process
8. Final acceptance testing
