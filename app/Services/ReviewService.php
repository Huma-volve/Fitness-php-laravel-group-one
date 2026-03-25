<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewService
{
    protected $trainerId;
    protected $filters;

    public function __construct($trainerId, array $filters = [])
    {
        $this->trainerId = $trainerId;
        $this->filters = $filters;
    }

    public function getFilteredQuery()
    {
        return Review::with('user')
            ->where('trainer_id', $this->trainerId)
            ->username($this->filters['username'] ?? null)
            ->rating($this->filters['star'] ?? null)
            ->comment($this->filters['comment'] ?? null)
            ->dateFrom($this->filters['date_from'] ?? null)
            ->dateTo($this->filters['date_to'] ?? null);
    }

    public function getReviews($sortBy = 'date', $perPage = 10): LengthAwarePaginator
    {
        $query = $this->getFilteredQuery();

        if ($sortBy === 'rating') {
            $query->orderBy('rating', 'desc');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getStats(): array
    {
        $allReviews = $this->getFilteredQuery();

        return [
            'avaregeRating' => round($allReviews->avg('rating') ?: 0, 2),
            'totalReviews' => $allReviews->count(),
            'positiveReviews' => $allReviews->where('rating', '>=', 4)->count(),
            'totalcomments' => $allReviews->whereNotNull('comment')->count(),
        ];
    }
}
