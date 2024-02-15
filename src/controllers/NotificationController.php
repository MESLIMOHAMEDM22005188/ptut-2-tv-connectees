<?php

namespace Controllers;

class NotificationController extends Controller{

    public function displaySuccessNotification($message){
        echo '<div class="notification success"><p>' . $message . '</p></div>';
    }

    public function displayErrorNotification($message){
        echo '<div class="notification error"><p>' . $message . '</p></div>';
    }

}
