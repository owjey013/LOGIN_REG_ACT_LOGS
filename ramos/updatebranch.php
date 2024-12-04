<?php  
require_once 'core/models.php'; 
require_once 'core/handleForms.php'; 

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<?php include 'navbar.php'; ?>

	<?php $getBranchByID = getBranchByID($pdo, $_GET['branchID']); ?>
	<form action="core/handleForms.php?branchID=<?php echo $_GET['branchID']; ?>" method="POST">
		<p>
			<label for="address">Address</label>
			<input type="text" name="address" value="<?php echo $getBranchByID['address']; ?>"></p>
		<p>
			<label for="address">Head Manager</label>
			<input type="text" name="headManager" value="<?php echo $getBranchByID['headManager']; ?>">
		</p>
		<p>
			<label for="address">Contact Number</label>
			<input type="text" name="contactNumber" value="<?php echo $getBranchByID['contactNumber']; ?>">
			<input type="submit" name="updateBranchBtn" value="Update">
		</p>
	</form>
</body>
</html>