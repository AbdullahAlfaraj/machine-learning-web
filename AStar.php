<?php
require_once 'Grid.php';

$grid_1d = [
    7, 'X', 9,
    4, 5, 6,
    1, 'X', 3,
];

$grid_1 = new Grid($grid_1d, 3, 3);

$start_row = 0;
$start_col = 0;

$goal_row = 2;
$goal_col = 2;

$max_row = 2;
$max_col = 2;


$b_find_path = runAstar($grid_1, $start_row, $start_col, $goal_row, $goal_col, $max_row, $max_col);
$start_node = $grid_1->nodes[$start_row][$start_col];
$goal_node = $grid_1->nodes[$goal_row][$goal_row];

$last_node = null;
if ($b_find_path)
    findReversePath($start_node, $goal_node, $last_node);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AStar Algorithm</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
       <link rel="stylesheet" type="text/css" href="../css/cssreset-min.css" />
        <style>

            

          
        </style>        

        <!--include jquery-->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <link type="text/css" href="css/grid_style.css" rel="stylesheet" />


        <script type='text/javascript' src="astar_js.js"></script>

        <script>
            function Position(row, col) {
                this.row = row;
                this.col = col;
            }
            set_moves = [new Position(3, 2),
                new Position(3, 3),
                new Position(3, 4),
                new Position(4, 4),
                new Position(4, 5)];

            console.log(set_moves);

            QuadTypeEnum = {
                GRASS: 'Grass',
                BUG: 'Bug',
                ROCK: 'Rock',
                GOAL: 'Goal'
            };

            grid_1d = [];//['Grass','Grass','Grass','Grass'];
            var selected_type = QuadTypeEnum.BUG;



            current_bug_index = {row: 3, col: 2};

            current_goal_index = {row: 0, col: 0};

            num_row = 8;//5; //randomNumberFromRange(5, 10);//5;
            num_col = 10;//6;

            var moves = ['a', 'b', 'c', 'd'];
            var current_move_index = 0;

            $(function () {

                num_row = 8;//5; //randomNumberFromRange(5, 10);//5;
                num_col = 10;//6;

                $("#row_i").val(num_row);
                $("#col_i").val(num_col);
                setGrid(num_row, num_col);
                $("#row_i").change(function () {
                    $grid = $('.grid_astar');
                    var row = $(this).val();
                    
                    //use one of the following but not both
                    $grid.data('num_row',row);//store the new number of rows value in the grid
                    num_row = row;//gloable variable
                    
                    var col = $grid.data('num_col');
                    setGrid(row, col);
                });
                $("#col_i").change(function () {
                    $grid = $('.grid_astar');
                    var row = $grid.data('num_row');
                    var col = $(this).val();
                    //use one of the following but not both
                    $grid.data('num_col',col);//store the new number of cols value in the grid
                    num_col = col;
                    
                    setGrid(row, col);
                });
                $("#random_grid").click(function () {

                    num_row = randomNumberInRange(5, 10); //5;
                    num_col = randomNumberInRange(5, 10);
                    $("#row_i").val(num_row);
                    $("#col_i").val(num_col);
                    setGrid(num_row, num_col);
                });
                $(".select_item").click(function () {
                    selected_type = $(this).data('type');
                    //alert("type is " + type);
                    //$('#selected_type').text(selected_type);
                    $("#current_item").attr('src',getImagePath(selected_type));

                });
                current_bug_index.row = 3;
                current_bug_index.col = 2;
                //current_goal_index.row = 5;
                //current_goal_index.col = 5;
                changeQuadType(current_bug_index.row, current_bug_index.col, QuadTypeEnum.BUG);

                current_goal_index.row = 4;
                current_goal_index.col = 5;
                changeQuadType(current_goal_index.row, current_goal_index.col, QuadTypeEnum.GOAL);


                $("#next_move").click(function () {
                    var moves = set_moves;
                    current_move_index >= moves.length - 1 ? current_move_index = current_move_index : ++current_move_index;

                    //$('#position').text(moves[current_move_index]);
                    //set_moves[current_move_index];
                    var row = set_moves[current_move_index].row;
                    var col = set_moves[current_move_index].col;
                    moveBug(current_bug_index, row, col);
                });
                $("#prev_move").click(function () {

                    current_move_index <= 0 ? current_move_index = current_move_index : --current_move_index;

//                    $('#position').text(moves[current_move_index]);
                    var row = set_moves[current_move_index].row;
                    var col = set_moves[current_move_index].col;
                    moveBug(current_bug_index, row, col);

                });
                $("#run_astar").click(function () {

                    user_input = {"num_row": num_row, "num_col": num_col, "grid_1d": grid_1d,
                        "start_index_2d": current_bug_index,
                        "goal_index_2d": current_goal_index};
                    jsPostSetMove(user_input, 'astar_handler', 'it worked');
                     current_move_index = 0;//reset the move index to the first move

                });
                $('#select_bug').data('type', QuadTypeEnum.BUG);
                $('#select_grass').data('type', QuadTypeEnum.GRASS);
                $('#select_rock').data('type', QuadTypeEnum.ROCK);
                $('#select_goal').data('type', QuadTypeEnum.GOAL);



                user_input = {"num_row": num_row, "num_col": num_col, "grid_1d": grid_1d,
                    "start_index_2d": current_bug_index,
                    "goal_index_2d": current_goal_index};
                jsPostSetMove(user_input, 'astar_handler', 'it worked');
               
            });






        </script>

    </head>
    <body>
        <!--<div class="container-fluid">-->

        <div id="window_width">Width:0</div>
        <div id="window_height">Height:0</div>
        <div class="container">
            <div class="c_div">
                <form>
                    Row:<input type="text" name="row" value="" id="row_i"/>
                    Column:<input type="text" name='col' value="" id="col_i" />
                    <input class='' type="button" id="create_grid" value ="Create Grid" />
                    <input class='' type="button" id="random_grid" value="Random Grid" />
                </form>

            </div>
            <img src="icon2/bug.png" alt="Smiley face" height="42" width="42" id='current_item'/>
            <button class='select_item' id='select_bug' data-type='1' title='Place the "Bug" in the starting location'>Bug</button>
            <button class='select_item' id='select_grass' data-type='2' title='"Grass" is an empty field'>Grass</button>
            <button class='select_item' id='select_rock' data-type='3' title='"Rock" is an obstacle that cann&apos;t be walked over '>Rock</button>
            <button class='select_item' id='select_goal' data-type='4' title='an Apple that represents the final destination of the Bug'>Goal</button>
            <div class='c_div' style='margin-bottom:10px;'>
                <button class='move' id='prev_move' title="move one step farther from the goal">Prev</button>
                <button class='move' id='next_move' title="move one step closer to the goal">Next</button>  
                <button class='move' id='run_astar' title="find the shortest path, if there is any!">Run</button>  
            </div>

            <div id='content_container'>

                <div class="grid_astar ">
                    <!-- <div class="row">
                         <div class="quad"></div>
                         <div class="quad"></div>
                     </div>
                     <div class="row">
                         <div class="quad"></div>
                         <div class="quad"></div>  
                     </div>
                     <div class="quad"></div>
                     <div class="quad"></div>
         
                     <div class="quad"></div>
                     <div class="quad2"></div>
                     <div class="quad" style="background:yellow;"></div>
                     <br />-->
                </div>
<!--                <div>Current Position:<span id='position'>none</span></div>-->
<!--                <div>Selecetd Type:<span id='selected_type'>none</span></div>-->
            </div>

        </div>

    </body>
