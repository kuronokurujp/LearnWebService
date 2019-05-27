<?php
    if(!empty($_POST)) {
        echo json_encode(array('name' => $_POST['name'], 'age' => $_POST['age']));
    }