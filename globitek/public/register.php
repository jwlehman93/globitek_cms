<?php
require_once('../private/initialize.php');

// Set default values for all variables the page needs.

// if this is a POST request, process the form
// Hint: private/functions.php can help
$formValues = ['first_name', 'last_name', 'email', 'username'];
if(is_post_request()) {
    $errors = [];
    
    
    foreach(['first_name', 'last_name'] as $value) {
        if(!has_length($_POST[$value], ['min' => 2])) {
            $errors[$value] = str_replace('_', ' ', ucfirst($value)) . ' must be more than 2 characters';
        }
        if(preg_match('/\A[A-Za-z\s\-,\.\']+\Z/', $_POST[$value]) === 0) {
            $errors[$value] = str_replace('_', ' ' , ucfirst($value)) . ' must only contain letters, and the symbols -,.\'';
        }
    }
    
    if(!has_length($_POST['username'], ['min' => 8])) {
        $errors['username'] = 'Username must be at least 8 characters';
    }
    
    if(preg_match('/\A[A-za-z0-9_]+\Z/', $_POST['username']) === 0){
        $errors['username'] = 'Username must only contain letters, numbers, or the symbol _';
    }
    
    $sql = "SELECT username FROM users WHERE username = '{$_POST['username']}'";
    $result_set = db_query($db, $sql);
    if(!$result_set) {
        echo db_error($db);
        db_close($db);
    } else if(db_num_rows($result_set) !== 0){
        $errors['username'] = 'Username is already taken';
    }
    
    if(!has_valid_email_format($_POST['email'])) {
        $errors['email'] = 'Email must be a valid format';
    }
    if(preg_match('/\A[A-za-z0-9_@\.]+\Z/', $_POST['email']) === 0) {
        $errors['email'] = 'Email must only contain letters, numbers, or the symbols _ @ .';
    }
    
    foreach($formValues as $value) {
        if(has_length($_POST[$value], ['exact' => 0])) {
            echo $_POST[$value];
            $errors[$value] = str_replace('_', ' ', ucfirst($value)) . ' cannot be blank';
        } elseif(!has_length($_POST[$value], ['max' => 255])) {
            $errors[$value] = str_replace('_', ' ', ucfirst($value)) . ' must be less than 255 characters';
        }
    }
    
    
    if(sizeof($errors) === 0) {
        $sanitized_first_name = addslashes($_POST['first_name']);
        $sanitized_last_name = addslashes($_POST['last_name']);
        $sql = "INSERT INTO users (first_name, last_name, email, username) VALUES ('${sanitized_first_name}','${sanitized_last_name}','{$_POST['email']}','{$_POST['username']}')";
        $result = db_query($db, $sql);
        if($result) {
            db_close($db);
            redirect_to('./registration_success.php');
            exit;
        } else {
            echo db_error($db);
            db_close($db);
            exit;
        }
    }
}
?>

  <?php $page_title = 'Register'; ?>
    <?php include(SHARED_PATH . '/header.php'); ?>

      <div id="main-content">
        <h1>Register</h1>
        <p>Register to become a Globitek Partner.</p>

        <?php
// TODO: display any form errors here
// Hint: private/functions.php can help
$inputValues = [];
if(is_post_request()) {
    foreach($errors as $error) {
    }
    echo display_errors($errors);
}
foreach($formValues as $value) {
    if(is_post_request()) {
        $inputValues[$value] = $_POST[$value];
    } else {
        $inputValues[$value] = '';
    }
}
?>

          <form action="register.php" method="post">
            <div class="form-group" <label for="first_name">First Name:</label>
              <input type="text" name="first_name" placeholder="First Name" value="<?php echo h($inputValues['first_name']);?>">
            </div>
            <div class="form-group">
              <label for="last_name">Last Name:</label>
              <input type="text" name="last_name" placeholder="Last Name" value="<?php echo h($inputValues['last_name']);?>">
            </div>
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" name="email" placeholder="Email" value="<?php echo h($inputValues['email'])?>">
            </div>
            <div class="form-group">
              <label for="username">Username:</label>
              <input type="text" name="username" placeholder="Username" value="<?php echo h($inputValues['username'])?>">
            </div>
            <button type="submit">Submit</button>
          </form>
      </div>

      <?php include(SHARED_PATH . '/footer.php'); ?>