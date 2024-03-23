<?php

/**
 * Theme Collection Block Template.
*/

// Create id attribute allowing for custom "anchor" value.
$id = 'theme-collection-' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'theme-collection';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}

// Load values and assing defaults.

echo $fgfdg =  get_field('fgfdg');
?>

nbnbvnvbnvbnvbnvbnb


<!-- Theme Collection Section End -->	