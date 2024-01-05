
<?php 

require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    /* 1 Ovo je SQL Query */
    $sql = "SELECT admin_id, password FROM admins WHERE username = ?";

    /* 2 Priprema za izvrsenje query-ja */
    $run = $conn->prepare($sql);

    /* 5 s=1? ; $username se stavlja na tu poziciju */ 
    $run->bind_param("s", $username);
    
    /* 3 Izvrsenje query-ja */
    $run->execute();

    /* 4 Rezultati query-ja */
    $results = $run->get_result();


    if($results->num_rows == 1){
        $admin = $results->fetch_assoc();

        if(password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];

            $conn->close();
            header('location: admin_dashboard.php');
        } else {

            $_SESSION['errpr'] = "Netacan password!";
            $conn->close();
            header('location: index.php');
            exit();
        }

        
    } else {
        $_SESSION['errpr'] = "Netacan username!";
        $conn->close();
        header('location: index.php');
        exit();
    }



}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teretana</title>
</head>
<body>

<?php 

if(isset($_SESSION['error'])) {
    echo $_SESSION['error'] . "<br>";
    unset($_SESSION['error']);
}

?>
    <form action="" method="POST">
        Username : <input type="text" name="username"><br>
        Password : <input type="password" name="password"><br>
        <input type="submit" values="Login">
    </form>
</body>
</html>