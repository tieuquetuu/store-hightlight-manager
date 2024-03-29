function SystemManagerInit() {
    let $table = $("#system-report-table");

    if (!$table.length) {
        return false;
    }

    let { ajaxSource } = $table.data();
    if (!ajaxSource) {
        return false;
    }

    let $dataTable = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxSource,
        // ajax: {
        //     url: ajaxSource,
        //     contentType: "application/json",
        //     type: "POST",
        //     data: function(data, settings){
        //         console.log(data)
        //         // return JSON.stringify( data );
        //     }
        // },
        columns: [
            {
                className:      'dt-control',
                orderable:      false,
                data:           null,
                defaultContent: '',
            },
            {
                className:      'details-control-id',
                orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    return data?.id
                }
            },
            {
                className:      'details-control-title',
                orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    return data?.title
                }
            },
            {
                className:      'details-control-category',
                orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    let categoryName = data?.category.map(obj => obj.name);

                    categoryName = categoryName.join(",");

                    return categoryName
                }
            },
            {
                className:      'details-control-author',
                orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    return data?.author?.display_name
                }
            },
            {
                className:      'text-center details-control-luot-xem',
                // orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    let totalScreenPageViews = 0;

                    let { analytics } = data;

                    if (!analytics || analytics.length <= 0) {
                        return totalScreenPageViews
                    }

                    for (let i = 0;i < analytics.length;i++) {
                        let analyticsItem = analytics[i];
                        totalScreenPageViews += parseInt(analyticsItem?.screenPageViews);
                    }

                    return `${totalScreenPageViews} lượt xem`;
                }
            },
            {
                className:      'text-center details-control-luot-click-cua-hang',
                // orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    let totalClick = 0;

                    let { analytics } = data;

                    let analytics_click_view_shop = analytics.filter(obj => obj.eventName === "click_view_shop");

                    if (!analytics || analytics.length <= 0 || !analytics_click_view_shop) {
                        return totalClick
                    }

                    for (let i = 0;i < analytics_click_view_shop.length;i++) {
                        let analyticsItem = analytics_click_view_shop[i];
                        totalClick += parseInt(analyticsItem?.eventCount);
                    }

                    return `${totalClick} lượt`
                }
            },
            {
                className:      'text-center details-control-luot-click-mua-hang',
                // orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    let totalClick = 0;

                    let { analytics } = data;

                    let analytics_click_buy_product = analytics.filter(obj => obj.eventName === "click_buy_product");

                    if (!analytics || analytics.length <= 0 || !analytics_click_buy_product) {
                        return totalClick
                    }

                    for (let i = 0;i < analytics_click_buy_product.length;i++) {
                        let analyticsItem = analytics_click_buy_product[i];
                        totalClick += parseInt(analyticsItem?.eventCount);
                    }

                    return `${totalClick} lượt`
                }
            },
            {
                className:      'text-right details-control-thoi-gian-xem-trung-binh',
                // orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    let averageSessionDuration = 0;

                    let { analytics } = data;
                    if (!analytics || analytics.length <= 0) {
                        return averageSessionDuration
                    }

                    for (let i = 0;i < analytics.length;i++) {
                        let analyticsItem = analytics[i];
                        averageSessionDuration += parseFloat(analyticsItem?.averageSessionDuration);
                    }

                    averageSessionDuration = averageSessionDuration / analytics.length;

                    return `${averageSessionDuration.toFixed(1)} giây`
                }
            },
            {
                className:      'details-control-status',
                orderable:      false,
                data:           null,
                defaultContent: 'không có dữ liệu',
                render: (row, type, data) => {
                    return data?.status
                }
            },
        ],
        dom: 'lBfrtip',
        buttons: [
            'excel', 'pdf'
        ]
    });

    $table.on('click', 'td.dt-control', function (e) {
        let $tr = $(this).closest('tr');
        let row = $dataTable.row($tr);
        let rowData = row.data()
        let { analytics } = rowData;

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            $tr.removeClass('shown');
        } else {
            // Open this row
            row.child(analyticsFormat(analytics)).show();
            // console.log(analyticsFormat(analytics))
            $tr.addClass('shown');
        }
    });

    function analyticsFormat(dataAnalytics) {
        // `d` is the original data object for the row
        let rows = '';

        if (dataAnalytics.length == 0) {
            rows = `
               <tr colspan="5">
                    <td class="text-center" colspan="5">Không có dữ liệu</td>
                </tr>
            `;
        } else {

            let data = {}

            let dataByHostNames = {};

            dataAnalytics.forEach(function(obj, count){
                let { eventName, eventCount, screenPageViews, hostName,averageSessionDuration  } = obj;
                let hostNameKey = hostName;

                if (!(hostNameKey in dataByHostNames)) {
                    dataByHostNames[hostNameKey] = {
                        "hostName": hostName,
                        "click_view_shop" : 0,
                        "click_buy_product" : 0,
                        "screenPageViews" : 0,
                        "averageSessionDuration" : 0,
                    };
                }

                if (eventName === "click_buy_product") {
                    dataByHostNames[hostNameKey]["click_buy_product"] += parseInt(eventCount);
                }
                if (eventName === "click_view_shop") {
                    dataByHostNames[hostNameKey]["click_view_shop"] += parseInt(eventCount);
                }
                dataByHostNames[hostNameKey]["screenPageViews"] += parseInt(screenPageViews);
                dataByHostNames[hostNameKey]["averageSessionDuration"] += parseFloat(averageSessionDuration);
            })

            // Map lại thời gian xem trung bình


            Object.values(dataByHostNames).forEach(function(d) {

                let totalItems = dataAnalytics.filter(obj => obj.hostName === d.hostName).length;

                 rows += `
                    <tr>
                        <td>${d.hostName}</td>
                        <td class="text-center">${d.screenPageViews} lượt xem</td>
                        <td class="text-center">${d.click_view_shop} lượt</td>
                        <td class="text-center">${d.click_buy_product} lượt</td>
                        <td class="text-right">${(d.averageSessionDuration / totalItems).toFixed(1)} giây</td>
                    </tr>
                 `
            });
        }

        return (
            `<table class="system-report-table-child-row-details display" style="width: 100%">
                <thead>
                    <tr>
                        <th>Tên miền</th>
                        <th class="text-center">Lượt xem</th>
                        <th class="text-center">Lượt click cửa hàng</th>
                        <th class="text-center">Lượt click mua hàng</th>
                        <th class="text-right">Thời gian xem trung bình</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}       
                </tbody>
            </table>`
        );
    }

    if ($("#filter-user").length) {
        $("#filter-user").on("change", function (e) {
            let value = e.currentTarget.value;
            Object.assign({ author: value },$dataTable.ajax.params());
            // $dataTable.ajax.params(newParams);

            let initialUrl = $dataTable.ajax.url();
            let newUrl = new URL(initialUrl);
            newUrl.searchParams.set("author", value);
            $dataTable.ajax.url(newUrl.href).ajax.reload();
        })
    }

    if ($("#filter-category").length) {
        $("#filter-category").on("change", function (e) {
            let categoryId = e.currentTarget.value;
            let initialUrl = $dataTable.ajax.url();
            let newUrl = new URL(initialUrl);
            newUrl.searchParams.set("category", categoryId);
            $dataTable.ajax.url(newUrl.href).ajax.reload();
        })
    }

    if ($("#filter-domains").length) {
        $("#filter-domains").on("change", function (e) {
            let domainName = e.currentTarget.value;
            let initialUrl = $dataTable.ajax.url();
            let newUrl = new URL(initialUrl);
            newUrl.searchParams.set("domain", domainName);
            $dataTable.ajax.url(newUrl.href).ajax.reload();
        })
    }

    if ($("#filter-daterange").length) {
        let start = moment().subtract(29, 'days');
        let end = moment();

        function cb(start, end) {
            $("#filter-daterange").html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            let initialUrl = $dataTable.ajax.url();
            let newUrl = new URL(initialUrl);
            newUrl.searchParams.set("date_ranges", JSON.stringify({
                start_date: start.format("YYYY-MM-DD"),
                end_date: end.format("YYYY-MM-DD")
            }));
            $dataTable.ajax.url(newUrl.href).ajax.reload();
        }

        $("#filter-daterange").daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb)

        cb(start, end);
    }
}

if (typeof $ != undefined) {
    $(document).ready(function() {
        SystemManagerInit();
    })
}