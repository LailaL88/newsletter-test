<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results</title>
    <link rel="stylesheet" href="./styles/result-styles.css">
</head>

<body>
    <form action="" method="post">
        <input type="submit" value="All" name="all">
    </form>
    <form action="" method="post">
        <input type="submit" value="Sort by date" name="by-date">
    </form>
    <form action="" method="post">
        <input type="submit" value="Sort by name" name="by-name">
    </form>
    <br>
<?php session_start();
$name=$_SESSION['name'];

try {
    $pdo=new PDO("mysql:host=localhost;dbname=magebit_test", "root", "");
    $sql="SELECT * FROM emails";
    $q=$pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);

    $myarray=array();
    $rowIds=array();

    while ($row=$q->fetch()) {
        $email=$row['email'];

        $a="@";
        $pos=strpos($email, $a)+1;
        $mailending=substr($email, $pos);

        array_push($myarray, $mailending);

        array_push($rowIds, $row['id']);
    }

    $uniquearray=array_unique($myarray);

    foreach ($uniquearray as $value) {
        $dotPos=strpos($value, ".");
        $afterDot=substr($value, $dotPos);
        $buttonText=str_replace($afterDot, "", $value);
        $capitalised=ucwords($buttonText);
        echo "<form action='' method='post'><input type='submit'value='$capitalised'name='$capitalised'></form>";

        if(isset($_POST["$capitalised"])) {
            $_SESSION['name']=$value;
            $name=$_SESSION['name'];
        }
    }

    foreach ($rowIds as $theId) {
        if(isset($_POST["$theId"])) {
            $sql="DELETE FROM `emails` WHERE `id` = $theId";
            $pdo->query($sql);
        }
    }

    if($_SESSION['input']) {
        $input=$_SESSION['input'];
    }

    else {
        $input="";
    }

    if(isset($_POST["search"])) {
        $input=$_POST["search-input"];
        $_SESSION['input']=$input;
    }

    if(isset($_POST["by-date"])) {
        $sql="SELECT * FROM emails  WHERE email REGEXP '$name$' AND email LIKE '%$input%'";
    }

    else if(isset($_POST["by-name"])) {
        $sql="SELECT * FROM emails  WHERE email REGEXP '$name$' AND email LIKE '%$input%' ORDER BY email";
    }

    else if(isset($_POST["all"])) {
        $sql='SELECT * FROM emails';
        $_SESSION['name']="";
        $_SESSION['input']="";
    }

    else if($name !="") {
        $sql="SELECT * FROM emails WHERE email REGEXP '$name$' AND email LIKE '%$input%'";
    }

    else {
        $sql='SELECT * FROM emails';           

    }

    if(isset($_POST["search"])) {
        $input=$_POST["search-input"];
        $sql="SELECT * FROM emails  WHERE email LIKE '%$input%'";
    }

    $q=$pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);

}

catch (PDOException $e) {
    die("Could not connect to the database  :". $e->getMessage());
}

?>
<br>
<form action="" method="post">
    <input type="text" placeholder="search for..." name="search-input">
    <input type="submit" value="Search" name="search">
</form>

<table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>Emails</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $q->fetch()): ?>
        <tr>
            <td>
                <form action="" method="post">
                    <input type="submit" value="Delete" name="<?php echo htmlspecialchars($row['id'])?>"
                        style="border:none;">
                </form>
                <?php echo htmlspecialchars($row['email']);?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>

</html>