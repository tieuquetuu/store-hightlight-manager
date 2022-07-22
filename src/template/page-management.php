<?php
/**
 * Template Name: Quản Lý Dữ Liệu
 *
 * @package willgroup
 */

if( ! is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}
$current_user = wp_get_current_user();
$current_link = get_the_permalink();

wp_head(); ?>

<main id="main" class="col-12 site-main" role="main">
    <h3 class="text-center">Report quyền hệ thống</h3>
    <table
        id="system-management-dataTable"
        class="table responsive table-striped display"
        data-ajax-source="<?php echo get_rest_url() . "hightlight/v1/reportSystemDataTable" ?>"
        style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Tác giả</th>
                <th class="text-center">Tổng lượt xem</th>
                <th class="text-center">Lượt click cửa hàng</th>
                <th class="text-center">Lượt click mua hàng</th>
                <th class="text-center">Thời gian xem trung bình</th>
                <th class="text-center">Tình trạng</th>
            </tr>
        </thead>
    </table>
</main>

<?php wp_footer(); ?>
