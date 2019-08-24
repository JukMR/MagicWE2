<?php

declare(strict_types=1);

namespace xenialdan\MagicWE2\task\action;

use pocketmine\block\Block;
use xenialdan\MagicWE2\helper\AsyncChunkManager;
use xenialdan\MagicWE2\helper\Progress;
use xenialdan\MagicWE2\selection\Selection;

class SetBiomeAction extends TaskAction
{
    public $addRevert = false;
    private $biomeId;

    public function __construct(int $biomeId)
    {
        $this->biomeId = $biomeId;
    }

    public static function getName(): string
    {
        return "Set biome";
    }

    /**
     * @param string $sessionUUID
     * @param Selection $selection
     * @param AsyncChunkManager $manager
     * @param null|int $changed
     * @param Block[] $newBlocks
     * @param Block[] $blockFilter
     * @param Block[] $oldBlocks blocks before the change
     * @return \Generator|Progress
     * @throws \Exception
     */
    public function execute(string $sessionUUID, Selection $selection, AsyncChunkManager $manager, ?int &$changed, array $newBlocks, array $blockFilter, array &$oldBlocks = []): \Generator
    {
        $changed = 0;
        $oldBlocks = [];
        $count = null;
        foreach (($all = $selection->getShape()->getLayer($manager)) as $vec2) {
            if (is_null($count)) $count = count($all);
            $manager->getChunk($vec2->x >> 4, $vec2->y >> 4)->setBiomeId($vec2->x % 16, $vec2->y % 16, $this->biomeId);
            $changed++;
            $progress = $changed / $count;
            yield new Progress($progress, "Changed Biome for $changed/$count blocks");
        }
    }
}