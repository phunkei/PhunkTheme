<?php
namespace Phunkei\PhunkTheme\ThemeAddons;
use Phunkei\PhunkTheme\ThemeAddons\ThemeAddon;

class ContactFormAddon extends ThemeAddon {

	public $contactFormFields;

	public $sender;
	public $receiver;
	public $subject;

	public function __construct() {
		add_action( 'wp_ajax_sendContactForm', [$this, 'sendContactForm']);
		add_action( 'wp_ajax_nopriv_sendContactForm', [$this, 'sendContactForm']);
	}

	public function draw() {
		echo $this->loadTemplate('template-parts/form-contact.php');
	}

	public function setupContactFormFields($fields = null) {
		if(!$fields) {
			$this->contactFormFields = [
				'firstname' => [
					'label' => 'Vorname',
					'required' => true,
					'type' => 'text'
				],
				'lastname' => [
					'label' => 'Nachname',
					'required' => true,
					'type' => 'text'
				],
				'email' => [
					'label' => 'E-Mail',
					'required' => true,
					'type' => 'email'
				],
				'phone' => [
					'label' => 'Telefon',
					'required' => true,
					'type' => 'text'
				],
				'message' => [
					'label' => 'Nachricht',
					'required' => true,
					'type' => 'text_multiline',
				],
			];
		}
		else {
			$this->contactFormFields = $fields;
		}
	}

	public function sendContactForm() {
		if(wp_verify_nonce($_POST['_wpnonce'], 'contactForm')) {
			status_header(200);
			$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
			if ( 'www.' === substr( $sitename, 0, 4 ) ) {
				$sitename = substr( $sitename, 4 );
			}

			$body = [];

			foreach($this->contactFormFields as $key => $field) {
				if($field['required'] && empty($_POST[$key])) {
					status_header(401);
					wp_die();
				}
				if($field['type'] == 'email' && !filter_var($_POST[$key], FILTER_VALIDATE_EMAIL)) {
					status_header(401);
					wp_die();
				}
				if($field['type'] == 'text_multiline') {
					$body[] = $field['label'] . ":<br />".PHP_EOL.nl2br($_POST[$key]);
				}
				else {
					$body[] = $field['label'] . ": ".$_POST[$key];
				}
				
			}
			$body = implode("<br />".PHP_EOL, $body);

			$headers = [];
			$headers[] = 'From: '.$sitename.' Kontaktformular <'.$this->sender.'>';
			$headers[] = 'ReplyTo: '.$_POST['email'];
			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			
			$send = wp_mail( $this->receiver, $this->subject, $body, $headers );
			echo $this->loadTemplate('template-parts/form-contact-feedback.php', ['send' => $send]);
		}
		else {
			status_header(400);
			echo "nonce invalid";
		}
		
		wp_die();
	}
}