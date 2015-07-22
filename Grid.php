<?php

/**
 * 
 */
abstract class QuadTypeEnum 
{ 
                const GRASS = 'Grass';
                const BUG = 'Bug';
                const ROCK= 'Rock';
                const GOAL= 'Goal';
            };

/**
 * 
 */
class Node {

    public $parent_node; //refrence to the parent node
    public $value;
    public $row, $col;
    public $f, $g, $h;

    public function __construct($value, $row, $col) {
        $this->value = $value;
        $this->row = $row;
        $this->col = $col;
    }

}

/**
 * 
 */
class Grid {

    public $nodes; //2d array() of Nodes
    public $num_row;
    public $num_col;

    /**
     * 
     * @param array      $grid_1d is an 1d array of value
     * @param type $num_row
     * @param type $num_col
     */
    public function __construct($grid_1d, $num_row, $num_col) {
        $this->setGrid($grid_1d, $num_row, $num_col);
    }

    public function setGrid($array_1d, $num_row, $num_col) {
        foreach ($array_1d as $i => $node) {
            $index = convert1DTo2DIndex($i, $num_col);
            $row = $index['row'];
            $col = $index['col'];
            $this->nodes[$row][$col] = new Node($node, $row, $col);
        }
    }

    public function getQuad($row, $col) {
        return $nodes[$row][$col];
    }

    public function setQuad($row, $col, $value) {
        $nodes[$row][$col] = $value;
    }

    public function printGrid() {
        echo '<pre>', print_r($this->nodes), '</pre>';
    }

}











/*****************specific helper function section*****************/



/**
 * function runAstar
 * 
 * traverse the grid useing the A* algorithm  
 * 
 * @param Grid      $grid will hold the map of the world we are searching 
 * @param int       $start_row the row of the start node 
 * @param int       $start_col the col of the start node
 * @param int       $goal_row  the row of the goal node
 * @param int       $goal_col the col of the goal node
 * @param int       $max_row the last row index
 * @param int       $max_col the last col index
 * @return bool     true if path exsit, false otherwise
 */
function runAstar($grid, $start_row, $start_col, $goal_row, $goal_col, $max_row, $max_col) {
    //initialize the open list
    $open_list = array();
//initialize the closed list
    $close_list = array();
    $b_end_search = false; //if true we must end the search
    $goal_node = &$grid->nodes[$goal_row][$goal_col];
    $start_node = &$grid->nodes[$start_row][$start_col];
    $start_node->f = 0;
    $open_list[] = &$start_node;
    sortNodes($open_list, 'ASCE');
    //while the open list is not empty
    while (!empty($open_list)) {
        $q = null; //current_node
        //find the node with the least f on the open list, call it "q"
        //pop q off the open list
        sortNodes($open_list, 'ASCE');
        pop($open_list, 0, $q);
        //generate q's 8 successors and set their parents to q
        $successors = findSuccessors($grid->nodes, $q->row, $q->col, $max_row, $max_col);
        foreach ($successors as $key => $successor) {
            $temp = clone $successor;
            $temp->parent_node = &$q;
            //if the successor is an obstacle 
            if (isObstacle($temp)) {
                continue; // skip this successor if it is an obstacle
            }
            if (isGoal($temp, $goal_node)) {
                $successor->parent_node = $temp->parent_node;
                $b_end_search = true;
                break;
            }


            $temp->g = $q->g + 1; //
            $temp->h = sqrt(pow(($goal_node->row - $temp->row), 2) + pow(($goal_node->col - $temp->col), 2));
            $temp->f = $temp->g + $temp->h;
//            
//            $successor->g = $q->g + 1; //
//            $successor->h = sqrt(pow(($goal_node->row - $successor->row), 2) + pow(($goal_node->col - $successor->col), 2));
//            $successor->f = $successor->g + $successor->h;
            //does exist in open list 
            if (doesExist($successor, $open_list) && ($successor->f < $temp->f))
                continue;
            //does exist in close list
            if (doesExist($successor, $close_list) && ($successor->f < $temp->f))
                continue;

            //
            $successor->parent_node = $temp->parent_node;
            $successor->g = $temp->g;
            $successor->h = $temp->h;
            $successor->f = $temp->f;
            $open_list[] = &$successors[$key]; //&$successor;
        }
        $close_list[] = &$grid->nodes[$q->row][$q->col]; //&$q;
        if ($b_end_search == true)
            break;
    }
    return $b_end_search ? true : false; //return true if there is a path, false otherwise
}



/**
 * function isGoal
 * //check if the given node is the goal
 * @param Node $node
 * @param Node $goal
 * @return bool     true if the $node is the same as $goal,false otherwise  
 */
function isGoal($node, $goal) {
    //return ($node->row == $goal->row && $node->col == $goal->col);

    return isSameNode($node, $goal);

}

/**
 * 
 * @param Node $node_1
 * @param Node $node_2
 * @return bool     true if both nodes are the same, false otherwise
 */
function isSameNode($node_1, $node_2) {
    return ($node_1->row == $node_2->row && $node_1->col == $node_2->col);
}

