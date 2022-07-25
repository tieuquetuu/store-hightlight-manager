<?php
/**
 * Template Name: Tổng Quan Số Liệu - Users
 *
 * @package willgroup
 */


if( ! is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}
$current_user = wp_get_current_user();
$current_link = get_the_permalink();

$get_user_meta = get_user_meta($current_user->ID);
$level_manager_user_meta = unserialize($get_user_meta["level_manager_data"][0]);

if (!in_array("level_3",$level_manager_user_meta) || !in_array("level_1",$level_manager_user_meta)) {
    wp_redirect( site_url() . "/tong-quan-so-lieu" );
    exit;
}

$users = get_users();
$categories = get_terms("re_cat");
$hostNames = \StoreHightLight\StoreHL::instance()->getHostNames();

$is_admin = in_array("administrator", $current_user->roles);
$ajaxArrayParams = array();
$str_params = http_build_query($ajaxArrayParams);
$ajax_source_url = get_rest_url() . "hightlight/v1/reportUsersDataTable?" . $str_params;

get_header() ?>

<main id="main" class="col-12 site-main" role="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col col-md-12">
                <table id="users-report-table"
                       class="table responsive table-striped display"
                       data-ajax-source="<?php echo $ajax_source_url ?>"
                       style="width: 100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Loại sản phẩm</th>
                            <th class="text-center">Tổng lượt xem</th>
                            <th class="text-center">Lượt click cửa hàng</th>
                            <th class="text-center">Lượt click mua hàng</th>
                            <th class="text-center">Thời gian xem trung bình</th>
                            <th class="text-center">Tình trạng</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</main>

<?php get_footer() ?>
