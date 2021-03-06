<?php

namespace StoreHightLight;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\Filter\InListFilter;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Analytics\Data\V1beta\Row;
use Google\Type\Date;
use Illuminate\Support\Str;

class StoreHLGA4 {
    private static $instance = NULL;

    protected static $credentials = NULL;

    protected static $properties = NULL;

    protected static $client = NULL;

    public static function instance() {
        if ( ! isset( self::$instance ) || ! ( self::$instance instanceof StoreHLGA4 ) ) {
            self::$instance = new StoreHLGA4();
//            self::$instance->setup_constants();
            /*if ( self::$instance->includes() ) {
                self::$instance->actions();
                self::$instance->filters();
            }*/
        }

        /**
         * Return the HLStore Instance
         */
        return self::$instance;
    }

    public static function credentials() {
        return STORE_HIGHT_LIGHT_GOOGLE_CREDENTIALS;
    }

    public static function public_credentials() {
        return STORE_HIGHT_LIGHT_GOOGLE_CREDENTIALS;
    }

    public static function client() {
        if (self::$client == NULL) {
            self::$client = new BetaAnalyticsDataClient(
                array(
                    'credentials' => self::credentials()
                )
            );
        }

        /**
         * Fire an action when the Schema is returned
         */
        do_action( 'store_hl_get_google_client', self::$client );

        /**
         * Return the Google Analystic Client after applying filters
         */
        return ! empty( self::$client ) ? self::$client : null;
    }

    public static function properties() {
        if (self::$properties == NULL) {
            self::$properties = STORE_HIGHT_LIGHT_GOOGLE_ANALYSTIC_PROPERTIES;
        }

        /**
         * Fire an action when the Properties is returned
         */
        do_action( 'store_hl_get_google_properties', self::$properties );

        /**
         * Return the Google Analystic Properties after applying filters
         */
        return ! empty( self::$properties ) ? self::$properties : null;
    }

