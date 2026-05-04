<?php

namespace App\Livewire\Auth;

use App\Enums\UserType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AdminLogin extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    public function mount(): void
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return;
        }

        if ($user->type === UserType::Admin && $user->is_verified) {
            $this->redirectRoute('dashboard.companies', navigate: true);

            return;
        }

        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    public function login(): void
    {
        $validated = $this->validate();

        if (! Auth::guard('web')->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'type' => UserType::Admin->value,
            'is_verified' => true,
        ], $this->remember)) {
            $this->addError('email', 'بيانات الدخول غير صحيحة أو أن الحساب لا يملك صلاحية الإدارة.');

            return;
        }

        session()->regenerate();

        $this->redirectRoute('dashboard.companies', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.admin-login')
            ->layout('layouts.auth', [
                'title' => 'دخول لوحة التحكم',
            ]);
    }
}