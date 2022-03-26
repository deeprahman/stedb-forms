<?php defined( 'ABSPATH' ) or die(); ?>

<?php
$stedb_forms_account         = get_option( 'stedb_forms_account', array() );
$stedb_forms_account_address = get_option( 'stedb_forms_account_address', array() );

/** countries list */
$stedb_forms_countries_list = array(
	"Afghanistan",
	"Albania",
	"Algeria",
	"American Samoa",
	"Andorra",
	"Angola",
	"Anguilla",
	"Antarctica",
	"Antigua and Barbuda",
	"Argentina",
	"Armenia",
	"Aruba",
	"Australia",
	"Austria",
	"Azerbaijan",
	"Bahamas",
	"Bahrain",
	"Bangladesh",
	"Barbados",
	"Barbuda",
	"Belarus",
	"Belgium",
	"Belize",
	"Benin",
	"Bermuda",
	"Bhutan",
	"Bolivia",
	"Bosnia and Herzegowina",
	"Botswana",
	"Bouvet Island",
	"Brazil",
	"British Indian Ocean Territory",
	"Brunei Darussalam",
	"Bulgaria",
	"Burkina Faso",
	"Burundi",
	"Caicos Islands",
	"Cambodia",
	"Cameroon",
	"Canada",
	"Cape Verde",
	"Cayman Islands",
	"Central African Republic",
	"Chad",
	"Chile",
	"China",
	"Christmas Island",
	"Cocos (Keeling) Islands",
	"Colombia",
	"Comoros",
	"Congo",
	"Congo, Democratic Republic of the",
	"Cook Islands",
	"Costa Rica",
	"Cote d'Ivoire",
	"Croatia",
	"Cuba",
	"Cyprus",
	"Czech Republic",
	"Denmark",
	"Djibouti",
	"Dominica",
	"Dominican Republic",
	"East Timor",
	"Ecuador",
	"Egypt",
	"El Salvador",
	"Equatorial Guinea",
	"Eritrea",
	"Estonia",
	"Ethiopia",
	"Falkland Islands (Malvinas)",
	"Faroe Islands",
	"Fiji",
	"Finland",
	"France",
	"French Guiana",
	"French Polynesia",
	"French Southern Territories",
	"Futuna Islands",
	"Gabon",
	"Gambia",
	"Georgia",
	"Germany",
	"Ghana",
	"Gibraltar",
	"Greece",
	"Greenland",
	"Grenada",
	"Guadeloupe",
	"Guam",
	"Guatemala",
	"Guernsey",
	"Guinea",
	"Guinea-Bissau",
	"Guyana",
	"Haiti",
	"Heard",
	"Herzegovina",
	"Holy See",
	"Honduras",
	"Hong Kong",
	"Hungary",
	"Iceland",
	"India",
	"Indonesia",
	"Iran (Islamic Republic of)",
	"Iraq",
	"Ireland",
	"Isle of Man",
	"Israel",
	"Italy",
	"Jamaica",
	"Jan Mayen Islands",
	"Japan",
	"Jersey",
	"Jordan",
	"Kazakhstan",
	"Kenya",
	"Kiribati",
	"Korea, Democratic People's Republic of",
	"Korea, Republic of",
	"Kuwait",
	"Kyrgyzstan",
	"Lao",
	"Latvia",
	"Lebanon",
	"Lesotho",
	"Liberia",
	"Libyan Arab Jamahiriya",
	"Liechtenstein",
	"Lithuania",
	"Luxembourg",
	"Macao",
	"Macedonia",
	"Madagascar",
	"Malawi",
	"Malaysia",
	"Maldives",
	"Mali",
	"Malta",
	"Marshall Islands",
	"Martinique",
	"Mauritania",
	"Mauritius",
	"Mayotte",
	"McDonald Islands",
	"Mexico",
	"Micronesia",
	"Miquelon",
	"Moldova",
	"Monaco",
	"Mongolia",
	"Montenegro",
	"Montserrat",
	"Morocco",
	"Mozambique",
	"Myanmar",
	"Namibia",
	"Nauru",
	"Nepal",
	"Netherlands",
	"Netherlands Antilles",
	"Nevis",
	"New Caledonia",
	"New Zealand",
	"Nicaragua",
	"Niger",
	"Nigeria",
	"Niue",
	"Norfolk Island",
	"Northern Mariana Islands",
	"Norway",
	"Oman",
	"Pakistan",
	"Palau",
	"Panama",
	"Papua New Guinea",
	"Paraguay",
	"Peru",
	"Philippines",
	"Pitcairn",
	"Poland",
	"Portugal",
	"Principe",
	"Puerto Rico",
	"Qatar",
	"Reunion",
	"Romania",
	"Russian Federation",
	"Rwanda",
	"Saint Barthelemy",
	"Saint Helena",
	"Saint Kitts",
	"Saint Lucia",
	"Saint Martin",
	"Saint Pierre",
	"Saint Vincent",
	"Samoa",
	"San Marino",
	"Sao Tome",
	"Saudi Arabia",
	"Senegal",
	"Serbia",
	"Seychelles",
	"Sierra Leone",
	"Singapore",
	"Slovakia",
	"Slovenia",
	"Solomon Islands",
	"Somalia",
	"South Africa",
	"South Georgia",
	"South Sandwich Islands",
	"Spain",
	"Sri Lanka",
	"Sudan",
	"Suriname",
	"Svalbard",
	"Swaziland",
	"Sweden",
	"Switzerland",
	"Syrian Arab Republic",
	"Taiwan",
	"Tajikistan",
	"Tanzania",
	"Thailand",
	"The Grenadines",
	"Timor-Leste",
	"Tobago",
	"Togo",
	"Tokelau",
	"Tonga",
	"Trinidad",
	"Tunisia",
	"Turkey",
	"Turkmenistan",
	"Turks Islands",
	"Tuvalu",
	"Uganda",
	"Ukraine",
	"United Arab Emirates",
	"United Kingdom",
	"United States",
	"United States Minor Outlying Islands",
	"Uruguay",
	"Uzbekistan",
	"Vanuatu",
	"Vatican City State",
	"Venezuela",
	"Vietnam",
	"Virgin Islands (British)",
	"Virgin Islands (US)",
	"Wallis",
	"Western Sahara",
	"Yemen",
	"Zambia",
	"Zimbabwe",
);

