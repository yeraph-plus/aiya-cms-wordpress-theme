<div class="friends-link">
    <span class="title">__('友情链接', 'AIYA')</span>
    wp_list_bookmarks('title_li=&categorize=0&before=&after=');
</div>
$args = array(
    'title_li' => '',
    'show_images' => true,
    'show_name' => true,
    'show_description' => true,
);wp_list_bookmarks($args);