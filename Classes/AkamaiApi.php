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

use Akamai\Open\EdgeGrid\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Core\Environment;

/**
 * Does the actual API calls via Guzzle, requires a valid Client object.
 */
class AkamaiApi
{
    protected string $network = 'staging';
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        if (Environment::getContext()->isProduction()) {
            $this->network = 'production';
        }
    }

    /**
     * Usually "production" or "staging".
     *
     * @param string $network
     */
    public function setNetwork(string $network): void
    {
        $this->network = $network;
    }

    public function invalidateUrl(string $url): ?ResponseInterface
    {
        return $this->invalidateUrls([$url]);
    }

    public function invalidateUrls(array $urls): ?ResponseInterface
    {
        try {
            return $this->client->post(
                '/ccu/v3/invalidate/url/' . $this->network,
                ['json' => ['objects' => array_values($urls)]]
            );
        } catch (ClientException $e) {
            return null;
            // Fail silently
            // This usually happens if there are other domains / zones triggered which should not be flushed
        }
    }

    public function invalidateByCpCode(string $cpCode): ?ResponseInterface
    {
        try {
            return $this->client->post(
                '/ccu/v3/invalidate/cpcode/' . $this->network,
                ['json' => ['objects' => [$cpCode]]]
            );
        } catch (ClientException $e) {
            return null;
            // Fail silently
            // This usually happens if there are other domains / zones triggered which should not be flushed
        }
    }
}
