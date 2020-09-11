<?php

if ($handle = opendir('.')) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {

            echo "<pre>$entry</pre>\n";
        }
    }

    closedir($handle);
}