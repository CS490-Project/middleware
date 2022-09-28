<?php


//get ucid and password from index.php (decode json)
$frontpassword = $_POST["password"];

//curl post to db.php
$data = ['ucid'=>$_POST["ucid"]];
$defaults = array(
    CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/users/get_user.php',
    CURLOPT_POST => true, 
    CURLOPT_POSTFIELDS => $data,
);

$ch = curl_init();  //initialize curl session
curl_setopt_array($ch, $defaults); 


//decode json from db.php
$response = json_decode(curl_exec($ch));
curl_close($ch);


//if username doesn't exist, echo 'invalid creds'
if ($response == "User not found."){ //check syntax
    echo "Invalid credentials."
}


//unhash password and compare to password gotten from post request
//json to index.php
if (password_verify($response->{'password'}, $frontpassword)){ //check syntax
    echo json_encode($role); //send role (teacher/student) to front
}
else{
    http_response_code(404);
    echo "Invalid credentials.";
    die();
}



?>