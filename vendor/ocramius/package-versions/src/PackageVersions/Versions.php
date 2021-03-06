<?php

declare(strict_types=1);

namespace PackageVersions;

use OutOfBoundsException;

/**
 * This class is generated by ocramius/package-versions, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
final class Versions
{
    public const ROOT_PACKAGE_NAME = 'codeigniter/framework';
    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    public const VERSIONS          = array (
  'chriskacerguis/codeigniter-restserver' => '3.1.1@6ced70d12c7e8d207eaded3f208a201330950b57',
  'eluceo/ical' => '0.16.0@97da0d94c9716e65c141066a2d96aa098379721b',
  'firebase/php-jwt' => 'v5.2.0@feb0e820b8436873675fd3aca04f3728eb2185cb',
  'google/apiclient' => 'v2.4.1@1fdfe942f9aaf3064e621834a5e3047fccb3a6da',
  'google/apiclient-services' => 'v0.134@4237f6725a48761701101e26e4575ddb053b5d80',
  'google/auth' => 'v1.8.0@c7b295feb248f138f462a1e6b7d635e4244204c5',
  'guzzlehttp/guzzle' => '6.5.3@aab4ebd862aa7d04f01a4b51849d657db56d882e',
  'guzzlehttp/promises' => 'v1.3.1@a59da6cf61d80060647ff4d3eb2c03a2bc694646',
  'guzzlehttp/psr7' => '1.6.1@239400de7a173fe9901b9ac7c06497751f00727a',
  'monolog/monolog' => '2.0.2@c861fcba2ca29404dc9e617eedd9eff4616986b8',
  'phpmailer/phpmailer' => 'v6.1.5@a8bf068f64a580302026e484ee29511f661b2ad3',
  'phpseclib/phpseclib' => '2.0.27@34620af4df7d1988d8f0d7e91f6c8a3bf931d8dc',
  'psr/cache' => '1.0.1@d11b50ad223250cf17b86e38383413f5a6764bf8',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.3@0f73288fd15629204f9d42b7055f72dacbe811fc',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'renanbr/bibtex-parser' => '2.0.2@dded1a67b559e4bd4cdc86e79cfe056e2bdc4d0d',
  'symfony/polyfill-intl-idn' => 'v1.15.0@47bd6aa45beb1cd7c6a16b7d1810133b728bdfcf',
  'symfony/polyfill-mbstring' => 'v1.16.0@a54881ec0ab3b2005c406aed0023c062879031e7',
  'symfony/polyfill-php72' => 'v1.15.0@37b0976c78b94856543260ce09b460a7bc852747',
  'amphp/amp' => 'v2.4.4@1e58d53e4af390efc7813e36cd215bd82cba4b06',
  'amphp/byte-stream' => 'v1.7.3@b867505edb79dda8f253ca3c3a2bbadae4b16592',
  'composer/semver' => '1.5.1@c6bea70230ef4dd483e6bbcab6005f682ed3a8de',
  'composer/xdebug-handler' => '1.4.1@1ab9842d69e64fb3a01be6b656501032d1b78cb7',
  'doctrine/annotations' => '1.10.2@b9d758e831c70751155c698c2f7df4665314a1cb',
  'doctrine/lexer' => '1.2.0@5242d66dbeb21a30dd8a3e66bf7a73b66e05e1f6',
  'felixfbecker/advanced-json-rpc' => 'v3.1.1@0ed363f8de17d284d479ec813c9ad3f6834b5c40',
  'felixfbecker/language-server-protocol' => 'v1.4.0@378801f6139bb74ac215d81cca1272af61df9a9f',
  'friendsofphp/php-cs-fixer' => 'v2.16.3@83baf823a33a1cbd5416c8626935cf3f843c10b0',
  'netresearch/jsonmapper' => 'v2.1.0@e0f1e33a71587aca81be5cffbb9746510e1fe04e',
  'nikic/php-parser' => 'v4.4.0@bd43ec7152eaaab3bd8c6d0aa95ceeb1df8ee120',
  'ocramius/package-versions' => '1.8.0@421679846270a5772534828013a93be709fb13df',
  'openlss/lib-array2xml' => '1.0.0@a91f18a8dfc69ffabe5f9b068bc39bb202c81d90',
  'paragonie/random_compat' => 'v9.99.99@84b4dfb120c6f9b4ff7b3685f9b8f1aa365a0c95',
  'php-cs-fixer/diff' => 'v1.3.0@78bb099e9c16361126c86ce82ec4405ebab8e756',
  'phpdocumentor/reflection-common' => '2.1.0@6568f4687e5b41b054365f9ae03fcb1ed5f2069b',
  'phpdocumentor/reflection-docblock' => '5.1.0@cd72d394ca794d3466a3b2fc09d5a6c1dc86b47e',
  'phpdocumentor/type-resolver' => '1.1.0@7462d5f123dfc080dfdf26897032a6513644fc95',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/event-dispatcher' => '1.0.0@dbefd12671e8a14ec7f180cab83036ed26714bb0',
  'sebastian/diff' => '4.0.1@3e523c576f29dacecff309f35e4cc5a5c168e78a',
  'squizlabs/php_codesniffer' => '3.5.5@73e2e7f57d958e7228fce50dc0c61f58f017f9f6',
  'symfony/console' => 'v5.0.8@5fa1caadc8cdaa17bcfb25219f3b53fe294a9935',
  'symfony/event-dispatcher' => 'v5.0.8@24f40d95385774ed5c71dbf014edd047e2f2f3dc',
  'symfony/event-dispatcher-contracts' => 'v2.0.1@af23c2584d4577d54661c434446fb8fbed6025dd',
  'symfony/filesystem' => 'v5.0.8@7cd0dafc4353a0f62e307df90b48466379c8cc91',
  'symfony/finder' => 'v5.0.8@600a52c29afc0d1caa74acbec8d3095ca7e9910d',
  'symfony/options-resolver' => 'v5.0.8@3707e3caeff2b797c0bfaadd5eba723dd44e6bf1',
  'symfony/polyfill-ctype' => 'v1.15.0@4719fa9c18b0464d399f1a63bf624b42b6fa8d14',
  'symfony/polyfill-php70' => 'v1.15.0@2a18e37a489803559284416df58c71ccebe50bf0',
  'symfony/polyfill-php73' => 'v1.15.0@0f27e9f464ea3da33cbe7ca3bdf4eb66def9d0f7',
  'symfony/process' => 'v5.0.8@3179f68dff5bad14d38c4114a1dab98030801fd7',
  'symfony/service-contracts' => 'v2.0.1@144c5e51266b281231e947b51223ba14acf1a749',
  'symfony/stopwatch' => 'v5.0.8@a1d86d30d4522423afc998f32404efa34fcf5a73',
  'vimeo/psalm' => '3.11.2@d470903722cfcbc1cd04744c5491d3e6d13ec3d9',
  'webmozart/assert' => '1.8.0@ab2cb0b3b559010b75981b1bdce728da3ee90ad6',
  'webmozart/glob' => '4.1.0@3cbf63d4973cf9d780b93d2da8eec7e4a9e63bbe',
  'webmozart/path-util' => '2.3.0@d939f7edc24c9a1bb9c0dee5cb05d8e859490725',
  'codeigniter/framework' => 'dev-master@b29e79ecc77f7d841a3785b8f4cd36aec3301423',
);

    private function __construct()
    {
    }

    /**
     * @throws OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     * @psalm-pure
     */
    public static function getVersion(string $packageName) : string
    {
        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }
}
