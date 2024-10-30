<?php

class Cardzware_Greeting_Cards_Seo_Service
{
    const CARD_CATEGORY_ID = 'card_category_id';
    const PAGE_TITLE = 'page_title';
    const META_DESCRIPTION = 'meta_description';
    const PAGE_TEXT_ABOVE = 'page_text_above';
    const PAGE_TEXT_BELOW = 'page_text_below';

    private $page_title = null;
    private $meta_description = null;
    private $page_text_above = null;
    private $page_text_below = null;
    private $breadcrumb;
    private $card_category_id;
    private $domain;
    private $all_seo_categories;

    function __construct($card_category_id, $breadcrumb, $domain, $all_seo_categories) {
        $this->card_category_id = $card_category_id;
        $this->breadcrumb = $breadcrumb;
        $this->domain = $domain;
        $this->all_seo_categories = $all_seo_categories;
    }

    function get_seo() {
        return self::escape_special_characters();
    }

    function get_page_title() {
        return $this->page_title ?? self::get_page_title_by_breadcrumb();
    }

    function set_page_title($page_title) {
        $this->page_title = $page_title;
    }

    function get_meta_description() {
        return $this->meta_description ?? implode(', ', [$this->domain, $this->get_page_title(), 'Greeting Cards']);
    }

    function set_meta_description($meta_description) {
        $this->meta_description = $meta_description;
    }

    function get_page_text_above() {
        if (!is_null($this->page_text_above)) {
            return $this->page_text_above;
        }

        $tmp_breadcrumb = $this->breadcrumb;
        return self::find_page_text_recursivity($tmp_breadcrumb,
                                                $this->all_seo_categories,
                                                self::PAGE_TEXT_ABOVE);
    }

    function set_page_text_above($text_above) {
        $this->page_text_above = $text_above;
    }

    function get_page_text_below() {
        if (!is_null($this->page_text_below)) {
            return $this->page_text_below;
        }

        $tmp_breadcrumb = $this->breadcrumb;
        array_pop($tmp_breadcrumb);
        return self::find_page_text_recursivity($tmp_breadcrumb,
                                               $this->all_seo_categories,
                                               self::PAGE_TEXT_BELOW);
    }

    function set_page_text_below($text_below) {
        $this->page_text_below = $text_below;
    }

    function get_card_category_id() {
        return $this->card_category_id;
    }

    function set_card_category_id($card_category_id) {
        $this->card_category_id = $card_category_id;
    }

    function all_current_category_has_seo() {
        return !empty($this->page_title) && !empty($this->meta_description) && !empty($this->page_text_above) && !empty($this->page_text_below);
    }

    function escape_special_characters() {
        return [
            self::PAGE_TITLE => addslashes(utf8_encode($this->get_page_title())),
            self::META_DESCRIPTION => esc_html(addslashes(utf8_encode($this->get_meta_description()))),
            self::PAGE_TEXT_ABOVE => addslashes(utf8_encode($this->get_page_text_above())),
            self::PAGE_TEXT_BELOW => addslashes(utf8_encode($this->get_page_text_below()))
        ];
    }

    function get_page_title_by_breadcrumb() {
        $page_title = '';
        foreach ($this->breadcrumb as $crumb) {
            if ($page_title != '' ) {
                $page_title .= ' > ';
            }
            $page_title .= $crumb['catName'];
        }

        return utf8_encode($page_title);
    }

    function find_page_text_recursivity($breadcrumb, $seo, $pageText) {
        if (count($breadcrumb) == 0) {
            return false;
        }

        $lastBreadcrumb = array_pop($breadcrumb);
        $categoryParentId = $lastBreadcrumb['catID'];
        foreach($seo as $key => $value) {
            if ($value[self::CARD_CATEGORY_ID] == $categoryParentId) {
                if (!empty($value[$pageText])) {
                    return $value[$pageText];
                }
                break;
            }
        }

        return self::find_page_text_recursivity($breadcrumb, $seo, $pageText);
    }
}
