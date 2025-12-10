<?php

$page_title = "Delete pictures";

include 'mysqli_connect.php';

include 'includes/header.html';

include 'includes/navbar.html';

if (!isset ($_SESSION ['username'])){
	header('location: index.php');
}
else{
    if (isset ($_POST['pictures_name'])){
        // Fetch the real filename from the DB so user input cannot be abused
        $stmt2 = mysqli_prepare($connection, 
            "SELECT pictures_name FROM pictures WHERE pictures_name = ? AND id_users = ?");
        mysqli_stmt_bind_param($stmt2, "si", $_POST['pictures_name'], $_SESSION['user_id']);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_bind_result($stmt2, $db_filename);
        mysqli_stmt_fetch($stmt2);
        mysqli_stmt_close($stmt2);

        if (!$db_filename) {
            die("Invalid picture selected.");
        }

        $pictures_name = $db_filename;
        
        // Validate characters
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $pictures_name)) {
            die("Invalid filename.");
        }

        $uploadsDir = realpath("uploads");
        $filePath   = realpath($uploadsDir . DIRECTORY_SEPARATOR . $pictures_name);

        if ($filePath === false || strpos($filePath, $uploadsDir) !== 0) {
            die("Invalid file path.");
        }

	    if (preg_match('/^[a-zA-Z0-9._-]+$/', $pictures_name, $matches)) {
	    	$safe_name = $matches[0];
	    	$path = "uploads/" . $safe_name;

            	if (file_exists($path)) {
                	if (unlink($path)) {
                    		echo "Removed picture: " . htmlspecialchars($path) . "<br>";
                    		echo "Removed picture " . htmlspecialchars($pictures_name) . ", continue with  " . "<a href=''>" . "deleting pictures" . "</a>";
                    		unset ($path);
                	} else {
                    		echo "Error deleting file.";
                	}
            	} else {
                	echo "File not found.";
            	}
	     } else {
	     	echo "Invalid filename format.";
	     }
        }
    }
    
    $sql1 = "SELECT users.users_username, pictures.pictures_name FROM pictures INNER JOIN users ON pictures.id_users = users.users_id";
    $result = mysqli_query ($connection, $sql1) or die (mysqli_error ($connection));
    
    echo "<form action='' method='POST'>";
    echo "<select name='pictures_name'>";
    if (mysqli_num_rows ($result) > 0){
        while ($row = mysqli_fetch_assoc ($result)){
            if($row['users_username'] == $_SESSION['username']){
                echo "<option value='" . $row['pictures_name'] . "'>" . $row['pictures_name'] . "</option>";
            }
        }
    }
    else {
        echo "Error 2";
    }
    echo "</select>";
    echo "<input type='submit' value='Delete picture'>";
    echo "</form>";

    include 'includes/footer.html';

    mysqli_close ($connection);
    unset($connection);
}

?>