    public static function reportByProductSlug($args) {
        $args = is_array($args) ? $args : null;

        if (!$args) {
            return null;
        }
        $slug = isset($args['slug']) && is_string($args['slug']) && strlen($args['slug']) > 0 ? $args['slug'] : false;

        $data = array();
        $result = array(
            "slug" => $slug,
        );
        $date_ranges = [
            new DateRange([
                'start_date' => '30daysAgo',
                'end_date' => 'today',
            ]),
        ];
        $limit = 1000;
        $offset = 0;

        $batchResponse = self::client()->batchRunReports([
            'property' => 'properties/' . self::properties(),
            'requests' => [
                new RunReportRequest([
                    'property' => 'properties/' . self::properties(),
                    'date_ranges' => $date_ranges,
                    'dimensions' => [
                        new Dimension(
                            [
                                'name' => 'eventName',
                            ],
                        ),
                    ],
                    'metrics' => [
                        new Metric(
                            [
                                'name' => 'eventCount', // T???ng s??? s??? ki???n
                            ],
                        ),
                    ],
                    'dimension_filter' => new FilterExpression(
                        [
                            'and_group' => new FilterExpressionList(
                                array(
                                    'expressions' => [
                                        new FilterExpression(
                                            [
                                                'filter' => new Filter(
                                                    [
                                                        'field_name' => "pagePath",
                                                        'string_filter' => new StringFilter(
                                                            [
                                                                'value' => $slug,
                                                                'match_type' => MatchType::CONTAINS,
                                                            ]
                                                        )
                                                    ]
                                                )
                                            ]
                                        ),

                                        new FilterExpression(
                                            [
                                                'filter' => new Filter(
                                                    [
                                                        'field_name' => "eventName",
                                                        'string_filter' => new StringFilter(
                                                            [
                                                                'value' => 'page_view',
                                                                'match_type' => MatchType::EXACT,
                                                            ]
                                                        )
                                                    ]
                                                )
                                            ]
                                        )
                                    ]
                                )
                            ),
                        ]
                    ),
                    'limit' => $limit,
                    'offset' => $offset
                ]) , // L???y danh s??ch l?????t xem

                new RunReportRequest([
                    'property' => 'properties/' . self::properties(),
                    'date_ranges' => $date_ranges,
                    'dimensions' => [
                        new Dimension(
                            [
                                'name' => 'eventName',
                            ],
                        ),
                    ],
                    'metrics' => [
                        new Metric(
                            [
                                'name' => 'eventCount', // T???ng s??? s??? ki???n
                            ],
                        ),
                    ],
                    'dimension_filter' => new FilterExpression(
                        [
                            'and_group' => new FilterExpressionList(
                                array(
                                    'expressions' => [
                                        new FilterExpression(
                                            [
                                                'filter' => new Filter(
                                                    [
                                                        'field_name' => "pagePath",
                                                        'string_filter' => new StringFilter(
                                                            [
                                                                'value' => $slug,
                                                                'match_type' => MatchType::CONTAINS,
                                                            ]
                                                        )
                                                    ]
                                                )
                                            ]
                                        ),

                                        new FilterExpression(
                                            [
                                                'filter' => new Filter(
                                                    [
                                                        'field_name' => "eventName",
                                                        'string_filter' => new StringFilter(
                                                            [
                                                                'value' => 'click_buy_product',
                                                                'match_type' => MatchType::EXACT,
                                                            ]
                                                        )
                                                    ]
                                                )
                                            ]
                                        )
                                    ]
                                )
                            ),
                        ]
                    ),
                    'limit' => $limit,
                    'offset' => $offset
                ]) , // L???y l?????t click button mua h??ng

                new RunReportRequest([
                    'property' => 'properties/' . self::properties(),
                    'date_ranges' => $date_ranges,
                    'dimensions' => [
                        new Dimension(
                            [
                                'name' => 'eventName',
                            ],
                        ),
                    ],
                    'metrics' => [
                        new Metric(
                            [
                                'name' => 'eventCount', // T???ng s??? s??? ki???n
                            ],
                        ),
                    ],
                    'dimension_filter' => new FilterExpression(
                        [
                            'and_group' => new FilterExpressionList(
                                array(
                                    'expressions' => [
                                        new FilterExpression(
                                            [
                                                'filter' => new Filter(
                                                    [
                                                        'field_name' => "pagePath",
                                                        'string_filter' => new StringFilter(
                                                            [
                                                                'value' => $slug,
                                                                'match_type' => MatchType::CONTAINS,
                                                            ]
                                                        )
                                                    ]
                                                )
                                            ]
                                        ),

                                        new FilterExpression(
                                            [
                                                'filter' => new Filter(
                                                    [
                                                        'field_name' => "eventName",
                                                        'string_filter' => new StringFilter(
                                                            [
                                                                'value' => 'click_view_shop',
                                                                'match_type' => MatchType::EXACT,
                                                            ]
                                                        )
                                                    ]
                                                )
                                            ]
                                        )
                                    ]
                                )
                            ),
                        ]
                    ),
                    'limit' => $limit,
                    'offset' => $offset
                ]) , // L???y l?????t click button c???a h??ng

//                new RunReportRequest([
//                    'property' => 'properties/' . HLSM_GOOGLE_ANALYSTIC_PROPERTY,
//                    'date_ranges' => $date_ranges,
//                    'dimensions' => [
//                        new Dimension(
//                            [
//                                'name' => 'eventName',
//                            ],
//                        ),
//                    ],
//                    'metrics' => [
//                        new Metric(
//                            [
//                                'name' => 'averageSessionDuration', // Th???i l?????ng trung b??nh (t??nh b???ng gi??y) trong c??c phi??n c???a ng?????i d??ng.
//                            ],
//                        ),
//                    ],
//                    'dimension_filter' => new FilterExpression(
//                        [
//                            'and_group' => new FilterExpressionList(
//                                array(
//                                    'expressions' => [
//                                        new FilterExpression(
//                                            [
//                                                'filter' => new Filter(
//                                                    [
//                                                        'field_name' => "pagePath",
//                                                        'string_filter' => new StringFilter(
//                                                            [
//                                                                'value' => $slug,
//                                                                'match_type' => MatchType::CONTAINS,
//                                                            ]
//                                                        )
//                                                    ]
//                                                )
//                                            ]
//                                        ),
//
//                                        /*new FilterExpression(
//                                            [
//                                                'filter' => new Filter(
//                                                    [
//                                                        'field_name' => "eventName",
//                                                        'in_list_filter' => new InListFilter(
//                                                            [
//                                                                'values' => ["page_view", "user_engagement", "scroll"]
//                                                            ]
//                                                        )
//                                                    ]
//                                                )
//                                            ]
//                                        )*/
//                                    ]
//                                )
//                            ),
//                        ]
//                    ),
//                    'limit' => $limit,
//                    'offset' => $offset
//                ]) , // L???y th???i gian xem trang trung b??nh
            ]
        ]);

        foreach ($batchResponse->getReports() as $reportKey => $report) {

            $report_total_rows = $report->getRowCount();
            $report_rows = $report->getRows();
            $report_json = json_decode($report->serializeToJsonString());
            $report_data = array();

            if ($report_total_rows == 1) {
                foreach ($report_rows as $report_row) {
                    $report_obj_key_name = $report_row->getDimensionValues()[0]->getValue();
                    $report_obj_value = $report_row->getMetricValues()[0]->getValue();
                    $report_data[$report_obj_key_name] = $report_obj_value;
                    $data = array_merge($data, $report_data);
                }
            }
        }

        // Set up data for result
        if ($data) :
            $result['data'] = json_decode(json_encode($data));
        endif;

        return $result;
    }

