<?php
namespace BetterEmbed\NeosEmbed\Service;

use Neos\Flow\Annotations as Flow;
use BetterEmbed\NeosEmbed\Domain\Dto\BetterEmbedRecord;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\NodeTemplate;
use Neos\ContentRepository\Domain\Service\Context;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Flow\Log\SystemLoggerInterface;
use Neos\Flow\Utility\Algorithms;
use GuzzleHttp\Client;

/**
 *
 * @Flow\Scope("singleton")
 */
class EmbedService {

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
     * @var SystemLoggerInterface
     */
    protected $systemLogger;


    public function initializeObject()
    {
        $this->context = $this->contextFactory->create(['workspaceName' => 'live']);
    }

    public function nodeUpdated(NodeInterface $node, Workspace $targetWorkspace = null): void
    {

        $nodeType = $node->getNodeType()->getName();

        if ($nodeType === 'BetterEmbed.NeosEmbed:Item') {
            $url = $node->getProperty('url');

            if (!empty($url)) {
                $recordNode = $this->getByUrl($url);
                $node->setProperty('record', $recordNode);
            }

        }
    }


    /**
     * @param string $url
     * @return NodeInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Neos\ContentRepository\Exception\NodeTypeNotFoundException
     */
    public function getByUrl(string $url) : NodeInterface {

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function callService(string $url) {

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
     * @throws \Neos\ContentRepository\Exception\NodeTypeNotFoundException
     */
    private function createRecordNode(BetterEmbedRecord $record) {

        $nodeType = $this->nodeTypeManager->getNodeType('BetterEmbed.NeosEmbed:Record');

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
        $nodeTemplate->setProperty('embedHtml', $record->getEmbedHtml());
        $nodeTemplate->setProperty('authorName', $record->getAuthorName());
        $nodeTemplate->setProperty('authorUrl', $record->getAuthorUrl());
        $nodeTemplate->setProperty('authorImage', $record->getAuthorImage());
        $nodeTemplate->setProperty('publishedAt', $record->getPublishedAt());

        /** @var NodeInterface $node */
        $node = $this->nodeService->findOrCreateBetterEmbedRootNode($this->context)->createNodeFromTemplate($nodeTemplate);

        return $node;
    }

}

