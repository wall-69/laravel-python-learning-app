<?php

namespace App\Policies;

use App\Enums\LectureStatus;
use App\Models\Lecture;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LecturePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function show(?User $user, Lecture $lecture)
    {
        // Anyone can see public lectures
        if ($lecture->status == LectureStatus::PUBLIC->value) {
            return true;
        }

        // Draft lectures are visibile only to admins
        return $user?->admin ?? Response::denyAsNotFound();
    }
}
