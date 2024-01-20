<?php
require_once 'config.php';

ini_set('upload_max_filesize', '1000M');
ini_set('post_max_size', '1000M');

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

$categoryFilter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    if (isset($_POST['druginfo_title'], $_POST['druginfo_file'])) {
        $druginfo_title = mysqli_real_escape_string($conn, $_POST['druginfo_title']);
        $druginfo_file = mysqli_real_escape_string($conn, $_POST['druginfo_file']);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Info</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Your existing styles... */

        .category-filter {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px; /* Adjust margin as needed */
            padding: 15px;
            border: 1px solid #ddd; /* Add border as desired */
            border-radius: 5px;
        }

        .category-filter form {
            display: flex;
            align-items: center;
        }

        .category-filter label {
            margin-right: 10px; /* Adjust margin as needed */
        }

        .category-filter select {
            padding: 8px;
            margin-right: 10px; /* Adjust margin as needed */
        }

        .category-filter input[type="submit"] {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .category-filter input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <?php require_once 'header.php'; ?>

    <div class="heading">
        <h3>DRUG INFO</h3>
    </div>

    <div class="category-filter">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
            <label for="category"><h1>Select Category:</h1></label>
            <select name="category" id="category">
                <option value="Legal" <?php echo ($categoryFilter == 'Legal') ? 'selected' : ''; ?>>Legal</option>
                <option value="Illegal" <?php echo ($categoryFilter == 'Illegal') ? 'selected' : ''; ?>>Illegal</option>
                <option value="Medicinal" <?php echo ($categoryFilter == 'Medicinal') ? 'selected' : ''; ?>>Medicinal</option>
                <option value="Recreational" <?php echo ($categoryFilter == 'Recreational') ? 'selected' : ''; ?>>Recreational</option>
            </select>
            <input type="submit" value="Filter">
        </form>
    </div>

    <section class="drug-info">
        <div class="box-container">
            <?php
            $query = "SELECT * FROM `druginfo`";

            if (!empty($categoryFilter)) {
                $query .= " WHERE `category`='$categoryFilter'";
            }

            $query .= " ORDER BY `category`, `title`";

            $select_druginfo = mysqli_query($conn, $query) or die(mysqli_error($conn));

            if (mysqli_num_rows($select_druginfo) > 0) {
                while ($fetch_druginfo = mysqli_fetch_assoc($select_druginfo)) {
            ?>
                    <form action="" method="post" class="box">
                        <?php
                        if (strpos($fetch_druginfo['file'], '.mp4') !== false) {
                        ?>
                            <div class="video-container">
                                <video controls>
                                    <source src="uploaded_img/<?php echo $fetch_druginfo['file']; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        <?php
                        } else {
                        ?>
                            <img class="file" src="uploaded_img/<?php echo $fetch_druginfo['file']; ?>" alt="">
                        <?php
                        }
                        ?>
                        <div class="name"><?php echo htmlspecialchars($fetch_druginfo['title']); ?></div>
                        <input type="hidden" name="druginfo_title" value="<?php echo htmlspecialchars($fetch_druginfo['title']); ?>">
                        <input type="hidden" name="druginfo_file" value="<?php echo htmlspecialchars($fetch_druginfo['file']); ?>">
                    </form>
            <?php
                }
            } else {
                echo '<p class="empty">No drug info added yet!</p>';
            }
            ?>
        </div>
    </section>

    <?php require_once 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>
