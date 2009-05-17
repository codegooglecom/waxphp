<?
    class InfoMessage {
        static function Show($title,$message) {
            $message = new Message($title,$message);
            $message->SetAttribute('class','message info');
            $message->AddStyle("background","#DDDDFF url(" . WPM::GetFromPackage('Alerts/Messages','image','info') .") no-repeat 10px 10px");
            $message->Show();
        }
    }
?>