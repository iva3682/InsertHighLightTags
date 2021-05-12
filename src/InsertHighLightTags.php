<?php
namespace iva3682\InsertHighLightTags;

/**
 * InsertHighLightTags short summary.
 *
 * InsertHighLightTags description.
 *
 * @version 1.0
 * @author Иван
 */
class InsertHighLightTags
{
    /**
     * Summary of $tagPositions
     * @var TagPosition[]
     */
    private $tagPositions = [];

    /**
     * Summary of $restoreItems
     * @var RestoreProductNameItem[]
     */
    private $restoreItems = [];

    public function __construct() {

    }

    public function addRestoreItem(RestoreProductNameItem $restoreProductNameItem) {
        $this->restoreItems[] = $restoreProductNameItem;
    }

    public function addTag(string $tagName, int $start, int $length) {
        $tag = new Tag($tagName);
        $tapPosition = new TagPosition($tag, $start, $length);

        $this->tagPositions[] = $tapPosition;
    }

    public function build(string $source) {
        usort($this->tagPositions, function (TagPosition $a, TagPosition $b) {
            if ($a->getStart() == $b->getStart()) {
                if ($a->getLength() == $b->getLength()) {
                    return 0;
                }

                return ($a->getLength() < $b->getLength()) ? 1 : -1;
            }

            return ($a->getStart() < $b->getStart()) ? -1 : 1;
        });

        foreach($this->tagPositions as $idx => $tagPosition) {
            $start = $tagPosition->getStart();

            $end = $start + $tagPosition->getLength() + $tagPosition->getTag()->getLengthOpen();

            $source = $this->mb_substr_replace($source, $tagPosition->getTag()->getOpen(), $start, 0);
            $source = $this->mb_substr_replace($source, $tagPosition->getTag()->getClose(), $end, 0);

            for($i = $idx + 1; $i < count($this->tagPositions); $i++) {
                $applyTagPosition = $this->tagPositions[$i];

                if($start <= $applyTagPosition->getStart()) {
                    if($applyTagPosition->getEnd() > $tagPosition->getEnd()) {
                        $applyTagPosition->setStart($applyTagPosition->getStart() + $tagPosition->getTag()->getLengthClose());
                    }

                    $applyTagPosition->setStart($applyTagPosition->getStart() + $tagPosition->getTag()->getLengthOpen());
                }
            }
        }

        return $source;
    }

    public function restore(string $model): string {
        usort($this->restoreItems, function (RestoreProductNameItem $a, RestoreProductNameItem $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return 0;
            }

            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        foreach($this->restoreItems as $restoreItem) {
            if(mb_strlen($model) < $restoreItem->getPosition()) {
                $model = str_pad($model, $restoreItem->getPosition() + $restoreItem->getLength());
            }

            $offRestore = ($restoreItem->getWrapCharLeft() == ' ' or $restoreItem->getWrapCharRight() == ' ') ? 0 : 1;
            $model = $this->mb_substr_replace($model, $restoreItem->getStringFill(), $restoreItem->getPosition(), $offRestore);

            foreach($this->tagPositions as $tagPositions) {
                if($tagPositions->getStart() >= $restoreItem->getPosition() and !$tagPositions->getIsAbsolute()) {
                    $tagPositions->setStart($tagPositions->getStart() + $restoreItem->getLength() - $offRestore);
                }
            }

            if($restoreItem->getPosition() and $restoreItem->getWrapCharLeft() != mb_substr($model, $restoreItem->getPosition() - 1, 1)) {
                $model = $this->mb_substr_replace($model, $restoreItem->getWrapCharLeft(), $restoreItem->getPosition(), 0);

                foreach($this->tagPositions as $tagPositions) {
                    if($tagPositions->getStart() >= $restoreItem->getPosition() and !$tagPositions->getIsAbsolute()) {
                        $tagPositions->setStart($tagPositions->getStart() + 1);
                    }
                }
            }

            $modelWrapRight = mb_substr($model, $restoreItem->getPosition() + $restoreItem->getLength(), 1);

            if($restoreItem->getPosition() + $restoreItem->getLength() + 1 <= mb_strlen($model) and $restoreItem->getWrapCharRight() != $modelWrapRight) {
                $model = $this->mb_substr_replace($model, $restoreItem->getWrapCharRight(), $restoreItem->getPosition() + $restoreItem->getLength(), 0);

                foreach($this->tagPositions as $tagPositions) {
                    if($tagPositions->getStart() >= $restoreItem->getPosition() + $restoreItem->getLength() and !$tagPositions->getIsAbsolute()) {
                        $tagPositions->setStart($tagPositions->getStart() + 1);
                    }
                }
            }
        }

        return $model;
    }

    public function extractTags(string $highlight, string $tagName, int $absoluteOffset = 0) {
        $tag = new Tag($tagName);

        $offset = 0;
        $tagPosition = [];

        while(($start = mb_strpos($highlight, $tag->getOpen(), $offset)) !== false) {
            $len = mb_strpos($highlight, $tag->getClose()) - $tag->getLengthOpen() - $start;

            $offset = $start + 1;

            $highlight = $this->str_replace_first($tag->getOpen(), '', $highlight);
            $highlight = $this->str_replace_first($tag->getClose(), '', $highlight);

            $tagPosition[] = new TagPosition($tag, $start + $absoluteOffset, $len, boolval($absoluteOffset));
        }

        $this->tagPositions = array_merge($this->tagPositions, $tagPosition);
    }

    private function mb_substr_replace($string, $replacement, $start, $length = NULL) {
        if (is_array($string)) {
            $num = count($string);
            // $replacement
            $replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad(array($replacement), $num, $replacement);
            // $start
            if (is_array($start)) {
                $start = array_slice($start, 0, $num);
                foreach ($start as $key => $value)
                    $start[$key] = is_int($value) ? $value : 0;
            }
            else {
                $start = array_pad(array($start), $num, $start);
            }
            // $length
            if (!isset($length)) {
                $length = array_fill(0, $num, 0);
            }
            elseif (is_array($length)) {
                $length = array_slice($length, 0, $num);
                foreach ($length as $key => $value)
                    $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
            }
            else {
                $length = array_pad(array($length), $num, $length);
            }
            // Recursive call
            return array_map(__FUNCTION__, $string, $replacement, $start, $length);
        }
        preg_match_all('/./us', (string)$string, $smatches);
        preg_match_all('/./us', (string)$replacement, $rmatches);
        if ($length === NULL) $length = mb_strlen($string);
        array_splice($smatches[0], $start, $length, $rmatches[0]);
        return join($smatches[0]);
    }

    private function str_replace_first($from, $to, $content) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $content, 1);
    }
}