<?php

function adminer_object() {
    class AdminerNoRoot extends Adminer {
        function login( $login, $password ) {
            return ($login != 'root');
        }
    }
    return new AdminerNoRoot;
}

include "adminer_no_root.php";
