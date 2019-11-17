<?php declare(strict_types=1);

namespace App\Services;

use Psr\Container\ContainerInterface;

class RssReaderFactory
{
    public function __invoke(ContainerInterface $container): RssReader
    {

        $config = $container->get('config')['news_rss'] ?? [];

        if (!isset($config['rss'])) {
            throw new \RuntimeException(
                'The rss configuration is missing'
            );
        }

        return new RssReader($config['rss']);
    }
}
