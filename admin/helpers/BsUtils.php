<?php  

namespace App\Helper;

final class BsUtils {

    public static function showMessage($title = NULL ,$alter = NULL ,$data = NULL)
    {
        // alter : success ,info ,error
        if (is_null($data))
            return "";

        if (!empty($alter))
            $alter = '-'.$alter;

        $html = "<div class='alert alert{$alter} fade in'>";
        $html .= "	<button data-dismiss='alert' class='close'>×</button>";

        if (is_string($data))
        {
            $html .= "		<strong>{$title} !</strong> {$data}.";
        } else
        {
            if (!empty($title))
                $html .= "		<h4>{$title} !</h4>";

            $html .= "		<ul>";
            foreach ($data as $row)
                $html .= "	<li>{$row}.</li>";
            $html .= "		</ul>";
        }

        $html .= "</div>";

        return $html;
    }

    /*
        <!-- Animate -->
        <link rel="stylesheet" href="/adminlte/plugins/animate/animate.min.css">
        <!-- bootstrap-notify -->
        <script src="/adminlte/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
     */
    public static function showNotifyMessage($title = NULL ,$alter = NULL ,$data = NULL, $delay = 1000)
    {
        // alter : success ,info ,error
        if (is_null($data))
            return "";

        if (is_null($title))
            $title = ucfirst($alter);

        if (is_string($data))
        {
            $message = '<p>'.$data.'</p>';
        } else
        {
            $message = "<ul>";
            foreach ($data as $row)
                $message .= "<li>{$row}.</li>";
            $message .= "</ul>";
        }

        $html = "<script>$(function () {";
        $html .= "setTimeout(function() { $.notify({ title: '<h4>{$title}</h4>', message: '{$message}'},{ delay: {$delay}, type: '{$alter}' }); }, 300);";
        $html .= "});</script>";

        return $html;
    }


}
