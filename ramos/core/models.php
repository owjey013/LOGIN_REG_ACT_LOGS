<?php  

require_once 'dbConfig.php';

function checkIfUserExists($pdo, $username) {
	$response = array();
	$sql = "SELECT * FROM userAccounts WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;

}

function insertNewUser($pdo, $username, $firstName, $lastName, $email, $gender, $address, $password) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO userAccounts (username, firstName, lastName, email, gender, address, password) 
		VALUES (?,?,?,?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $firstName, $lastName, $email, $gender, $address, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}

function getAllUsers($pdo) {
	$sql = "SELECT * FROM userAccounts";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllbranch($pdo) {
	$sql = "SELECT * FROM branch";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllbranchBySearch($pdo, $search_query) {
	$sql = "SELECT * FROM branch WHERE 
			CONCAT(address,headManager,
				contactNumber,
				date_added,added_by,
				last_updated,
				last_updated_by) 
			LIKE ?";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute(["%".$search_query."%"]);
	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getBranchByID($pdo, $branchID) {
	$sql = "SELECT * FROM branch WHERE branchID = ?";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute([$branchID])) {
		return $stmt->fetch();
	}
}

function insertAnActivityLog($pdo, $operation, $branchID, $address, 
		$headManager, $contactNumber, $username) {

	$sql = "INSERT INTO activityLogs (operation, branchID, address, 
		headManager, contactNumber, username) VALUES(?,?,?,?,?,?)";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$operation, $branchID, $address, 
		$headManager, $contactNumber, $username]);

	if ($executeQuery) {
		return true;
	}

}

function getAllactivityLogs($pdo) {
	$sql = "SELECT * FROM activityLogs 
			ORDER BY date_added DESC";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute()) {
		return $stmt->fetchAll();
	}
}

function insertABranch($pdo, $address, $headManager, $contactNumber, $added_by) {
	$response = array();
	$sql = "INSERT INTO branch (address, headManager, contactNumber, added_by) VALUES(?,?,?,?)";
	$stmt = $pdo->prepare($sql);
	$insertBranch = $stmt->execute([$address, $headManager, $contactNumber, $added_by]);

	if ($insertBranch) {
		$findInsertedItemSQL = "SELECT * FROM branch ORDER BY date_added DESC LIMIT 1";
		$stmtfindInsertedItemSQL = $pdo->prepare($findInsertedItemSQL);
		$stmtfindInsertedItemSQL->execute();
		$getBranchID = $stmtfindInsertedItemSQL->fetch();

		$insertAnActivityLog = insertAnActivityLog($pdo, "INSERT", $getBranchID['branchID'], 
			$getBranchID['address'], $getBranchID['headManager'], 
			$getBranchID['contactNumber'], $_SESSION['username']);

		if ($insertAnActivityLog) {
			$response = array(
				"status" =>"200",
				"message"=>"Branch addedd successfully!"
			);
		}

		else {
			$response = array(
				"status" =>"400",
				"message"=>"Insertion of activity log failed!"
			);
		}
		
	}

	else {
		$response = array(
			"status" =>"400",
			"message"=>"Insertion of data failed!"
		);

	}

	return $response;
}

function updateBranch($pdo, $address, $headManager, $contactNumber, 
	$last_updated, $last_updated_by, $branchID) {

	$response = array();
	$sql = "UPDATE branch
			SET address = ?,
				headManager = ?,
				contactNumber = ?, 
				last_updated = ?, 
				last_updated_by = ? 
			WHERE branchID = ?
			";
	$stmt = $pdo->prepare($sql);
	$updateBranch = $stmt->execute([$address, $headManager, $contactNumber, 
	$last_updated, $last_updated_by, $branchID]);

	if ($updateBranch) {

		$findInsertedItemSQL = "SELECT * FROM branch WHERE branchID = ?";
		$stmtfindInsertedItemSQL = $pdo->prepare($findInsertedItemSQL);
		$stmtfindInsertedItemSQL->execute([$branchID]);
		$getBranchID = $stmtfindInsertedItemSQL->fetch(); 

		$insertAnActivityLog = insertAnActivityLog($pdo, "UPDATE", $getBranchID['branchID'], 
			$getBranchID['address'], $getBranchID['headManager'], 
			$getBranchID['contactNumber'], $_SESSION['username']);

		if ($insertAnActivityLog) {

			$response = array(
				"status" =>"200",
				"message"=>"Updated the branch successfully!"
			);
		}

		else {
			$response = array(
				"status" =>"400",
				"message"=>"Insertion of activity log failed!"
			);
		}

	}

	else {
		$response = array(
			"status" =>"400",
			"message"=>"An error has occured with the query!"
		);
	}

	return $response;

}


function deleteABranch($pdo, $branchID) {
	$response = array();
	$sql = "SELECT * FROM branch WHERE branchID = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$branchID]);
	$getBranchByID = $stmt->fetch();

	$insertAnActivityLog = insertAnActivityLog($pdo, "DELETE", $getBranchByID['branchID'], 
		$getBranchByID['address'], $getBranchByID['headManager'], 
		$getBranchByID['contactNumber'], $_SESSION['username']);

	if ($insertAnActivityLog) {
		$deleteSql = "DELETE FROM branch WHERE branchID = ?";
		$deleteStmt = $pdo->prepare($deleteSql);
		$deleteQuery = $deleteStmt->execute([$branchID]);

		if ($deleteQuery) {
			$response = array(
				"status" =>"200",
				"message"=>"Deleted the branch successfully!"
			);
		}
		else {
			$response = array(
				"status" =>"400",
				"message"=>"Insertion of activity log failed!"
			);
		}
	}
	else {
		$response = array(
			"status" =>"400",
			"message"=>"An error has occured with the query!"
		);
	}

	return $response;
}


// $getAllbranchBySearch = getAllbranchBySearch($pdo, "Silang");
// echo "<pre>";
// print_r($getAllbranchBySearch);
// echo "<pre>";



?>