<?php

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Exception\BadResponseException;
use Pixelant\PxaSocialFeed\SignalSlot\EmitSignalTrait;
use TYPO3\CMS\Core\Http\HttpRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BaseSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
abstract class BaseSource implements FeedSourceInterface
{
    use EmitSignalTrait;

    /**
     * @var Configuration
     */
    protected $configuration = null;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Append endpoint url with get parameters based on fields
     *
     * @param string $url
     * @param array $fields
     * @return string
     */
    protected function addFieldsAsGetParametersToUrl($url, array $fields)
    {
        $url .= empty($fields) ? '' : ('?' . http_build_query($fields));

        return $url;
    }

    /**
     * Get request to api url
     *
     * @param string $url
     * @param array $additionalOptions
     * @return \HTTP_Request2_Response
     * @throws BadResponseException
     */
    protected function performApiGetRequest($url, $additionalOptions = [])
    {
        /** @var HttpRequest $httpRequest */
        $httpRequest = GeneralUtility::makeInstance(
            HttpRequest::class,
            $url,
            'GET'
        );

        if (isset($additionalOptions['headers']) && is_array($additionalOptions['headers'])) {
            foreach ($additionalOptions['headers'] as $header => $headerValue) {
                $httpRequest->setHeader([$header => $headerValue]);
            }
        }

        /** @var \HTTP_Request2_Response $response */
        $response = $httpRequest->send();

        if ($response->getStatus() === 200) {
            return $response;
        } else {
            $body = (string)$response->getBody();
            // @codingStandardsIgnoreStart
            throw new BadResponseException("Api request return status '{$response->getStatus()}' while trying to request '$url' with message '$body'", 1562910160643);
            // @codingStandardsIgnoreEnd
        }
    }
}
