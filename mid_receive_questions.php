<?php

//RETURN ALL QUESTIONS FROM DB TO MAKE EXAM (send questions to front)


$json = file_get_contents('php://input');
$data = json_decode($json, true);


if ($data['teacher_id']){ //i send teacher id to back
    //all_questions file


    $options = array(
        CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/questions/all_questions.php',  //correct url
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



    


    /*$response = json_decode(curl_exec($ch), true); //back send me json econded stuff
    echo json_encode($response); //send to front*/



    //put everything in if statement
    }
?>