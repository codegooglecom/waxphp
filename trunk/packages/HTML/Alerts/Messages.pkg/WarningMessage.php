<?
    class WarningMessage {
        static function Show($title,$message) {
            $message = new Message($title,$message);
            $message->SetAttribute('class','message warning');
            $message->AddStyle("background","#FFFFDD url(" . WPM::GetFromPackage('Alerts/Messages','image','warning') .") no-repeat 10px 10px");
            $message->Show();
        }
    }
?>