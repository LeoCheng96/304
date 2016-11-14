
<!DOCTYPE html>

<?php 
    ini_set('session.save_path', '../session');
    session_start();

    if(!isset($_SESSION['user_session']))
    {
        header("Location: logout.php");
    }
    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $conn = OCILogon("ora_q7p7", "a56269087", "ug");
    $db_conn = $conn;


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
            printErrorDialog($cmdstr, $e['message']);
            $success = False;
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
        echo "<table class='table'>";
        echo "<tr><th>OrderNumber</th><th>Name</th><th>TakeOut</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[3] . "</td></tr>"; 
        }
        echo "</table>";

    }

    function printDishDropdown() {
        global $conn;
        $st = OCIParse($conn, 'select * from Dishes');
        $result = OCIExecute($st);
        while ($row = oci_fetch_array($st, OCI_BOTH)) {
            print '<option value="'. $row[0] . '">';
            print $row[0] . ' - $';
            print $row[1];
            print '</option>';
        }
    }


// Connect Oracle...
if ($db_conn) {

    if (array_key_exists('insertsubmit', $_POST)) {
        //var_dump($_POST);
        
        //Getting the values from user and insert data into the table
        $tuple = array (
            ":bind1" => $_POST['name'],
            ":bind2" => $_POST['credit_card_number'],
            ":bind3" => $_POST['take_out']
        );
        if ($_POST['phone_number']) {
            
        }
        $alltuples = array ($tuple);
        executeBoundSQL("insert into Customer values (seq_customer.nextval, :bind1, :bind2, :bind3)", $alltuples);

        $r = executePlainSQL("select seq_customer.currval from Customer");
        $cu = OCI_Fetch_Array($r, OCI_BOTH);

        //Creates an order in Order_makes
        $tup = array(
            ":bind1" => $cu[0],                         //Customer Order #
            ":bind2" => $_POST['dish_type'],             //Dish type
            ":bind3" => $_POST['payment_type']          //Payment Type
        );
        $atps = array($tup);
        executeBoundSQL("insert into Order_Makes values(seq_order_makes.nextval, :bind1, :bind2, :bind3, 'false')", $atps);



        // $employee_id = executePlainSQL("SELECT employee_id FROM ( SELECT employee_id FROM chef ORDER BY dbms_random.value ) WHERE rownum = 1", OCI_DEFAULT);
        // $em = OCI_Fetch_Array($employee_id, OCI_BOTH);
        OCICommit($db_conn);
    }

    if (array_key_exists('updatesubmit', $_POST)) {
        // Update tuple using data from user
        $tuple = array (
            ":bind1" => $_POST['c_order_number'],
            ":bind2" => $_POST['take_out']
        );
        $alltuples = array (
            $tuple
        );
        executeBoundSQL("update Customer set take_out = :bind2 where c_order_number = :bind1", $alltuples);
        OCICommit($db_conn);
    }   

    if ($_POST && $success) {
        //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
        //var_dump($_POST);
        ocilogoff($db_conn);
        header("location: customer.php");
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

<link href="customer.css" rel="stylesheet">
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
        <h1 align="center"> Customer Interface </h1>

        <div class="common-form">
            <?php 
                if ($db_conn) {
                    $result = executePlainSQL("select * from Dishes");
                    echo "<table class='table'>";
                    echo "<tr><th>Dish</th><th>Price($)</th></tr>";

                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>" . $row[0] . "</td><td> $" . $row[1] . "</td>";
                    }
                    echo "</table>";    

                    $result = executePlainSQL("Select DISTINCT d.type from Dishes_Makes_ConsistsOf d 
                        where d.employee_id IN (Select employee_id from Chef where position = 'Head Chef')
                        Group By d.type");
                    echo "<table class='table'>";
                    echo "<tr><th>Current dishes made by Head chef </th></tr>";

                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>" . $row[0] . "</td>";
                    }
                    echo "</table>";   

                    $result = executePlainSQL("Select DISTINCT d.type from Dishes_Makes_ConsistsOf d 
                        where d.employee_id IN (Select employee_id from Chef where position = 'Sous Chef')
                        Group By d.type");
                    echo "<table class='table'>";
                    echo "<tr><th>Current dishes made by Sous chef </th></tr>";

                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>" . $row[0] . "</td>";
                    }
                    echo "</table>";   

                }
            ?>    
        </div>

        <form class="common-form form-horizontal" role="form" method="POST" action="customer.php">
            <h3 align="center"> Create Customer </h3>
            <div class="form-group">
              <label for="name" class="control-label col-sm-5">Name</label>
              <div class="col-sm-5">
                  <input type="text" class="form-control" id="name" name="name" placeholder="Enter customer name">
              </div>
            </div>
            <div class="form-group">
                <label for="credit_card_number" class="control-label col-sm-5">Credit Card Number</label>
                <div class="col-sm-5">
                    <input min="0" type="number" class="form-control" id="credit_card_number" name="credit_card_number" placeholder="Enter credit card number">
                </div>
            </div>

            <div class="form-group"> 
                <label for="take_out" class="control-label col-sm-5">Take out</label>
                <div class="col-sm-5">
                    <select id="take_out" name="take_out" class="form-control col-sm-5">
                        <option value="yes">Yes</option>
                        <option value="no ">No</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
              <label for="sel1" class="control-label col-sm-5">Select Dish</label>
              <div class="col-sm-5">
                  <select class="form-control" id="sel1" name="dish_type">
                    <?php 
                        printDishDropdown();
                    ?>
                  </select>
              </div>
            </div>
            <div class="form-group">
              <label for="sel2" class="control-label col-sm-5">Select Payment type</label>
              <div class="col-sm-5">
                  <select class="form-control" id="sel2" name="payment_type">
                    <option>Cash</option>
                    <option>Credit</option>
                  </select>
              </div>
            </div>

            <div class="form-group">
                <div class='col-sm-offset-5 col-sm-10'>
                <button type="submit" class="btn btn-default" name="insertsubmit">Submit</button>
                </div>    
            </div>
        </form>


        <form class="common-form form-horizontal" role="form" method="POST" action="customer.php">
            <h3 align="center"> Update Customer </h3>
            <div class="form-group">
              <label for="c_order_number" class="control-label col-sm-5">Customer Order # to update</label>
              <div class="col-sm-5">
                  <input min="0" type="number" class="form-control" id="c_order_number" name="c_order_number">
              </div>
            </div>
            <div class="form-group">
                <label for="name" class="control-label col-sm-5">Take Out</label>
                <div class="col-sm-5">
                    <select id="take_out" name="take_out" class="form-control col-sm-5">
                        <option value="yes">Yes</option>
                        <option value="no ">No</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class='col-sm-offset-5 col-sm-10'>
                <button type="submit" class="btn btn-default" name="updatesubmit">Update</button>
                </div>    
            </div>
        </form>   

        <div class="common-form">
            <?php 
                $result = executePlainSQL("select * from Customer order by c_order_number asc");
                printResult($result);
            ?>    
        </div>
        

        

        <!--  <div class="form-group">
            <label for="sel1" class="control-label col-sm-2">Select Dish</label>
                <div class="col-sm-5">
                <select class="form-control" id="sel1" name="dish_type">
                    <?php 
                        printDishDropdown();
                    ?>
                </select>
            </div>
        </div> -->
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

