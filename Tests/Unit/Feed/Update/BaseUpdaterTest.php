<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed\Update;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\Feed\Update\BaseUpdater;
use Pixelant\PxaSocialFeed\Tests\Unit\CreateMock;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class BaseUpdaterTest
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Feed\Update
 */
class BaseUpdaterTest extends UnitTestCase
{
    use CreateMock;

    /**
     * @var BaseUpdater
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = $this->getAccessibleMock(BaseUpdater::class, ['update'], [], '', false);
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function addOrUpdateFeedItemSaveItemInFeedsStorage()
    {
        $feed = new Feed();
        $feedStorage = new ObjectStorage();

        $this->inject($this->subject, 'feedRepository', $this->createMockTrait(FeedRepository::class));
        $this->inject($this->subject, 'feeds', $feedStorage);

        $this->subject->_call('addOrUpdateFeedItem', $feed);

        $this->assertEquals(1, $feedStorage->count());
    }

    /**
     * @test
     */
    public function addOrUpdateFeedItemCallAddOnNewItem()
    {
        $feed = new Feed();
        $mockedRepository = $this->createMockTrait(FeedRepository::class);
        $mockedRepository
            ->expects($this->once())
            ->method('add')
            ->with($feed);

        $this->inject($this->subject, 'feedRepository', $mockedRepository);
        $this->inject($this->subject, 'feeds', $this->createMockTrait(ObjectStorage::class));

        $this->subject->_call('addOrUpdateFeedItem', $feed);
    }

    /**
     * @test
     */
    public function addOrUpdateFeedItemCallUpdateOnExistingItem()
    {
        $feed = new Feed();
        $feed->_setProperty('uid', 1);

        $mockedRepository = $this->createMockTrait(FeedRepository::class);
        $mockedRepository
            ->expects($this->once())
            ->method('update')
            ->with($feed);

        $this->inject($this->subject, 'feedRepository', $mockedRepository);
        $this->inject($this->subject, 'feeds', $this->createMockTrait(ObjectStorage::class));

        $this->subject->_call('addOrUpdateFeedItem', $feed);
    }

    /**
     * @test
     */
    public function encodeMessageForSimpleStringReturnSameString()
    {
        $value = 'test string';

        $this->assertEquals($value, $this->subject->_call('encodeMessage', $value));
    }
}
