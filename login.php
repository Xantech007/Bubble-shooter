<?php include "inc/header.php"; ?>

<?php
if($_POST){
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    if($user && password_verify($_POST['password'], $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['balance'] = $user['balance'];
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid login";
    }
}
?>

<form method="POST">
<input name="email" placeholder="Email"><br>
<input name="password" type="password"><br>
<button>Login</button>
</form>

<?php include "inc/footer.php"; ?>
