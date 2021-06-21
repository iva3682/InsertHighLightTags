<?php
namespace iva3682\InsertHighLightTags;

/**
 * TagPosition short summary.
 *
 * TagPosition description.
 *
 * @version 1.0
 * @author Иван
 */
class TagPosition
{
    private $tag = null;
    private $start = 0;
    private $end = 0;
    private $length = 0;

    public function __construct(Tag $tag, int $start, int $length) {
        if($length < 0) {
            throw new \Exception('Length have to positive.');
        }

        $this->tag = $tag;
        $this->start = $start;
        $this->length = $length;
        $this->end = $start + $length;
    }

    public function getTag(): Tag { return $this->tag; }
    public function getStart(): int { return $this->start; }
    public function getLength(): int { return $this->length; }
    public function getEnd(): int { return $this->end; }

    public function setStart(int $start) {
        $this->start = $start;
        $this->end = $start + $this->length;
    }
}