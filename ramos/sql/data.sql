CREATE TABLE userAccounts (
	userID INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50),
	firstName VARCHAR(50),
	lastName VARCHAR(50),
	email VARCHAR(50),
	gender VARCHAR(50),
	address VARCHAR(50),
	password TEXT,
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE branch (
	branchID INT AUTO_INCREMENT PRIMARY KEY,
	address VARCHAR(50),
	headManager VARCHAR(50),
	contactNumber VARCHAR(50),
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
	added_by VARCHAR(50),
	last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	last_updated_by VARCHAR(50)
);

CREATE TABLE activityLogs (
	activityLogsID INT AUTO_INCREMENT PRIMARY KEY,
	operation VARCHAR(50),
	branchID INT,
	address VARCHAR(50),
	headManager VARCHAR(50),
	contactNumber VARCHAR (50),
	username VARCHAR(50),
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);