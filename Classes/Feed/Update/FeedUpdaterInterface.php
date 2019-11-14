<?php

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;

/**
 * Class FeedUpdaterInterface
 * @package Pixelant\PxaSocialFeed\Feed\Update
 */
interface FeedUpdaterInterface
{
    /**
     * Create/Update feed items
     *
     * @param FeedSourceInterface $source
     */
    public function update(FeedSourceInterface $source);

    /**
     * Persist all updates
     */
    public function persist();

    /**
     * Clean outdated records after persist
     *
     * @param Configuration $configuration Import configuration
     */
    public function cleanUp(Configuration $configuration);
}
