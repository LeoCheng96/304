<!DOCTYPE html>
<html lang="en">
  <head>
   
    <title>CPSC 304</title>

    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #eee;
      }

      .form-signin {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin .checkbox {
        font-weight: normal;
      }
      .form-signin .form-control {
        position: relative;
        height: auto;
        -webkit-box-sizing: border-box;
           -moz-box-sizing: border-box;
                box-sizing: border-box;
        padding: 10px;
        font-size: 16px;
      }
      .form-signin .form-control:focus {
        z-index: 2;
      }
      .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
      }
      .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
      }

    </style>
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  </head>

  <body>

    <div class="container">

      <form class="form-signin">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="user_account" class="sr-only">Email address</label>
        <input type="text" id="user_account" name="user_account" class="form-control" placeholder="User account" required autofocus>
        <label for="input_password" class="sr-only">Password</label>
        <input type="password" id="input_password" name="input_password" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> 

    <script>
      $('.form-signin').submit(function (e) {
        var acc = $('#user_account').val(), pwd = $('#input_password').val();
        console.log('acc =', acc, ' pwd = ', pwd);
        $.ajax({
          type: "POST",
            url: 'login.php',
            dataType: 'json',
            data: {
              user_account: acc,
              input_password: pwd
            },
            success: function (response) {
              console.log(response);
              if (response == 'success') {
                var add = acc + '.php';
                console.log('redirect to ', add);
                window.location.href = add;
                return false;
              } 
            },
            error: function (err) {
              //alert(e.statusText);
              console.log(err);
              alert(err.responseText);
            }
        });
        e.preventDefault();
        return false;
      });
    </script>
  </body>
</html>
