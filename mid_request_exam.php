<?php

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data['exam_id']){ //i send teacher id
    //all_questions file
    
    $options = array(
    CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/exams/get_exam.php', //correct url
    CURLOPT_POST => true, 
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER, array('Content-Type:application/json'),
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