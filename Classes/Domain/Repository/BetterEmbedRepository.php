<?php

namespace BetterEmbed\NeosEmbed\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeData;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;

/**
 * @Flow\Scope("singleton")
 */
class BetterEmbedRepository extends NodeDataRepository
{
    const ENTITY_CLASSNAME = NodeData::class;
    const BETTER_EMBED_ROOT_NODE_NAME = 'better-embeds';
}
