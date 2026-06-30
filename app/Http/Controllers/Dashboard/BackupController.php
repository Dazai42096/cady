<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    private array $tables = [
        'users',
        'customers',
        'customer_users',
        'generators',
        'quotations',
        'quote_requests',
        'maintenance_contracts',
        'maintenance_visits',
        'rentals',
        'whatsapp_messages',
        'audit_logs',
    ];

    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index()
    {
        $directory = storage_path('app/backups');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $files = collect(glob($directory . DIRECTORY_SEPARATOR . '*.json') ?: [])
            ->map(function ($path) {
                return [
                    'name' => basename($path),
                    'size' => $this->humanFileSize(filesize($path)),
                    'modified_at' => date('Y-m-d H:i:s', filemtime($path)),
                ];
            })
            ->sortByDesc('modified_at')
            ->values();

        return view('dashboard.backups.index', compact('files'));
    }

    public function generate(Request $request)
    {
        $directory = storage_path('app/backups');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $timestamp = now()->format('Ymd-His');
        $filename = "cady-backup-{$timestamp}.json";
        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        $snapshot = [
            'meta' => [
                'app' => config('app.name', 'CADY EST'),
                'generated_at' => now()->toIso8601String(),
                'generated_by' => [
                    'id' => $request->user()?->id,
                    'name' => $request->user()?->name,
                    'email' => $request->user()?->email,
                ],
                'database_connection' => config('database.default'),
                'backup_type' => 'application_json_export',
            ],
            'tables' => [],
        ];

        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)->get()->map(function ($row) {
                return (array) $row;
            })->values()->all();

            $snapshot['tables'][$table] = [
                'count' => count($rows),
                'rows' => $rows,
            ];
        }

        file_put_contents(
            $path,
            json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->log($request, 'backup.generated', [
            'filename' => $filename,
            'tables' => array_keys($snapshot['tables']),
            'size_bytes' => filesize($path),
        ]);

        return back()->with('success', "Backup generated successfully: {$filename}");
    }

    public function download(Request $request, string $filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/' . $filename);

        abort_unless(file_exists($path), 404);

        $this->log($request, 'backup.downloaded', [
            'filename' => $filename,
        ]);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function delete(Request $request, string $filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/' . $filename);

        abort_unless(file_exists($path), 404);

        unlink($path);

        $this->log($request, 'backup.deleted', [
            'filename' => $filename,
        ]);

        return back()->with('success', "Backup deleted successfully: {$filename}");
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    private function log(Request $request, string $action, array $data): void
    {
        try {
            $this->auditLogService->log(
                action: $action,
                entityType: User::class,
                entityId: $request->user()?->id,
                oldValues: [],
                newValues: array_merge($data, [
                    'admin_email' => $request->user()?->email,
                    'ip' => $request->ip(),
                    'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
                ])
            );
        } catch (\Throwable) {
            //
        }
    }
}