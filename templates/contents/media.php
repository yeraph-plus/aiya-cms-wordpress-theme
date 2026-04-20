<?php

if (!defined('ABSPATH')) {
    exit;
}

$post_obj = new AYA_Post_In_While();

$playlist_props = function_exists('aya_plyr_playlist_get_props') ? aya_plyr_playlist_get_props() : null;

// Hardcoded options for this template
$player_options = [
    'autoplay' => false,
    'controls' => ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
    'settings' => ['captions', 'quality', 'speed', 'loop'],
    'ratio' => '16:9',
];

?>
<article>
    <h1 class="text-3xl font-bold mb-4"><?php echo $post_obj->title; ?></h1>

    <div class="mb-8">
        <?php
        aya_react_island('plyr-player', [
            'playlist' => $playlist_props,
            'options' => $player_options
        ]);
        ?>
    </div>

    <div class="prose max-w-none">
        <?php echo $post_obj->content; ?>
    </div>

    <?php
    aya_render_hydrated_island(
        'hy-content-end',
        ['endDivider' => true]
    );
    ?>
</article>
