<?php

namespace App\Livewire\Dashboard;

use App\Models\Ad;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdsPage extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public ?int $selectedItemId = null;

    public function showItem(int $id): void
    {
        $this->selectedItemId = $id;
    }

    public function closeModal(): void
    {
        $this->selectedItemId = null;
    }

    #[\Livewire\Attributes\Computed]
    public function selectedItem(): ?Ad
    {
        if (! $this->selectedItemId) {
            return null;
        }
        return Ad::with('company')->withCount('applications')->find($this->selectedItemId);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $ads = Ad::query()
            ->with('company')
            ->withCount('applications')
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.$this->search.'%';

                $query->where(function (Builder $builder) use ($term): void {
                    $builder
                        ->where('job_name', 'like', $term)
                        ->orWhere('additional_details', 'like', $term)
                        ->orWhere('req1', 'like', $term)
                        ->orWhere('req2', 'like', $term)
                        ->orWhere('req3', 'like', $term)
                        ->orWhere('req4', 'like', $term)
                        ->orWhere('req5', 'like', $term)
                        ->orWhere('task1', 'like', $term)
                        ->orWhere('task2', 'like', $term)
                        ->orWhere('task3', 'like', $term)
                        ->orWhere('task4', 'like', $term)
                        ->orWhere('task5', 'like', $term)
                        ->orWhereHas('company', function (Builder $company) use ($term): void {
                            $company
                                ->where('username', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            })
            ->latest()
            ->paginate(8);

        return view('livewire.dashboard.ads-page', [
            'ads' => $ads,
        ])->layout('layouts.dashboard', [
            'title' => 'الإعلانات',
            'heading' => 'إدارة الإعلانات',
            'subheading' => '',
        ]);
    }
}