<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DeployGitInfoRuntime implements RuntimeExtensionInterface
{
    private const INIT_COMMIT = 'a9f8b20d780a11c586f11fc29e74a8c3fe01eab7';

    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {}

    public function getDeployGitInfo(): string
    {
        /** @var string $projectDir */
        $projectDir = $this->parameterBag->get('kernel.project_dir');

        $filePath = $projectDir . '/git-info.txt';

        if (file_exists($filePath)) {
            if (false === $deployGitInfo = file_get_contents($filePath)) {
                return self::INIT_COMMIT;
            }

            return trim($deployGitInfo);
        }

        return self::INIT_COMMIT;
    }
}
