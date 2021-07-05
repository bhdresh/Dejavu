<?php
include 'db.php';
// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$auth_key=preg_replace("/[^0-9a-zA-Z]/","",$_POST["auth_key"]);
	$mysqli = db_connect();
	$stmt = $mysqli->prepare("SELECT * FROM Users WHERE auth_key=?");
	$stmt->bind_param("s", $auth_key);
	$stmt->execute();
	$result = $stmt->get_result();
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			$user_id=$row['user_id'];
		}
	} else {
		echo "Invalid Auth Key";
		exit();
	}

    // Check if file was uploaded without errors
    if(isset($_FILES["uploaded_file"]) && $_FILES["uploaded_file"]["error"] == 0){
	$allowed = array("pcap" => "application/octet-stream", "mp4" => "video/mp4");
	$filename = preg_replace("/[^0-9a-zA-Z\.]/","",$_FILES["uploaded_file"]["name"]);
        //$filename = $_FILES["uploaded_file"]["name"];
        $filetype = $_FILES["uploaded_file"]["type"];
        $filesize = $_FILES["uploaded_file"]["size"];
    
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
    
        // Verify MYME type of the file
        if(in_array($filetype, $allowed)){
            // Check whether file exists before uploading it
            if(file_exists("/var/dejavufiles/captures/" . $filename)){
                echo $filename . " is already exists.";
            } else{
                move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], "/var/dejavufiles/captures/" . $filename);
                echo "Your file was uploaded successfully.";
            } 
        } else{
            echo "Error: There was a problem uploading your file. Please try again."; 
        }
    } else{
        echo "Error: " . $_FILES["uploaded_file"]["error"];
    }
}
?>
