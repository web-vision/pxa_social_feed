<?php

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;

/**
 * Class TwitterFeedUpdater
 * @package Pixelant\PxaSocialFeed\Feed\Update
 */
class TwitterFeedUpdater extends BaseUpdater
{

    /**
     * Create/Update feed items
     *
     * @param FeedSourceInterface $source
     */
    public function update(FeedSourceInterface $source)
    {
        $items = $source->load();

        foreach ($items as $rawData) {
            $feedItem = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id_str'],
                $source->getConfiguration()->getStorage()
            );

            if ($feedItem === null) {
                $feedItem = $this->createFeedItem($rawData, $source->getConfiguration());
            }

            $this->updateFeedItem($feedItem, $rawData);

            // Call hook
            $this->emitSignal('beforeUpdateTwitterFeed', [$feedItem, $rawData, $source->getConfiguration()]);

            $this->addOrUpdateFeedItem($feedItem);
        }
    }

    /**
     * Create new twitter feed
     *
     * @param array $rawData
     * @param Configuration $configuration
     * @return Feed
     */
    protected function createFeedItem(array $rawData, Configuration $configuration)
    {
        $feedItem = $this->objectManager->get(Feed::class);
        $date = new \DateTime($rawData['created_at']);

        $feedItem->setPostDate($date);
        $feedItem->setPostUrl(
            'https://twitter.com/' . $configuration->getSocialId() . '/status/' . $rawData['id_str']
        );
        $feedItem->setConfiguration($configuration);
        $feedItem->setExternalIdentifier($rawData['id_str']);
        $feedItem->setPid($configuration->getStorage());
        $feedItem->setType(Token::TWITTER);

        return $feedItem;
    }

    /**
     * Update feed item properties with raw data
     *
     * @param Feed $feedItem
     * @param array $rawData
     * @return void
     */
    protected function updateFeedItem(Feed $feedItem, array $rawData)
    {
        // Update text
        $text = $rawData['full_text'] ?: $rawData['text'] ?: '';
        if ($feedItem->getMessage() != $text) {
            $feedItem->setMessage($this->encodeMessage($text));
        }

        // Media
        $image = isset($rawData['entities']['media'][0]['media_url_https']) ? $rawData['entities']['media'][0]['media_url_https'] : '';
        if ($feedItem->getImage() != $image) {
            $feedItem->setImage($image);
        }

        if (isset($rawData['retweeted_status']['favorite_count'])) {
            $likes = (int)$rawData['retweeted_status']['favorite_count'];
        } elseif (isset($rawData['favorite_count'])) {
            $likes = (int)$rawData['favorite_count'];
        } else {
            $likes = 0;
        }

        if ($likes != $feedItem->getLikes()) {
            $feedItem->setLikes($likes);
        }
    }
}
