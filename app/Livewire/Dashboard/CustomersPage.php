<?php

namespace App\Livewire\Dashboard;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersPage extends Component
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
        $customers = Customer::query()
            ->with('profile')
            ->withCount('projects')
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.$this->search.'%';

                $query->where(function (Builder $builder) use ($term): void {
                    $builder
                        ->where('username', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone_number', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('profile', function (Builder $profile) use ($term): void {
                            $profile
                                ->where('title', 'like', $term)
                                ->orWhere('college', 'like', $term)
                                ->orWhere('tech1', 'like', $term)
                                ->orWhere('tech2', 'like', $term)
                                ->orWhere('tech3', 'like', $term);
                        });
                });
            })
            ->latest()
            ->paginate(8);

        $stats = [
            [
                'label' => 'إجمالي العملاء',
                'value' => Customer::query()->count(),
                'hint' => 'كل حسابات العملاء',
                'icon' => 'customers',
            ],
            [
                'label' => 'عملاء مفعّلون',
                'value' => Customer::query()->where('is_verified', true)->count(),
                'hint' => 'جاهزون لنشر المشاريع',
                'icon' => 'shield',
            ],
            [
                'label' => 'المشاريع المنشورة',
                'value' => Project::query()->count(),
                'hint' => 'إجمالي مشاريع العمل الحر',
                'icon' => 'chart',
            ],
            [
                'label' => 'انضمام هذا الشهر',
                'value' => Customer::query()->where('created_at', '>=', now()->startOfMonth())->count(),
                'hint' => 'عدد العملاء الجدد',
                'icon' => 'spark',
            ],
        ];

        return view('livewire.dashboard.customers-page', [
            'customers' => $customers,
            'stats' => $stats,
        ])->layout('layouts.dashboard', [
            'title' => 'العملاء',
            'heading' => 'إدارة العملاء',
            'subheading' => 'تابع الحسابات التي تنشر المشاريع، وتحقق من الحالة المهنية والنشاط الحالي لكل عميل.',
        ]);
    }
}