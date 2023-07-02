<?php

namespace BetterEmbed\NeosEmbed\Service;

use BetterEmbed\NeosEmbed\Domain\Repository\BetterEmbedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Neos\ContentRepository\Domain\Factory\NodeFactory;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\NodeTemplate;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;
use Neos\ContentRepository\Domain\Service\Context;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Eel\Exception;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Exception\IllegalObjectTypeException;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\AssetCollectionRepository;
use Neos\Media\Domain\Repository\TagRepository;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @Flow\Scope("singleton")
 */
class NodeService
{

    const ASSET_COLLECTION_TITLE = 'BetterEmbed';

    /**
     * @Flow\Inject
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * @Flow\Inject
     * @var BetterEmbedRepository
     */
    protected $betterEmbedRepository;

    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @Flow\Inject
     * @var NodeTypeManager
     */
    protected $nodeTypeManager;

    /**
     * @Flow\Inject
     * @var NodeDataRepository
     */
    protected $nodeDataRepository;

    /**
     * @var NodeInterface
     */
    protected $betterEmbedRootNode;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var AssetCollectionRepository
     */
    protected $assetCollectionRepository;

    /**
     * @Flow\Inject
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @param Context $context
     *
     * @return NodeInterface
     */
    public function findOrCreateBetterEmbedRootNode(Context $context)
    {

        if ($this->betterEmbedRootNode instanceof NodeInterface) {
            return $this->betterEmbedRootNode;
        }

        $betterEmbedRootNodeData = $this->betterEmbedRepository->findOneByPath('/' . BetterEmbedRepository::BETTER_EMBED_ROOT_NODE_NAME, $context->getWorkspace());

        if ($betterEmbedRootNodeData !== null) {
            $this->betterEmbedRootNode = $this->nodeFactory->createFromNodeData($betterEmbedRootNodeData, $context);

            return $this->betterEmbedRootNode;
        }

        $nodeTemplate = new NodeTemplate();
        $nodeTemplate->setNodeType($this->nodeTypeManager->getNodeType('unstructured'));
        $nodeTemplate->setName(BetterEmbedRepository::BETTER_EMBED_ROOT_NODE_NAME);

        $rootNode = $context->getRootNode();

        $this->betterEmbedRootNode = $rootNode->createNodeFromTemplate($nodeTemplate);
        $this->betterEmbedRepository->persistEntities();

        return $this->betterEmbedRootNode;
    }

    /**
     * @param string $title
     * @return AssetCollection
     * @throws IllegalObjectTypeException
     */
    public function findOrCreateBetterEmbedAssetCollection(string $title = self::ASSET_COLLECTION_TITLE): AssetCollection
    {
        /** @var AssetCollection $assetCollection */
        $assetCollection = $this->assetCollectionRepository->findByTitle($title)->getFirst();

        if ($assetCollection === null) {
            $assetCollection = new AssetCollection($title);

            $this->assetCollectionRepository->add($assetCollection);
            $this->persistenceManager->allowObject($assetCollection);
        }

        return $assetCollection;
    }

    /**
     * @param string $label
     * @return Tag
     * @throws IllegalObjectTypeException
     */
    public function findOrCreateBetterEmbedTag(string $label, ArrayCollection $assetCollections): Tag
    {
        /** @var Boolean $doCreateTag */
        $doCreateTag = false;

        /** @var Tag $tag */
        $tag = $this->tagRepository->findByLabel($label)->getFirst();

        if ($tag === null) { // check if tag exists
            return $this->createTag($label, $assetCollections);
        }

        /** @var AssetCollection $collection */
        foreach ($tag->getAssetCollections() as $collection) { //check if tag has the accoring asset collection assigned
            if ($collection->getTitle() === self::ASSET_COLLECTION_TITLE) {
                return $tag;
            }
        }

        return $this->createTag($label, $assetCollections); // create tag anyway
    }

    /**
     * @param string $label
     * @param ArrayCollection $assetCollections
     * @return Tag
     * @throws IllegalObjectTypeException
     */
    private function createTag(string $label, ArrayCollection $assetCollections): Tag
    {
        $tag = new Tag($label);
        $tag->setAssetCollections($assetCollections);

        $this->tagRepository->add($tag);
        $this->persistenceManager->allowObject($tag);

        return $tag;
    }

    /**
     * @param NodeInterface $node
     * @param string $url
     * @return \Traversable
     * @throws Exception
     */
    public function findRecordByUrl(NodeInterface $node, string $url)
    {

        $fq = new FlowQuery([$node]);

        /** @var \Traversable $result */
        $result = $fq->find(sprintf('[instanceof BetterEmbed.NeosEmbed:Record][url="%s"]', $url))->get(0);

        return $result;
    }

    public function removeEmbedNode(NodeInterface $node)
    {
        $node->setRemoved(true);
        if ($node->isRemoved()) {
            $this->nodeDataRepository->remove($node);
            return;
        }
    }
}
