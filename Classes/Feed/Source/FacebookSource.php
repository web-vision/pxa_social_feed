<?php

namespace Pixelant\PxaSocialFeed\Feed\Source;

/**
 * Class FacebookSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
class FacebookSource extends BaseFacebookSource
{
    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load()
    {
        $fb = $this->getConfiguration()->getToken()->getFb();
        $response = $fb->get(
            $this->generateEndPoint($this->getConfiguration()->getSocialId(), 'feed')
        );

        return $this->getDataFromResponse($response);
    }

    /**
     * Return fields for endpoint request
     *
     * @return array
     */
    protected function getEndPointFields()
    {
        return [
            'likes.summary(true).limit(0)',
            'message',
            'attachments',
            'created_time',
            'updated_time',
        ];
    }
}
