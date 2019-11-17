<?php declare(strict_types=1);


namespace App\Services;


use App\Models\News;
use Zend\Feed\Reader\Entry\EntryInterface;
use Zend\Feed\Reader\Reader;

class RssReader
{
    /**
     * @var string
     */
    private $url;

    /**
     * RssReader constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return News[]
     */
    public function getLastFive(): array
    {
        $limit = 5;
        $feeds = Reader::import($this->url);
        $news = [];
        /** @var EntryInterface $feed */
        foreach ($feeds as $feed) {
            if ($limit-- < 1) {
                break;
            }
            $newsEntity = new News();
            $newsEntity->setTitle($feed->getTitle());
            $newsEntity->setDescription($feed->getDescription());
            $newsEntity->setUri($feed->getLink());

            if (isset($feed->getEnclosure()->url)) {
                $newsEntity->setImageUrl($feed->getEnclosure()->url);
            }

            $news[] = $newsEntity;
        }
        return $news;
    }
}