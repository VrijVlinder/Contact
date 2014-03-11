<?php if(!defined('APPLICATION')) die();
// if div has class "AjaxForm" and there is a form in this div, the form will be ajax-posted
$this->AddCssFile('contact.css', 'plugins/Contact');
$this->AddJsFile('jquery.gardenhandleajaxform.js');
?>



<div class="ContactForm AjaxForm">
<?php echo $this->Form->Open()?>
<?php echo $this->Form->Errors()?>
<h1><?php echo T('Contact')?></h1>

<ul>
	<li><?php echo $this->Form->Label('Your Name', 'YourName').$this->Form->TextBox('YourName')?></li>
	<li><?php echo $this->Form->Label('Your Email', 'YourEmail').$this->Form->TextBox('YourEmail')?></li>
	<li><?php echo $this->Form->Label('Message', 'Message').$this->Form->TextBox('Message', array('Multiline' => True))?></li>
    <li><?php echo $this->Form->Label('Check this if You Are Not a Bot').$this->Form->Radio('Checkbox')?></li>
</ul>

<!--<div class="Center"></div>-->
<?php echo $this->Form->Button('Send Message')?>
<?php echo $this->Form->Close()?>
</div>