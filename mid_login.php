<?php


//get ucid and password from index.php (decode json)
$frontpassword = $_POST["password"];

//curl post to db.php
$data = ['ucid'=>$_POST["ucid"]];
$defaults = array(
    CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/users/get_user.php',
    CURLOPT_POST => true, 
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_RETURNTRANSFER => true
);

$ch = curl_init();  //initialize curl session
curl_setopt_array($ch, $defaults); 


//decode json from db.php
$response = json_decode(curl_exec($ch), true);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


//unhash password and compare to password gotten from post request, make sure username exists
//json to index.php
if (password_verify($frontpassword, $response['password']) && $status == 200){ //check syntax
    $returnarr = array(
      "ucid" => $response["ucid"],
      "role" => $response["role"]
    );
    echo json_encode($returnarr); //send role (teacher/student) to front
}
else{
    http_response_code(401);
    echo "Invalid credentials.";
    die();
}
?>
