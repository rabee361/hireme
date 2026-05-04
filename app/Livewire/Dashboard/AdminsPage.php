<?php

namespace App\Livewire\Dashboard;

use App\Models\Admin;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminsPage extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $admins = Admin::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.$this->search.'%';

                $query->where(function (Builder $builder) use ($term): void {
                    $builder
                        ->where('username', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone_number', 'like', $term);
                });
            })
            ->latest()
            ->paginate(8);

        $stats = [
            [
                'label' => 'إجمالي المشرفين',
                'value' => Admin::query()->count(),
                'hint' => 'الحسابات التي تدير النظام',
                'icon' => 'admins',
            ],
            [
                'label' => 'مشرفون موثّقون',
                'value' => Admin::query()->where('is_verified', true)->count(),
                'hint' => 'يمكنهم الوصول إلى اللوحة',
                'icon' => 'shield',
            ],
            [
                'label' => 'حماية إضافية',
                'value' => Admin::query()->whereNotNull('two_factor_confirmed_at')->count(),
                'hint' => 'حسابات فعّلت التحقق الثنائي',
                'icon' => 'spark',
            ],
            [
                'label' => 'تمت إضافتهم هذا الشهر',
                'value' => Admin::query()->where('created_at', '>=', now()->startOfMonth())->count(),
                'hint' => 'وتيرة نمو فريق الإدارة',
                'icon' => 'chart',
            ],
        ];

        return view('livewire.dashboard.admins-page', [
            'admins' => $admins,
            'stats' => $stats,
        ])->layout('layouts.dashboard', [
            'title' => 'المشرفون',
            'heading' => 'إدارة المشرفين',
            'subheading' => 'تحكم في حسابات الإدارة، مستويات التفعيل، وحالة الأمان الخاصة بمشرفي النظام.',
        ]);
    }
}