    public static function DimensionExplain($dimensionSlug) {
        $slug = is_string($dimensionSlug) && strlen($dimensionSlug) > 0 ? $dimensionSlug : NULL;

        $name = NULL;

        switch ($slug) {
            case "hostName":
                $name = "T??n mi???n";
                break;
            case "pageTitle":
                $name = "Ti??u ????? trang";
                break;
            case "pageLocation":
                $name = "???????ng d???n chi ti???t";
                break;
            case "pagePath":
                $name = "???????ng d???n";
                break;
            case "sessions":
                $name = "S??? phi??n ( traffic )";
                break;
            case "eventName":
                $name = "T??n s??? ki???n";
                break;
            default:
                $name = $slug;
        }

        return $name;
    }

    public static function MetricExplain($metricSlug) {
        $slug = is_string($metricSlug) && strlen($metricSlug) > 0 ? $metricSlug : NULL;

        $name = NULL;

        switch ($slug) {
            case "eventCount":
                $name = "T???ng s??? s??? ki???n";
                break;
            case "activeUsers":
                $name = "Ng?????i d??ng";
                break;
            case "sessions":
                $name = "T???ng s??? phi??n";
                break;
            case "screenPageViews":
                $name = "L?????t xem";
                break;
            case "userEngagementDuration":
                $name = "T???ng th???i gian xem (s)";
                break;
            case "averageSessionDuration":
                $name = "Th???i l?????ng trung b??nh ( gi??y )";
                break;
            case "engagedSessions":
                $name = "S??? phi??n k??o d??i tr??n 10s";
                break;
            case "engagementRate":
                $name = "T??? l??? t????ng t??c";
                break;
            default:
                $name = $slug;
        }

        return $name;
    }

