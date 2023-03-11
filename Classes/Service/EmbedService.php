<?php

namespace BetterEmbed\NeosEmbed\Service;

use GuzzleHttp\Exception\GuzzleException;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\Exception\NodeException;
use Neos\ContentRepository\Exception\NodeTypeNotFoundException;
use Neos\Flow\Annotations as Flow;
use BetterEmbed\NeosEmbed\Domain\Dto\BetterEmbedRecord;
use Doctrine\Common\Collections\ArrayCollection;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\NodeTemplate;
use Neos\ContentRepository\Domain\Service\Context;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Flow\Exception;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\Utility\Algorithms;
use GuzzleHttp\Client;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\Media\Domain\Strategy\AssetModelMappingStrategyInterface;
use Neos\Neos\Domain\Service\NodeSearchService;

/**
 *
 * @Flow\Scope("singleton")
 */
class EmbedService
{

    /**
     * @var Context
     */
    protected $context;

    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @Flow\Inject
     * @var NodeService
     */
    protected $nodeService;

    /**
     * @Flow\Inject
     * @var NodeTypeManager
     */
    protected $nodeTypeManager;


    /**
     * @Flow\Inject
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @Flow\Inject
     * @var AssetModelMappingStrategyInterface
     */
    protected $mappingStrategy;

    /**
     * @var ArrayCollection
     */
    protected $assetCollections;

    /**
     * @Flow\Inject
     * @var NodeSearchService
     */
    protected $nodeSearchService;


    public function initializeObject()
    {
        $this->context = $this->contextFactory->create(['workspaceName' => 'live']);
        $this->assetCollections = new ArrayCollection([$this->nodeService->findOrCreateBetterEmbedAssetCollection()]);
    }

    /**
     * @param NodeInterface $node
     * @param Workspace|null $targetWorkspace
     * @throws GuzzleException
     * @throws NodeException
     * @throws NodeTypeNotFoundException
     */
    public function nodeUpdated(NodeInterface $node): void
    {

        if ($node->getNodeType()->isOfType('BetterEmbed.NeosEmbed:Mixin.Item')) {
            $url = $node->getProperty('url');

            if (!empty($url)) {
                $recordNode = $this->getByUrl($url, true);
                $node->setProperty('record', $recordNode);
            }
        }
    }

    /**
     * @param NodeInterface $node
     * @param Workspace|null $targetWorkspace
     * @throws GuzzleException
     * @throws NodeException
     * @throws NodeTypeNotFoundException
     */
    public function nodeRemoved(NodeInterface $node): void
    {

        if ($node->getNodeType()->isOfType('BetterEmbed.NeosEmbed:Mixin.Item')) {
            $url = $node->getProperty('url');

            if (!empty($url) && count($this->nodeSearchService->findByProperties(['url' => str_replace('"','',json_encode($url))], ['BetterEmbed.NeosEmbed:Mixin.Item'], $this->context)) <= 1) {
                $recordNode = $this->getByUrl($url);
                if ($recordNode) {
                    $this->nodeService->removeEmbedNode($recordNode);
                }
            }
        }
    }

    /**
     * @param string $url
     * @param bool $createIfNotFound
     * @return NodeInterface|null
     * @throws Exception
     * @throws GuzzleException
     * @throws NodeTypeNotFoundException
     * @throws \Neos\Eel\Exception
     */
    public function getByUrl(string $url, $createIfNotFound = false)
    {
        /** @var NodeInterface $record */
        $node = $this->nodeService->findRecordByUrl($this->context->getRootNode(), $url);

        if ($node == null && $createIfNotFound) {

            $urlParts = parse_url( $url );

            if(strstr($urlParts['host'], 'facebook')) {
                throw new Exception('Facebook URLs are not supported due GDPR consent gateway protection.');
            }

            if(strstr($urlParts['host'], 'instagram')) {
                throw new Exception('Instagram URLs are not supported due GDPR consent gateway protection.');
            }

            $record = $this->callService($url);
            $node = $this->createRecordNode($record);
        }

        return $node;
    }


