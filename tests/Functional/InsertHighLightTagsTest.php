<?php
namespace iva3682\InsertHighLightTags\test\Functional;

use PHPUnit\Framework\TestCase;
use iva3682\InsertHighLightTags\InsertHighLightTags;
use iva3682\InsertHighLightTags\RestoreProductNameItem;

/**
 * InsertHighLightTagsTest short summary.
 *
 * InsertHighLightTagsTest description.
 *
 * @version 1.0
 * @author Иван
 */
class InsertHighLightTagsTest extends TestCase
{
    public function additionProvider()
    {
        return [
            [
                'highlightsData' => [
                    'paramTitle.model.fullname' => [
                        '-<mark>вок</mark> easy chef <mark>28</mark> <mark>см</mark> g2701972'
                    ],
                    'paramTitle.model.ngram' => [
                        '-<u>вок</u> easy chef 28 см g2701972'
                    ]
                ],
                'categoryPos' => [
                    [
                        'offset' => 6,
                        'length' => 9
                    ]
                ],
                'brandPos' => [
                    [
                        'offset' => 0,
                        'length' => 5
                    ]
                ],
                'productName' => 'TEFAL Сковорода-вок Easy Chef 28 см G2701972',
                'productNameHL' => '<brand>TEFAL</brand> <category>Сковорода</category>-<mark><u>вок</u></mark> Easy Chef <mark>28</mark> <mark>см</mark> G2701972'
            ],
            [
                'highlightsData' => [
                    'paramTitle.model.fullname' => [
                        'посуда 04192628 <mark>вок</mark>'
                    ],
                    'paramTitle.model.ngram' => [
                        'посуда 0<u>419</u>2628 <u>вок</u>'
                    ]
                ],
                'categoryPos' => [
                    [
                        'offset' => 22,
                        'length' => 9
                    ]
                ],
                'brandPos' => [
                    [
                        'offset' => 7,
                        'length' => 5
                    ]
                ],
                'productName' => 'Посуда Tefal 04192628 сковорода ВОК',
                'productNameHL' => 'Посуда <brand>Tefal</brand> 0<u>419</u>2628 <category>сковорода</category> <mark><u>ВОК</u></mark>'
            ],
            [
                'highlightsData' => [
                    'paramTitle.model.fullname' => [
                        '<mark>для</mark> <mark>соло</mark> <mark>cmw</mark> <mark>2070m</mark> для <mark>пищи</mark>'
                    ]
                ],
                'categoryPos' => [
                    [
                        'offset' => 0,
                        'length' => 13
                    ],
                    [
                        'offset' => 14,
                        'length' => 4
                    ]
                ],
                'brandPos' => [
                    [
                        'offset' => 28,
                        'length' => 5
                    ]
                ],
                'productName' => 'Микроволновая печь для соло Candy CMW 2070M для пищи',
                'productNameHL' => '<category>Микроволновая</category> <category>печь</category> <mark>для</mark> <mark>соло</mark> <brand>Candy</brand> <mark>CMW</mark> <mark>2070M</mark> для <mark>пищи</mark>'
            ],
            [
                'highlightsData' => [
                    'paramTitle.model.fullname' => [
                        '( ) <mark>cmw</mark>'
                    ]
                ],
                'categoryPos' => [

                ],
                'brandPos' => [
                    [
                        'offset' => 1,
                        'length' => 5
                    ]
                ],
                'productName' => '(Канди) CMW',
                'productNameHL' => '(<brand>Канди</brand>) <mark>CMW</mark>'
            ]
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testBuild($highlightsData, $categoryPos, $brandPos, $productName, $productNameHL)
    {
        $InsertHighLightTags = new InsertHighLightTags();

        foreach($highlightsData as $field => $highlight) {
            $hlItem = $highlight[0];

            switch($field) {
                case 'paramTitle.model.ngram': {
                    $InsertHighLightTags->extractTags($hlItem, 'u');
                    break;
                }
                case 'paramTitle.model.fullname': {
                    $InsertHighLightTags->extractTags($hlItem, 'mark');
                    break;
                }
            }
        }

        foreach($categoryPos as $pos) {
            $RestoreProductNameItem = new RestoreProductNameItem($pos['offset'], $pos['length']);
            $InsertHighLightTags->addRestoreItem($RestoreProductNameItem);
        }

        foreach($brandPos as $pos) {
            $RestoreProductNameItem = new RestoreProductNameItem($pos['offset'], $pos['length']);
            $InsertHighLightTags->addRestoreItem($RestoreProductNameItem);
        }

        $InsertHighLightTags->restore($productName);

        foreach($categoryPos as $pos) {
            $InsertHighLightTags->addTag('category', $pos['offset'], $pos['length']);
        }

        foreach($brandPos as $pos) {
            $InsertHighLightTags->addTag('brand', $pos['offset'], $pos['length']);
        }

        $hl = $InsertHighLightTags->build($productName);

        $this->assertSame($productNameHL, $hl);
    }

}