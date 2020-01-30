<?php
namespace BetterEmbed\NeosEmbed\Service;

use BetterEmbed\NeosEmbed\Domain\Repository\BetterEmbedRepository;
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
use Neos\Flow\Persistence\PersistenceManagerInterface;

/**
 * @Flow\Scope("singleton")
 */
class NodeService
{

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
     * @param NodeInterface $node
     * @param string $url
     * @return \Traversable
     * @throws Exception
     */
    public function findRecordByUrl(NodeInterface $node, string $url) {

        $fq = new FlowQuery([$node]);

        /** @var \Traversable $result */
        $result = $fq->find(sprintf('[instanceof BetterEmbed.NeosEmbed:Record][url="%s"]', $url))->get(0);

        return $result;
    }

    public function removeEmbedNode(NodeInterface $node) {
        $node->setRemoved(true);
        if ($node->isRemoved()) {
            $this->nodeDataRepository->remove($node);
            return;
        }
    }


}
