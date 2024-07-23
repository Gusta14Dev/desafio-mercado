<?php 

if (!function_exists("dd")) {
    function dd(...$args) {
        echo "<pre style='color:#FF8400;font-weight: bold; background-color: #18171B;width:100%; padding: 8px'>";
            foreach ($args as $arg) {
                echo "\"<span style='color:#56DB3A;'>";
                    if (is_array($arg)) {
                        print_r($arg);
                    } else if (is_object($arg)) {
                        var_dump($arg);
                    } else {
                        echo $arg;
                    }
                echo "</span>\" \n";
            }
        echo "</pre>";

        die();
    }
}

if (!function_exists("dump")) {
    function dump(...$args) {
        echo "<pre style='color:#FF8400;font-weight: bold; background-color: #18171B;width:100%; padding: 8px'>";
            foreach ($args as $arg) {
                echo "\"<span style='color:#56DB3A;'>";
                    if (is_array($arg)) {
                        print_r($arg);
                    } else if (is_object($arg)) {
                        var_dump($arg);
                    } else {
                        echo $arg;
                    }
                echo "</span>\" \n";
            }
        echo "</pre>";
    }
}

if (!function_exists("env")) {
    function env($variable) {
        return $_ENV[$variable];
    }
}