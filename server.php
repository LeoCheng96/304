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
            printErrorDialog($cmdstr, $e['message']);
            $success = False;
        }
        
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            printErrorDialog($cmdstr, $e['message']);
            $success = False;
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
            printErrorDialog($cmdstr, $e['message']);
            $success = False;
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
                printErrorDialog($cmdstr, $e['message']);
                $success = False;
            }
        }
        
    }
    
    function printResult($result) { //prints results from a select statement
        echo "<br>Server_Cancel_Order:<br>";
        echo "<table>";
        echo '<table class="table">';
        echo "<tr><th>EmployeeID</th><th>Name</th><th>Order Number</th><th>SignedIn</th></tr>";
        
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
        }
        echo "</table>";
        
    }
    
    function printResult3($result) { //prints results from a select statement
        echo "<br>Customer:<br>";
        echo "<table>";
        echo '<table class="table">';
        echo "<tr><th>Order Number</th><th>Name</th><th>CreditCardNumber</th><th>TakeOut</th></tr>";
        
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>"; //or just use "echo $row[0]"
        }
        echo "</table>";
        
    }
    
    function printResult4($result) { //prints results from a select statement
        echo "<br>Order_Makes:<br>";
        echo "<table>";
        echo '<table class="table">';
        echo "<tr><th>Order Number</th><th>CustomerOrderNumber</th><th>Type</th><th>CashOrCredit</th><th>Paid?</th></tr>";
        
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>"; //or just use "echo $row[0]"
        }
        echo "</table>";
        
    }
    
    function printResult5($result) { //prints results from a select statement
        echo "<br>Unserved/Paying Customers<br>";
        echo "<table>";
        echo '<table class="table">';
        echo "<tr><th>Order Number</th><th>CustomerName</th></tr>";
        
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>"; //or just use "echo $row[0]"
        }
        echo "</table>";
        
    }
    
    
    function printResult6($result) { //prints results from a select statement
        echo "<br>Working Servers Output<br>";
        echo "<table>";
        echo '<table class="table">';
        echo "<tr><th>EmployeeID</th><th>Name</th><th>Order Number</th><th>SignedIn</th></tr>";
        
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
        }
        echo "</table>";
        
    }
    
    function printResult7($result) { //prints results from a select statement
        echo "<br>Dishes_Makes_ConsistsOf:<br>";
        echo "<table>";
        echo '<table class="table">';
        echo "<tr><th>Type</th><th>EmployeeID</th><th>Order Number</th><th>Status</th><th>InventoryUsage</th></tr>";
        
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] .  "</td></tr>"; //or just use "echo $row[0]"
        }
        echo "</table>";
        
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
                                ":bind2" => $_POST['name'],
                                ":bind3" => $_POST['c_order_number']
                                
                                );
                $alltuples = array ($tuple);
                executeBoundSQL("insert into Server_Cancel_Order values (:bind1, :bind2, :bind3, 'no')", $alltuples);
                OCICommit($db_conn);
                
            } else
                if (array_key_exists('deletesubmit', $_POST)) {
                    
                    $tuple = array (
                                    ":bind1" => $_POST['c_order_number'],
                                    
                                    );
                    $alltuples = array ($tuple);
                    executeBoundSQL("delete from Customer where c_order_number = :bind1", $alltuples);
                    OCICommit($db_conn);
                    
                } else
                    if (array_key_exists('updatesubmit', $_POST)) {
                        // Update tuple using data from user
                        $tuple = array (
                                        ":bind1" => $_POST['employee_id'],
                                        ":bind2" => $_POST['newONumber']
                                        
                                        );
                        $alltuples = array (
                                            $tuple
                                            );
                        executeBoundSQL("update Server_Cancel_Order set c_order_number=:bind2 where employee_id=:bind1", $alltuples);
                        OCICommit($db_conn);
                        
                        
                        
                        
                    } else
                        if (array_key_exists('updateorder', $_POST)) {
                            
                            $tuple = array (
                                            ":bind1" => $_POST['c_order_number'],
                                            ":bind2" => $_POST['is_paid'],
                                            ":bind3" => $_POST['type'],
                                            
                                            );
                            $alltuples = array ($tuple);
                            executeBoundSQL("update Order_Makes set is_paid =:bind2 where c_order_number=:bind1", $alltuples);
                            
                            
                            
                            
                            OCICommit($db_conn);
                            
                            if ($_POST['is_paid']) {
                                            $cmd = "select type from order_makes where c_order_number = " . $_POST['c_order_number'];
                                $result = executePlainSQL($cmd);
                                            
                                            $row = OCI_Fetch_Array($result);
                                            $type = $row[0];
                                            
                                            $acmd = "select fullness from uses where type = '" . $type . "'";
                                $result = executePlainSQL($acmd);
                                $row = OCI_Fetch_Array($result);
                                $fullness = $row[0];
                                
                                $employee_id = executePlainSQL("SELECT employee_id FROM ( SELECT employee_id FROM chef ORDER BY dbms_random.value ) WHERE rownum = 1", OCI_DEFAULT);
                                $em = OCI_Fetch_Array($employee_id, OCI_BOTH);
                                $em = $em[0];
                                
                                $cmd = "insert into Dishes_makes_consistsof values('" . $type . "'," . $em . "," . $_POST['c_order_number'] . ", 'incomplete'," .
                                $fullness . ")";
                                
                                executePlainSQL($cmd);
                                
                                OCICommit($db_conn);
                            
                            }
                            
                            
                            
                        
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
                                        executeBoundSQL("update Server_Cancel_Order set signed_in=:bind2 where employee_id=:bind1", $alltuples);
                                        OCICommit($db_conn);
                                        
                                    } else {
                                        if (array_key_exists('deletedish', $_POST)) {
                                            
                                            $tuple = array (
                                                            ":bind1" => $_POST['order_number'],
                                                            
                                                            );
                                            $alltuples = array ($tuple);
                                            executeBoundSQL("delete from Dishes_Makes_ConsistsOf where order_number = :bind1", $alltuples);
                                            OCICommit($db_conn);
                                            
                                        }
                                    }
        
        
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

