<?php


$fields = array(
        array('label' => 'To', 'name' => 'to'),
        array('label' => 'From', 'name' => 'from'),
        array('label' => 'Additional header', 'name' => 'header'),
        array('label' => 'Return-path', 'name' => 'returnpath'),
        array('label' => 'Subject', 'name' => 'subject'),
        array('label' => 'Body', 'name' => 'body')
    );

$values = array();

// Empty default values
foreach ($fields as $field) { $values[$field['name']] = ''; }

// Get stored values from cookie
if (isset($_COOKIE['MailformDefaults']))
{
    $stored = json_decode($_COOKIE['MailformDefaults'], true);
    if ($stored)
        $values = array_merge($values, $stored);
}

if (isset($_POST['to']))
{
    $values = array_merge($values, $_POST);
    $headers = "From: " . $values['from'];
    if ($values['header'])
        $headers .= "\n" . $values['header'];
    $result = mail($values['to'], $values['subject'], $values['body'], $headers, ($values['returnpath'] ? "-f {$values['returnpath']}" : ''));
    setcookie("MailformDefaults", json_encode($values), time() + (86400*365));
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mailform</title>
        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <style type="text/css">
            body { padding-top: 40px; padding-bottom: 20px; }
        </style>
    </head>

    <body>

        <div class="container">

            <div class="col-lg-offset-2 col-lg-10">
                <h1>Mailer</h1>
                <p>
                    Basic SMTP client for experimenting with outgoing e-mail.<br>
                    The values entered in the form are saved in a cookie for subsequent use.
                </p>
            </div>

            <form role="form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" class="form-horizontal">

<?php foreach ($fields as $f): ?>

                <div class="form-group">
                    <label for="<?= $f['name'] ?>" class="col-md-2 control-label"><?= $f['label'] ?></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="<?= $f['name'] ?>" id="<?= $f['name'] ?>" placeholder="<?= $f['label'] ?>" value="<?= htmlentities($values[$f['name']]) ?>">
                    </div>
                </div>

<?php endforeach; ?>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </div>

            </form>

<?php if (isset($result)): ?>
<?php if ($result): ?>
    <div class="alert alert-success">
        OK, mail delivered to local MTA
    </div>
<?php else: ?>
    <div class="alert alert-error">
        Error: Could not deliver mail to local MTA
    </div>
<?php endif; ?>
<?php endif; ?>


        </div>

    </body>
</html>