<?php

/*
 * This file is part of TYPO3 CMS-based extension "akamai" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

return [
    'akamai:purge' => [
        'class' => \B13\Akamai\Command\PurgeCommand::class,
        'schedulable' => true,
    ]
];