    /**
     * @param string $url
     * @return BetterEmbedRecord
     * @throws GuzzleException
     * @throws \Exception
     */
    private function callService(string $url)
    {

        $client = new Client();
        $response = $client->request(
            'GET',
            'https://api.betterembed.com/api/v0/item',
            ['query' => ['url' => $url]]
        );

        $response = new BetterEmbedRecord(json_decode((string) $response->getBody()));

        return $response;
    }

    /**
     * @param BetterEmbedRecord $record
     * @return NodeInterface
     * @throws NodeTypeNotFoundException
     */
    private function createRecordNode(BetterEmbedRecord $record)
    {

        $assetOriginal = $record->getThumbnailUrl(); //original asset may have get parameters in the url
        $asset = preg_replace('/(^.*\.(jpg|jpeg|png|gif)).*$/', '$1', $assetOriginal); //asset witout get parametes for neos import
        $extension = preg_replace('/^.*\.(jpg|jpeg|png|gif)$/', '$1', $asset); // asset extension

        $image = null;
        if (filter_var($assetOriginal, FILTER_VALIDATE_URL)) {
            // If the $asset is the same as $extension then there was no matching extension in this case use the mime type defined by hosting server
            if ($asset === $extension) {
                $client = new Client();
                $mimeType = $client->head($asset)->getHeader('Content-Type')[0];
                $extension = str_replace('image/', '', str_replace('x-', '', $mimeType)); // account for image/png and image/x-png mimeTypes
            } else {
                $mimeType = 'image/' . $extension;
            }
            
            $resource = $this->resourceManager->importResource($assetOriginal);
            $tags = new ArrayCollection([$this->nodeService->findOrCreateBetterEmbedTag($record->getItemType(), $this->assetCollections)]);

            /** @var Image $image */
            $image = $this->assetRepository->findOneByResourceSha1($resource->getSha1());
            if ($image === null) {
                $image = new Image($resource);
                $image->getResource()->setFilename(md5($record->getUrl()) . '.' . $extension);
                $image->getResource()->setMediaType($mimeType);
                $image->setAssetCollections($this->assetCollections);
                $image->setTags($tags);
                $this->assetRepository->add($image);
            }
        }

        /** @var NodeType $nodeType */
        $nodeType = $this->nodeTypeManager->getNodeType('BetterEmbed.NeosEmbed:Record');

        /** @var NodeTemplate $nodeTemplate */
        $nodeTemplate = new NodeTemplate();
        $nodeTemplate->setNodeType($nodeType);
        $nodeTemplate->setName(Algorithms::generateUUID());
        $nodeTemplate->setProperty('url', $record->getUrl());
        $nodeTemplate->setProperty('itemType', $record->getItemType());
        $nodeTemplate->setProperty('title', $record->getTitle());
        $nodeTemplate->setProperty('body', $record->getBody());
        $nodeTemplate->setProperty('thumbnailUrl', $record->getThumbnailUrl());
        $nodeTemplate->setProperty('thumbnailContentType', $record->getThumbnailContentType());
        $nodeTemplate->setProperty('thumbnailContent', $record->getThumbnailContent());
        $nodeTemplate->setProperty('thumbnail', $image);
        $nodeTemplate->setProperty('embedHtml', $record->getEmbedHtml());
        $nodeTemplate->setProperty('authorName', $record->getAuthorName());
        $nodeTemplate->setProperty('authorUrl', $record->getAuthorUrl());
        $nodeTemplate->setProperty('authorImage', $record->getAuthorImage());
        $nodeTemplate->setProperty('publishedAt', $record->getPublishedAt());
        $nodeTemplate->setProperty('uriPathSegment', 'embed-' . random_int(0000000000, 9999999999));

        /** @var NodeInterface $node */
        $node = $this->nodeService->findOrCreateBetterEmbedRootNode($this->context)->createNodeFromTemplate($nodeTemplate);

        return $node;
    }
}
