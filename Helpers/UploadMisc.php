<?php
namespace Helpers;
class UploadMisc {
    private $profile;

    function __construct($profile) {
        $this->profile = $profile;
    }

    public function startUpload() {
        $result = [];
        // Quote
        if (isset($_POST['quote']) && !empty($_POST["quote"])) {
            $result["quote"] = $this->uploadQuote($_POST["quote"]);
        }

        // Link
        if (isset($_POST['link']) && !empty($_POST["link"])) {
            $result["link"] = $this->uploadLink($_POST["link"]);
        }
        $this->profile->save();
        return $result;
    }

    private function uploadQuote($quote) {
        // TODO, SET MAXIMUM CHARS
        if (strlen($quote) > 60) {
            return false;
        }
        else {
            $sanitizedQuote = nl2br(htmlspecialchars($quote));
            $this->profile->quote = $sanitizedQuote;
            return $sanitizedQuote;
        }
    }

    private function uploadLink($link) {
        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            return false;
        }
        else {
            $this->profile->link = $link;
            return $link;
        }
    }
}
