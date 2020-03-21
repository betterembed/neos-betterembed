<?php

namespace BetterEmbed\NeosEmbed\Service;

use GuzzleHttp\Exception\GuzzleException;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\Exception\NodeException;
use Neos\ContentRepository\Exception\NodeTypeNotFoundException;
use Neos\Flow\Annotations as Flow;
use BetterEmbed\NeosEmbed\Domain\Dto\BetterEmbedRecord;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\NodeTemplate;
use Neos\ContentRepository\Domain\Service\Context;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\Utility\Algorithms;
use GuzzleHttp\Client;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\Media\Domain\Strategy\AssetModelMappingStrategyInterface;

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


    public function initializeObject()
    {
        $this->context = $this->contextFactory->create(['workspaceName' => 'live']);
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
                $recordNode = $this->getByUrl($url);
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

            if (!empty($url)) {
                $recordNode = $this->getByUrl($url);
                $this->nodeService->removeEmbedNode($recordNode);
            }
        }
    }

    /**
     * @param string $url
     * @return NodeInterface
     * @throws GuzzleException
     * @throws NodeTypeNotFoundException
     */
    public function getByUrl(string $url): NodeInterface
    {

        /** @var NodeInterface $record */
        $node = $this->nodeService->findRecordByUrl($this->context->getRootNode(), $url);

        if ($node == null) {
            $record = $this->callService($url);
            $node = $this->createRecordNode($record);
        }

        return $node;
    }


    /**
     * @param string $url
     * @return BetterEmbedRecord
     * @throws GuzzleException
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

        $asset = preg_replace('/(^.*\.(jpg|jpeg|png|gif)).*$/', '$1', $record->getThumbnailUrl());
        $extension = preg_replace('/^.*\.(jpg|jpeg|png|gif)$/', '$1', $asset);

        $resource = $this->resourceManager->importResource($asset);

        /** @var Image $resourceObj */
        $image = new Image($resource);
        $image->getResource()->setFilename(md5($record->getUrl()) . '.' . $extension);
        $image->getResource()->setMediaType('image/jpeg');
        $this->assetRepository->add($image);

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
