<?php
ob_start();
session_start();
$title = "Register";
$active = "register";
require_once 'partials/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['name'])) {
        $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $password = sha1(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

        $query = $con->prepare("SELECT * FROM user WHERE email = ?");
        $query->execute(array($email));
        if ($query->rowCount() > 0) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC)[0];
            if ($password === $data['password']) {
                $_SESSION['user'] = 1;
                $_SESSION['name'] = ucfirst($data['name']);
                $_SESSION['user_id'] = intval($data['id']);
            } else {
                $_SESSION['errors'] = ['pw'=>'The Email/Password Entered is Not Correct'];
                header('Location:'.$_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            $_SESSION['errors'] = ['em'=>'The Email Entered Does Not Exist'];
            header('Location:'.$_SERVER['HTTP_REFERER']);
            exit();
        }
        unset($_SESSION['errors']['pw']);
        unset($_SESSION['errors']['em']);
        header('Location: index.php');
        exit();
    } else {
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!checkDB('user', 'email', $email)) {
            $gender = filter_var($_POST['gendre'], FILTER_SANITIZE_STRING);
            $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $query = $con->prepare("INSERT INTO `user`(`name`, `email`, `gendre`, `country`, `password`) VALUES (?,?,?,?,?)");
            $query->execute(array($name, $email, $gender, $country,sha1($password)));
            if ($query->rowCount() > 0) {
                header('Location:index.php');
                exit();
            }
        } else { ?>
            <div class="container py-5 mb-5">
                <div class="alert alert-danger text-center py-1">
                    <h2 class="h1 py-5">
                        Email Already Exists
                    </h2>
                </div>
            </div>
        <?php }
    }
} elseif (isset($_GET['action']) && $_GET['action']==='login') {?>
    <form id="loginForm" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" class="py-3">
        <div class="container">
            <div class="form-group">
                <label class="control-label bgFont">Email: </label>
                <input required data-check="[^A-Za-z0-9_@\\.\\-]" name="email" type="email" class="form-control" placeholder="Email">
            </div>
            <div class="form-group mb-5">
                <label class="control-label bgFont">Password: </label>
                <input required data-check="[^A-Za-z0-9_]" name="password" type="password" class="form-control" placeholder="Password">
            </div>
            <?php if (isset($_SESSION['errors']) && count($_SESSION['errors']) > 0) {?>
                <div class="alert alert-danger hidden">
                    <?php echo isset($_SESSION['errors']['pw'])?$_SESSION['errors']['pw']:isset($_SESSION['errors']['em'])?$_SESSION['errors']['em']:'';
                    unset($_SESSION['errors']['pw']);
                    unset($_SESSION['errors']['em']);?>
                </div>
            <?php } ?>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-center">
                    <input type="submit" value="Login" class="btn btn-secondary btn-lg regBtn">
                </div>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-center">
                    <input type="submit" value="Forget Password" class="btn btn-secondary btn-lg regBtn">
                </div>
            </div>
            <div class="form-group mb-1">
                <div class="d-flex justify-content-center">
                    <input type="submit" value="Sign Up" class="btn btn-secondary btn-lg regBtn">
                </div>
            </div>
            
        </div>
    </form>
    <script>
        document.body.onload = function () {
            var Form = $('#registerForm');
            Form.on('submit', function (e) {
                formValidation(e, e.target);
            });
        };
        function formValidation (e, F) {
            var errorInputs = validateInputs(F.id);
            if (errorInputs) {
                e.preventDefault();
            }
        }
        function validateInputs(Form) {
            var error = false;
            $('input[type="password"], input[type="email"]', $('#' + Form)).each(function () {
                var input = $(this),
                    regEx = input.data('check'),
                    v = input.val();
                if (v === '' || v.match(regEx)) {
                    input.addClass('border-danger');
                    error = true;
                } else {
                    input.removeClass('border-danger');
                }
            });
            return error;
        }
    </script>
<?php } else { ?>
    <div class="container pt-3 reg-form">
        <form class="col-12 col-sm-10 col-md-8 col-xl-6" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"
              id="registerForm" name="registerForm" enctype="multipart/form-data">

            <div class="form-group">
                <label for="name" class="control-label">Name</label>
                <input required data-check="[^A-Za-z ]" id="name" name="name" type="text" class="form-control" placeholder="Your Name">
            </div>

            <div class="form-group">
                <label for="email" class="control-label">Email</label>
                <input required data-check="[^A-Za-z0-9_@\\.\\-]" id="email" name="email" type="email" class="form-control" placeholder="Your Email">
            </div>

            <div class="form-group">
                <label class="control-label">Gendre</label>
                <div class="row no-gutters justify-content-start">
                    <div class="form-check-inline">
                        <label class="form-check-label mr-2" for="exampleRadios1">
                            Male
                        </label>
                        <input data-check="[^A-Za-z]" id="exampleRadios1" class="form-check-input" type="radio" name="gendre" value="male"
                               checked>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label mr-2" for="exampleRadios2">
                            Female
                        </label>
                        <input data-check="[^A-Za-z]" class="form-check-input" type="radio" name="gendre" id="exampleRadios2" value="female">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="country" class="control-label">Country</label>
                <select class="custom-select" name="country">
                    <option selected value="">Choose Your Country</option>
                    <option value="egypt">Egypt</option>
                    <option value="kenya">Kenya</option>
                    <option value="ghana">Ghana</option>
                    <option value="rwanda">Rwanda</option>
                    <option value="tanzania">Tanzania</option>
                    <option value="uganda">Uganda</option>
                    <option value="guyana">Guyana</option>
                    <option value="guinea">Guinea</option>
                    <option value="ivory coast">Ivory Coast</option>
                </select>
            </div>

            <div class="form-group">
                <label for="address" class="control-label">Password </label>
                <input name="password" type="password" class="form-control" placeholder="password">
                <input name="confirmpassword" type="password" class="form-control my-2" placeholder="confirm password">

            </div>
 
            <div class="form-group mb-5">
                <input type="submit" class="form-control btn btn-success" value="Sign Up">
            </div>

        </form>
    </div>
    <script src="validation.js"></script>
    <?php
}
require_once 'partials/footer.html';
ob_end_flush();
?>