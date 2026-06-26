<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsPage extends Component
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
    public function selectedItem(): ?Project
    {
        if (! $this->selectedItemId) {
            return null;
        }
        return Project::with('customer')->withCount('applications')->find($this->selectedItemId);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $projects = Project::query()
            ->with('customer')
            ->withCount('applications')
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.$this->search.'%';

                $query->where(function (Builder $builder) use ($term): void {
                    $builder
                        ->where('name', 'like', $term)
                        ->orWhere('details', 'like', $term)
                        ->orWhere('tool1', 'like', $term)
                        ->orWhere('tool2', 'like', $term)
                        ->orWhere('tool3', 'like', $term)
                        ->orWhere('tool4', 'like', $term)
                        ->orWhere('tool5', 'like', $term)
                        ->orWhereHas('customer', function (Builder $customer) use ($term): void {
                            $customer
                                ->where('username', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            })
            ->latest()
            ->paginate(8);

        return view('livewire.dashboard.projects-page', [
            'projects' => $projects,
        ])->layout('layouts.dashboard', [
            'title' => 'المشاريع',
            'heading' => 'إدارة المشاريع',
            'subheading' => '',
        ]);
    }
}