<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('amount', [$this, 'formatAmount'])
        ];
    }

    public function formatAmount(int $amount): string
    {
        return number_format($amount / 100, 2, ',', '.') . ' €';
    }
}
