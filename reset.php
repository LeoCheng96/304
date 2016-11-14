<?php 

    $conn = OCILogon("ora_q7p7", "a56269087", "ug");

    function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the
            // connection handle
            echo htmlentities($e['message']);
            $success = False;
        }
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo htmlentities($e['message']);
            $success = False;
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
        var_dump($statement);

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
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
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                echo "<br>";
                $success = False;
            }
        }

    }   

    function createUsersInfo() {
        executePlainSQL("Drop table Users");
        executePlainSQL("create table Users (user_id number, user_account varchar2(15), password varchar2(15))");
        executePlainSQL("insert into Users values(1, 'customer', 'aaaa')");
        executePlainSQL("insert into Users values(2, 'server', 'aaaa')");
        executePlainSQL("insert into Users values(3, 'chef', 'aaaa')");
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
        executePlainSQL("create table Customer (c_order_number number, name varchar2(30), credit_card_number number CHECK ( credit_card_number > 99999999999 AND credit_card_number < 9999999999999999), take_out varchar2(10), primary key (c_order_number))");
        executePlainSQL("create table Dishes (type varchar2(20), price number, primary key (type))");
        executePlainSQL("create table Server_Cancel_Order (employee_id number, name varchar(30), c_order_number number, signed_in varchar2(30), primary key(employee_id), foreign key (c_order_number) references Customer ON DELETE CASCADE)");
        executePlainSQL("create table Order_Makes (order_number number, c_order_number number, type varchar2(20), cash_or_credit varchar(10), is_paid varchar2(10), primary key (order_number), foreign key (c_order_number) references Customer ON DELETE CASCADE)");
        executePlainSQL("create table Process (order_number number, employee_id number, primary key (order_number), foreign key (order_number) references Order_Makes ON DELETE CASCADE, foreign key (employee_id) references Server_Cancel_Order ON DELETE CASCADE)");
        executePlainSQL("create table Phone_Customer (c_order_number number, name varchar(20), credit_card_number number, take_out varchar(20), pick_up_time number, phone_number number, primary key (c_order_number), foreign key (c_order_number) references Customer ON DELETE CASCADE)");
        executePlainSQL("create table Dishes_Makes_ConsistsOf (type varchar(20), employee_id number, order_number number, status varchar(10), inventory_usage number, primary key (type, order_number), foreign key (type) references Dishes ON DELETE CASCADE, foreign key (employee_id) references Chef ON DELETE CASCADE, foreign key (order_number) references Order_Makes ON DELETE CASCADE)");
        executePlainSQL("create table Uses (fullness number, type varchar(20), primary key (fullness, type), foreign key (fullness) references Inventory ON DELETE CASCADE, foreign key (type) references Dishes ON DELETE CASCADE)");
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
        
        // executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Fruit', 01, 001, 'complete', 5)");
        // executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Sandwich', 02, 002, 'incomplete', 10)");
        // executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Creme Brulee', 03, 003, 'incomplete', 15)");
        // executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Mini Cheesecake', 04, 004, 'complete', 5)");
        // executePlainSQL("insert into Dishes_Makes_ConsistsOf values ('Giant Cheesecake', 05, 005, 'incomplete', 20)");
        
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

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = $conn;


// Connect Oracle...
if ($db_conn) {

    createUsersInfo();
    populateTables();
    insertTuples();
    OCICommit($db_conn);
    OCILogoff($db_conn);
    header("location: main.php");
    
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}

?>
