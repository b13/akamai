<?php
declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "akamai" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\Akamai\Provider;

use B13\Akamai\AkamaiClientFactory;
use B13\Akamai\AkamaiApi;
use B13\Proxycachemanager\Provider\ProxyProviderInterface;
use Psr\Http\Message\RequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Uses Akamai's Guzzle Client Wrapper
 *
 * Ensure to set the appropriate Akamai configuration via .edgerc or ENV variables
 */
class AkamaiProxyProvider implements ProxyProviderInterface
{
    /**
     * @var AkamaiApi|bool|null
     */
    protected $api;

    public function shouldRequestBeMarkedAsCached(RequestInterface $request): bool
    {
        return true;
    }

    public function flushCacheForUrls(array $urls): void
    {
        if (!$this->isActive()) {
            return;
        }
        if (empty($urls)) {
            return;
        }
        $this->api->invalidateUrls($urls);
    }

    public function flushAllUrls($urls = []): void
    {
        if (!$this->isActive()) {
            return;
        }
        $allSites = GeneralUtility::makeInstance(SiteFinder::class)->getAllSites();
        $cpCodeFound = false;
        foreach ($allSites as $site) {
            $cpCode = $site->getConfiguration()['settings']['cdn']['akamai_cpcode'] ?? null;
            if ($cpCode !== null && !empty($cpCode)) {
                if ($site->getConfiguration()['settings']['cdn']['akamai_auth_section'] ?? false) {
                    $api = $this->getAkamaiApi($site->getConfiguration()['cdn']['akamai_auth_section']);
                    if ($api !== null) {
                        $api->invalidateByCpCode((string)$cpCode);
                        $cpCodeFound = true;
                    }
                } else {
                    $this->api->invalidateByCpCode((string)$cpCode);
                    $cpCodeFound = true;
                }
            }
        }
        // No site was configured for a valid cpcode, let's flush all URLs given
        if ($cpCodeFound === false && !empty($urls)) {
            $this->api->invalidateUrls($urls);
        }
    }

    public function isActive(): bool
    {
        if ($this->api === null) {
            $this->api = $this->getAkamaiApi() ?? false;
        }
        return $this->api !== false;
    }

    protected function getAkamaiApi($section = 'default'): ?AkamaiApi
    {
        $client = AkamaiClientFactory::create(GeneralUtility::makeInstance(ExtensionConfiguration::class), $section);
        if ($client !== null) {
            return GeneralUtility::makeInstance(AkamaiApi::class, $client);
        }
        return null;
    }

}
