<?php

namespace App\Livewire\Dashboard;

use App\Enums\NotificationTopic;
use App\Enums\NotificationType;
use App\Enums\UserType;
use App\Models\User;
use App\Services\Notifications\NotificationAudience;
use App\Services\Notifications\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SendNotificationsPage extends Component
{
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:2000')]
    public string $body = '';

    #[Validate('required|string|in:users,user_type,topic')]
    public string $audienceType = 'users';

    #[Validate('nullable|string|in:student,customer,company,admin')]
    public string $userType = 'student';

    #[Validate('nullable|string|in:all-students,all-companies,all-customers')]
    public string $topic = 'all-students';

    #[Validate('array')]
    public array $selectedUserIds = [];

    public string $search = '';

    public function send(NotificationService $notificationService): void
    {
        $validated = $this->validate();

        $audience = match ($validated['audienceType']) {
            'users' => NotificationAudience::forUsers(
                NotificationType::AdminAnnouncement,
                $validated['title'],
                $validated['body'],
                $this->selectedUserIds,
                ['source' => 'dashboard'],
            ),
            'user_type' => NotificationAudience::forUserType(
                NotificationType::AdminAnnouncement,
                $validated['title'],
                $validated['body'],
                UserType::from($validated['userType']),
                ['source' => 'dashboard'],
            ),
            default => NotificationAudience::forTopic(
                NotificationType::AdminAnnouncement,
                $validated['title'],
                $validated['body'],
                NotificationTopic::from($validated['topic']),
                ['source' => 'dashboard'],
            ),
        };

        $notificationService->queue($audience, auth()->user());

        $this->reset('title', 'body', 'selectedUserIds');
        session()->flash('notification-status', 'تمت جدولة الإشعار بنجاح.');
    }

    public function render(): View
    {
        $users = User::query()
            ->when($this->search !== '', fn ($query) => $query->where('username', 'like', '%'.$this->search.'%'))
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.dashboard.send-notifications-page', [
            'users' => $users,
            'topics' => NotificationTopic::cases(),
            'userTypes' => [UserType::Student, UserType::Company, UserType::Customer, UserType::Admin],
        ])->layout('layouts.dashboard', [
            'title' => 'إرسال الإشعارات',
            'heading' => 'إرسال الإشعارات',
            'subheading' => 'إدارة الإشعارات الفردية والجماعية باستخدام Firebase من خلال اللوحة.',
        ]);
    }
}