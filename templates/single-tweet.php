<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!have_posts()) {
    aya_template_none();
    return;
}

while (have_posts()) {
    the_post();
    the_content();
}

aya_comments_template();