<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DeployInfoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('deploy_git_info', [DeployGitInfoRuntime::class, 'getDeployGitInfo']),
            new TwigFunction('deploy_time_info', [DeployTimeInfoRuntime::class, 'getDeployTimeInfo']),
        ];
    }
}
