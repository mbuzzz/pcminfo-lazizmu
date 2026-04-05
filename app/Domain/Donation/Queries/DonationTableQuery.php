<?php

declare(strict_types=1);

namespace App\Domain\Donation\Queries;

use App\Domain\Donation\Models\Donation;
use Illuminate\Database\Eloquent\Builder;

final class DonationTableQuery
{
    public function build(): Builder
    {
        return Donation::query()
            ->with(['campaign', 'donor', 'verifier', 'verifications'])
            ->latest();
    }
}
