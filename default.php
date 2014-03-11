<?php
if(!defined('APPLICATION')) die();

$PluginInfo['Contact'] = array(
'Name' => 'Contact',	
'Version' => '1.3',
'Description' => 'A Contact Form that anyone can access to leave info and a message.',
'Author' => 'VrijVlinder',
'HasLocale' => True,
);

class ContactPlugin extends Gdn_Plugin{
	
	public function PluginController_Contact_Create($Sender){
		$Sender->Form = Gdn::Factory('Form');
		$Email = new Gdn_Email();
		if($Sender->Head){
			$Title = $Sender->Head->Title( T('Contact') );
			$Sender->Head->Title($Title);
			$Sender->AddJsFile('js/library/jquery.autogrow.js');
			$Sender->AddJsFile('jquery.gardenhandleajaxform.js');
                        $Sender->RemoveCssFile('admin.css'); 
                        $Sender->AddCssFile('style.css');
                        $Sender->AddCssFile('contact.css','plugins/Contact/contact.css');
                        $Sender->MasterView = 'default';
                       

			$Sender->Head->AddString('<script type="text/javascript">jQuery(document).ready(function($){$("textarea.TextBox").autogrow();})</script>');
		}

		if($Sender->Form->IsPostBack() != False){
			$Validation = new Gdn_Validation();
			$Validation->ApplyRule('YourName', 'Required', 'You must enter your name.');
			$Validation->ApplyRule('YourEmail', 'Email', 'You must enter your e-mail (will not be published).');
			$Validation->ApplyRule('Message', 'Required',"You must include a Message");
      $Validation->ApplyRule('Checkbox', 'Required',"You must check the box");
			$FormValues = $Sender->Form->FormValues();
			$Validation->Validate($FormValues);
			$Sender->Form->SetValidationResults($Validation->Results());
			if($Sender->Form->ErrorCount() == 0){
				$Name = $FormValues['YourName'];
                $Subject = sprintf('Contact From %s %s', $Name, date('j M Y H:i'));
                $Email->Subject($Subject);
                $Email->To( C('Garden.Email.SupportAddress', ''));
                $Email->From($FormValues['YourEmail']);
                $Email->Message($FormValues['Message']);
                $Option=GetIncomingValue('Form/Checkbox');
                try{
                    $Email->Send();
                }catch(Exception $Exception){
            
                    $Sender->Form->AddError(strip_tags($Exception->GetMessage()));
                }

				if($Sender->Form->ErrorCount() == 0){
					$Sender->StatusMessage = T('Thank you for your message.');
					$Sender->RedirectUrl = Url("/");
				}
			}
		
}

		$Sender->Render(dirname(__FILE__).DS.'contact.php');
	}

public function Base_Render_Before($Sender) {
        $Session = Gdn::Session();
       if ($Sender->Menu) {
           $Sender->Menu->AddLink('Contact',T('Contact'), '/contact');
         }
    }
public function Send($EventName = '') {
      
      if (Gdn::Config('Garden.Email.UseSmtp')) {
         $this->Contact->IsSMTP();
         $SmtpHost = Gdn::Config('Garden.Email.SmtpHost', '');
         $SmtpPort = Gdn::Config('Garden.Email.SmtpPort', 25);
         if (strpos($SmtpHost, ':') !== FALSE) {
            list($SmtpHost, $SmtpPort) = explode(':', $SmtpHost);
         }

         $this->Contact->Host = $SmtpHost;
         $this->Contact->Port = $SmtpPort;
         $this->Contact->SMTPSecure = Gdn::Config('Garden.Email.SmtpSecurity', '');
         $this->Contact->Username = $Username = Gdn::Config('Garden.Email.SmtpUser', '');
         $this->Contact->Password = $Password = Gdn::Config('Garden.Email.SmtpPassword', '');
         if(!empty($Username))
            $this->Contact->SMTPAuth = TRUE;

         
      } else {
         $this->Contact->IsMail();
      }
      
      if($EventName != ''){
         $this->EventArguments['EventName'] = $EventName;
         $this->FireEvent('SendMail');
      }

      $this->Contact->ThrowExceptions(TRUE);
      if (!$this->Contact->Send()) {
         throw new Exception($this->Contact->ErrorInfo);
      }
      
      return true;
   }
    
	public function Setup() {
  
             $matchroute = '^contact(/.*)?$';
             $target = 'plugin/Contact$1';
        
             if(!Gdn::Router()->MatchRoute($matchroute))
                  Gdn::Router()->SetRoute($matchroute,$target,'Internal'); 
          
    }

}