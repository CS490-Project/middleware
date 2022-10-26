<?php


//SAVE EXAM IN BACK WHEN STUDENT SUBMITS
//recieve from front
$json = file_get_contents('php://input');
$data = json_decode($json, true);


//send to back
if ($data['exam_id'] && $data['student_id']){ //change this

    $options = array(
        CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/exams/submit_exam.php',
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

?>