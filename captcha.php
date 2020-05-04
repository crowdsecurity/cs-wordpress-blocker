<?php
use Inc\Gregwar\Captcha\CaptchaBuilder;
use Inc\Gregwar\Captcha\PhraseBuilder;


function checkCaptcha() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Checking that the posted phrase match the phrase stored in the session
        if (isset($_POST['phrase']) && PhraseBuilder::comparePhrases($_SESSION['phrase'], $_POST['phrase'])) {
            unset($_SESSION['phrase']);
            return true;
        } else {
            return false;
        }
    }
    return false;
}


function setCaptcha() {
    $captcha = new CaptchaBuilder;
    $_SESSION['phrase'] = $captcha->getPhrase();
    $img =$captcha->build()->inline();
    echo "<html>";
    echo "<form method=\"post\">";
    echo "<img src=\"$img\" />";
    echo "<input type=\"text\" name=\"phrase\" />";
    echo "<input type=\"submit\" />";
    echo "</form>";
    echo "</html>";
    wp_die("Please fill the captcha.");
}