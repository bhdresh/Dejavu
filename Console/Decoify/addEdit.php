<?php

// Start session
session_start();

$postData = $userData = array();

// Get session data
$sessData = !empty($_SESSION['sessData']) ? $_SESSION['sessData'] : '';

// Get status message from session
if (!empty($sessData['status']['msg'])) {
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}

// Get posted data from session
if (!empty($sessData['postData'])) {
    $postData = $sessData['postData'];
    unset($_SESSION['sessData']['postData']);
}

// Get user data
if (!empty($_GET['id'])) {
    include 'db.class.php';
    $db = new DB();
    $conditions['where'] = array(
        'id' => $_GET['id'],
    );
    $conditions['return_type'] = 'single';
    $userData = $db->getRows('users', $conditions);
}

// Pre-filled data
$userData = !empty($postData) ? $postData : $userData;

// Define action
$actionLabel = !empty($_GET['id']) ? 'Edit' : 'Add';

?>

<!-- Display status message -->
<?php if (!empty($statusMsg) && ($statusMsgType == 'success')) { ?>
    <div class="alert alert-success"><?php echo $statusMsg; ?></div>
<?php } elseif (!empty($statusMsg) && ($statusMsgType == 'error')) { ?>
    <div class="alert alert-danger"><?php echo $statusMsg; ?></div>
<?php } ?>

<!-- Add/Edit form -->
<div class="panel panel-default">
    <div class="panel-heading"><?php echo $actionLabel; ?> User <a href="index.php" class="glyphicon glyphicon-arrow-left"></a></div>
    <div class="panel-body">
        <form method="post" action="userAction.php" class="form">
            <div class="form-group">
                <label>ID</label>
                <input type="text" class="form-control" name="id" value="<?php echo !empty($userData['id']) ? $userData['id'] : ''; ?>">
            </div>
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="username" value="<?php echo !empty($userData['username']) ? $userData['username'] : ''; ?>">
            </div>
            <div class="form-group">
                <label>Role</label>
                <input type="text" class="form-control" name="role" value="<?php echo !empty($userData['role']) ? $userData['role'] : ''; ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" name="email" value="<?php echo !empty($userData['email']) ? $userData['email'] : ''; ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <input type="text" class="form-control" name="status" value="<?php echo !empty($userData['status']) ? $userData['status'] : ''; ?>">
            </div>
            <div class="form-group">
                <label>TimeZone</label>
                <input type="text" class="form-control" name="timezone" value="<?php echo !empty($userData['timezone']) ? $userData['timezone'] : ''; ?>">
            </div>
            
            <input type="submit" name="userSubmit" class="btn btn-success" value="SUBMIT" />
        </form>
    </div>
</div>