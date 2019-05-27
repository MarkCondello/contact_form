<?php
$mailSent = false;
$suspect = false;
$pattern = '/Content-type:|Bcc:|Cc:/i';

function isSuspect($value, $pattern, &$suspect) {
    if(is_array($value)){
        foreach($value as $item) {
            isSuspect($item, $pattern, $suspect); // recursion, $suspect is not added by reference here
        }
    } else {
        if(preg_match($pattern, $value)) {
            $suspect = true;
        }
    }
}
isSuspect($_POST, $pattern, $suspect);

if(!$suspect) :
    foreach ($_POST as $key => $value) {
        $value = is_array($value) ? $value : trim($value); //if the post array values are an array
        //if the value is an empty string and is in the $required array
        if(empty($value) && in_array($key, $required)) {
            //echo "within required check conditional: " . $key . "<br/>";
            $missing[] = $key;
            $$key = ''; // resetting the value of $key
        } elseif(in_array($key, $expected)) {
            $$key = $value;
            //echo "within expected check conditional: " . $$key . "<br/>";
        }
    }

    //validate user email
    if (!$missing && !empty($email)) :
        $validemail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if($validemail) {
            $headers[] = "Reply-to: $validemail";
        } else {
            $errors['email'] = true;
        }
    endif;    

    //if no errors create headers and message body
    if (!$errors && !$missing) :
        $headers = implode("\r\n", $headers);    
        //initialize message
        $message = '';
        foreach($expected as $field) :
            if(isset($$field) && !empty($$field)) {
                $val = $$field;
            } else {
                $val = 'Not selected';
            }    
            //If an array, expand to a comma seperated string
            if (is_array($val)) {
                $val = implode(', ', $val);
            }
            //replace underscores in field value
            $field = str_replace('_', ' ', $field);
            $message .= ucfirst($field) . ": $val\r\n\r\n";
        endforeach;
        //max chars
        $message = wordwrap($message, 70);
        $mailSent = mail($to, $subject, $message, $headers, $authorized);
        if(!$mailSent) {
            $errors['mailfail'] = true;
        }  
    endif;
endif;

/* print_r($missing);
echo "<br/>";
print_r($expected); */

//this script uses a variables variable. Once a value is assigned to a $$ variable, it becomes its name value.
//eg $$var = 'john'; $var is named as 'john';
//http://php.net/manual/en/language.variables.variable.php

//In this script we are looping through the $_POST array to see if any of the values are empty, and to check if they are required

// if the $key in the $_POST variable is within the $required array and it is empty, add the $key to the $missing array
//else if it is not in the $required array but is in the $expected array, set the $$key to the value