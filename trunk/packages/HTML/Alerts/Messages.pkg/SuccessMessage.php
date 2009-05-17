<?
    class SuccessMessage {
        static function Show($title,$message) {
            $message = new Message($title,$message);
            $message->SetAttribute('class','message success');
            $message->style->background = "#DDFFDD url(" . WPM::GetFromPackage('Alerts/Messages','image','success') . ") no-repeat 10px 10px";
            $message->Show();
        }
    }
?>