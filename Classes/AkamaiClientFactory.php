<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "akamai" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\Akamai;

use Akamai\Open\EdgeGrid\Authentication\Exception\ConfigException;
use Akamai\Open\EdgeGrid\Client;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Builds a client based on the system configuration.
 *
 * The client is then wrapped in the Akamai Api object. If the authentication credentials are not available,
 * no client is created.
 */
class AkamaiClientFactory
{
    public static function create(ExtensionConfiguration $extensionConfiguration = null, string $section = 'default'): ?Client
    {
        $extensionConfiguration = $extensionConfiguration ?? GeneralUtility::makeInstance(ExtensionConfiguration::class);
        try {
            $configType = static::getConfigType($extensionConfiguration);
            if ($configType === 'edgerc') {
                return Client::createFromEdgeRcFile($section, Environment::getProjectPath() . '.edgerc');
            }
            return Client::createFromEnv($section);
        } catch (ConfigException $e) {
            return null;
        }
    }

    protected static function getConfigType(ExtensionConfiguration $extensionConfiguration): string
    {
        return $extensionConfiguration->get('akamai', 'configType');
    }

}
