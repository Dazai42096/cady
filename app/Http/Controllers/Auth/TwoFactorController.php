<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function showChallenge()
    {
        if (!session()->has('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20'],
        ]);

        $user = User::find(session('two_factor_user_id'));

        if (!$user || !$user->hasTwoFactorEnabled()) {
            session()->forget(['two_factor_user_id', 'two_factor_remember']);

            return redirect()->route('login');
        }

        if (!$this->verifyCode($user, $request->code)) {
            $this->logEvent($request, 'auth.two_factor_failed', $user);

            throw ValidationException::withMessages([
                'code' => 'The two-factor authentication code is invalid.',
            ]);
        }

        $remember = session()->pull('two_factor_remember', false);
        session()->forget('two_factor_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $this->logEvent($request, 'auth.two_factor_success', $user);

        return redirect()->route($this->redirectRouteForRole($user->role));
    }

    public function setup(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $google2fa = new Google2FA();

        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();

            $user->forceFill([
                'two_factor_secret' => Crypt::encryptString($secret),
                'two_factor_enabled' => false,
                'two_factor_confirmed_at' => null,
                'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
            ])->save();
        } else {
            $secret = Crypt::decryptString($user->two_factor_secret);
        }

        $company = config('app.name', 'CADY EST');
        $otpauthUrl = $google2fa->getQRCodeUrl($company, $user->email, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(220),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($otpauthUrl);

        return view('auth.two-factor-setup', [
            'user' => $user,
            'secret' => $secret,
            'qrSvg' => $qrSvg,
            'recoveryCodes' => $user->two_factor_recovery_codes ?? [],
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (!$user->two_factor_secret) {
            throw ValidationException::withMessages([
                'code' => 'Please generate a two-factor secret first.',
            ]);
        }

        if (!$this->verifyCode($user, $request->code)) {
            throw ValidationException::withMessages([
                'code' => 'The two-factor authentication code is invalid.',
            ]);
        }

        $user->forceFill([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->logEvent($request, 'auth.two_factor_enabled', $user);

        return redirect()->route('two-factor.setup')->with('success', 'Two-factor authentication has been enabled.');
    }

    public function disable(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->forceFill([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->logEvent($request, 'auth.two_factor_disabled', $user);

        return redirect()->route('two-factor.setup')->with('success', 'Two-factor authentication has been disabled.');
    }

    private function verifyCode(User $user, string $code): bool
    {
        $code = trim($code);

        if ($user->two_factor_secret) {
            $secret = Crypt::decryptString($user->two_factor_secret);

            if ((new Google2FA())->verifyKey($secret, $code, 2)) {
                return true;
            }
        }

        $recoveryCodes = $user->two_factor_recovery_codes ?? [];

        if (in_array($code, $recoveryCodes, true)) {
            $remainingCodes = array_values(array_filter(
                $recoveryCodes,
                fn ($recoveryCode) => $recoveryCode !== $code
            ));

            $user->forceFill([
                'two_factor_recovery_codes' => $remainingCodes,
            ])->save();

            return true;
        }

        return false;
    }

    private function generateRecoveryCodes(): array
    {
        return collect(range(1, 10))
            ->map(fn () => Str::upper(Str::random(10)) . '-' . Str::upper(Str::random(10)))
            ->all();
    }

    private function logEvent(Request $request, string $action, User $user): void
    {
        try {
            $this->auditLogService->log(
                action: $action,
                entityType: User::class,
                entityId: $user->id,
                newValues: [
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                ]
            );
        } catch (\Throwable) {
            //
        }
    }

    private function redirectRouteForRole(?string $role): string
    {
        return match ($role) {
            'admin', 'sales', 'support' => 'dashboard.index',
            'customer' => 'portal.index',
            default => 'login',
        };
    }
}