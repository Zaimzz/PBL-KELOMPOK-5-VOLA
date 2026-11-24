<?php

namespace App\Policies;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->organizerProfile !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->organizerProfile?->id === $event->organizer_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->organizerProfile !== null;
    }

    /**
     * Determine whether the user can update the model.
     * Only draft and rejected events can be edited.
     */
    public function update(User $user, Event $event): bool
    {
        if ($user->organizerProfile?->id !== $event->organizer_id) {
            return false;
        }

        return in_array($event->status, [
            EventStatus::Draft,
            EventStatus::Rejected,
        ], true);
    }

    /**
     * Determine whether the user can delete the model.
     * Only draft and rejected events can be deleted.
     */
    public function delete(User $user, Event $event): bool
    {
        if ($user->organizerProfile?->id !== $event->organizer_id) {
            return false;
        }

        return in_array($event->status, [
            EventStatus::Draft,
            EventStatus::Rejected,
        ], true);
    }

    /**
     * Determine whether the user can submit the event for review.
     * Requires: event is draft, EO is owner, EO is verified.
     */
    public function submit(User $user, Event $event): bool
    {
        $organizerProfile = $user->organizerProfile;

        if ($organizerProfile?->id !== $event->organizer_id) {
            return false;
        }

        if ($organizerProfile->verification_status !== VerificationStatus::Verified) {
            return false;
        }

        // Allow submitting both draft and rejected events
        return in_array($event->status, [
            EventStatus::Draft,
            EventStatus::Rejected,
        ], true);
    }

    /**
     * Determine whether the user can close event registration.
     * Only active events can be closed.
     */
    public function close(User $user, Event $event): bool
    {
        return $user->organizerProfile?->id === $event->organizer_id
            && $event->status === EventStatus::Active;
    }

    /**
     * Determine whether the user can mark the event as finished.
     * Active and closed events can be finished.
     */
    public function finish(User $user, Event $event): bool
    {
        return $user->organizerProfile?->id === $event->organizer_id
            && in_array($event->status, [
                EventStatus::Active,
                EventStatus::Closed,
            ], true);
    }

    /**
     * Determine whether the user can manage positions of the event.
     * Allowed for draft and rejected events.
     */
    public function managePositions(User $user, Event $event): bool
    {
        if ($user->organizerProfile?->id !== $event->organizer_id) {
            return false;
        }

        return in_array($event->status, [
            EventStatus::Draft,
            EventStatus::Rejected,
        ], true);
    }
}