/**
 * function findSuccessors
 * find the neighboring nodes of the given location
 * Note: here we only find the four closest neighbors( top, right,bottom,left)
 * 
 * @param array(Node)[][] $nodes    the grid, a 2d array of nodes
 * @param int $row        the current node row's index
 * @param int $col        the current node col's index
 * @param int $max_row     the last row index in the gird
 * @param int $max_col     the last col index in the grid
 * @return array(Node)  $successors      the array of successors
 */
function &findSuccessors(&$nodes, $row, $col, $max_row, $max_col) {
    $successors = array();
    $next_row = $row;
    $next_col = $col;




    //top 
    if ($row > 0 && $row <= $max_row) {
        $next_row = $row - 1;
        $next_col = $col;

        $successors[] = $nodes[$next_row][$next_col];
    }
    //bot
    if ($row >= 0 && $row < $max_row) {
        $next_row = $row + 1;
        $next_col = $col;

    $successors[] = $nodes[$next_row][$next_col];


 //       $successors[] = $nodes[$next_row][$next_col];
        //$next_col = $col - 1;
    }

    //right

    if ($col >= 0 && $col < $max_col) {
        $next_row = $row;
        $next_col = $col + 1;
        $successors[] = $nodes[$next_row][$next_col];
    }
    //left
    if ($col > 0 && $col <= $max_col) {
        $next_row = $row;
        $next_col = $col - 1;
        $successors[] = $nodes[$next_row][$next_col];
    }

    return $successors;
}

/**
 * function doesExist
 * check if the node already exist in the list   
 * @param Node $node    the node we want to check 
 * @param array(Node) $list    an array of nodes we want to check against
 * @return boolean      return true if the node exsit, false otherwise
 */
function doesExist($node, $list) {
    $result = false;
    foreach ($list as $element) {

        if (($node->row == $element->row) && ($node->col == $element->col)) {
            $result = true;
            break;
        }
    }
    return $result;
}

/**
 * function findReversePath
 * construct the a reversed path from the goal to the start node
 * Note: we need to revese the path array if we want the path from the start to the goal 
 * @param Node $start_node  the first node in the path
 * @param Node $goal_node   the last node in the path
 * @param Node $last_node   the last node been visited in the A* algorthim , we should remove this paramter cause it is not needed
 * @return array(Node)      the shortest path from the goal to the start 
 */
function findReversePath($start_node, $goal_node, $last_node) {
    $path = array();
    $current_node = null;
    //var_dump($goal_node);
    //   echo '<pre>', print_r($goal_node), '</pre>';
    if ($goal_node->parent_node != null) {
        $path[] = $goal_node;
        $current_node = $goal_node->parent_node;
    }
    while ($current_node->parent_node != null) {
        $path[] = $current_node;
        $current_node = $current_node->parent_node;
    }
    if (isSameNode($current_node, $start_node)) {
        $path[] = $current_node;
    }
//    echo '<br /> start of the path <br />';
//    echo '<pre>', print_r($path), '</pre>';
//    echo '<br /> end of the path <br />';
    return $path;
}

/**
 * function isObstacle
 * check if the given node is an obstacle or not
 * 
 * @param Node $node    the node in question
 * @return bool         true if the node is an obstacle, false otherwise
 */
function isObstacle($node) {
    $obstacle = QuadTypeEnum::ROCK;//'X';

    return $node->value == $obstacle;
}


/**
 * function sortNodes
 * 
 * this function will sort a list of astar nodes depending on the value of their 'f' attribute
 * order it ascendingly or descendingly. 
 * 
 * @param 1D array $nodes    the data we want to sort
 * @param string $order     tkae two values ASCE and DESC
 * 
 */
function sortNodes(&$nodes, $order) {

    usort($nodes, function($a, $b) use($order) {

        $result = '';
        if ($order == 'ASCE') {//ascending order
            $result = $a->f > $b->f;
        } else // $order == 'DESC' , descending order
            $result = $a->f < $b->f;
        return $result;
    });
    $nodes = array_values($nodes);
    //return $nodes;
}



/*****************general helper function section*****************/

/**
 * function convert2DTo1DIndex
 * take 2d index and converted to its equvelant 1d array's index 
 * @param int $row      row's index
 * @param int $col      col's index
 * @param int $num_col  last col index + 1
 * @return int          the 1d index
 */
function convert2DTo1DIndex($row, $col, $num_col) {
    $index_1d = $row * $num_col + $col;

    return $index_1d;
}

/**
 * function convert1DTo2DIndex
 * take a 1d index and converted to 2d index 
 * @param int $index_1d     the 1d index
 * @param int $num_col      the last col index + 1
 * @return 1D array(int)    hold the 2d index
 */
function convert1DTo2DIndex($index_1d, $num_col) {
    $index_2d = ['row' => 0, 'col' => 0];
//index_2d[0] = //row
//index_2d[1] = //col;
    $index_2d['row'] = floor($index_1d / $num_col);
    $index_2d['col'] = $index_1d % $num_col;
    return $index_2d;
}

/**
 * function pop
 * pop an element of the list
 * @param array     $list
 * @param (int or string)   $index
 * @param refrence      $dis the element that will hold the result
 * @return return the refrence 
 */
function pop(&$list, $index, &$dis) {
    $dis = $list[$index];
    unset($list[$index]);
    return $dis;
}
?>