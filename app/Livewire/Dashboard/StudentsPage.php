<?php

namespace App\Livewire\Dashboard;

use App\Models\AdApplication;
use App\Models\ProjectApplication;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsPage extends Component
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
        $students = Student::query()
            ->with([
                'profile' => fn (Builder $query) => $query->withCount(['adApplications', 'projectApplications']),
            ])
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
                'label' => 'إجمالي الطلاب',
                'value' => Student::query()->count(),
                'hint' => 'كل حسابات الطلاب المسجلة',
                'icon' => 'students',
            ],
            [
                'label' => 'طلاب موثّقون',
                'value' => Student::query()->where('is_verified', true)->count(),
                'hint' => 'مؤهلون للتقديم على الفرص',
                'icon' => 'shield',
            ],
            [
                'label' => 'طلبات إعلانات العمل',
                'value' => AdApplication::query()->count(),
                'hint' => 'مرتبطة بالإعلانات المنشورة',
                'icon' => 'chart',
            ],
            [
                'label' => 'طلبات المشاريع',
                'value' => ProjectApplication::query()->count(),
                'hint' => 'إجمالي طلبات العمل الحر',
                'icon' => 'spark',
            ],
        ];

        return view('livewire.dashboard.students-page', [
            'students' => $students,
            'stats' => $stats,
        ])->layout('layouts.dashboard', [
            'title' => 'الطلاب',
            'heading' => 'إدارة الطلاب',
            'subheading' => 'تابع ملفات الطلاب، المهارات، وسجل التقديمات على الإعلانات والمشاريع في واجهة واحدة.',
        ]);
    }
}