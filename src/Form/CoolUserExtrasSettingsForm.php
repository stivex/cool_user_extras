<?php

/**
* @file
* Contains \Drupal\cool_user_extras\Form\CoolUserExtrasSettingsForm.
*/

namespace Drupal\cool_user_extras\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
* Implements the CoolUserExtrasSettingsForm form controller.
*
* @see \Drupal\Core\Form\ConfigFormBase
*/
class CoolUserExtrasSettingsForm extends ConfigFormBase {
	
	//Property that saves the service (by injection) and we will be able to use it into this class
	protected $entityTypeManager;
	
	public function __construct(EntityTypeManagerInterface $entityTypeManager) {
		$this->entityTypeManager = $entityTypeManager;
	}
	
	public static function create(ContainerInterface $container) {
		return new static(
			$container->get('entity_type.manager'),
		);
	}
	
	//Function that retrieves us the form identifier
	public function getFormId() {
		return 'cool_user_extras_settings_form';
	}
	
	//Function that retrieves us the name of the configuration object that is used to read and save the module configuration
	//To have access on module configuration object: $config = $this->config('cool_user_extras.settings');
	//For reading configuration: $config->get('allowed_types');
	//For writing configuration: $config->set('allowed_types', $allowed_types);
	protected function getEditableConfigNames() {
		return ['cool_user_extras.settings'];
	}
	
	
	//Method that generates/builds the form fields
	public function buildForm(array $form, FormStateInterface $form_state) {
		
		//We get the object that it will serve us to retrieve the module configuration values and load them into the fields
		$config = $this->config('cool_user_extras.settings');
		
		//We get the existing roles
		$roles = $this->entityTypeManager->getStorage('user_role')->loadMultiple();
		$roles_options = array();
		foreach ($roles as $role) {
			
			//The 'anonymous' and 'authenticated' roles we don't allow to choose, because Drupal don't allow to assign them in automatic mode
			if ($role->id() != 'anonymous' && $role->id() != 'authenticated') {
				$roles_options[$role->id()] = $role->label();
			}
			
		}
		
		//List (array key/value) with all types of content available (this function is included into Drupal API)
		$types_of_content = node_type_get_names();
		
		$form['tabs_container'] = [
			'#type' => 'vertical_tabs',
		];
		
		//We create a new tab
		$form['tab_sync'] = [
			'#type' => 'details',
			'#title' => $this->t('Sync/import users'),
			'#description' => $this->t(''),
			'#group' => 'tabs_container',
		];
		
		$form['tab_sync']['help_sync_item'] = [
			'#title' => $this->t('Sync/import users from other database'),
			'#type' => 'item',
			'#markup' => $this->t('This utility helps you to import users (name and mail) from other database (MySQL/MariaDB).<br>Passwords <strong>won\'t be imported</strong>. It will generate a random password for all imported users.'),
		];
		
		$form['tab_sync']['group_connection'] = [
			'#type' => 'details',
			'#title' => $this->t('Connection params'),
			'#description' => $this->t('Params to connect on external database server.'),
			'#open' => TRUE,
		];
		
		$form['tab_sync']['group_connection']['host'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Host'),
			'#required' => TRUE,
			'#size' => 40,
			'#maxlength' => 80,
			'#default_value' => (is_null($config->get('host'))) ? '' : base64_decode($config->get('host')),
			'#description' => $this->t('The server ip or domain.'),
		];
		
		$form['tab_sync']['group_connection']['port'] = [
			'#type' => 'number',
			'#title' => $this->t('Port'),
			'#required' => TRUE,
			'#default_value' => (is_null($config->get('port'))) ? '' : base64_decode($config->get('port')),
			'#description' => $this->t('The port number.'),
		];
		
		$form['tab_sync']['group_connection']['database'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Database'),
			'#required' => TRUE,
			'#size' => 40,
			'#maxlength' => 80,
			'#default_value' => (is_null($config->get('database'))) ? '' : base64_decode($config->get('database')),
			'#description' => $this->t('The database name.'),
		];
		
		$form['tab_sync']['group_connection']['user'] = [
			'#type' => 'textfield',
			'#title' => $this->t('User'),
			'#required' => TRUE,
			'#size' => 40,
			'#maxlength' => 80,
			'#default_value' => (is_null($config->get('user'))) ? '' : base64_decode($config->get('user')),
			'#description' => $this->t('The user name to connect to the database.'),
		];
		
		$form['tab_sync']['group_connection']['pass'] = [
			'#type' => 'password',
			'#title' => $this->t('Password'),
			'#required' => TRUE,
			'#size' => 40,
			'#maxlength' => 80,
			'#default_value' => (is_null($config->get('pass'))) ? '' : base64_decode($config->get('pass')),
			'#description' => $this->t('The password to connect to the database.'),
		];
		
		$form['tab_sync']['query'] = [
			'#type' => 'textarea',
			'#title' => t('SQL select'),
			'#required' => TRUE,
			'#cols' => 50,
			'#rows' => 5,
			'#default_value' => (is_null($config->get('query'))) ? '' : base64_decode($config->get('query')),
			'#description' => $this->t('Type here a SQL query that retrieves usernames and their mails.'),
		];
		
		$form['tab_sync']['column_username'] = [
			'#type' => 'textfield',
			'#title' => $this->t('User name column'),
			'#required' => TRUE,
			'#size' => 40,
			'#maxlength' => 80,
			'#default_value' => (is_null($config->get('column_username'))) ? '' : base64_decode($config->get('column_username')),
			'#description' => $this->t('Username column from SQL query above.'),
		];
		
		$form['tab_sync']['column_mail'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Mail column'),
			'#required' => TRUE,
			'#size' => 40,
			'#maxlength' => 80,
			'#default_value' => (is_null($config->get('column_mail'))) ? '' : base64_decode($config->get('column_mail')),
			'#description' => $this->t('Mail column from SQL query above.'),
		];
		
		$form['tab_sync']['roles'] = [
			'#type' => 'checkboxes',
			'#title' => $this->t('Roles'),
			'#default_value' => (is_null($config->get('roles'))) ? [] : $config->get('roles'),
			'#options' => $roles_options,
			'#description' => $this->t('Select the roles that you want users get.'),
		];
		
		$form['tab_sync']['cron'] = [
			'#type' => 'checkbox',
			'#title' => $this->t('Execute with cron.'),
			'#default_value' => (is_null($config->get('cron'))) ? '' : base64_decode($config->get('cron')),
			'#description' => $this->t('Each time the cron executes, it tries to sync/import users.'),
		];
		
		
		//We create a new tab
		$form['tab_default'] = [
			'#type' => 'details',
			'#title' => $this->t('Load default user'),
			'#description' => '',
			'#group' => 'tabs_container',
		];
		
		$form['tab_default']['help_default_item'] = [
			'#title' => $this->t('Load the owner user by default on user entity reference field when you create a new entity.'),
			'#type' => 'item',
			'#markup' => $this->t('If you enable this option, the field also will become read only when you add or edit.'),
		];
		
		$form['tab_default']['load_default_user'] = [
			'#type' => 'checkboxes',
			'#title' => $this->t('Entities'),
			'#default_value' => (is_null($config->get('load_default_user'))) ? [] : $config->get('load_default_user'),
			'#options' => $types_of_content,
			'#description' => $this->t('Select the entities that you want to load the user by default.'),
		];
		
		$form['tab_default']['role_excluded'] = [
			'#type' => 'select',
			'#title' => $this->t('Role excluded'),
			'#default_value' => (is_null($config->get('role_excluded'))) ? 'administrator' : base64_decode($config->get('role_excluded')),
			'#options' => $roles_options,
			'#description' => $this->t('Select the role that you don\'t want to apply this behavior.'),
		];
		
		//Unlike normal forms, in the configuration forms is not necessary adding the submit button
		//About this already will be taken care of the buildForm() method (of the parent class)
		return parent::buildForm($form, $form_state);
		
	}
	
	//Method that do the fields validation just after click the submit button
	public function validateForm(array &$form, FormStateInterface $form_state) {
		
		//No validation will be done
		parent::validateForm($form, $form_state);
		
	}
	
	//Method that do the necessary operations/managements when form validation has passed successfully
	public function submitForm(array &$form, FormStateInterface $form_state) {
		
		//We get the object that will serve us to save the module configuration values
		$config = $this->config('cool_user_extras.settings');
		
		//We get the values that are into the form
		$fields_and_values = $form_state->cleanValues()->getValues();
		
		//We iterate all values received and we save them
		foreach ($fields_and_values as $field => $value) {
			
			//We don't take into account 'item' type fields
			if (!str_ends_with($field,'_item') && !is_array($value)) {
				$config->set($field, base64_encode($value));
			} else if ($field == 'roles' && is_array($value)) {
				//Roles array
				$config->set($field, $value);
			} else if ($field == 'load_default_user' && is_array($value)) {
				//Entities array that we want to load by default into field user reference
				$config->set($field, $value);
			}
			
		}
		
		//We save the changes persistently into the database
		$config->save();
		
		//Submit the form
		parent::submitForm($form, $form_state);
		
	}
	
}
