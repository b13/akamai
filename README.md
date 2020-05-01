# Akamai CDN Adapter for TYPO3

When TYPO3 is behind Akamai's EdgeGrid CDN, this extension is a perfect companion
for you.

This extension hides the complexity for Akamai's EdgeGrid API to purge caches.

## Installation

As EXT:akamai is using some PHP libraries, this extension is currently only useful
when running TYPO3 in composer mode. You can install this extension by using composer:

    composer req b13/akamai

## Usage

By default, EXT:akamai ships with a `akamai:purge` CLI command to purge a content provider
group (CP) or a specific URL. TYPO3 is using the graceful "invalidate" endpoints.

It is possible to purge a single or multiple URLs

    ./vendor/bin/typo3 akamai:purge --url=https://example.com/page1 --url=https://example.com/page2

or purge a whole Content Provider by its CP code

    ./vendor/bin/typo3 akamai:purge --url=https://example.com/page1 --url=https://example.com/page2

EXT:akamai chooses the network by determining the TYPO3 Context and only uses the
production network if TYPO3 Context is set to Production. However, this can be overridden
by a `--network=staging` or `--network=production` setting.

## Integration into TYPO3 Backend

EXT:akamai can be used in conjunction with TYPO3's Proxy Cache Manager Extension.

Using the Akamai Adapter for EXT:proxycachemanager flushes page caches directly
when modifying a page. This is perfect if you're dealing with akamai configuration
that not just caches your static assets but also your pages.

For this, ensure to set the class `\B13\Akamai\Provider\AkamaiProxyProvider` in
the settings of EXT:proxycachemanager.

## Configuration

### Credentials

Akamai uses a `.edgerc` credentials file, which currently
request to be in TYPO3's main directory (project path, also where
your composer file resides).

This file might look like this:

    [default]
    client_secret = xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
    host = xxxxx.purge.akamaiapis.net
    access_token = xxxxx
    client_token = xxxxx

It is also possible to use Environment variables directly.

    AKAMAI_DEFAULT_CLIENT_SECRET = xxxxx
    AKAMAI_DEFAULT_HOST = xxxxx.purge.akamaiapis.net
    AKAMAI_DEFAULT_ACCESS_TOKEN = xxxxx
    AKAMAI_DEFAULT_CLIENT_TOKEN = xxxxx

Use the Extension setting `configType` to choose between the Environment
and the `.edgerc` mode.

Please note that the host never contains the URL scheme - no matter
what configuration type you're choosing.

The default section can be configured as well, in case your installation has
multiple endpoints at Akamai.

### Using multiple sections / CDN endpoints in Site Configuration

It is possible to define the CP code by site when using the integration of
`EXT:proxycachemanager`. Ensure to set this in your TYPO3 site `config.yaml`:

    settings:
      cdn:
        akamai_cpcode: "12345"
        akamai_auth_section: "superbowl_campaign"



## License

The extension is licensed under GPL v2+, same as the TYPO3 Core. For details see the LICENSE file in this repository.

## Open Issues

If you find an issue, feel free to create an issue on GitHub or a pull request.

## Credits

This extension was created by [Benni Mack](https://github.com/bmack) in 2020 for [b13 GmbH](https://b13.com).

[Find more TYPO3 extensions we have developed](https://b13.com/useful-typo3-extensions-from-b13-to-you) that help us deliver value in client projects. As part of the way we work, we focus on testing and best practices to ensure long-term performance, reliability, and results in all our code.
