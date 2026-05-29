<?php

namespace App\Livewire\Dashboard;

use App\Models\Ad;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class HomePage extends Component
{
    #[Url(as: 'range', except: 'last_month')]
    public string $range = 'last_month';

    /**
     * @var array<string, string>
     */
    public array $rangeOptions = [
        'last_week' => 'آخر أسبوع',
        'last_month' => 'آخر شهر',
        'last_3_months' => 'آخر 3 أشهر',
        'last_year' => 'آخر سنة',
    ];

    public function render(): View
    {
        if (! array_key_exists($this->range, $this->rangeOptions)) {
            $this->range = 'last_month';
        }

        $period = $this->resolveRange($this->range);

        $companyTotal = Company::query()->count();
        $customerTotal = Customer::query()->count();
        $studentTotal = Student::query()->count();
        $adsTotal = Ad::query()->count();
        $projectsTotal = Project::query()->count();

        $stats = [
            [
                'label' => 'الشركات',
                'value' => number_format($companyTotal),
                'hint' => number_format($this->countInPeriod(Company::class, $period['start'], $period['end'])).' جديد خلال '.$period['label'],
                'icon' => 'companies',
                'accent' => 'text-brand-600 dark:text-brand-300',
            ],
            [
                'label' => 'العملاء',
                'value' => number_format($customerTotal),
                'hint' => number_format($this->countInPeriod(Customer::class, $period['start'], $period['end'])).' جديد خلال '.$period['label'],
                'icon' => 'customers',
                'accent' => 'text-amber-500 dark:text-amber-300',
            ],
            [
                'label' => 'الطلاب',
                'value' => number_format($studentTotal),
                'hint' => number_format($this->countInPeriod(Student::class, $period['start'], $period['end'])).' جديد خلال '.$period['label'],
                'icon' => 'students',
                'accent' => 'text-rose-500 dark:text-rose-300',
            ],
        ];

        $charts = [
            'users_growth' => [
                'type' => 'line',
                'labels' => $period['buckets']->pluck('label')->all(),
                'datasets' => [
                    [
                        'label' => 'الشركات',
                        'data' => $this->bucketCounts($period['buckets'], Company::class),
                        'borderColor' => '#159868',
                        'backgroundColor' => 'rgba(21, 152, 104, 0.12)',
                        'fill' => false,
                        'pointRadius' => 3,
                        'pointHoverRadius' => 5,
                    ],
                    [
                        'label' => 'العملاء',
                        'data' => $this->bucketCounts($period['buckets'], Customer::class),
                        'borderColor' => '#fb923c',
                        'backgroundColor' => 'rgba(251, 146, 60, 0.12)',
                        'fill' => false,
                        'pointRadius' => 3,
                        'pointHoverRadius' => 5,
                    ],
                    [
                        'label' => 'الطلاب',
                        'data' => $this->bucketCounts($period['buckets'], Student::class),
                        'borderColor' => '#f43f5e',
                        'backgroundColor' => 'rgba(244, 63, 94, 0.12)',
                        'fill' => false,
                        'pointRadius' => 3,
                        'pointHoverRadius' => 5,
                    ],
                ],
                'options' => [
                    'plugins' => [
                        'legend' => [
                            'position' => 'top',
                        ],
                        'tooltip' => [
                            'rtl' => true,
                        ],
                    ],
                ],
            ],
            'user_types' => [
                'type' => 'doughnut',
                'labels' => ['الشركات', 'العملاء', 'الطلاب'],
                'datasets' => [
                    [
                        'label' => 'أنواع المستخدمين',
                        'data' => [$companyTotal, $customerTotal, $studentTotal],
                        'backgroundColor' => ['#159868', '#fb923c', '#f43f5e'],
                        'hoverOffset' => 8,
                    ],
                ],
                'options' => [
                    'cutout' => '72%',
                    'plugins' => [
                        'legend' => [
                            'position' => 'left',
                        ],
                    ],
                ],
            ],
            'opportunities' => [
                'type' => 'doughnut',
                'labels' => ['إعلانات الشركات', 'المشاريع'],
                'datasets' => [
                    [
                        'label' => 'أنواع الفرص',
                        'data' => [$adsTotal, $projectsTotal],
                        'backgroundColor' => ['#26c281', '#f59e0b'],
                        'hoverOffset' => 8,
                    ],
                ],
                'options' => [
                    'cutout' => '72%',
                    'plugins' => [
                        'legend' => [
                            'position' => 'left',
                        ],
                    ],
                ],
            ],
        ];

        return view('livewire.dashboard.home-page', [
            'stats' => $stats,
            'charts' => $charts,
            'rangeOptions' => $this->rangeOptions,
            'selectedRangeLabel' => $period['label'],
        ])->layout('layouts.dashboard', [
            'title' => 'لوحة التحكم',
            'heading' => 'نظرة عامة',
            'subheading' => 'مؤشرات مركزة عن الشركات والعملاء والطلاب مع توزيع أنواع المستخدمين والفرص.',
        ]);
    }

    /**
     * @return array{start: CarbonImmutable, end: CarbonImmutable, label: string, buckets: Collection<int, array{start: CarbonImmutable, end: CarbonImmutable, label: string}>}
     */
    protected function resolveRange(string $range): array
    {
        $now = CarbonImmutable::now();

        return match ($range) {
            'last_week' => [
                'start' => $now->subDays(6)->startOfDay(),
                'end' => $now->endOfDay(),
                'label' => 'آخر أسبوع',
                'buckets' => collect(range(6, 0))
                    ->map(fn (int $offset) => [
                        'start' => $now->subDays($offset)->startOfDay(),
                        'end' => $now->subDays($offset)->endOfDay(),
                        'label' => $now->subDays($offset)->locale('ar')->translatedFormat('D'),
                    ]),
            ],
            'last_3_months' => [
                'start' => $now->subWeeks(11)->startOfWeek(),
                'end' => $now->endOfWeek(),
                'label' => 'آخر 3 أشهر',
                'buckets' => collect(range(11, 0))
                    ->map(fn (int $offset) => [
                        'start' => $now->subWeeks($offset)->startOfWeek(),
                        'end' => $now->subWeeks($offset)->endOfWeek(),
                        'label' => $now->subWeeks($offset)->startOfWeek()->locale('ar')->translatedFormat('d M'),
                    ]),
            ],
            'last_year' => [
                'start' => $now->subMonths(11)->startOfMonth(),
                'end' => $now->endOfMonth(),
                'label' => 'آخر سنة',
                'buckets' => collect(range(11, 0))
                    ->map(fn (int $offset) => [
                        'start' => $now->subMonths($offset)->startOfMonth(),
                        'end' => $now->subMonths($offset)->endOfMonth(),
                        'label' => $now->subMonths($offset)->locale('ar')->translatedFormat('M'),
                    ]),
            ],
            default => [
                'start' => $now->subDays(29)->startOfDay(),
                'end' => $now->endOfDay(),
                'label' => 'آخر شهر',
                'buckets' => collect(range(29, 0))
                    ->map(fn (int $offset) => [
                        'start' => $now->subDays($offset)->startOfDay(),
                        'end' => $now->subDays($offset)->endOfDay(),
                        'label' => $now->subDays($offset)->locale('ar')->translatedFormat('d M'),
                    ]),
            ],
        };
    }

    protected function countInPeriod(string $modelClass, CarbonImmutable $start, CarbonImmutable $end): int
    {
        return $modelClass::query()
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /**
     * @param  Collection<int, array{start: CarbonImmutable, end: CarbonImmutable, label: string}>  $buckets
     * @return array<int, int>
     */
    protected function bucketCounts(Collection $buckets, string $modelClass): array
    {
        return $buckets
            ->map(fn (array $bucket): int => $this->countInPeriod($modelClass, $bucket['start'], $bucket['end']))
            ->all();
    }
}
