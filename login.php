<?php 
	ini_set('session.save_path', '../session');
	session_start();

	if (isset($_POST['user_account']) && isset($_POST['input_password'])) {
		
		$user_account = trim($_POST['user_account']);
		$password = trim($_POST['input_password']);

		$conn = OCILogon("ora_q7p7", "a56269087", "ug");
		$cmd = 'select password from Users where user_account = :account';
		$stm = OCIParse($conn, $cmd);
		oci_bind_by_name($stm, ":account", $user_account);

		if (!$stm) {
			$e = OCI_Error($conn);
			echo json_encode($e['message']);
		} 
		$r = OCIExecute($stm, OCI_DEFAULT);
   		if (!$r) {
            $e = OCI_Error($stm); // For OCIExecute errors pass the statementhandle
            echo ($e['message']);
        } 

        $row = oci_fetch_assoc($stm);
        //echo $row->password . ' $password = ' . $password;
        
        if (strcmp($row['PASSWORD'], $password) == 0) {
        	$_SESSION['user_session'] = $user_account;
        	echo json_encode('success');
        } else {
        	echo " incorrect user account or password";
        }

	} else {
		echo "no information has been sent to server";
	}
?>