/**
 * get auth var
 * @var bool $stedb_forms_auth
 */
?>

<div class="stedb-forms-content wrap">
    <h2><?php esc_html_e( 'STEdb Settings', 'stedb-forms' ); ?></h2>

	<?php settings_errors(); ?>

    <hr/>

    <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
		<?php settings_fields( 'stedb-forms-settings-group' ); ?>

        <h2 class="title"><?php esc_html_e( 'Account Settings', 'stedb-forms' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="stedb-forms-account-user-id">
						<?php esc_html_e( 'User', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <input type="password" name="stedb_forms_account[user_id]" id="stedb-forms-account-user-id"
                           class="regular-text" value="<?php echo esc_attr( $stedb_forms_account['user_id'] ); ?>">
                    <button type="button" class="button stedb-forms-account-user-id-visibility">
                        <span class="stedb-forms-show-text">
                            <?php esc_html_e( 'Show', 'stedb-forms' ); ?>
                        </span>
                        <span class="stedb-forms-hide-text"
                              style="display: none;">
                            <?php esc_html_e( 'Hide', 'stedb-forms' ); ?>
                        </span>
                    </button>
                    <p class="description">
						<?php esc_html_e( 'user id for the STEdb API.', 'stedb-forms' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="stedb-forms-account-secret">
						<?php esc_html_e( 'Secret', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <input type="password" name="stedb_forms_account[secret]" id="stedb-forms-account-secret"
                           class="regular-text" value="<?php echo esc_attr( $stedb_forms_account['secret'] ); ?>">
                    <button type="button" class="button stedb-forms-account-secret-visibility">
                        <span class="stedb-forms-show-text">
                            <?php esc_html_e( 'Show', 'stedb-forms' ); ?>
                        </span>
                        <span class="stedb-forms-hide-text"
                              style="display: none;">
                            <?php esc_html_e( 'Hide', 'stedb-forms' ); ?>
                        </span>
                    </button>
                    <p class="description">
						<?php esc_html_e( 'secret code for the STEdb API.', 'stedb-forms' ); ?>
                    </p>
                </td>
            </tr>
			<?php if ( $stedb_forms_auth ): ?>
                <tr>
                    <th scope="row">
                        <label for="stedb-forms-account-base-url">
							<?php esc_html_e( 'Base URL', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" id="stedb-forms-account-base-url"
                               class="regular-text" value="<?php echo esc_attr( $stedb_forms_account['base_url'] ); ?>"
                               disabled>
                        <p class="description">
							<?php esc_html_e( 'base url for the STEdb API.', 'stedb-forms' ); ?>
                        </p>
                    </td>
                </tr>
			<?php endif; ?>
        </table>

		<?php if ( $stedb_forms_auth ): ?>

            <h2 class="title"><?php esc_html_e( 'Account Address Settings', 'stedb-forms' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label>
							<?php esc_html_e( 'Address', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="stedb_forms_account_address[address]"
                               id="stedb-forms-account-address-address" class="regular-text"
                               value="<?php echo esc_attr( $stedb_forms_account_address['address'] ); ?>"
                               aria-label="<?php esc_attr_e( 'Address line 1', 'stedb-forms' ); ?>">
                        <p class="description">
							<?php esc_html_e( 'Address line 1', 'stedb-forms' ); ?>
                        </p>
                        <br/>
                        <input type="text" name="stedb_forms_account_address[address2]"
                               id="stedb-forms-account-address-address2" class="regular-text"
                               value="<?php echo esc_attr( $stedb_forms_account_address['address2'] ); ?>"
                               aria-label="<?php esc_attr_e( 'Address line 2', 'stedb-forms' ); ?>">
                        <p class="description">
							<?php esc_html_e( 'Address line 2', 'stedb-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="stedb-forms-account-address-city">
							<?php esc_html_e( 'City', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="stedb_forms_account_address[city]"
                               id="stedb-forms-account-address-city"
                               class="regular-text"
                               value="<?php echo esc_attr( $stedb_forms_account_address['city'] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="stedb-forms-account-address-state-province">
							<?php esc_html_e( 'State/Province', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="stedb_forms_account_address[state_province]"
                               id="stedb-forms-account-address-state-province" class="regular-text"
                               value="<?php echo esc_attr( $stedb_forms_account_address['state_province'] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="stedb-forms-account-address-zip-code">
							<?php esc_html_e( 'Zip/Postal Code', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="stedb_forms_account_address[zip_code]"
                               id="stedb-forms-account-address-zip-code"
                               value="<?php echo esc_attr( $stedb_forms_account_address['zip_code'] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="stedb-forms-account-address-country">
							<?php esc_html_e( 'Country', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="stedb_forms_account_address[country]"
                                id="stedb-forms-account-address-country">
                            <option value="" hidden disabled
								<?php selected( $stedb_forms_account_address['country'], false ); ?>>
								<?php esc_html_e( 'Select Country...', 'stedb-forms' ); ?>
                            </option>
							<?php foreach ( $stedb_forms_countries_list as $stedb_forms_address_country ): ?>
                                <option value="<?php echo esc_attr( $stedb_forms_address_country ); ?>"
									<?php selected( $stedb_forms_account_address['country'], $stedb_forms_address_country ); ?>>
									<?php echo esc_html( $stedb_forms_address_country ); ?>
                                </option>
							<?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
		<?php endif; ?>

        <p class="submit">
            <input type="submit" class="button-primary"
                   value="<?php esc_attr_e( 'Save Changes', 'stedb-forms' ); ?>"/>
        </p>

    </form>

	<?php if ( ! $stedb_forms_auth ): ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <h2 class="title"><?php esc_html_e( 'Create Account in API', 'stedb-forms' ); ?></h2>

            <input type="hidden" name="action" value="stedb_forms_create_account">
			<?php wp_nonce_field( 'stedb_forms_create_account' ); ?>

            <p class="description">
				<?php esc_html_e( 'To create a new account, click the button and the system will automatically register you as a user with the API.', 'stedb-forms' ); ?>
            </p>

            <p class="submit">
                <input type="submit" class="button-primary"
                       value="<?php esc_attr_e( 'Create Account', 'stedb-forms' ); ?>"/>
            </p>
        </form>
	<?php endif; ?>

	<?php //todo: delete debug ?>
    <br/>
    <hr/>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <h2 class="title"><?php esc_html_e( 'Debug', 'stedb-forms' ); ?></h2>

        <input type="hidden" name="action" value="stedb_forms_debug_pure_db">
		<?php wp_nonce_field( 'stedb_forms_debug_pure_db' ); ?>

        <p class="submit">
            <input type="submit" class="button-primary"
                   value="<?php esc_attr_e( 'Pure the database', 'stedb-forms' ); ?>"/>
        </p>
        <p class="description">
            This completely deletes the tables in the database. After use, the plugin must be deactivated and then
            activated.
        </p>
    </form>

</div>
