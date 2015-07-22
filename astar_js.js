current_goal_index = {row: 0, col: 0};

//function convert2DTo1DIndex
//usefull to convert 2D array index to 1D array index
// 
//row       : int     the vertical index
//col       : int     the horizontal index
//num_col   : int     the max horizontal index + 1
//return    : int     the 1D corresponding index  
function convert2DTo1DIndex(row, col, num_col)
{
    var index_1d = row * num_col + col;

    return index_1d;
}

//function convert1DTo2DIndex
//usefull to convert 1D array index to 2D array index
//
//index_1d  : int     the 1D index we want to convert 
//num_col   : int     the max horizontal index + 1
//
//return    : obj{row,col}     the 2D corresponding index
//row       : int     the vertical index
//col       : int     the horizontal index
function convert1DTo2DIndex(index_1d, num_col)
{
    var index_2d = {row: 0, col: 0};
    //index_2d[0] = //row
    //index_2d[1] = //col;
    index_2d.row = Math.floor(index_1d / num_col);
    index_2d.col = index_1d % num_col;
    return index_2d;
}

//function convertNumTypeToEnumType
//a mathmatical function that map a set of numerical values to their desired Enum values 
//type      : int       a number
//return    : Enum(QuadTypeEnum)       an Enum value that represent the block on the grid
function convertNumTypeToEnumType(type)
{

    if (type <= 4 && type >= 0)//[0,4] = grass
    {
        type = QuadTypeEnum.GRASS;
    }
    else if (type >= 5 && type <= 6)//[5,6] = rock
    {
        type = QuadTypeEnum.ROCK;
        
    }
    else if (type == 7)//[7] = bug 
    {
        type = QuadTypeEnum.BUG;
    }
    return type;
}


//function randomNumberInRange
//Note: keep in mind this function is a pseudorandom number generator
//min       : int       the smallest number could be returned
//max       : int       the largest number could be returned
//return    : int       the random number
function randomNumberInRange(min, max)
{
    var randomNumber = Math.floor(Math.random() * (max - min + 1) + min);
    return randomNumber;
}



//function findQuadJQ
//this function will traverse the DOM and return the jq element in the gird, using the 2d index of the element  
//index_row     : int       the vertical index of the element
//index_col     : int       the horizontal index of the element
//return        : (JQ element)      quad
function findQuadJQ(index_row, index_col)
{
    index_row = index_row + 1;//increment by one cause nth-child use indexing start from 1 not 0
    index_col = index_col + 1;
    $row = $('.grid_astar .row_g:nth-child(' + index_row + ')');
    //console.log($row[0]);
    $col = $row.children(':nth-child(' + index_col + ')');
    //console.log($col[0]);
    return $col;
}


//function getImagePath
//return the picture path when its given it corresponding value
//given the value of GRASS will return the picture of the GRASS to be used in html img element
//type      : Enum(QuadTypeEnum)  the type (e.g. GRASS , ROCK, ... etc)    
//return    : string        the path of the image
function getImagePath(type)
{
    var src = '';
    if (type == QuadTypeEnum.GRASS)
        src = "icon2/grass_1.png";
    else if (type == QuadTypeEnum.BUG)
        src = "icon2/bug.png";
    else if (type == QuadTypeEnum.ROCK)
        src = "icon2/rock.png";
    else if (type == QuadTypeEnum.GOAL)
        src = "icon2/apple_1.png";
    return src;
}


//function getQuadType
//return the type of the given Quad
//row       : int   row index
//col       : int   col index
//return    : Enum(QuadTypeEnum)    the type value
function getQuadType(row,col)
{
    $quad = findQuadJQ(row,col);
    return $quad.data('type');
}

