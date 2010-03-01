<?php

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Current HandBrake Ripping Jobs</title>
        <script lang="javascript">

        </script>
        <style>
body {
    font-family: verdana, helvetica, sans-serif;
}

a {
    color: gray;
}

label {
    margin-left: 1em;
    margin-right: 1em;
}

table {
    border-collapse: collapse;
}

tr {
    border: 1px dashed darkGray;
}

tr:hover {
    background-color: lightBlue;
}

#errors {
    border: 1px solid firebrick;
    background: peachpuff;
    color: darkred;
    margin: 1em;
}

#messages {
    border: 1px solid cornflowerblue;
    background: lightcyan;
    color: darkblue;
    margin: 1em;
}

.default {
    background: beige;
    color: darkgray;
    font-style: italic;
}

.icon {
    height: 16px;
    width: 16px;
}
        </style>
    </head>
    <body>
        <h1>Current HandBrake Ripping Jobs</h1>

        <?php if ($errors) { ?>
            <div id="errors">
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if ($messages) { ?>
            <div id="messages">
                <ul>
                    <?php foreach ($messages as $message) { ?>
                        <li><?php echo htmlspecialchars($message); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>


    </body>
</html>