<div class="inner-container">
<div class="form-group">


<h1 align="center">Server Interface</h1>

<div>
</div>
<div>
</div>



<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<h3 align="center">New Server</h3>
<div class="form-group">
<label for="e_num" class="control-label col-sm-5">Employee ID #</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="e_num" name="employee_id" placeholder="Enter Employee ID">
</div>
</div>

<div class="form-group">
<label for="name" class="control-label col-sm-5">Name</label>
<div class="col-sm-5">
<input type="text" class="form-control" id="name" name="name" placeholder="Enter Server Name">
</div>
</div>

<div class="form-group">
<label for="c_order" class="control-label col-sm-5">Order Number</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="c_order" name="c_order_number" placeholder="Enter Order Number">
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-5 col-sm-10">
<button type="submit" class="btn btn-default" name="insertsubmit">Submit</button>
</div>
</div>

</form>


<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<h3 align="center">Sign In Sheet</h3>
<div class="form-group">
<label for="employee_id" class="control-label col-sm-5">Employee ID #</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="employee_id" name="employee_id" placeholder="Enter Employee ID">
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



<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<h3 align="center">Update Server Order Number</h3>
<div class="form-group">
<label for="o_ord" class="control-label col-sm-5">Employee ID #</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="o_ord" name="employee_id" placeholder="Enter Employee ID">
</div>
</div>

<div class="form-group">
<label for="n_ord" class="control-label col-sm-5">New Order #</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="n_ord" name="newONumber" placeholder="Enter New Order Number">
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-5 col-sm-10">
<button type="submit" class="btn btn-default" name="updatesubmit">Update</button>
</div>
</div>

</form>



<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<h3 align="center">Delete Customer</h3>
<div class="form-group">
<label for="c_ord" class="control-label col-sm-5"> Order Number</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="c_ord" name="c_order_number" placeholder="Enter Customer Order Number">
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-5 col-sm-10">
<button type="submit" class="btn btn-default" name="deletesubmit">Delete</button>
</div>
</div>

</form>





<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<h3 align="center">Update Order(Paid/Not Paid)</h3>
<div class="form-group">
<label for="pc_ord" class="control-label col-sm-5"> Order Number</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="pc_ord" name="c_order_number" placeholder="Enter Order Number">
</div>
</div>

<div class="form-group">
<label for="is_paid" class="control-label col-sm-5">Paid?</label>
<div class="col-sm-5">
<select class="form-control" id="is_paid" name="is_paid">
<option value = "true">Yes</option>
<option value = "false">No</option>
</select>
</div>
</div>


<div class="form-group">
<div class="col-sm-offset-5 col-sm-10">
<button type="submit" class="btn btn-default" name="updateorder">Submit</button>
</div>
</div>


</form>

<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<h3 align="center">Find Unserved/Paying Customers</h3>

<div class="form-group">
<div class="col-sm-offset-5 col-sm-10">
<button type="submit" class="btn btn-default" name="findorder">Find</button>
</div>
</div>

</form>


<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<div class="form-group">

<h3 align="center">Working Servers</h3>

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


<form class="form-horizontal common-form" role="form" method="POST" action="server.php">

<h3 align="center">Delete Dish</h3>
<div class="form-group">
<label for="dish" class="control-label col-sm-5"> Order Number</label>
<div class="col-sm-5">
<input min="1" type="number" class="form-control" id="dish" name="order_number" placeholder="Enter Customer Order Number">
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-5 col-sm-10">
<button type="submit" class="btn btn-default" name="deletedish">Delete</button>
</div>
</div>

</form>



<div class="common-form">
<?php
    $result = executePlainSQL("select * from Server_Cancel_Order");
    $result3 = executePlainSQL("select * from Customer");
    $result4 = executePlainSQL("select * from Order_Makes");
    $result7 = executePlainSQL("select * from Dishes_Makes_ConsistsOf");
    printResult($result);
    printResult3($result3);
    printResult4($result4);
    printResult7($result7);
    ?>

<?php

if (array_key_exists('findorder', $_POST)){
    //Find the order number and name of an "unserved customer" (customer order number tied to a server that is not signed in) and those that have already paid so we may prioritize unserved and paying customers first.
    
    $result5 = executePlainSQL("select C.c_order_number, C.name from Customer C where not exists (select S.c_order_number from Server_Cancel_Order S where S.signed_in='yes' and not exists (select O.c_order_number from Order_Makes O where C.c_order_number = O.c_order_number and O.is_paid='true'))", $alltuples);
    
    printResult5($result5);
    OCICommit($db_conn);
}

if (array_key_exists('checksubmit', $_POST)) {
        //
        
        $signed_in = $_POST['signed_in'];
        $cmd = "select * from Server_Cancel_Order where signed_in = :bind1";
        $stm = OCIParse($db_conn, $cmd);
        oci_bind_by_name($stm, ":bind1", $signed_in);
        OCIExecute($stm, OCI_DEFAULT);
        printResult6($stm);
        OCICommit($db_conn);
        
    }
    
    
if ($_POST && $success) {
        //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
        OCILogoff($db_conn);
        header("location: server.php");
    
    }
    
    //Commit to save changes...
    ?>

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