    public static function ThongKeSoLieuHeThong($args) {
        $args = is_array($args) ? $args : null;
        $dimensions = isset($args['dimensions']) && is_array($args['dimensions']) ? $args['dimensions'] : array();
        $metrics = isset($args['metrics']) && is_array($args['metrics']) ? $args['metrics'] : array();
        $has_date_ranges = isset($args['date_ranges']) && is_array($args['date_ranges']) ? TRUE : FALSE;
//        $dimension_filters = isset($args['dimension_filters']) && is_array($args['dimension_filters']) && count($args['dimension_filters']) > 0 ? $args['dimension_filters'] : array();
//
//        $and_group = isset($dimension_filters['and_group']) && is_array($dimension_filters['and_group']) && count($dimension_filters['and_group']) > 0 ? $dimension_filters['and_group'] : null;
//        $and_group_expressions = isset($and_group['expressions']) && is_array($and_group['expressions']) && count($and_group['expressions']) ? $and_group['expressions'] : array();

        $default_date_range = array(
            'start_date' => '2022-07-01', // B???t ?????u t??? tr?????c
            'end_date' => 'today', // T???i h??m nay
        );
        $raw_date_ranges = $has_date_ranges ? $args['date_ranges'] : array($default_date_range);

        // Map the date ranges key
        $date_ranges = array_map(function($date_item_name) {
            return new DateRange($date_item_name);
        }, $raw_date_ranges);

        // Map the dimensions key
        $dimensions = array_map(function($d_item_name) {
            return new Dimension([
                'name' => $d_item_name
            ]);
        }, $dimensions);
        // Map the metrics key
        $metrics = array_map(function($m_item_name) {
            return new Metric([
                'name' => $m_item_name
            ]);
        }, $metrics);

//        // Map the and group dimension array
//        $and_group_expressions = array_map(function($and_group_item){
//            $and_group_item['filter'] = new Filter($and_group_item['filter']);
//            return new FilterExpression($and_group_item);
//        },  $and_group);
//
//        // Map the dimension_filter
//        $dimension_filter_options = new FilterExpression(
//            array(
//                'and_group' => new FilterExpressionList(
//                     array(
//                         'expressions' => $and_group_expressions
//                     )
//                )
//            )
//        );

        // L???c d??? li???u theo danh s??ch event
        $filterByEventName = new FilterExpression(
            array(
                'filter' => new Filter(
                    array(
                        'field_name' => "eventName",
                        'in_list_filter' => new InListFilter(
                            array(
                                'values' => array(
                                    "page_view",
//                                    "user_engagement",
                                    "userEngagementDuration",
                                    "click_buy_product",
                                    "click_view_shop",
                                    "view_product_item"
                                ),
                            )
                        )
                    )
                )
            )
        );

        // L???c d??? li???u theo ???????ng d???n
        $filterByPathName = new FilterExpression(
            array(
                'filter' => new Filter(
                    [
                        'field_name' => "pagePath",
                        'string_filter' => new StringFilter(
                            [
                                'value' => '/product',
                                'match_type' => MatchType::BEGINS_WITH,
                            ]
                        )
                    ]
                )
            )
        );

        $options = array(
            'property' => 'properties/' . self::properties(),
            'dateRanges' => $date_ranges,
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dimensionFilter' => new FilterExpression(array(
                'and_group' => new FilterExpressionList(
                    array(
                        'expressions' => array(
//                            $filterByPathName,
                            $filterByEventName,
                        )
                    )
                ),
                /*'or_group' => new FilterExpressionList(
                    array(
                        'expressions' => array(
                            new FilterExpression(
                                array(
                                    'filter' => new Filter(
                                        [
                                            'field_name' => "pagePath",
                                            'string_filter' => new StringFilter(
                                                [
                                                    'value' => '/product',
                                                    'match_type' => MatchType::CONTAINS,
                                                ]
                                            )
                                        ]
                                    )
                                )
                            ),
                            new FilterExpression(
                                array(
                                    'filter' => new Filter(
                                        [
                                            'field_name' => "pagePath",
                                            'string_filter' => new StringFilter(
                                                [
                                                    'value' => '/nha-dat',
                                                    'match_type' => MatchType::CONTAINS,
                                                ]
                                            )
                                        ]
                                    )
                                )
                            )
                        )
                    )
                )*/
            )),
            'limit' => 10000,
            'offset' => 0
        );

        $response = self::client()->runReport($options);

        /*$response = self::client()->runReport([
            'property' => 'properties/' . self::properties(),
            'dateRanges' => [
                new DateRange([
                    'start_date' => '2022-07-01', // B???t ?????u t??? tr?????c
                    'end_date' => 'today', // T???i h??m nay
                ]),
            ],
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dimensions' => [
                new Dimension([
                    'name' => "hostName", // Danh s??ch t??n mi???n
                ])
            ],
            'metrics' => [
                new Metric([
                    'name' => "sessions", // ?????m c??c s??? ki???n
                ]),
                new Metric([
                    'name' => "eventCount", // ?????m c??c s??? ki???n
                ]),
            ],
        ]);*/

        return $response;
    }

    public static function AnalyticsGoogleBatchReport($args) {
        $args = is_array($args) ? $args : null;
        $limit = 1000;
        $offset = 0;
        $default_date_ranges = [
            new DateRange([
                'start_date' => '2022-01-01',
                'end_date' => 'today',
            ])
        ];

        /**
         * Setup index report
         */
        $domain_report_index = 0;
        $screen_pageview_report_index = 1;
        $click_buy_product_report_index = 2;
        $clivk_view_shop_report_index = 3;
        $average_session_duration_report_index = 4;

        /**
         *
         */

        /**
         * ?????nh ngh??a c??c request b??o c??o
         */
        // L???y danh s??ch t??n mi???n,
        $domain_report_options = [
            'property' => 'properties/' . self::properties(),
            'dimensions' => array(
                new Dimension([
                    'name' => 'hostName'
                ])
            ),
            'metrics' => array(
                new Metric([
                    'name' => 'activeUsers'
                ]),
                new Metric([
                    'name' => 'screenPageViews'
                ])
            ),
            'date_ranges' => $default_date_ranges,
            'limit' => $limit,
            'offset' => $offset
        ];
        // L???y danh s??ch l?????t xem theo ???????ng d???n
        $screen_pageview_report_options = array(
            'property' => 'properties/' . self::properties(),
            'dimensions' => array(
                new Dimension([
                    'name' => 'pagePath'
                ])
            ),
            'metrics' => array(
                new Metric([
                    'name' => 'screenPageViews'
                ])
            ),
            /*'dimension_filter' => new FilterExpression(
                [
                    'filter' => new Filter(
                        [
                            'field_name' => "eventName",
                            'string_filter' => new StringFilter(
                                [
                                    'value' => 'page_view',
                                    'match_type' => MatchType::EXACT,
                                ]
                            )
                        ]
                    )
                ]
            ),*/
            'date_ranges' => $default_date_ranges,
            'limit' => $limit,
            'offset' => $offset
        );
        // L???y danh s??ch l?????t click mua h??ng
        $click_buy_product_report_options = array(
            'property' => 'properties/' . self::properties(),
            'dimensions' => array(
                new Dimension([
                    'name' => 'pagePath'
                ])
            ),
            'metrics' => array(
                new Metric([
                    'name' => 'eventCount'
                ])
            ),
            'date_ranges' => $default_date_ranges,
            'dimension_filter' => new FilterExpression(
                [
                    'filter' => new Filter(
                        [
                            'field_name' => "eventName",
                            'string_filter' => new StringFilter(
                                [
                                    'value' => 'click_buy_product',
                                    'match_type' => MatchType::EXACT,
                                ]
                            )
                        ]
                    )
                ]
            ),
            'limit' => $limit,
            'offset' => $offset
        );
        // L???y danh s??ch l?????t click xem c???a h??ng
        $click_view_shop_report_options = array(
            'property' => 'properties/' . self::properties(),
            'dimensions' => array(
                new Dimension([
                    'name' => 'pagePath'
                ])
            ),
            'metrics' => array(
                new Metric([
                    'name' => 'eventCount'
                ])
            ),
            'date_ranges' => $default_date_ranges,
            'dimension_filter' => new FilterExpression(
                [
                    'filter' => new Filter(
                        [
                            'field_name' => "eventName",
                            'string_filter' => new StringFilter(
                                [
                                    'value' => 'click_view_shop',
                                    'match_type' => MatchType::EXACT,
                                ]
                            )
                        ]
                    )
                ]
            ),
            'limit' => $limit,
            'offset' => $offset
        );
        // L???y danh s??ch th???i gian xem trung b??nh
        $average_session_duration_report_options = array(
            'property' => 'properties/' . self::properties(),
            'dimensions' => array(
                new Dimension([
                    'name' => 'pagePath'
                ])
            ),
            'metrics' => array(
                new Metric([
                    'name' => 'averageSessionDuration'
                ])
            ),
            'date_ranges' => $default_date_ranges,
            'limit' => $limit,
            'offset' => $offset
        );
        $batchReports = self::client()->batchRunReports([
            'property' => 'properties/' . self::properties(),
            'requests' => [
                new RunReportRequest($domain_report_options),
                new RunReportRequest($screen_pageview_report_options),
                new RunReportRequest($click_buy_product_report_options),
                new RunReportRequest($click_view_shop_report_options),
                new RunReportRequest($average_session_duration_report_options),
            ]
        ]);

        $reports = $batchReports->getReports();

        /**
         *
         */
        $domain_report = $reports[$domain_report_index];
        $screen_pageview_report = $reports[$screen_pageview_report_index];
        $click_buy_product_report = $reports[$click_buy_product_report_index];
        $click_view_shop_report = $reports[$clivk_view_shop_report_index];
        $average_session_duration_report = $reports[$average_session_duration_report_index];

        $reports_json_array = array();
        /*$reports_json_array = array_map(function($reportItem){
            return $reportItem->serializeToJsonString();
        }, $reports);*/

        foreach ($reports as $reportKey => $report) {
            if ($reportKey == $domain_report_index) {
                $reports_json_array["domain_data"] = json_decode($report->serializeToJsonString());
            }
            if ($reportKey == $domain_report_index) {
                $reports_json_array["domain_data"] = json_decode($report->serializeToJsonString());
            }
            if ($reportKey == $domain_report_index) {
                $reports_json_array["domain_data"] = json_decode($report->serializeToJsonString());
            }
            if ($reportKey == $domain_report_index) {
                $reports_json_array["domain_data"] = json_decode($report->serializeToJsonString());
            }
//            array_push($reports_json_array, json_decode($report->serializeToJsonString()));
        }

        return array(
            "keys_index" => array(
                "domain_report_index" => $domain_report_index,
                "screen_pageview_report_index" => $screen_pageview_report_index,
                "click_buy_product_report_index" => $click_buy_product_report_index,
                "clivk_view_shop_report_index" => $clivk_view_shop_report_index,
                "average_session_duration_report_index" => $average_session_duration_report_index,
            ),
            "reports" => $reports_json_array
        );
    }

    public static function GArunReport(Array $args = array()) {
        $param_dimensions = isset($args['dimensions']) && is_array($args['dimensions']) && count($args['dimensions']) > 0 ? $args['dimensions'] : null;
        $param_metrics = isset($args['metrics']) && is_array($args['metrics']) && count($args['metrics']) > 0 ? $args['metrics'] : null;
        $param_date_ranges = isset($args['dateRanges']) && is_array($args['dateRanges']) && count($args['dateRanges']) > 0 ? $args['dateRanges'] : [array(
            'start_date' => '2022-01-01',
            'end_date' => 'today',
        )];

        if (!$param_dimensions) {
            return "Missing dimension";
        } elseif(!$param_metrics) {
            return "Missing metric";
        }

        $date_ranges = array_map(function ($dateRange){
            return new DateRange($dateRange);
        }, $param_date_ranges);

        $dimensions = array_map(function($dimension_name){
            return new Dimension(
                array(
                    "name" => $dimension_name
                )
            );
        }, $param_dimensions);

        $metrics = array_map(function($metric_name){
            return new Metric(
                array(
                    "name" => $metric_name
                )
            );
        }, $param_metrics);

        $report = self::client()->runReport([
            'property' => 'properties/' . self::properties(),
            'dateRanges' => $date_ranges,
            'dimensions' => $dimensions,
            'metrics' => $metrics
        ]);

        return $report;
    }


    /**
     * @description Chuy???n ?????i d??? li???u b??o c??o v??? d???ng m???ng xem ???????c
     * @param $report
     * @return array
     */
    public static function makeReportPretty($report) {
        $json_report = json_decode($report->serializeToJsonString());

        $dimensionHeaders = $json_report->dimensionHeaders;
        $metricHeaders = $json_report->metricHeaders;

        $rows = $json_report->rows;

        $data = array();

        foreach ($rows as $row) {
            $item = array();
            $dimensionValues = $row->dimensionValues;
            $metricValues = $row->metricValues;

            foreach ($dimensionValues as $dimensionValueIndex => $dimensionValue) {
                $dimensionItemName = $dimensionHeaders[$dimensionValueIndex]->name;
                $item[$dimensionItemName] = $dimensionValue->value;
            }

            foreach ($metricValues as $metricValueIndex => $metricValue) {
                $metricItemName = $metricHeaders[$metricValueIndex]->name;
                $item[$metricItemName] = $metricValue->value;
            }

            array_push($data, (object) $item);
        }

        return $data;
    }

    /**
     * @param $request
     * @return RunReportResponse
     * @throws \Google\ApiCore\ApiException
     */
    public static function makeRunReport($request) {
        return self::client()->runReport([
            'property' => $request->getProperty(),
            'dateRanges' => $request->getDateRanges(),
            'dimensions' => $request->getDimensions(),
            'metrics' => $request->getMetrics(),
            'dimensionFilter' => $request->getDimensionFilter(),
            'metricFilter' => $request->getMetricFilter(),
            'limit' => $request->getLimit(),
            'offset' => $request->getOffset()
        ]);
    }

    /**
     * @description B??o c??o s??? li???u theo t??n mi???n
     * @return RunReportRequest
     */
    public static function RequestReportSummaryData(Array $args = []) {

        $defaultFilterNotByHostNames = new FilterExpression([
            "not_expression" => new FilterExpression([
                "filter" => new Filter([
                    "field_name" => "hostName",
                    "in_list_filter" => new InListFilter([
                        "values" => ["localhost", "127.0.0.1"]
                    ])
                ])
            ])
        ]);
        $defaultFilterByEventNames = new FilterExpression([
            "filter" => new Filter([
                "field_name" => "eventName",
                "in_list_filter" => new InListFilter([
                    "values" => ["page_view","click_buy_product", "click_view_shop"]
                ])
            ])
        ]);

        $dimension_filter_and_groups = array(
            $defaultFilterNotByHostNames,
            $defaultFilterByEventNames,
            /*new FilterExpression([
                "filter" => new Filter([
                    "field_name" => "pageTitle",
                    "string_filter" => new StringFilter([
                        "value" => $args["pageTitle"],
                        "match_type" =>
                    ])
                ])
            ])*/
        );

        $request = new RunReportRequest([
            "property" => 'properties/' . self::properties(),
            "date_ranges" => array(
                new DateRange([
                    'start_date' => '2022-01-01', // T??? tr?????c
                    'end_date' => 'today', // ?????n h??m nay
                ])
            ),
            "dimensions" => array(
                new Dimension([
                    "name" => "hostName" // T??n mi???n
                ]),
                new Dimension([
                    "name" => "pagePath" // ???????ng d???n
                ]),
                new Dimension([
                    "name" => "pageTitle" // Ti??u ????? trang
                ]),
                new Dimension([
                    "name" => "eventName" // T??n s??? ki???n
                ])
            ),
            "metrics" => array(
                new Metric([
                    "name" => "activeUsers" // ?????m S??? Ng?????i D??ng
                ]),
                new Metric([
                    "name" => "eventCount" // ?????m S??? S??? Ki???n
                ]),
                new Metric([
                    "name" => "sessions" // ?????m session
                ]),
                new Metric([
                    "name" => "screenPageViewsPerSession" // Th???i gian xem trung b??nh
                ]),
                new Metric([
                    "name" => "screenPageViews" // Th???i gian xem trung b??nh
                ]),
                new Metric([
                    "name" => "averageSessionDuration" // Th???i gian xem trung b??nh
                ]),
                new Metric([
                    "name" => "bounceRate" // Th???i gian xem trung b??nh
                ])
            ),
            "dimension_filter" => new FilterExpression([
                "and_group" => new FilterExpressionList([
                    "expressions" => $dimension_filter_and_groups
                ]),
            ]),
            "limit" => 100000,
            "offset" => 0
        ]);
        return $request;
    }
}