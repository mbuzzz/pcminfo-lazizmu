<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $amount,
        public string $currency = 'IDR',
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Money amount must be greater than or equal to zero.');
        }
    }

    public static function zero(string $currency = 'IDR'): self
    {
        return new self(0, $currency);
    }

    public function add(self $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->ensureSameCurrency($other);

        return new self(max(0, $this->amount - $other->amount), $this->currency);
    }

    private function ensureSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Money currency mismatch.');
        }
    }
}
