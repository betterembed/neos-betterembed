<?php
namespace BetterEmbed\NeosEmbed;

use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\ContentRepository\Domain\Model\Node;
use BetterEmbed\NeosEmbed\Service\EmbedService;

class Package extends BasePackage
{
    /**
     * @param Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(Node::class, 'nodeUpdated', EmbedService::class, 'nodeUpdated', false);
        $dispatcher->connect(Node::class, 'nodeRemoved', EmbedService::class, 'nodeRemoved', false);
    }
}
