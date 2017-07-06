<?php

        $subject = '忘記密碼通知信';
        $message = '您的密碼是 ： 0204885995';
        $headers = 'From: alex.lee@mojoint.com' . "\r\n".
            'Reply-To: alex.lee@com' . "\r\n".
            'X-Mailer: PHP/'. phpversion();

        mail( 'xelalee@gmail.com', $subject, $message, $headers );
