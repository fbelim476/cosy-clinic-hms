<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class PatientRepository extends BaseRepository
{
    public function __construct(Patient $model)
    {
        parent::__construct($model);
    }

    public function search(string $query, int $limit = 10): Collection
    {
        return $this->query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('mobile', 'like', "%{$query}%")
                    ->orWhere('patient_id', 'like', "%{$query}%");
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function findByMobile(string $mobile): ?Patient
    {
        return $this->query()->where('mobile', $mobile)->first();
    }
}
