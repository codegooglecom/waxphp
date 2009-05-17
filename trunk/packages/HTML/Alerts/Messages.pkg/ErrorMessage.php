<?
    class ErrorMessage {
        static function Show($title,$message) {
            $message = new Message($title,$message);
            $message->SetAttribute('class','message error');
            $message->AddStyle("background","#FFDDDD url(" . WPM::GetFromPackage('Alerts/Messages','image','error') .") no-repeat 10px 10px");
            $message->Show();
        }
    }
?>