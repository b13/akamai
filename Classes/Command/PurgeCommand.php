<?php
declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "akamai" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\Akamai\Command;

use B13\Akamai\AkamaiClientFactory;
use B13\Akamai\AkamaiApi;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Generic Purge CLI Command to invalidate a set of URLs
 * or a whole Content Provider. The latter might be useful for deployments.
 *
 * Use
 *  vendor/bin/typo3 akamai:purge -n production -cp 123456
 * for invalidating by CP Code.
 *
 * Use
 *  vendor/bin/typo3 akamai:purge -n production -url https://example.com/my-page/
 * for invalidating by URL
 *
 * See https://developer.akamai.com/api/core_features/fast_purge/v3.html
 */
class PurgeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Purge Akamai caches')
            ->addOption(
                'network',
                '',
                InputOption::VALUE_REQUIRED,
                'Network to purge caches (staging or production). If not set, this option is based on the TYPO3_CONTEXT environment variable.',
                ''
            )
            ->addOption(
                'cpcode',
                'cp',
                InputOption::VALUE_REQUIRED,
                'Useful if all caches of a specific CP code should be invalidated',
                ''
            )
            ->addOption(
                'url',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'A list of absolute URLs to purge. Cannot be used in conjunction with "content-provider" obviously',
                []
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $client = AkamaiClientFactory::create(GeneralUtility::makeInstance(ExtensionConfiguration::class));
        if ($client === null) {
            $io->error('Akamai is not configured, aborting.');
            return 1;
        }

        $cpCode = $input->getOption('cpcode');
        $urls = $input->getOption('url');

        $akamai = new AkamaiApi($client);

        $network = strtolower($input->getOption('network'));
        if ($network !== '') {
            $akamai->setNetwork($network);
        }

        if (!$io->isQuiet()) {
            $io->title('Invalidating cache via POST request');
        }
        try {
            if ($cpCode !== '') {
                $response = $akamai->invalidateByCpCode($cpCode);
            } else {
                $response = $akamai->invalidateUrls($urls);
            }
        } catch (ClientException $e) {
            $io->error(
                [
                    'An error occurred while purging caches',
                    (string)$e->getResponse()->getBody()->getContents()
                ]
            );
            return 1;
        }
        if ($response && $response->getStatusCode() < 300) {
            if (!$io->isQuiet()) {
                $io->success('Done - status code ' . $response->getStatusCode());
                if ($output->isVerbose()) {
                    $io->section('Response details');
                    $io->text((string)$response->getBody()->getContents());
                }
            }
        } else {
            $io->error(
                [
                    'An error occurred while purging caches',
                    $response ? (string)$response->getBody()->getContents() : ''
                ]
            );
            return 1;
        }
        return 0;
    }
}
