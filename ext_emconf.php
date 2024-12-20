<?php
/*
 * This file is part of TYPO3 CMS-based extension "akamai" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Akamai CDN Adapter',
    'description' => 'Akamai CDN Adapter for TYPO3 allows to flush CDN caches related to a TYPO3 installation',
    'category' => 'plugin',
    'author' => 'Benjamin Mack',
    'author_email' => 'typo3@b13.com',
    'state' => 'stable',
    'author_company' => 'b13 GmbH',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-12.9.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
            'proxycachemanager' => '*'
        ],
    ],
];
