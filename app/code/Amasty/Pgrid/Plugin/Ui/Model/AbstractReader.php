<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Plugin\Ui\Model;

class AbstractReader
{
    /**
     * Added settings for product_columns
     *
     * @return array
     */
    protected function addColumnsSettings()
    {
        $result = [
            'name'     => 'config',
            'xsi:type' => 'array',
            'item'     => [
                'childDefaults'      => [
                    'name'     => 'childDefaults',
                    'xsi:type' => 'array',
                    'item'     => [
                        'storageConfig' => [
                            'name'     => 'storageConfig',
                            'xsi:type' => 'array',
                            'item'     => [
                                'provider'  => [
                                    'name'     => 'provider',
                                    'xsi:type' => 'string',
                                    'value'    => 'ns = ${ $.ns }, index = bookmarks'
                                ],
                                'root'      => [
                                    'name'     => 'root',
                                    'xsi:type' => 'string',
                                    'value'    => 'columns.${ $.index }'
                                ],
                                'namespace' => [
                                    'name'     => 'namespace',
                                    'xsi:type' => 'string',
                                    'value'    => 'current.${ $.storageConfig.root }'
                                ]
                            ]
                        ],
                        'fieldAction'   => [
                            'name'     => 'fieldAction',
                            'xsi:type' => 'array',
                            'item'     => [
                                'provider' => [
                                    'name'     => 'provider',
                                    'xsi:type' => 'string',
                                    'value'    => 'product_listing.product_listing.product_columns_amasty_editor'
                                ],
                                'target'   => [
                                    'name'     => 'target',
                                    'xsi:type' => 'string',
                                    'value'    => 'startEdit'
                                ],
                                'params'   => [
                                    'name'     => 'params',
                                    'xsi:type' => 'array',
                                    'item'     => [
                                        0 => [
                                            'name'     => '0',
                                            'xsi:type' => 'string',
                                            'value'    => '${ $.$data.rowIndex }'
                                        ],
                                        1 => [
                                            'name'     => '1',
                                            'xsi:type' => 'string',
                                            'value'    => '${ $.$data.column.index }'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'component'          => [
                    'name'     => 'component',
                    'xsi:type' => 'string',
                    'value'    => 'Amasty_Pgrid/js/grid/listing'
                ],
                'storageConfig'      => [
                    'name'     => 'storageConfig',
                    'xsi:type' => 'array',
                    'item'     => [
                        'namespace' => [
                            'name'     => 'namespace',
                            'xsi:type' => 'string',
                            'value'    => 'current'
                        ],
                        'provider'  => [
                            'name'     => 'provider',
                            'xsi:type' => 'string',
                            'value'    => 'ns = ${ $.ns }, index = bookmarks'
                        ]
                    ]
                ],
                'componentType'      => [
                    'name'     => 'componentType',
                    'xsi:type' => 'string',
                    'value'    => 'columns'
                ],
                'amastyEditorConfig' => [
                    'name'     => 'amastyEditorConfig',
                    'xsi:type' => 'array',
                    'item'     => [
                        'enabled'      => [
                            'name'     => 'enabled',
                            'xsi:type' => 'boolean',
                            'value'    => true
                        ],
                        'clientConfig' => [
                            'name'     => 'clientConfig',
                            'xsi:type' => 'array',
                            'item'     => [
                                'saveUrl'            => [
                                    'name'     => 'saveUrl',
                                    'xsi:type' => 'url',
                                    'path'     => 'amasty_pgrid/index/inlineEdit'
                                ],
                                'validateBeforeSave' => [
                                    'name'     => 'validateBeforeSave',
                                    'xsi:type' => 'boolean',
                                    'value'    => false
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $result;
    }

    /**
     * Added settings for listing_top
     *
     * @return array
     */
    protected function addListingToolbarSettings()
    {
        $result = [
            'name'     => 'config',
            'xsi:type' => 'array',
            'item'     => [
                'component'           => [
                    'name'     => 'component',
                    'xsi:type' => 'string',
                    'value'    => 'Amasty_Pgrid/js/grid/controls/columns'
                ],
                'displayArea'         => [
                    'name'     => 'displayArea',
                    'xsi:type' => 'string',
                    'value'    => 'dataGridActions'
                ],
                'columnsData'         => [
                    'name'     => 'columnsData',
                    'xsi:type' => 'array',
                    'item'     => [
                        'provider' => [
                            'name'     => 'provider',
                            'xsi:type' => 'string',
                            'value'    => 'product_listing.product_listing.product_columns'
                        ]
                    ]
                ],
                'columnsExtra'        => [
                    'name'     => 'columnsExtra',
                    'xsi:type' => 'array',
                    'item'     => [
                        'categories' => [
                            'name'      => 'categories',
                            'xsi:type'  => 'string',
                            'translate' => 'true',
                            'value'     => 'Categories'
                        ],
                        'links'      => [
                            'name'      => 'links',
                            'xsi:type'  => 'string',
                            'translate' => 'true',
                            'value'     => 'Links'
                        ]
                    ]
                ],
                'storageConfig'       => [
                    'name'     => 'storageConfig',
                    'xsi:type' => 'array',
                    'item'     => [
                        'provider'  => [
                            'name'     => 'provider',
                            'xsi:type' => 'string',
                            'value'    => 'product_listing.product_listing.listing_top.bookmarks'
                        ],
                        'namespace' => [
                            'name'     => 'namespace',
                            'xsi:type' => 'string',
                            'value'    => 'current'
                        ]
                    ]
                ],
                'editorCellConfig'    => [
                    'name'     => 'editorCellConfig',
                    'xsi:type' => 'array',
                    'item'     => [
                        'provider' => [
                            'name'     => 'provider',
                            'xsi:type' => 'string',
                            'value'    => 'product_listing.product_listing.product_columns_amasty_editor_cell'
                        ]
                    ]
                ],
                'listingFilterConfig' => [
                    'name'     => 'listingFilterConfig',
                    'xsi:type' => 'array',
                    'item'     => [
                        'provider' => [
                            'name'     => 'provider',
                            'xsi:type' => 'string',
                            'value'    => 'product_listing.product_listing.listing_top.listing_filters'
                        ]
                    ]
                ],
                'clientConfig'        => [
                    'name'     => 'clientConfig',
                    'xsi:type' => 'array',
                    'item'     => [
                        'saveUrl'            => [
                            'name'     => 'saveUrl',
                            'xsi:type' => 'url',
                            'path'     => 'mui/index/render'
                        ],
                        'validateBeforeSave' => [
                            'name'     => 'validateBeforeSave',
                            'xsi:type' => 'boolean',
                            'value'    => 'false'
                        ]
                    ]
                ]
            ]
        ];

        return $result;
    }

    /**
     * Added settings for product grid
     *
     * @param array $result
     *
     * @return array
     */
    protected function addAmastySettings($result)
    {
        if (isset($result['listing_top']['children']['columns_controls']['arguments']['data']['item']['config'])) {
            $result['listing_top']['children']['columns_controls']['arguments']['data']['item']['config'] =
                $this->addListingToolbarSettings();

            if (isset($result['product_columns']['arguments']['data']['item']['config'])) {
                $result['product_columns']['arguments']['data']['item']['config'] = $this->addColumnsSettings();
            }

            if (isset($result['product_columns']['attributes'])) {
                $result['product_columns']['attributes'] =
                    ['class' => \Amasty\Pgrid\Ui\Component\Listing\Columns::class];
            }
        }

        return $result;
    }
}
