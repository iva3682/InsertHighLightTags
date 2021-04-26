<?php
namespace iva3682\InsertHighLightTags;

/**
 * ApplyTagPosition short summary.
 *
 * ApplyTagPosition description.
 *
 * @version 1.0
 * @author Иван
 */
class ApplyTagPosition
{
    private $tag = null;
    private $start = 0;
    private $end = 0;

    public function __construct(Tag $tag, int $start, int $end) {
        if($start > $end) {
            throw new \Exception('Start have to less or equal end.');
        }

        $this->tag = $tag;
        $this->start = $start;
        $this->end = $end;
    }

    public function getTag(): Tag { return $this->tag; }
    public function getStart(): int { return $this->start; }
    public function getEnd(): int { return $this->end; }

    public function setEnd(int $end) { $this->end = $end; }
}