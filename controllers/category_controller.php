<?php

require_once '../classes/category_class.php';

/**
 * Category Controller Functions
 */

function fetch_categories_ctr($user_id)
{
    $category = new Category();
    $categories = $category->getCategoriesByUser($user_id);
    if ($categories !== false) {
        return $categories;
    }
    return false;
}

function add_category_ctr($cat_name, $user_id)
{
    $category = new Category();
    $result = $category->add($cat_name, $user_id);
    if ($result) {
        return $result;
    }
    return false;
}

function update_category_ctr($cat_id, $cat_name, $user_id)
{
    $category = new Category();
    $result = $category->edit($cat_id, $cat_name, $user_id);
    if ($result) {
        return true;
    }
    return false;
}

function delete_category_ctr($cat_id, $user_id)
{
    $category = new Category();
    $result = $category->delete($cat_id, $user_id);
    if ($result) {
        return true;
    }
    return false;
}

function get_category_by_id_ctr($cat_id, $user_id)
{
    $category = new Category();
    $result = $category->get($cat_id, $user_id);
    if ($result) {
        return $result;
    }
    return false;
}
?>
