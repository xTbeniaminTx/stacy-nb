<?php

$stacy_nb_latest_news_section_enable = get_theme_mod('latest_news_section_enable', 'on');
if ($stacy_nb_latest_news_section_enable != 'off') {

    if (get_theme_mod('blog_type', false)) {
        if (get_theme_mod('blog_type', 'list') == 'default') {
            stacy_nb_default_blog_type();
        } else {
            stacy_nb_list_blog_type();
        }
    } else {
        if (get_option('stacy_nb_user', 'new') == 'old') {
            stacy_nb_default_blog_type();
        } else {
            stacy_nb_list_blog_type();
        }
    }
}