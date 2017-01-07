<?php

// is_blank('abcd')
function is_blank($value='') {
    return !isset($value) || trim($value) == '';
}

// has_length('abcd', ['min' => 3, 'max' => 5])
function has_length($value, $options=array()) {
    $length = strlen($value);
    if(isset($options['max']) && (strlen($value) > $options['max'])) {
        return false;
    } else if(isset($options['min']) && (strlen($value) < $options['min'])) {
        return false;
    } else if(isset($options['exact']) && (strlen($value) != $options['exact'])) {
        return false;
    } else {
        return true;
    }
}

// has_valid_email_format('test@test.com')
function has_valid_email_format($email) {
    if (strpos($email, '@') !== false) {
        return true;
    }
    return false;
}
?>