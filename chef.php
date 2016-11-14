<!DOCTYPE html>

<?php

ini_set('session.save_path', '../session');
    session_start();

    if(!isset($_SESSION['user_session']))
    {
        header("Location: logout.php");
    }
//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

//keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_q7p7", "a56269087", "ug");


function printErrorDialog($cmd, $err) {
        echo "<div  class='modal fade in error-modal' style='display:block' tabindex='-1' role='dialog'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h4 class='modal-title'>Error</h4>
                  </div>
                  <div class='modal-body'>
                  <p>" . $cmd . "</p>
                    <p>" . $err ."</p>
                  </div>
                  <div class='modal-footer'>
                    <button class='error-modal-btn' type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                  </div>
                </div>
              </div>
            </div>";
    }

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        
        $e = OCI_Error($db_conn); // For OCIParse errors pass the
        // connection handle
       
        $success = False;
        printErrorDialog($cmdstr, $e['message']);

    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle

        $success = False;
        printErrorDialog($cmdstr, $e['message']);
    } else {

    }
    return $statement;

}











function executeBoundSQL($cmdstr, $list) {
    /* Sometimes a same statement will be excuted for severl times, only
     the value of variables need to be changed.
     In this case you don't need to create the statement several times;
     using bind variables can make the statement be shared and just
     parsed once. This is also very useful in protecting against SQL injection. See example code below for       how this functions is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        $e = OCI_Error($db_conn);
        $success = False;
        printErrorDialog($cmdstr, $e['message']);
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

        }
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            
            $e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
                    $success = False;
        printErrorDialog($cmdstr, $e['message']);

        }
    }

}

function printResult($result) { //prints results from a select statement


    echo "<br>Got data from Chef table<br>";
    echo "<table>";
    echo '<table class="table">';
    echo "<tr><th>EmployeeID</th><th>Position</th><th>Name</th><th>SignedIn</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";

}

function printResult2($result) { //prints results from a select statement
    echo "<br>Got data from Dishes_Makes_ConsistsOf Table<br>";
    echo "<table>";
    echo '<table class="table">';
    echo "<tr><th>Type</th><th>EmployeeID</th><th>Order Number</th><th>Status</th><th>InventoryUsage</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";

}

function printResult3($result) { //prints results from a select statement
    echo "<br>Currently Working Chefs<br>";
    echo "<table>";
    echo '<table class="table">';
    echo "<tr><th>EmployeeID</th><th>Position</th><th>Name</th><th>SignedIn</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";

}

function printResult4($result, $val) { //prints results from a select statement
    echo "<br>Data from Dishes_Makes_ConsistsOf<br>";
    echo "<table>";
    echo '<table class="table">';
    echo "<tr><th>" . $val ."</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";

}

function printResult5($result) { //prints results from a select statement
    echo "<br>Number of Chefs Cooking<br>";
    echo "<table>";
    echo '<table class="table">';
    echo "<tr><th>Count</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";

}

function printResult6($result) { //prints results from a select statement
    echo "<br>Total Inventory Usage<br>";
    echo "<table>";
    echo '<table class="table">';
    echo "<tr><th>Total</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";

}

function printResult7($result) { //prints results from a select statement
    echo "<br>Data for on duty chefs who have incomplete dishes<br>";
    echo "<table>";
    echo '<table class="table">';
    echo "<tr><th>Slackers</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";

}

function printPositionDropdown() {
        global $conn;
        $st = OCIParse($conn, 'select position from Chef');
        $result = OCIExecute($st);
        while ($row = oci_fetch_array($st, OCI_RETURN_NULLS+OCI_ASSOC)) {
           print '<option>';
           foreach ($row as $item) {
              print $item ? htmlentities($item) : ' ';
           }
           print '</option>';
        }
    }


function populateTables() {

        executePlainSQL("Drop table Uses");
        executePlainSQL("Drop table Updates");
        executePlainSQL("Drop table Dishes_Makes_ConsistsOf");
        executePlainSQL("Drop table Phone_Customer");
        executePlainSQL("Drop table Inventory");
        executePlainSQL("Drop table Process");
        executePlainSQL("Drop table Chef");
        executePlainSQL("Drop table Server_Cancel_Order");
        executePlainSQL("Drop table Order_Makes");
        executePlainSQL("Drop table Dishes");
        executePlainSQL("Drop table Customer");

        executePlainSQL("Drop sequence seq_customer");
        executePlainSQL("Drop sequence seq_order_makes");

        // Create new table...
        executePlainSQL("create table Chef (employee_id number, position varchar2(30), name varchar2(30), signed_in varchar2(30), primary key (employee_id))");
        executePlainSQL("create table Inventory (fullness number, primary key (fullness))");
        executePlainSQL("create table Customer (c_order_number number, name varchar2(30), credit_card_number number, take_out varchar2(10), primary key (c_order_number))");
        executePlainSQL("create table Dishes (type varchar2(20), price number, primary key (type))");
        executePlainSQL("create table Server_Cancel_Order (employee_id number, name varchar(30), c_order_number number not null, signed_in varchar2(30), primary key(employee_id), foreign key (c_order_number) references Customer ON DELETE CASCADE)");
        executePlainSQL("create table Order_Makes (order_number number, c_order_number number, type varchar2(20), cash_or_credit varchar(10), is_paid varchar2(10), primary key (order_number), foreign key (c_order_number) references Customer ON DELETE CASCADE)");
        executePlainSQL("create table Process (order_number number, employee_id number, primary key (order_number), foreign key (order_number) references Order_Makes ON DELETE CASCADE, foreign key (employee_id) references Server_Cancel_Order ON DELETE CASCADE)");
        executePlainSQL("create table Phone_Customer (c_order_number number, name varchar(20), credit_card_number number, take_out varchar(20), pick_up_time number, phone_number number, primary key (c_order_number), foreign key (c_order_number) references Customer ON DELETE CASCADE)");
        executePlainSQL("create table Dishes_Makes_ConsistsOf (type varchar(20), employee_id number, order_number number, status varchar(10), inventory_usage number, primary key (type), foreign key (type) references Dishes ON DELETE CASCADE, foreign key (employee_id) references Chef ON DELETE CASCADE, foreign key (order_number) references Order_Makes ON DELETE CASCADE)");
        executePlainSQL("create table Uses (fullness number, type varchar(20), primary key (fullness, type), foreign key (fullness) references Inventory ON DELETE CASCADE, foreign key (type) references Dishes_Makes_ConsistsOf ON DELETE CASCADE)");
        executePlainSQL("create table Updates (employee_id number, fullness number, primary key (employee_id, fullness), foreign key (employee_id) references Chef ON DELETE CASCADE, foreign key (fullness) references Inventory ON DELETE CASCADE)");


        //Creates sequences for primary keys
        executePlainSQL("CREATE SEQUENCE seq_customer MINVALUE 1 START WITH 6 INCREMENT BY 1 CACHE 10");
        executePlainSQL("CREATE SEQUENCE seq_order_makes MINVALUE 1 START WITH 6 INCREMENT BY 1 CACHE 10");

    }

    function insertTuples() {
        executePlainSQL("insert into Chef values(01, 'Head Chef', 'Leo', 'yes')");
        executePlainSQL("insert into Chef values(02, 'Master Chef', 'Kevin', 'no')");
        executePlainSQL("insert into Chef values(03, 'Line Cook', 'John', 'yes')");
        executePlainSQL("insert into Chef values(04, 'Sous Chef', 'Josiah', 'no')");
        executePlainSQL("insert into Chef values(05, 'Saucier', 'Gabe', 'yes')");

        executePlainSQL("insert into Inventory values(100)");
        executePlainSQL("insert into Inventory values(90)");
        executePlainSQL("insert into Inventory values(80)");
        executePlainSQL("insert into Inventory values(70)");
        executePlainSQL("insert into Inventory values(60)");
        executePlainSQL("insert into Inventory values(50)");
        executePlainSQL("insert into Inventory values(40)");
        executePlainSQL("insert into Inventory values(30)");
        executePlainSQL("insert into Inventory values(20)");
        executePlainSQL("insert into Inventory values(10)");
        executePlainSQL("insert into Inventory values(0)");

        executePlainSQL("insert into Customer values(001, 'Jacob', 795034622121, 'yes')");
        executePlainSQL("insert into Customer values(002, 'Francis', 143725152512, 'yes')");
        executePlainSQL("insert into Customer values(003, 'Bobby', 825484465623, 'no')");
        executePlainSQL("insert into Customer values(004, 'Jessica', 680797673431, 'yes')");
        executePlainSQL("insert into Customer values(005, 'Andrew', 749035611521, 'no')");

        executePlainSQL("insert into Server_Cancel_Order values(06, 'Jacquelynn', 001, 'yes')");
        executePlainSQL("insert into Server_Cancel_Order values(07, 'Timothy', 002, 'no')");
        executePlainSQL("insert into Server_Cancel_Order values(08, 'Blake', 003, 'yes')");
        executePlainSQL("insert into Server_Cancel_Order values(09, 'Denice', 004, 'no')");
        executePlainSQL("insert into Server_Cancel_Order values(10, 'Aaron', 005, 'yes')");


        executePlainSQL("insert into Dishes values('Fruit', 10)");
        executePlainSQL("insert into Dishes values('Sandwich', 10)");
        executePlainSQL("insert into Dishes values('Creme Brulee', 20)");
        executePlainSQL("insert into Dishes values('Mini Cheesecake', 20)");
        executePlainSQL("insert into Dishes values('Giant Cheesecake', 50)");

        executePlainSQL("insert into Order_Makes values(1, 1, 'Fruit', 'cash', 'true')");
        executePlainSQL("insert into Order_Makes values(2, 2, 'Sandwich', 'credit', 'true')");
        executePlainSQL("insert into Order_Makes values(3, 3, 'Creme Brulee', 'cash', 'true')");
        executePlainSQL("insert into Order_Makes values(4, 4, 'Mini Cheesecake', 'credit', 'false')");
        executePlainSQL("insert into Order_Makes values(5, 5, 'Giant Cheesecake', 'cash', 'false')");

        executePlainSQL("insert into Process values(001, 06)");
        executePlainSQL("insert into Process values(002, 07)");
        executePlainSQL("insert into Process values(003, 08)");
        executePlainSQL("insert into Process values(004, 09)");
        executePlainSQL("insert into Process values(005, 10)");

        executePlainSQL("insert into Phone_customer values(001, 'Jacob', 795034622121, 'yes', 1200, 6041111111)");
        executePlainSQL("insert into Phone_customer values(002, 'Francis', 143725152512, 'yes', 1300, 6041111112)");
        executePlainSQL("insert into Phone_customer values(003, 'Bobby', 825484465623, 'no', 1400, 6041111113)");
        executePlainSQL("insert into Phone_customer values(004, 'Jessica', 680797673431, 'yes', 1500, 6041111114)");
        executePlainSQL("insert into Phone_customer values(005, 'Andrew', 749035611521, 'no', 1600, 6041111115)");

        executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Fruit', 01, 001, 'complete', 5)");
        executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Sandwich', 02, 002, 'incomplete', 10)");
        executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Creme Brulee', 03, 003, 'incomplete', 15)");
        executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Mini Cheesecake', 04, 004, 'complete', 5)");
        executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Giant Cheesecake', 05, 005, 'incomplete', 20)");

        executePlainSQL("insert into Uses values(60, 'Fruit')");
        executePlainSQL("insert into Uses values(70, 'Sandwich')");
        executePlainSQL("insert into Uses values(80, 'Creme Brulee')");
        executePlainSQL("insert into Uses values(90, 'Mini Cheesecake')");
        executePlainSQL("insert into Uses values(100, 'Giant Cheesecake')");

        executePlainSQL("insert into Updates values(01, 90)");
        executePlainSQL("insert into Updates values(02, 80)");
        executePlainSQL("insert into Updates values(03, 70)");
        executePlainSQL("insert into Updates values(04, 60)");
        executePlainSQL("insert into Updates values(05, 100)");
    }


?>





<?php

// Connect Oracle...
$success = True; 
if ($db_conn) {



    if (array_key_exists('reset', $_POST)) {

        populateTables();
        insertTuples();
        OCICommit($db_conn);

    } else
        if (array_key_exists('insertsubmit', $_POST)) {

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['employee_id'],
                ":bind2" => $_POST['position'],
                ":bind3" => $_POST['name']
               
                
            );
            $alltuples = array ($tuple);
            executeBoundSQL("insert into Chef values (:bind1, :bind2, :bind3, 'no')", $alltuples);
            OCICommit($db_conn);

            

            } else
                if (array_key_exists('deletesubmit', $_POST)) {

                    $tuple = array (
                        ":bind1" => $_POST['employee_id'],
                        ":bind2" => $_POST['position'],
                        ":bind3" => $_POST['name'],
                        ":bind4" => $_POST['signed_in']
        
                    );
                    $alltuples = array ($tuple);
                    executeBoundSQL("delete from Chef where employee_id = :bind1", $alltuples);
                    OCICommit($db_conn);
               
        } else
            if (array_key_exists('updatesubmit', $_POST)) {
                // Update tuple using data from user
                $tuple = array (
                    ":bind1" => $_POST['employee_id'],
                    ":bind2" => $_POST['position']
                    
              
                );
                $alltuples = array (
                    $tuple
                );
                executeBoundSQL("update Chef set position=:bind2 where employee_id=:bind1", $alltuples);
                OCICommit($db_conn);
             
                
                } else
                    if (array_key_exists('signinsubmit', $_POST)) {
                // Update tuple using data from user
                $tuple = array (
                    ":bind1" => $_POST['employee_id'],
                    ":bind2" => $_POST['signed_in']
                    
                );
                $alltuples = array (
                    $tuple
                );
                executeBoundSQL("update Chef set signed_in=:bind2 where employee_id=:bind1", $alltuples);
                 OCICommit($db_conn);


            
            
            } else
                if (array_key_exists('makesubmit', $_POST)) {

                    $tuple = array (
                        ":bind1" => $_POST['employee_id'],
                        ":bind2" => $_POST['inventory_usage']

                       
                    
        
                    );
                    $alltuples = array ($tuple);
                    executeBoundSQL("update Dishes_Makes_ConsistsOf set status = 'complete' 
                     where employee_id=:bind1 AND employee_id IN (select employee_id from Chef where signed_in = 'yes')", $alltuples);
                    //executeBoundSQL("insert into Inventory values (5)")
                    //executeBoundSQL("update Inventory set fullness = fullness - 2 where fullness = 100", $alltuples);
                    OCICommit($db_conn);
                
            
                } else
                if (array_key_exists('dostuff', $_POST)) {
                    // Insert data into table...
                    executePlainSQL("insert into Customer values (001, 'Frank')");
                    // Inserting data into table using bound variables
                    $list1 = array (
                        ":bind1" => 6,
                        ":bind2" => "All"
                    );
                    $list2 = array (
                        ":bind1" => 7,
                        ":bind2" => "John"
                    );
                    $allrows = array (
                        $list1,
                        $list2
                    );
                    executeBoundSQL("insert into Customer values (:bind1, :bind2)", $allrows); //the function takes a list of lists
                    // Update data...
                    //executePlainSQL("update tab1 set nid=10 where nid=2");
                    // Delete data...
                    //executePlainSQL("delete from Customer where c_order_number=001");
                    OCICommit($db_conn);
                }

    //Commit to save changes...
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}


?>






<html>
<head>
    <title></title>

<!-- Jquery -->
<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<style>
body {
    background-image: url('http://tsss.ca/wp-content/uploads/2013/01/food.jpg');

}

.inner-container {
    background-color: lightgrey;
    opacity: 0.9;
    width: 75%;
    margin: auto;
}

.common-form {
    margin: 10px;
    padding: 10px;
    border: solid 2px black;
}

.logout-container {
    display: inline-block;
    margin: 25px;
    position: fixed;

}

</style>


</head>
<body>
<div class="logout-container">
    <button id="logout" class="btn btn-default">Logout</button>
</div>


<div class="container">

<div class = "inner-container">
    <h1 align="center"> Chef Interface </h1>
<form class="form-horizontal common-form" role="form" method="POST" action="chef.php">
    


<h1>  </h1>
    <div class="form-group">
          <h3 align = "center">New Chef</h3>
          <label for="employee_id" class="control-label col-sm-5">Employee ID #</label>
          <div class="col-sm-5">
              <input min = "1" type="number" class="form-control" id="employee_id" name="employee_id" placeholder="Enter Employee ID">
          </div>
      </div>
      <div class="form-group">
          <label for="position" class="control-label col-sm-5">Select Position</label>
          <div class="col-sm-5">
              <select class="form-control" id="position" name="position">
                <option value="Master Chef">Master Chef</option>
                <option value="Line Cook">Line Cook</option>
                <option value="Sous Chef">Sous Chef</option>
                <option value="Saucier">Saucier</option>
                
              </select>
          </div>
        </div>

        <div class="form-group">
            <label for="name" class="control-label col-sm-5">Name</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Chef Name">
            </div>
        </div>


        <div class="form-group">  
            <div class = "col-sm-offset-5 col-sm-10">
                <button type="submit" class="btn btn-default" name="insertsubmit">Submit</button>
            </div>
        </div>

    </form>





<form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Sign In Sheet</h3>
          <label for="employee_id" class="control-label col-sm-5">Employee ID #</label>
          <div class="col-sm-5">
              <input min = "1" type="number" class="form-control" id="employee_id" name="employee_id" placeholder="Enter your employee #">
          </div>
        </div>

        <div class="form-group">
          <label for="signed_in" class="control-label col-sm-5">Sign In or Out</label>
          <div class="col-sm-5">
              <select class="form-control" id="signed_in" name="signed_in">
                <option value = "yes">Yes</option>
                <option value = "no">No</option>
              </select>
          </div>
        </div>

        <div class="form-group">
          
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="signinsubmit">SignIn</button>
        </div>
        </div>

    </form>






<form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Change Duties</h3>
          <label for="employee_id" class="control-label col-sm-5">Employee ID #</label>
          <div class="col-sm-5">
              <input min = "1" type="number" class="form-control" id="employee_id" name="employee_id" placeholder="Enter your employee #">
          </div>
        </div>

        <div class="form-group">
          <label for="position" class="control-label col-sm-5">Select Position</label>
          <div class="col-sm-5">
              <select class="form-control" id="position" name="position">
                <option value="Master Chef">Master Chef</option>
                <option value="Line Cook">Line Cook</option>
                <option value="Sous Chef">Sous Chef</option>
                <option value="Saucier">Saucier</option>
                
              </select>
          </div>
        </div>

        <div class="form-group">
          
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="updatesubmit">Update</button>
        </div>
        </div>

    </form>


    <form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Fire Chef</h3>
          <label for="employee_id" class="control-label col-sm-5">Employee ID #</label>
          <div class="col-sm-5">
              <input min = "1" type="number" class="form-control" id="employee_id" name="employee_id" placeholder="Enter your employee #">
          </div>
        </div>

        <div class="form-group">
          
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="deletesubmit">Fire</button>
        </div>
        </div>

    </form>


    <form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Working Chefs</h3>
          <label for="signed_in" class="control-label col-sm-5">Signed In?</label>
          <div class="col-sm-5">
              <select class="form-control" id="signed_in" name="signed_in">
                <option value = "yes">Yes</option>
                <option value = "no">No</option>
              </select>
          </div>
        </div>

        <div class="form-group">
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="checksubmit">Check</button>
        </div>
        </div>

    </form>

    <form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Find Chefs</h3>
          <label for="position" class="control-label col-sm-5">Position</label>
          <div class="col-sm-5">
              <select class="form-control" id="position" name="position">
                <option value="Master Chef">Master Chef</option>
                <option value="Line Cook">Line Cook</option>
                <option value="Sous Chef">Sous Chef</option>
                <option value="Saucier">Saucier</option>
              </select>
          </div>
        </div>

        <div class="form-group">
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="findsubmit">Find</button>
        </div>
        </div>

    </form>




<form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Dishes Projection</h3>
  
          <label for="employee_id" class="control-label col-sm-5">Column</label>
          <div class="col-sm-5">
              <select class="form-control" id="employee_id" name="employee_id">
                <option value = "type">Type</option>
                <option value = "employee_id">Employee ID</option>
                <option value = "status">Status</option>
                <option value = "inventory_usage">Inventory Usage</option>

              </select>
          </div>
        </div>

        <div class="form-group">
          
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="listsubmit">List</button>
        </div>
        </div>

    </form>


<form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Cook</h3>
          <label for="employee_id" class="control-label col-sm-5">Employee ID #</label>
          <div class="col-sm-5">
              <input min = "1" type="number" class="form-control" id="employee_id" name="employee_id" placeholder="Enter your employee #">
          </div>
        </div>

        <div class="form-group">
          
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="makesubmit">Make Dish</button>
        </div>
        </div>

    </form>

    <form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Count Working Chefs</h3>
        </div>

        <div class="form-group">
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="countsubmit">Count</button>
        </div>
        </div>

    </form>

    <form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Total Inventory Usage</h3>
        </div>

        <div class="form-group">
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="totalsubmit">Sum</button>
        </div>
        </div>

    </form>

    <form class="form-horizontal common-form" role="form" method="POST" action="chef.php">

        <div class="form-group">
          <h3 align = "center">Chefs who are slacking</h3>
        </div>

        <div class="form-group">
            <div class = "col-sm-offset-5 col-sm-10">
            <button type="submit" class="btn btn-default" name="joinsubmit">Find</button>
        </div>
        </div>

    </form>

<div class="common-form">
<?php
    $result = executePlainSQL("select * from Chef");
               $result2 = executePlainSQL("select * from Dishes_Makes_ConsistsOf");
                printResult($result);
                printResult2($result2);
                          
        ?>
                          
<?php 

if (array_key_exists('checksubmit', $_POST)) {
                // Update tuple using data from user
                

                $signed_in = $_POST['signed_in'];
                $cmd = "select * from Chef where signed_in = :bind1";
                $stm = OCIParse($db_conn, $cmd);
                oci_bind_by_name($stm, ":bind1", $signed_in);
                OCIExecute($stm, OCI_DEFAULT);
                printResult3($stm);
                OCICommit($db_conn);
            }

                          
?>

<?php 

if (array_key_exists('countsubmit', $_POST)) {
                // Update tuple using data from user
                $tuple = array (
                    ":bind1" => $_POST['signed_in'],
                    ":bind2" => $_POST['employee_id'],
                    ":bind3" => $_POST['name'],
                    ":bind4" => $_POST['position']

                
                    
                );
                $alltuples = array (
                    $tuple
                );
                $result = executePlainSQL("select COUNT(employee_id) from Chef where signed_in = 'yes' group by signed_in", $alltuples);
                printResult5($result);
                OCICommit($db_conn);
            }


                ?>

<?php 

if (array_key_exists('findsubmit', $_POST)) {
                // Update tuple using data from user
                $position = $_POST['position'];
                $cmd = "select * from Chef where position = :bind1";
                $stm = OCIParse($db_conn, $cmd);
                oci_bind_by_name($stm, ":bind1", $position);
                OCIExecute($stm, OCI_DEFAULT);
                printResult($stm);
                OCICommit($db_conn);
            }


                ?>


<?php 
                if (array_key_exists('joinsubmit', $_POST)) {
                // Update tuple using data from user
                $tuple = array (
                    ":bind1" => $_POST['employee_id'],
                    ":bind2" => $_POST['signed_in']
                   

                    
                );
                $alltuples = array (
                    $tuple
                );
                $result = executePlainSQL("select name from Dishes_Makes_ConsistsOf NATURAL JOIN Chef where status = 'incomplete' AND signed_in='yes'", $alltuples);
                printResult7($result);
                OCICommit($db_conn);

                 }

?>


<?php 
 if (array_key_exists('totalsubmit', $_POST)) {
                // Update tuple using data from user
                $tuple = array (
                    ":bind1" => $_POST['signed_in'],
                    ":bind2" => $_POST['employee_id'],
                    ":bind3" => $_POST['name'],
                    ":bind4" => $_POST['position']

                
                    
                );
                $alltuples = array (
                    $tuple
                );
                $result = executePlainSQL("select SUM(inventory_usage) from Dishes_Makes_ConsistsOf", $alltuples);
                printResult6($result);
                OCICommit($db_conn);

                 }


                ?>
<?php 
                if (array_key_exists('listsubmit', $_POST)) {
                    $employee_id = $_POST['employee_id'];
                $cmd = "select " . $employee_id . " from Dishes_Makes_ConsistsOf";
                $stm = OCIParse($db_conn, $cmd);
                OCIExecute($stm, OCI_DEFAULT);
                printResult4($stm, $employee_id);
                OCICommit($db_conn);

                 }

                if ($_POST && $success) {
                          //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
                          OCILogoff($db_conn);
                          
                          header("location: chef.php");
                          
                          }
                ?>

</div>


    

     

</div>
</div>
</div>

<script>
    $('.error-modal-btn').on('click', function () {
    $('.error-modal').remove();
});

    $('#logout').on('click', function () {
    $.ajax({
      type: "POST",
        url: 'logout.php',
        dataType: 'json',
        data: {},
        success: function (response) {
          console.log(response);
            if (response == 'success' ) {
                window.location.href = 'main.php';
            }
        }
    }); 
});
</script>


</body>
</html>




