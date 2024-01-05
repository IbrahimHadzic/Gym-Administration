<?php 

/*                    <?php foreach($training_plan_id as $plan): ?>
                        <option value="<?= $plan['plan_id'] ?>">
                            <?= $plan['name'] ?>
                        </option>
                    <?php endforeach; ?> */



require_once 'config.php';

if(!isset($_SESSION['admin_id'])){
    header('location: index.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    
    <title>Admin dashboard</title>
</head>
<body>
    

<?php if(isset($_SESSION['success_message'])) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success_message'];
        unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close" ></button>
    </div>
<?php endif; ?>




<div class="container">

    <div class="row">
        <div class="col-md-12">
            <h2>Member list</h2>
            <a href="export.php?what=members" class="btn btn-success btn-sm">Export</a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Trainer</th>
                        <th>Photo</th>
                        <th>Training Plan</th>
                        <th>Access Card</th>
                        <th>Created At</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT members.*, 
                    training_plans.name AS training_plan_name,
                    trainers.first_name AS trainer_first_name,
                    trainers.last_name AS trainer_last_name
                    FROM `members`
                    LEFT JOIN `training_plans` ON members.training_plan_id = training_plans.plan_id
                    LEFT JOIN `trainers` ON members.trainer_id = trainers.trainer_id;";

                    $run = $conn->query($sql);

                    $results = $run->fetch_all(MYSQLI_ASSOC);
                    $select_members = $results;

                    foreach($results as $result) : ?>

                        <tr>
                            <td><?php echo $result['first_name']; ?></td>
                            <td><?php echo $result['last_name']; ?></td>
                            <td><?php echo $result['email']; ?></td>
                            <td><?php echo $result['phone_number']; ?></td>
                            <td><?php if($result['trainer_first_name']){
                                echo $result['trainer_first_name'] . " " . $result['trainer_last_name'];
                            } else {
                                echo "Nema trenera";
                            } ?></td>
                            <td><img style="width: 30px" src="<?php echo $result['photo_path']; ?>"> </td>
                            <td><?php 
                            if($result['training_plan_name']) {
                                echo $result['training_plan_name'];
                            } else{
                                echo "Nema plana";
                            }
                            ?></td>
                            <td><a target="_blank" href="<?php echo $result['access_card_pdf_path']; ?>">Access card</a> </td>
                            <td><?php  
                            
                            $create_at = strtotime($result['created_at']); 
                            $new_date = date("F, jS, Y", $create_at);
                            echo $new_date;
                            ?></td>
                            <td>
                                <form action="delete_member.php" method="POST">
                                    <input type="hidden" name="member_id" value="<?php echo $result['member_id']; ?>">
                                    <button>DELETE</button>
                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                    
                </tbody>
            </table>
        </div>

        <col-md-12>
        <h2>Trainers List</h2>
        <a href="export.php?what=trainers" class="btn btn-success btn-sm">Export</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>


                <?php 
                $sql = "SELECT * FROM trainers";
                $run = $conn->query($sql);
                $results = $run->fetch_all(MYSQLI_ASSOC);
                $select_trainers = $results;

                foreach($results as $result) : ?>
                    <tr>
                        <td><?php echo $result['first_name'] ?></td>
                        <td><?php echo $result['last_name'] ?></td>
                        <td><?php echo $result['email'] ?></td>
                        <td><?php echo $result['phone_number'] ?></td>
                        <td><?php echo date("F jS, Y", strtotime($result['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </col-md-12>
    


        <div class="row mb-5">
            <div class="col-md-6">
                <h2>Register Member</h2>
                <form action="register_member.php" method="post" enctype="multipart/form-data">
                    First name: <input class="form-control" type="text" name="first_name"><br>
                    Last name: <input class="form-control" type="text" name="last_name"><br>
                    E-mail: <input class="form-control" type="email" name="email"><br>
                    Phone number: <input class="form-control" type="text" name="phone_number"><br>
                    Training plan:
                    <select class="form-control" name="training_plan_id" id="">
                        <option value="" disabled selected>Training Plan</option>
                        <?php 
                        $sql = "SELECT * FROM training_plans";
                        $run = $conn->query($sql);
                        $results = $run->fetch_all(MYSQLI_ASSOC);



                        foreach($results as $result) {

                            echo "<option value='" . $result['plan_id'] . "'>" . $result['name'] . "</option>";
                        }
                        ?>
                    </select><br>
                    <input type="hidden" name="photo_path" id="photoPathInput">
                    
                    <div id="dropzone-upload" class="dropzone"></div>

                    <input class="btn btn-primary mt-3" type="submit" value="Register Member">
                </form>
            </div>
            <div class="col-md-6">
                <h2>Register Trainer</h2>
                <form action="register_trainer.php" method="POST">
                    First Name: <input class="form-control" type="text" name="first_name">
                    Last Name: <input class="form-control" type="text" name="last_name">
                    E=mail: <input class="form-control" type="text" name="email">
                    Phone Number: <input class="form-control" type="text" name="phone_number">
                    <input class="btn btn-primary" type="submit" value="Register Trainer">

                    
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h2>Assing Trainer to Member</h2>
                <form action="assing_trainer.php" method="POST">
                    <label for="">Select Member</label>
                    <select name="member" class="form-select">
                        <?php foreach($select_members as $member) : ?>
                            <option value="<?php echo $member['member_id'] ?>">
                                <?php echo $member['first_name'] . " " . $member['last_name']; ?>
                            </option>

                        <?php endforeach; ?>
                    </select>
                    <label for="">Select Trainer</label>
                    <select name="trainer" class="form-select">
                        <?php foreach($select_trainers as $trainer) : ?>
                                <option value="<?php echo $trainer['trainer_id'] ?>">
                                    <?php echo $trainer['first_name'] . " " . $trainer['last_name']; ?>
                                </option>

                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Assing Trainer</button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php 
    $conn->close();
    ?>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script>
    Dropzone.options.dropzoneUpload = {
        url: "upload_photo.php",
        paramName: "photo",
        maxFileSize: 20, // MB
        acceptedFiles: "image/*",
        init: function () {
            this.on("success", function (file, response) {
                // Parse the JSON response
                const jsonResponse = JSON.parse(response);
                // Check if the file was uploaded successfully
                if (jsonResponse.success) {
                    // Set the hidden input's value to the uploaded file's path
                    document.getElementById("photoPathInput").value = jsonResponse.photo_path;
                } else {
                    console.error(jsonResponse.error);
                }
            });
        }
    };

</script>

</body>


</html>




