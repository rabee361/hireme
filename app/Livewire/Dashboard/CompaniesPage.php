<?php

namespace App\Livewire\Dashboard;

use App\Models\Ad;
use App\Models\Company;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CompaniesPage extends Component
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
        $companies = Company::query()
            ->with('profile')
            ->withCount('ads')
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
                                ->where('tech1', 'like', $term)
                                ->orWhere('tech2', 'like', $term)
                                ->orWhere('tech3', 'like', $term);
                        });
                });
            })
            ->latest()
            ->paginate(8);

        $stats = [
            [
                'label' => 'إجمالي الشركات',
                'value' => Company::query()->count(),
                'hint' => 'كل الحسابات المصنفة كشركات',
                'icon' => 'companies',
            ],
            [
                'label' => 'شركات مفعلة',
                'value' => Company::query()->where('is_verified', true)->count(),
                'hint' => 'جاهزة للنشر والتفاعل',
                'icon' => 'shield',
            ],
            [
                'label' => 'الإعلانات المنشورة',
                'value' => Ad::query()->count(),
                'hint' => 'إجمالي الإعلانات المرتبطة بالشركات',
                'icon' => 'chart',
            ],
            [
                'label' => 'انضمام هذا الشهر',
                'value' => Company::query()->where('created_at', '>=', now()->startOfMonth())->count(),
                'hint' => 'النمو الحالي في الشركات',
                'icon' => 'spark',
            ],
        ];

        return view('livewire.dashboard.companies-page', [
            'companies' => $companies,
            'stats' => $stats,
        ])->layout('layouts.dashboard', [
            'title' => 'الشركات',
            'heading' => 'إدارة الشركات',
            'subheading' => 'راجع الحسابات التي تنشر الإعلانات، وحالة التفعيل، والنشاط الحالي من شاشة واحدة.',
        ]);
    }
}