//function moveBug
//move the starting point from one location to another
//position      : obj{row,col}      current position of the bug
//new_row       : int       the bug new row
//new_col       : int       the bug new col
function moveBug(position, new_row, new_col)
{
    //dont do anything if the new and the current locations are the same 
    if(position.row == new_row &&
            position.col == new_col)
        return;
    
    var old_position = jQuery.extend({},position);//do shallow copy
    
    //change current bug position to grass
    changeQuadType(position.row, position.col, QuadTypeEnum.GRASS);
    //set current bug position to new value
    
    position.row = new_row;
    position.col = new_col;
    //change the quad in the grid to reflect the new position of the bug
    changeQuadType(position.row, position.col, QuadTypeEnum.BUG);
    
      //if the bug leave the goal location we should runder the goal again
    if(old_position.row == current_goal_index.row &&
            old_position.col == current_goal_index.col)
    {
        moveGoal(current_goal_index, current_goal_index.row, current_goal_index.col);
        
        
    }

}


//function moveGoal
//move the goal from one position to another
//position      : obj{row,col}      current position of the goal
//new_row       : int       the goal new row
//new_col       : int       the goal new col
function moveGoal(position, new_row, new_col)
{
    //1)check if bug is in the current goal location
    if(getQuadType(position.row,position.col) == QuadTypeEnum.BUG)
    {
        //do nothing
    }
    else//2) otherwise change current goal position to grass
    {
        
    changeQuadType(position.row, position.col, QuadTypeEnum.GRASS);
    }
    
    //set current bug position to new value
    position.row = new_row;
    position.col = new_col;
    //3)change the quad in the grid to reflect the new position of the goal
    changeQuadType(position.row, position.col, QuadTypeEnum.GOAL);

}

//function changeQuadType
//change the type of a quad
//index_row     : int       quad row index
//index_col     : int       quad col index
//type          : Enum(QuadTypeEnum)        the type of the quad
function changeQuadType(index_row, index_col, type)
{
    $quad = findQuadJQ(index_row, index_col);
    $quad.data('type', type);
    var index_1d = convert2DTo1DIndex(index_row,index_col,num_col);
    grid_1d[index_1d] =  type;
    
    var src = getImagePath(type);
    $quad.attr('src', src);
}

//function createQuad
//create a JQ element, an image to be exact. the element will represent a quad in a gird and will hold its type and location 
//index     : int       the col index of the quad
//type      : int       the numerical type of the quad that will be converted to QuadTypeEnum
//width     : int       the width of the quad
//return    : JQ element        the quad to be returned, so we could append it to the dom
function createQuad(index, type, width)
{
//    var $div = $("<div>", {class: "quad_g",
//        "data-index": index,
//        "data-type": ''});
    var $img = $("<img>", {src: "",
        class: "quad_g",
        "data-index": index,
        "data-type": ''});
    var src = '';

    type = convertNumTypeToEnumType(type);
    src = getImagePath(type);
    $img.attr('src', src);
    //$div = $img;
    $img.click(function () {
//                    alert("you clicked me!\n" +
//                            "col:" + this->data('index')
//                            +"row:");


        var $current_element = $(this);
        var col_index = $current_element.data('index');
        var row_index = $current_element.parent('.row_g').data('index');
        /*alert("row: " + row_index + "\n" +
         "col: " + col_index);*/
        if (selected_type == QuadTypeEnum.BUG)
            moveBug(current_bug_index, row_index, col_index);
        else if (selected_type == QuadTypeEnum.GOAL)
            moveGoal(current_goal_index, row_index, col_index);
        else
            changeQuadType(row_index, col_index, selected_type);

    });
    //$img.width(width);
    $img.css('width', width +"px");
    return $img;
}

//function createRow
//create a row and return it. we will use createQuad to create quads and then appends them this row 
//note: row and quad have the same height
//index     : int       the row index
//height    : int       the height of the row/quad
//return    : (JQ element)      the DOM representation of the row
function createRow(index, height)
{
    var $div = $("<div>", {class: "row_g",
        "data-index": index
    });
    $div.height(height);
    return $div;
}


