<?php

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$grading_params = [
    "student_id" => $data['student_id'],
    "exam_id" => $data['exam_id']
];

//middle requests questions and student answers from back
$answer_request = array(
    CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/answers/get_student_answers.php',
    CURLOPT_POST => true, 
    CURLOPT_POSTFIELDS => json_encode($grading_params),
    CURLOPT_HTTPHEADER, array('Content-Type:application/json'),
    CURLOPT_RETURNTRANSFER => true
);

$ch = curl_init();  //initialize curl session
curl_setopt_array($ch, $answer_request); 


$answers = curl_exec($ch); //student_id, exam_id, answer (student raw text), question_id, description, fname, value
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$answers = json_decode($answers, true);
$graded_answers = []; //final array to add all questions to

//
foreach($answers as $ans){

    $result = []; //array to add all testcases to
    
    $result["description"] = $ans['description'];
    $result["student_answer"] = $ans['answer'];
    $result["fname"] = $ans['fname'];
    $result["value"] = floatval($ans['value']);
    $result["question_id"] = $ans['question_id'];
    $result["constraints"] = $ans['constraints'];

    //request testcases from back
    $test_case_request = array(
        CURLOPT_URL => 'https://afsaccess4.njit.edu/~gc348/CS490/backend/questions/get_test_cases.php',
        CURLOPT_POST => true, 
        CURLOPT_POSTFIELDS => json_encode(["question_id" => $result['question_id']]),
        CURLOPT_HTTPHEADER, array('Content-Type:application/json'),
        CURLOPT_RETURNTRANSFER => true
    );
    
    $ch = curl_init();  //initialize curl session
    curl_setopt_array($ch, $test_case_request); 


    $test_cases = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch); //close curl

    $test_cases = json_decode($test_cases, true);
    
    


    //get student's fname
    $student_fname = substr($result["student_answer"], 0, strpos($result["student_answer"], "("));
    $student_fname = preg_replace("/def /", "", $student_fname);


    //check if student's fname matches real fname, else replace
    $a = $result['student_answer'];
    $b = $a;
    $fnamededuction = 0;
    if ($student_fname !== $result['fname']){
        $fnamededuction = 5;
        $b = preg_replace('/'.$student_fname.'/', $result['fname'], $a);
    }
    
    
    $test_case_results = array(
        [
            "test_case_id" => NULL,
            "expected" => "fname->'{$result['fname']}'", 
            "actual" => "'{$student_fname}'",
            "pts_possible" => 5.00,
            "pts_earned" => 5.00 - $fnamededuction
            
        ]
    );

    $ptspertc = 0;
    //check constraints
    if($result['constraints'] != null){
        //check if constraint is satisfied
        $actual = "None";
        $ptsdeducted = 5.0;
        if ($result['constraints'] == 'Recursion'){
            if (substr_count($result['student_answer'], $student_fname) == 2){
                $actual = "Recursion";
                $ptsdeducted = 0.0;
            }
        }
        else{ //if while or for loop
            if (substr_count($result['student_answer'], $result['constraints']) >= 1){
                $actual = "{$result['constraints']}";
                $ptsdeducted = 0.0;
            }
        }

        array_push($test_case_results, [
            "test_case_id" => NULL,
            "expected" => "constraint->'{$result['constraints']}'", 
            "actual" => "'{$actual}'",
            "pts_possible" => 5.00,
            "pts_earned" => 5.00 - $ptsdeducted
        ]);
        $ptspertc = ($result["value"]-10)/count($test_cases); //assume name=5pts, constraint=5pts
    }
    else{
        $ptspertc = ($result["value"]-5)/count($test_cases); //assume name=5pts
    }


    //run function for each testcase
    foreach($test_cases as $tc){

        //define vars
        $given_input = $tc["test_in"];
        $expected_output = $tc["test_out"];
        $ptslost = 0;
        $output = array();

        //append testcase
        $input = $b; 
        $input .= "\nprint({$given_input})";

        //run
        file_put_contents("run.py", $input);
        exec("python run.py", $output);

        //compare actual to expected, deduct pts
        if ($output[0] !== $expected_output){
            $ptslost = $ptspertc;
        }
        



        //add to strings to send to front
        $expected = "{$given_input}->{$expected_output}";
        $run = "{$output[0]}";
        

        $test_case_res= array(
            "test_case_id" => $tc["id"],
            "expected" => $expected, 
            "actual" => $run, 
            "pts_possible" => $ptspertc,
            "pts_earned" => $ptspertc - $ptslost
        );

        array_push($test_case_results, $test_case_res);

        //delete file
        unlink("run.py");     
    }
    
    
    //add testcase results to testcase array
    $result["test_cases"] = $test_case_results;

    //add testcase array to (final) question array
    array_push($graded_answers, $result);

}

//send to front
echo json_encode($graded_answers);

?>
