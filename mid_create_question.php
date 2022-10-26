<?php

//any data i need that goes front to back, I NEED TO INTERCEPT
    //points
    //(real) funct name
    //raw text of student exam
    //test cases (in + out) for each (real) funct name
        //do i tho? i may just pull from backend


/*
 *
 *Use Case #1 (this is correct)
 *
 */

//TEACHER CREATING QUESTIONS (send questions to back)
//recieve from front
$json = file_get_contents('php://input');
$data = json_decode($json, true);


//send to back
if ($data['description']){

    $options = array(
        CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/questions/create_question.php', //correct url
        CURLOPT_POST => true, 
        CURLOPT_POSTFIELDS => json_encode($data), //front must do this too
        CURLOPT_HTTPHEADER, array('Content-Type:application/json'), //front must do this too
        CURLOPT_RETURNTRANSFER => true
    );
    
    $ch = curl_init();  //initialize curl session
    curl_setopt_array($ch, $options); 
    $response = curl_exec($ch);
    
    
    //receive nothing back and close curl
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo $response;
}