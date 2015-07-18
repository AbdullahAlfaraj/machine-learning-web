<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'jq_functions.php';
require_once 'Grid.php';

function position($row, $col) {
    return array('row' => $row, 'col' => $col);
}

//$_POST['do'] = 5;
//$_POST['do'] = [5,3];
//if(isset($_POST["do"]))
//        echo 'isset var'; 
//echo '<br />',print_r($_POST['grid_1d']),'<br />';
//$ui_grid_1d = getGetPostVariable('grid_1d', 'p');//does not work becuase $_POST["grid_1d"] is an array
//$ui_grid_1d = ['grass'];
if (isset($_POST["grid_1d"]))
    $ui_grid_1d = $_POST["grid_1d"];

if (isset($_POST["start_index_2d"]))
    $ui_start_index_2d = $_POST["start_index_2d"];

if (isset($_POST["goal_index_2d"]))
    $ui_goal_index_2d = $_POST["goal_index_2d"];


$ui_num_row = getGetPostVariable('num_row', 'p');
$ui_num_col = getGetPostVariable('num_col', 'p');
$grid_1 = new Grid($ui_grid_1d, $ui_num_row, $ui_num_col);


$start_row = $ui_start_index_2d['row']; //0;
$start_col = $ui_start_index_2d['col']; //0;

$goal_row = $ui_goal_index_2d['row']; //4;
$goal_col = $ui_goal_index_2d['col']; //5;

$max_row = $ui_num_row - 1;
$max_col = $ui_num_col - 1;

$b_find_path = runAstar($grid_1, $start_row, $start_col, $goal_row, $goal_col, $max_row, $max_col);
$start_node = $grid_1->nodes[$start_row][$start_col];
$goal_node = $grid_1->nodes[$goal_row][$goal_col];

$last_node = null;
$raw_path = array();
if ($b_find_path) {
    $raw_path = findReversePath($start_node, $goal_node, $last_node);
    $raw_path = array_reverse($raw_path);
}
$path = array();
foreach ($raw_path as $node) {
    $row = $node->row;
    $col = $node->col;
    $path[] = position($row, $col);
}

//$path = [position(0, 0),
// position(0, 1),
//    position(1, 1)];
$output = array('path' => $path);
//$post_data = json_encode(array('like_votes' => $audio_file['like_votes'], 'dislike_votes' => $audio_file['dislike_votes']));
$post_data = json_encode($output);


echo $post_data;
