Plugin Name: AIESEC University Registration 
Description: AIESEC University Registration plugin connected with AIESEC Poland Marketing tool
Version: 0.1
Author: Krzysztof Jackowski
Author e-mail: krzysztof.jackowski@aiesec.net
Author URI: https://www.linkedin.com/profile/view?id=202008277&trk=nav_responsive_tab_profile_pic
License: GPL 

Plugin was created to manage basic operations in AIESEC University program. 

How plugin works?
Goal of the plugin is to manage registrations and leads based on utm tags. Management of plugin for AIESEC Uni delivery team works via Podio workspaces where are applications with languages and languages groups. From there plugin gets data and puts into form to be displayed on the website. Also in the workspace there is app for registrations where all applications are saved from form. Moreover plugin uses Marketing tool API to save leads in database.

	
	|--------------------------------------------------------------------------
	| config.json
	|--------------------------------------------------------------------------
	|
	| Here are saved api information to connect with Marketing tool
	|
	

	
	|--------------------------------------------------------------------------
	| form.html
	|--------------------------------------------------------------------------
	|
	| Form template with basic javascript to manage it. Template is updated 
	| based on information recived by plugin. All the variables are encapsulated with {} brackets
	|
	

	
	|--------------------------------------------------------------------------
	| manage_leads.php
	|--------------------------------------------------------------------------
	|
	| Script executed when lead opens website. Saves cookie file with lead information 
	| and updates database via API
	|
	

	
	|--------------------------------------------------------------------------
	| manage_registration.php
	|--------------------------------------------------------------------------
	|
	| Script executed when submit button is pressed. Checks if lead visited website before based
	| on cookie file. If file doesn't exists gets current lead information and saves in DB via API.
	| If cookie is saved retrieves lead data from file and updates DB record via API.
	| If cookie is saved but next registration is performed, gets new lead information and saves in DB
	| Moreover executes podio_api.php script. 
	|
	

	
	|--------------------------------------------------------------------------
	| plugin.php
	|--------------------------------------------------------------------------
	|
	| Main script of plugin. Perfomes some config operations, updates form template 
	| and gets languages data from Podio.
	|
	

	
	|--------------------------------------------------------------------------
	| podio_api.php
	|--------------------------------------------------------------------------
	|
	| Connects to Podio and saves data of new customer.
	|
	

	
	|--------------------------------------------------------------------------
	| podio_keys.php
	|--------------------------------------------------------------------------
	|
	| Class that stores all credentials required to connect to Podio LC workspaces. 
	| If you want to add new LC clone workspace on Podio, go to Developer options of each application
	| and copy app id and app token. Update PodioKeys class in the same way that previous LCs. Do not change 
	| client secret and client id!
	|
	

	
	|--------------------------------------------------------------------------
	| style.css
	|--------------------------------------------------------------------------
	|
	| Basic styles to display form. 
	|
	