//function setGrid
//create a grid, and populate it randomly
//note: two values will be gloably modefied.1) grid_1d is a 1d representation of the map/grid 
//                                          2) $(".grid_astar") is the DOM/JQ element representation of the grid
//num_row   : int       the number of rows in the grid, this is the last row index + 1
//num_col   : int       the number of cols in the grid, this is the last col index + 1
//
function setGrid(num_row, num_col)
{
    //cant make more than 100 X 100 grid or take any nigitive value 
    if (num_row > 100 || num_col > 100 ||
            num_row <= 0 || num_col <= 0)
        return;
    $grid = $(".grid_astar");
    $grid.empty();
    grid_1d = [];//empty grid_1d
    $grid.data('num_row', num_row);
    $grid.data('num_col', num_col);
    var height = $(window).height();
    var width = $(window).width();
    var grid_width = width / 2;
    var grid_height = height / 2;
    var quad_length = 0;
    //var num_row = 10;
    //var num_col = 10;
    var width_of_col = grid_width / num_col;
    var height_of_col = grid_height / num_row;
    if (width_of_col > height_of_col)
    {
        //we need to figure out the length of a quad
        quad_length = height_of_col;
    }
    else
    {

        quad_length = width_of_col;
    }
    //floor is need it, so we could avoid the Subpixel Layout/Roundding error problem
    quad_length = Math.floor(quad_length);
    //recalculate the width and height of the grid
    grid_height = quad_length * num_row;
    grid_width = quad_length * num_col;
    //var width_of_col = grid_width/num_col;
    //var height_of_col = width_of_col;
    $(".grid_astar").width(grid_width);
    //$('.grid_astar').css('width', grid_width +"px");
     
    for (var i = 0; i < num_row; ++i)//for each row do
    {
        var $row = createRow(i, quad_length);
        for (var j = 0; j < num_col; ++j)//for each col do
        {
            var type = randomNumberInRange(0, 6)//1;//normal type
            var $quad = createQuad(j, type, quad_length);
            $row.append($quad);
            //
            var value = convertNumTypeToEnumType(type);
            grid_1d.push(value);
        }
        $(".grid_astar").append($row);
    }

    $('#window_width').text("Width:" + width + "px");
    $('#window_height').text("Height:" + height + "px");
}



//function jsPostSetMove
//make an ajax post request to retrieve the shortest path from starting point to the goal
//Note: sometime there is no such a path.
// 
//user_input    : obj{...}      the input that will be posted, this is the data the application need to calculate the set_moves
//user_input.num_row    : int                   the last row index + 1
//user_input.num_col    : int                   the last col index + 1
//user_input.grid_1d    : 1D array              the gird/map in 1D array,note: 1d array is easer to deal with than 2d array in this case
//user_input.start_index_2d : obj{row,col}      starting location of the bug
//user_input.goal_index_2d  : obj{row,col}      the goal location, the location of the apple
//
//handler_url       : string        (url/path) of the application that will receive the request
//success_message   : string        the message would be printed when the request succeeded     
//
//ex:
//user_input = {"num_row": num_row, "num_col": num_col, "grid_1d": grid_1d,
//                        "start_index_2d": current_bug_index,
//                        "goal_index_2d": current_goal_index};
function jsPostSetMove(user_input, handler_url, success_message)
{

    $.post(handler_url, user_input, function (data, status) {
       // alert(success_message);
        var obj = jQuery.parseJSON(data);
        //$('#read_comment_div').html(obj['comments']);
        //set_moves is the path from the starting location to the goal
        set_moves = obj['path'];
        console.log(obj['path']);
        colorPath(set_moves);
        //updateCommentSectionEvent();
    });

    console.log(grid_1d);
}


//function colorPath
//highlight the path from the start to the goal node
//
//path  : array of nodes{row,col}         the shortest path between the start and the goal nodes
function colorPath(path)
{

   //remove highlight from all nodes in the grid
    $('.grid_astar > .row_g .quad_g').css({"border-color":"black"});
   
    
    //find the jq array of the path
    $.each(path,function(index,value){
   
    $quad = findQuadJQ(value.row,value.col);
    $quad.css({"border-color":"orange"});
});
    
    
}