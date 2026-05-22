<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('CosyClinic-queue', function ($user) {
    return $user !== null;
});

Broadcast::channel('CosyClinic-dashboard', function ($user) {
    return $user !== null;
});

Broadcast::channel('CosyClinic-pharmacy', function ($user) {
    return $user->hasAnyRole(['pharmacist', 'super-admin']);
});

Broadcast::channel('CosyClinic-doctor.{doctorId}', function ($user, $doctorId) {
    if ($user->hasRole('super-admin')) {
        return true;
    }

    return $user->doctor?->id === (int) $doctorId;
});
