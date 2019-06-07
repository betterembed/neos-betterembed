<?php
namespace BetterEmbed\NeosEmbed\Aspect\Runtime;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Service\Context;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use BetterEmbed\NeosEmbed\Domain\Repository\BetterEmbedRepository;
use BetterEmbed\NeosEmbed\Service\NodeService;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Fusion\Core\Runtime;
use Neos\Utility\ObjectAccess;

/**
 * @Flow\Aspect
 */
class FusionRuntimeAspect
{
    /**
     * @Flow\Inject
     * @var NodeService
     */
    protected $nodeService;

    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @var Context
     */
    protected $liveContext;

    public function initializeObject()
    {
        $this->liveContext = $this->contextFactory->create(['workspaceName' => 'live']);
    }

    /**
     * @param JoinPointInterface $joinPoint
     * @Flow\Before("method(Neos\Fusion\Core\Runtime->pushContextArray())")
     *
     * @return void
     */
    public function extendContextArrayWithBetterEmbedRootNode(JoinPointInterface $joinPoint)
    {
        $contextArray = $joinPoint->getMethodArgument('contextArray');
        if (isset($contextArray['node']) && $contextArray['node'] instanceof NodeInterface) {
            $contextArray[BetterEmbedRepository::BETTER_EMBED_ROOT_NODE_NAME] = $this->nodeService->findOrCreateBetterEmbedRootNode($this->liveContext);
            $joinPoint->setMethodArgument('contextArray', $contextArray);
        }
    }

    /**
     * @param JoinPointInterface $joinPoint
     * @Flow\AfterReturning("method(Neos\Fusion\Core\Runtime->pushContext(key == 'node'))")
     *
     * @return void
     */
    public function extendContextWithBetterEmbedRootNode(JoinPointInterface $joinPoint)
    {
        /** @var Runtime $runtime */
        $runtime = $joinPoint->getProxy();

        $renderingStack = ObjectAccess::getProperty($runtime, 'renderingStack', true);
        $contextArray = array_pop($renderingStack);

        $contextArray[BetterEmbedRepository::BETTER_EMBED_ROOT_NODE_NAME] = $this->nodeService->findOrCreateBetterEmbedRootNode($this->liveContext);

        $renderingStack[] = $contextArray;
        ObjectAccess::setProperty($runtime, 'renderingStack', $renderingStack);
    }
}
