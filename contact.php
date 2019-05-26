<?php
  $errors = [];
  $missing = [];

  if(isset($_POST['send'])){   //if the send button is added to the post super global array
    $expected = ['name', 'email', 'comments', 'extras'];
    $required = ['name', 'comments'];

    $to = 'Mark Condello <condellomark@gmail.com>';
    $subject = "Feedback from online form.";
    $headers = [];
    $headers[] = 'From: webmaster@example.com';
    $headers[] = 'CC: another@example.com';
    $headers[] = 'Content-type: text/plain; charset-utf-8';
    $authorized = '-fcondellomark@gmail.com';

    if(!isset($_POST['extras'])){
      $_POST['extras'] = [];
    }

     $minimumChecked = 1;
    if(count($_POST['extras']) < $minimumChecked){
      $errors['extras'] = true; 
    }  

    require './includes/process_mail.php';
    if($mailSent) {
      header('Location: thanks.php');
      exit;
    }
  }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Conditional error messages</title>
<link href="styles.css" rel="stylesheet" type="text/css">
</head>

<body>
<h1>Contact Us</h1>

<?php if($_POST && ($suspect || isset($errors['mailfail']))) : ?>
  <p class="warning">Sorry your message can not be sent.</p>
<?php endif ?>
<?php if($errors || $missing) : ?>
    <p class="warning">Please fix the items indicated.</p>
<?php endif; ?>
<form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
  <p>
    <label for="name">Name:
      <?php if($missing && in_array('name', $missing)) : ?>
         <span class="warning">Please enter your name.</span>
      <?php endif;
      ?>
    </label>
    <input type="text" name="name" id="name"
    <?php 
      if($errors || $missing) {
        echo 'value="' . htmlentities($name) . '"';
      }
    ?>
    >
  </p>
  <p>
    <label for="email">Email:
      <?php if($missing && in_array('email', $missing)) :?>
          <span class="warning">Please enter your email.</span>
      <?php elseif(isset($errors['email'])):?>
        <span class="warning">Invalid email address.</span> 
     <?php endif;
      ?>
    </label>
    <input type="email" name="email" id="email"
    <?php 
      if($errors || $missing) {
        echo 'value="' . htmlentities($email) . '"';
      }
    ?>
    >
  </p>
  <p>
    <label for="comments">Comments:
      <?php if($missing && in_array('comments', $missing)) : ?>
          <span class="warning">You forgot to add a comment.</span>
      <?php endif;
      ?>
    
    </label>
    <textarea name="comments" id="comments">
    <?php 
      if($errors || $missing) {
        echo  htmlentities($comments);
      }
    ?>
    </textarea>
  </p>

  <fieldset>
    <legend>Optional extras
    <?php if($errors['extras']): ?>
      <span class="warning">Please select at least <?= $minimumChecked; ?> checkbox.</span>
      <br>
    <?php endif;?> 
    </legend>
    <input type="checkbox" name="extras[]" value="one" id="option_one"
    <?php if($_POST && in_array('one',  $_POST['extras'])){
      echo 'checked';
      }?>>
    <label for="option_one">Option One</label>
    <br>
    <input type="checkbox" name="extras[]" value="two" id="option_two"       
    <?php if($_POST && in_array('two', $_POST['extras'])){
      echo 'checked';
      }?>>
    <label for="option_two">Option Two</label>
    <br>
    <input type="checkbox" name="extras[]" value="three" id="option_three"
    <?php if($_POST && in_array('three', $_POST['extras'])){
      echo 'checked';
      }?>>
    <label for="option_three">Option Three</label>
    <br>
  </fieldset>

  <p>
    <input type="submit" name="send" id="send" value="Send Comments">
  </p>
</form>

<pre>
  <?php
/*     if($_POST ) {
      echo "<pre>";
      print_r($_POST);
      echo "</pre>";

    } */
  ?>  
</pre>
<pre>
  <?php
    if($_POST && $mailSent) {
      echo "Message: \n\n";
      echo htmlentities($message);
      echo "Headers: \n\n";
      echo htmlentities($headers);
    }
  ?>  
</pre>
</body>
</html>