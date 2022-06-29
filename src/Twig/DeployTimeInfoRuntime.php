<?php

declare(strict_types=1);

namespace App\Twig;

use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DeployTimeInfoRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {}

    public function getDeployTimeInfo(): string
    {
        /** @var string $projectDir */
        $projectDir = $this->parameterBag->get('kernel.project_dir');

        $filePath = $projectDir . '/deploy-time.txt';

        if (file_exists($filePath)) {
            if (false === $deployTimeInfo = file_get_contents($filePath)) {
                return (new DateTime())->format('Y-m-d_H:i:s');
            }

            return (new DateTime(trim($deployTimeInfo)))->format('Y-m-d_H:i:s');
        }

        return (new DateTime())->format('Y-m-d_H:i:s');
    }
}
