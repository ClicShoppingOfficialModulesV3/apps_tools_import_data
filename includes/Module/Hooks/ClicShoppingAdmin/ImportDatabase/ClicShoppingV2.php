<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\ImportDatabase;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;
  use ClicShopping\Apps\Tools\ImportData\Classes\ClicShoppingAdmin\ImportDatabase;

  class ClicShoppingV2
  {
    protected $PrefixTable;
    protected $db;

    public function __construct()
    {
      $this->db = Registry::get('Db');
      $this->PrefixTable = HTML::outputProtected($_POST['prefix_tables']);
    }

    public function execute()
    {
      global $mysqli;

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      Registry::set('ImportDatabase', new ImportDatabase());
      $CLICSHOPPING_ImportDatabase = Registry::get('ImportDatabase');
      /*
        clean table
      */
      $this->db->delete('address_book');
      $this->db->delete('banners');
      $this->db->delete('banners_history');
      /*
            $this->db->delete('blog_categories');
            $this->db->delete('blog_categories_description');
            $this->db->delete('blog_content');
            $this->db->delete('blog_content_description');
            $this->db->delete('blog_content_to_categories');
      */
      $this->db->delete('categories');
      $this->db->delete('categories_description');
      /*
            $this->db->delete('contact_customers');
            $this->db->delete('contact_customers_follow');
      */
      $this->db->delete('customers');
      $this->db->delete('customers_groups');
      $this->db->delete('customers_info');
      $this->db->delete('customers_notes');
      /*
            $this->db->delete('discount_coupons');
            $this->db->delete('discount_coupons_to_categories');
      */
      $this->db->delete('groups_to_categories');

      $this->db->delete('manufacturers');
      $this->db->delete('manufacturers_info');

      $this->db->delete('newsletters');

      $this->db->delete('pages_manager');
      $this->db->delete('pages_manager_description');

      $this->db->delete('orders');
      $this->db->delete('orders_pages_manager');
      $this->db->delete('orders_products');
      $this->db->delete('orders_status_history');
      $this->db->delete('orders_total');
      $this->db->delete('orders_status');
      $this->db->delete('orders_status_history');

      $this->db->delete('orders_products_attributes');
      $this->db->delete('orders_products_download');
      $this->db->delete('products_attributes');
      $this->db->delete('products_attributes_download');
      $this->db->delete('products_options');
      $this->db->delete('products_options_values');
      $this->db->delete('products_options_values_to_products_options');
      /*
            INSERT INTO `clic_orders_status` (`orders_status_id`, `language_id`, `orders_status_name`, `public_flag`, `downloads_flag`, `support_orders_flag`) VALUES
            (1, 1, 'Pending', 1, 0, 0),
      (1, 2, 'En instance', 1, 0, 0),
      (2, 1, 'processing', 1, 0, 0),
      (2, 2, 'Traitement en cours', 1, 0, 0),
      (3, 1, 'Delivered', 1, 0, 0),
      (3, 2, 'Livré', 1, 0, 0),
      (4, 1, 'Cancelled', 1, 0, 0),
      (4, 2, 'Annulé', 1, 0, 0);
      */
      $this->db->delete('products');
      $this->db->delete('products_description');
      $this->db->delete('products_groups');
      $this->db->delete('products_images');
      $this->db->delete('products_notifications');
      $this->db->delete('products_to_categories');
      /*
            $this->db->delete('products_favorites');
            $this->db->delete('products_featured');
      */


      $this->db->delete('reviews');
      $this->db->delete('reviews_description');

      $this->db->delete('specials');

//******************************************
//Languages --−> risques de conflits avec la bd originelles
//******************************************
      $clicshopping_languages = $CLICSHOPPING_ImportDatabase->readLanguage();

      $i = 0;
      $cl = [];

      foreach ($clicshopping_languages as $languages) {
        $cl[$i] = $languages['code'];

        $i++;
      }

      $Qlanguages = $mysqli->query('select *
                                     from ' . $this->PrefixTable . 'languages
                                   ');
      echo '<hr>';
      echo '<div>table_languages</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qlanguages->num_rows . '</div>';

      $i = 0;

      while ($data = $Qlanguages->fetch_assoc()) {
        if ($cl[$i] != $data['code'] && $data['code'] != 'fr') {
          $sql_data_array = ['name' => $data['name'],
            'code' => $data['code'],
            'image' => $data['image'],
            'directory' => $data['directory'],
            'sort_order' => (int)HTML::sanitize($data['sort_order']),
            'status' => 1,
          ];

          $this->db->save('languages', $sql_data_array);
          echo '<p class="text-info"> new language imported : ' . $data['code'] . '</p>';
        } else {
          echo '<p class="text-info"> No item to import, exist inside db : ' . $data['code'] . '</p>';
        }

        $i++;
      }

      echo '<hr>';

//******************************************
// table_addess_book
//******************************************
      $QAddressBook = $mysqli->query('select distinct *
                                      from ' . $this->PrefixTable . 'address_book
                                    ');
      echo '<hr>';
      echo '<div>table_address_book</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QAddressBook->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QAddressBook->fetch_assoc()) {
//address_book
        $sql_data_array = ['address_book_id' => (int)HTML::sanitize($data['address_book_id']),
          'customers_id' => (int)HTML::sanitize($data['customers_id']),
          'entry_gender' => $data['entry_gender'],
          'entry_company' => $data['entry_company'],
          'entry_siret' => $data['entry_siret'],
          'entry_ape' => $data['entry_ape'],
          'entry_tva_intracom' => $data['entry_tva_intracom'],
          'entry_cf' => $data['entry_cf'],
          'entry_piva' => $data['entry_piva'],
          'entry_firstname' => $data['entry_firstname'],
          'entry_lastname' => $data['entry_lastname'],
          'entry_street_address' => $data['entry_street_address'],
          'entry_suburb' => $data['entry_suburb'],
          'entry_postcode' => $data['entry_postcode'],
          'entry_city' => $data['entry_city'],
          'entry_state' => $data['entry_state'],
          'entry_country_id' => (int)HTML::sanitize($data['entry_country_id']),
          'entry_zone_id' => HTML::sanitize($data['entry_zone_id']),
          'entry_telephone' => $data['entry_telephone'],
        ];
        $this->db->save('address_book', $sql_data_array);
      }

//******************************************
// table_banners
//******************************************

      $Qbanners = $mysqli->query('select *
                                  from ' . $this->PrefixTable . 'banners
                                  ');
      echo '<hr>';
      echo '<div>table_banners</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qbanners->num_rows . '</div>';
      echo '<div>The languages has the id 1 (english), adjust after</div>';
      echo '<hr>';

      while ($data = $Qbanners->fetch_assoc()) {
        $sql_data_array = ['banners_id' => (int)$data['banners_id'],
          'banners_title' => $data['banners_title'],
          'banners_url' => $data['banners_url'],
          'banners_image' => $data['banners_image'],
          'banners_group' => $data['banners_group'],
          'banners_target' => $data['banners_target'],
          'banners_html_text' => $data['banners_html_text'],
          'expires_impressions' => (int)$data['expires_impressions'],
          'expires_date' => $data['expires_date'],
          'date_scheduled' => $data['date_scheduled'],
          'date_added' => $data['date_added'],
          'date_status_change' => $data['date_status_change'],
          'status' => (int)HTML::sanitize($data['status']),
          'languages_id' => (int)HTML::sanitize($data['languages_id']),
          'customers_group_id' => (int)HTML::sanitize($data['customers_group_id']),
          'languages_id' => 99,
          'banners_title_admin' => $data['banners_title']
        ];

        $this->db->save('banners', $sql_data_array);
      }

//******************************************
// blog_categories
//******************************************

      $Qcheck = $this->db->query('show tables like ":table_blog_categories"');

      if ($Qcheck->fetch() === true) {

        $Qblog = $mysqli->query('select  distinct *
                                  from ' . $this->PrefixTable . 'blog_categories
                                ');
        echo '<hr>';
        echo '<div>table_blog_categories</div>';
        echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qblog->num_rows . '</div>';
        echo '<hr>';

        while ($data = $Qblog->fetch_assoc()) {
          $sql_data_array = [
            'blog_categories_id' => (int)HTML::sanitize($data['blog_categories_id']),
            'blog_categories_image' => $data['blog_categories_image'],
            'parent_id' => (int)HTML::sanitize($data['parent_id']),
            'sort_order' => (int)HTML::sanitize($data['sort_order']),
            'date_added' => $data['date_added'],
            'last_modified' => $data['last_modified'],
            'customers_group_id' => (int)HTML::sanitize($data['customers_group_id']),
          ];

          $this->db->save('blog_categories', $sql_data_array);
        }
      }
//******************************************
// blog_categories description
//******************************************
      $Qcheck = $this->db->query('show tables like ":table_blog_categories_desciption"');

      if ($Qcheck->fetch() === true) {

        $QblogCategories = $mysqli->query('select  *
                                          from ' . $this->PrefixTable . 'blog_categories_description
                                        ');
        echo '<hr>';
        echo '<div>table_blog_categories_description</div>';
        echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QblogCategories->num_rows . '</div>';
        echo '<hr>';

        // blog_categories_description
        while ($data = $QblogCategories->fetch_assoc()) {
          $sql_data_array = [
            'blog_categories_id' => (int)HTML::sanitize($data['blog_categories_id']),
            'language_id' => (int)HTML::sanitize($data['language_id']),
            'blog_categories_name' => $data['blog_categories_name'],
            'blog_categories_description' => $data['blog_categories_description'],
            'blog_categories_head_title_tag' => $data['blog_categories_head_title_tag'],
            'blog_categories_head_desc_tag' => $data['blog_categories_head_desc_tag'],
            'blog_categories_head_keywords_tag' => $data['blog_categories_head_keywords_tag'],
          ];

          $this->db->save('blog_categories_description', $sql_data_array);
        }
      }


//******************************************
// table_blog_content
//******************************************
      $Qcheck = $this->db->query('show tables like ":table_content"');

      if ($Qcheck->fetch() === true) {

        $QblogContent = $mysqli->query('select distinct *
                                      from ' . $this->PrefixTable . 'blog_content
                                    ');
        echo '<hr>';
        echo '<div>table_blog_content</div>';
        echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QblogContent->num_rows . '</div>';
        echo '<hr>';

        while ($data = $QblogContent->fetch_assoc()) {

          $sql_data_array = [
            'blog_content_id' => (int)HTML::sanitize($data['blog_content_id']),
            'blog_content_date_added' => $data['blog_content_date_added'],
            'blog_content_last_modified' => $data['blog_content_last_modified'],
            'blog_content_date_available' => $data['blog_content_date_available'],
            'blog_content_status' => (int)HTML::sanitize($data['blog_content_status']),
            'blog_content_archive' => (int)HTML::sanitize($data['blog_content_archive']),
            'admin_user_name' => $data['admin_user_name'],
            'blog_content_sort_order' => (int)HTML::sanitize($data['blog_content_sort_order']),
            'blog_content_author' => $data['blog_content_author'],
            'customers_group_id' => (int)HTML::sanitize($data['customers_group_id ']),
          ];

          $this->db->save('blog_content', $sql_data_array);
        }
      }
//******************************************
// table_blog_content description
//******************************************
      $Qcheck = $this->db->query('show tables like ":table_content_description"');

      if ($Qcheck->fetch() === true) {
        $QblogContentDescription = $mysqli->query('select *
                                             from ' . $this->PrefixTable . 'blog_content_description
                                             ');
        echo '<hr>';
        echo '<div>table_blog_content_description</div>';
        echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QblogContentDescription->num_rows . '</div>';
        echo '<hr>';

// blog_content_description
        while ($data = $QblogContentDescription->fetch_assoc()) {

          $sql_data_array = [
            'blog_content_id' => (int)HTML::sanitize($data['blog_content_id']),
            'language_id' => (int)HTML::sanitize($data['language_id']),
            'blog_content_name' => $data['blog_content_name'],
            'blog_content_description' => $data['blog_content_description'],
            'blog_content_url' => $data['blog_content_url'],
            'blog_content_viewed' => (int)HTML::sanitize($data['blog_content_viewed']),
            'blog_content_head_title_tag' => $data['blog_content_head_title_tag'],
            'blog_content_head_desc_tag' => $data['blog_content_head_desc_tag'],
            'blog_content_head_keywords_tag' => $data['blog_content_head_keywords_tag'],
            'blog_content_head_tag_product' => $data['blog_content_head_tag_product'],
            'blog_content_head_tag_blog' => $data['blog_content_head_tag_blog'],
            'blog_content_description_summary' => $data['blog_content_description_summary'],
          ];

          $this->db->save('blog_content_description', $sql_data_array);
        }
      }

//******************************************
// table__blog_content_to_categories
//******************************************
      $Qcheck = $this->db->query('show tables like ":table_content_to_categories"');

      if ($Qcheck->fetch() === true) {

        $QblogContentToCategories = $mysqli->query('select *
                                                    from ' . $this->PrefixTable . 'blog_content_to_categories
                                                  ');
        echo '<hr>';
        echo '<div>blog_content_to_categories</div>';
        echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QblogContentToCategories->num_rows . '</div>';
        echo '<hr>';

        while ($data = $QblogContentToCategories->fetch_assoc()) {
          $sql_data_array = [
            'blog_content_id' => (int)HTML::sanitize($data['blog_content_id']),
            'blog_categories_id' => (int)HTML::sanitize($data['blog_categories_id']),
          ];

          $this->db->save('blog_content_to_categories', $sql_data_array);
        }
      }
//******************************************
// table_categories
//******************************************
      $Qcategories = $mysqli->query('select *
                                     from ' . $this->PrefixTable . 'categories c
                                    ');
      echo '<hr>';
      echo '<div>table_categories</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qcategories->fetch_assoc()) {
//categories
        $sql_data_array = ['categories_id' => HTML::sanitize($data['categories_id']),
          'categories_image' => $data['categories_image'],
          'parent_id' => HTML::sanitize($data['parent_id']),
          'sort_order' => HTML::sanitize($data['sort_order']),
          'date_added' => $data['date_added'],
          'last_modified' => $data['last_modified'],
          'virtual_categories' => 0
        ];

        $this->db->save('categories', $sql_data_array);
      }

//description
      $Qcategories = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'categories_description cd
                                  ');
      echo '<hr>';
      echo '<div>table_categories_description</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcategories->num_rows . '</div>';
      echo '<hr>';
      /*
            while ($data = $Qcategories->fetch_assoc()) {
              echo $data['categories_id'] . ' - ' . $data['categories_name'] . '<br />';

      // categories_description
              foreach ($clicshopping_languages as $languages) {
                $sql_data_array = ['categories_id' => (int)$data['categories_id'],
                                   'language_id' => (int)$languages['languages_id'],
                                   'categories_name' => $data['categories_name'],
                                   'categories_description ' => null,
                                   'categories_head_title_tag' => $data['products_seo_title'],
                                   'categories_head_desc_tag' => $data['products_seo_description'],
                                   'categories_head_keywords_tag' => $data['products_seo_keywords']
                                  ];
                $this->db->save('categories_description', $sql_data_array);
              }
            }
      */
//******************************************
//contact_customers
//******************************************
      /*
            $Qcontact = $mysqli->query('select distinct *
                                        from ' . $this->PrefixTable .'contact_customers
                                        ');

            echo '<hr>';
            echo '<div>table_contact_customers</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcontact->num_rows . '</div>';
            echo '<hr>';

            while ($data = $Qcontact->fetch_assoc()) {
              $sql_data_array = [
                                  'contact_customers_id' => (int)HTML::sanitize($data['contact_customers_id']),
                                  'contact_department' => $data['contact_department'],
                                  'contact_name' => $data['contact_name'],
                                  'contact_email_address' => $data['contact_email_address'],
                                  'contact_email_subject' => $data['contact_email_subject'],
                                  'contact_enquiry' => $data['contact_enquiry'],
                                  'contact_date_added'=> $data['contact_date_added'],
                                  'languages_id' => (int)HTML::sanitize($data['languages_id']),
                                  'contact_customers_archive' => (int)HTML::sanitize($data['contact_customers_archive']),
                                  'contact_customers_status' => HTML::sanitize($data['contact_customers_status']),
                                  'customer_id' => (int)HTML::sanitize($data['customer_id']),
                                  'contact_telephone' => $data['contact_telephone'],
                                ];

                $this->db->save('contact_customers', $sql_data_array);
            }

      //******************************************
      //contact_customers_follow
      //******************************************
            $QcontactFollow = $mysqli->query('select distinct *
                                              from ' . $this->PrefixTable .'contact_customers_follow
                                            ');

            echo '<hr>';
            echo '<div>table_contact_customers</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QcontactFollow->num_rows . '</div>';
            echo '<hr>';

            while ($data = $QcontactFollow->fetch_assoc()) {
              $sql_data_array = [
                                  'id_contact_customers_follow' => (int)HTML::sanitize($data['id_contact_customers_follow']),
                                  'contact_customers_id ' => (int)HTML::sanitize($data['contact_customers_id']),
                                  'administrator_user_name' => $data['administrator_user_name'],
                                  'customers_response ' => $data['customers_response'],
                                  'contact_date_sending' => $data['contact_date_sending'],
                                ];

                $this->db->save('contact_customers_follow', $sql_data_array);
            }
      */
//******************************************
//Customers
//******************************************
      $Qcustomers = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'customers
                                   ');
      echo '<hr>';
      echo '<div>table_customers</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcustomers->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qcustomers->fetch_assoc()) {
        $sql_data_array = [
          'customers_id' => (int)HTML::sanitize($data['customers_id']),
          'customers_company' => $data['customers_company'],
          'customers_siret' => $data['customers_siret'],
          'customers_ape' => $data['customers_ape'],
          'customers_tva_intracom' => $data['customers_tva_intracom'],
          'customers_tva_intracom_code_iso' => $data['customers_tva_intracom_code_iso'],
          'customers_gender' => $data['customers_gender'],
          'customers_firstname' => $data['customers_firstname'],
          'customers_lastname' => $data['customers_lastname'],
          'customers_dob' => $data['customers_dob'],
          'customers_email_address' => $data['customers_email_address'],
          'customers_default_address_id' => (int)$data['customers_default_address_id'],
          'customers_telephone' => $data['customers_telephone'],
          'customers_fax' => $data['customers_fax'],
          'customers_password' => $data['customers_password'],
          'customers_newsletter' => $data['customers_newsletter'],
          'languages_id' => (int)HTML::sanitize($data['languages_id']),
          'customers_group_id' => (int)HTML::sanitize($data['customers_group_id']),
          'member_level' => (int)HTML::sanitize($data['member_level']),
          'customers_options_order_taxe' => (int)HTML::sanitize($data['customers_options_order_taxe']),
          'customers_modify_company' => (int)HTML::sanitize($data['customers_modify_company']),
          'customers_modify_address_default' => (int)HTML::sanitize($data['customers_modify_address_default']),
          'customers_add_address' => (int)HTML::sanitize($data['customers_add_address']),
          'customers_cellular_phone' => $data['customers_cellular_phone'],
          'customers_email_validation' => (int)HTML::sanitize($data['customers_email_validation']),
          'customer_discount' => (float)$data['customer_discount'],
          'client_computer_ip' => $data['client_computer_ip'],
          'provider_name_client' => $data['provider_name_client'],
          'customer_website_company' => $data['customer_website_company'],
        ];

        $this->db->save('customers', $sql_data_array);
      }

//******************************************
//Customers groups
//******************************************
      $QcustomersGroups = $mysqli->query('select distinct *
                                          from ' . $this->PrefixTable . 'customers_groups
                                         ');
      echo '<hr>';
      echo '<div>table_customers_groups</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QcustomersGroups->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qcustomers->fetch_assoc()) {
        $sql_data_array = [
          'customers_group_id' => (int)HTML::sanitize($data['customers_group_id']),
          'customers_group_name' => $data['customers_group_name'],
          'customers_group_discount' => (float)$data['customers_group_discount'],
          'color_bar' => $data['color_bar'],
          'group_order_taxe' => (int)HTML::sanitize($data['group_order_taxe']),
          'group_payment_unallowed' => $data['group_payment_unallowed'],
          'group_shipping_unallowed' => $data['group_shipping_unallowed'],
          'customers_group_name' => $data['group_tax'],
          'customers_group_quantity_default' => (int)HTML::sanitize($data['customers_group_quantity_default']),
        ];

        $this->db->save('customers_groups', $sql_data_array);
      }

//******************************************
//Customers Info
//******************************************
      $QcustomersInfo = $mysqli->query('select *
                                         from ' . $this->PrefixTable . 'customers_info
                                       ');
      echo '<hr>';
      echo '<div>table_customers_info</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QcustomersInfo->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QcustomersInfo->fetch_assoc()) {
        $sql_data_array = [
          'customers_info_id' => (int)$data['customers_info_id'],
          'customers_info_date_of_last_logon' => $data['customers_info_date_of_last_logon'],
          'customers_info_number_of_logons' => (int)$data['customers_info_number_of_logons'],
          'customers_info_date_account_created' => $data['customers_info_date_account_created'],
          'customers_info_date_account_last_modified' => $data['customers_info_date_account_last_modified'],
          'global_product_notifications' => $data['global_product_notifications'],
          'password_reset_key' => $data['password_reset_key']
        ];

        $this->db->save('customers_info', $sql_data_array);
      }

//******************************************
//Customers notes
//******************************************

      $QcustomersNotes = $mysqli->query('select distinct *
                                         from ' . $this->PrefixTable . 'customers_notes
                                       ');
      echo '<hr>';
      echo '<div>table_customers_notes</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QcustomersNotes->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QcustomersNotes->fetch_assoc()) {
        $sql_data_array = [
          'customers_notes_id' => (int)HTML::sanitize($data['customers_notes_id']),
          'customers_id' => (int)HTML::sanitize($data['customers_id']),
          'customers_notes' => (int)HTML::sanitize($data['customers_notes']),
          'customers_notes_date' => $data['customers_notes_date'],
          'user_administrator' => $data['user_administrator'],
        ];

        $this->db->save('customers_notes', $sql_data_array);
      }


//******************************************
//discount coupons
//******************************************
      /*
            $QdiscountCoupons = $mysqli->query('select distinct *
                                               from ' . $this->PrefixTable .'discount_coupons
                                             ');
            echo '<hr>';
            echo '<div>table_discount_coupons</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QdiscountCoupons->num_rows . '</div>';
            echo '<hr>';

            while($data = $QdiscountCoupons->fetch_assoc()) {
              $sql_data_array = [
                                  'coupons_id' => $data['coupons_id'],
                                  'coupons_description' =>$data['coupons_description'],
                                  'coupons_discount_amount' => (float)($data['coupons_discount_amount']),
                                  'coupons_discount_type' => $data['coupons_discount_type'],
                                  'coupons_date_start' => $data['coupons_date_start'],
                                  'coupons_date_end' => $data['coupons_date_end'],
                                  'coupons_max_use' => (int)HTML::sanitize($data['coupons_max_use']),
                                  'coupons_min_order ' => (float)$data['coupons_min_order '],
                                  'coupons_number_available ' => $data['coupons_number_available '],
                                  'coupons_create_account_b2c' => $data['coupons_create_account_b2c'],
                                  'coupons_create_account_b2b' => $data['coupons_create_account_b2b'],
                                  'coupons_twitter' => $data['coupons_twitter'],
                                ];

                $this->db->save('discount_coupons', $sql_data_array);
            }


      //******************************************
      //discount_coupons_to_categories
      //******************************************
            $QdiscountCouponsToCategories = $mysqli->query('select distinct *
                                                             from ' . $this->PrefixTable .'discount_coupons_to_categories
                                                           ');
            echo '<hr>';
            echo '<div>discount_coupons_to_categories</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QdiscountCouponsToCategories->num_rows . '</div>';
            echo '<hr>';

            while ($data = $QdiscountCouponsToCategories->fetch_assoc()) {
              $sql_data_array = [
                                  'coupons_id' => $data['coupons_id'],
                                  'categories_id' => (int)HTML::sanitize($data['categories_id']),
                                ];

                $this->db->save('discount_coupons_to_categories', $sql_data_array);
            }

      //******************************************
      //discount_coupons_to_customers
      //******************************************
            $QdiscountCouponsToCustomers = $mysqli->query('select distinct *
                                                           from ' . $this->PrefixTable .'discount_coupons_to_customers
                                                         ');
            echo '<hr>';
            echo '<div>discount_coupons_to_customers</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QdiscountCouponsToCustomers->num_rows . '</div>';
            echo '<hr>';

            while($data = $QdiscountCouponsToCustomers->fetch_assoc()) {
              $sql_data_array = [
                                  'coupons_id' => $data['coupons_id'],
                                  'customers_id' => (int)HTML::sanitize($data['customers_id']),
                                ];

                $this->db->save('discount_coupons_to_customers', $sql_data_array);
            }

      //******************************************
      //discount_coupons_to_manufacturers
      //******************************************
            $QdiscountCouponsToManufacturers = $mysqli->query('select distinct *
                                                               from ' . $this->PrefixTable .'discount_coupons_to_manufacturers
                                                             ');
            echo '<hr>';
            echo '<div>discount_coupons_to_customers</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QdiscountCouponsToManufacturers->num_rows . '</div>';
            echo '<hr>';

            while ($data = $QdiscountCouponsToManufacturers->fetch_assoc()) {
              $sql_data_array = [
                'coupons_id' => $data['coupons_id'],
                'manufacturers_id' => (int)HTML::sanitize($data['manufacturers_id']),
              ];

                $this->db->save('discount_coupons_to_manufacturers', $sql_data_array);
            }


      //******************************************
      //discount_coupons_to_orders_id
      //******************************************
            $QdiscountCouponsToOrders = $mysqli->query('select distinct *
                                                       from ' . $this->PrefixTable .'discount_coupons_to_orders
                                                     ');
            echo '<hr>';
            echo '<div>discount_coupons_to_orders</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QdiscountCouponsToOrders->num_rows . '</div>';
            echo '<hr>';

            while ($data = $QdiscountCouponsToOrders->fetch_assoc()) {
              $sql_data_array = [
                                  'coupons_id' => $data['coupons_id'],
                                  'manufacturers_id' => (int)HTML::sanitize($data['orders_id']),
                                ];

                $this->db->save('discount_coupons_to_manufacturers', $sql_data_array);
            }


      //******************************************
      //discount_coupons_to_prodcuts
      //******************************************
            $QdiscountCouponsToProducts = $mysqli->query('select *
                                                           from ' . $this->PrefixTable .'discount_coupons_to_products
                                                         ');
            echo '<hr>';
            echo '<div>discount_coupons_to_orders</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QdiscountCouponsToProducts->num_rows . '</div>';
            echo '<hr>';

            while($data = $QdiscountCouponsToProducts->fetch_assoc()) {
              $sql_data_array = [
                                  'coupons_id' => $data['coupons_id'],
                                  'manufacturers_id' => (int)HTML::sanitize($data['products_id']),
                                ];

                $this->db->save('discount_coupons_to_manufacturers', $sql_data_array);
            }


      //******************************************
      // 	feedback_order_reviews_id
      //******************************************
      /*
            $Qfeedback_order = $mysqli->query('select *
                                                from ' . $this->PrefixTable .'feedback_order_reviews c,
                                                     ' . $this->PrefixTable .'feedback_order_reviews_description cd
                                                where c.feedback_order_reviews_id = cd.feedback_order_reviews_id
                                              ');
            echo '<hr>';
            echo '<div>table_blog_categories</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qfeedback_order->num_rows . '</div>';
            echo '<hr>';

            while ($data = $Qfeedback_order->fetch_assoc()) {
              echo $data[' 	feedback_order_reviews_id'] . ' - ' . $data['blog_content_name'] . '<br />';
      //feedback_order_reviews
              $sql_data_array = [
                                  'feedback_order_reviews_id' => (int)HTML::sanitize($data['blog_content_id']),
                                  'customers_id' => (int)HTML::sanitize($data['customers_id']),
                                  'customers_name' => $data['customers_name'],
                                  'feedback_order_reviews_rating' => (int)HTML::sanitize($data['feedback_order_reviews_rating']),
                                  'date_added' => $data['date_added'],
                                  'last_modified' => $data['last_modified'],
                                  'feedback_order_reviews_read' => (int)HTML::sanitize($data['feedback_order_reviews_read']),
                                  'status' => (int)HTML::sanitize($data['status']),
                                  'feedback_accept_to_publish' => (int)HTML::sanitize($data['customers_group_id']),
                                  'orders_id' => (int)HTML::sanitize($data['orders_id']),
                                ];

      //        $this->db->save('feedback_order_reviews', $sql_data_array);
      // feedback_order_reviews_description
              $i = 0;
              foreach ($clicshopping_languages as $languages) {
                $cl[$i] = $languages['languages_id'];

                $sql_data_array = [
                                    'feedback_order_reviews_id' => (int)HTML::sanitize($data['feedback_order_reviews_id']),
                                    'languages_id' => (int)HTML::sanitize($cl[$i]),
                                    'feedback_order_reviews_text' => $data['feedback_order_reviews_text'],
                                  ];
              if ($_GET['action'] == 'final_process_import_database') {
                $this->db->save('feedback_order_reviews_description', $sql_data_array);
              }

                $i = $i+1;
              }
            }
      */

//******************************************
//groups_to_categories
//******************************************
      $QgroupsToCategories = $mysqli->query('select distinct *
                                             from ' . $this->PrefixTable . 'groups_to_categories
                                           ');
      echo '<hr>';
      echo '<div>groups_to_categories</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QgroupsToCategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QgroupsToCategories->fetch_assoc()) {
        $sql_data_array = [
          'customers_group_id' => (int)HTML::sanitize($data['customers_group_id']),
          'categories_id' => (int)HTML::sanitize($data['categories_id']),
          'discount ' => (float)$data['discount'],
        ];

        //      if ($_GET['action'] == 'final_process_import_database') {
        $this->db->save('groups_to_categories', $sql_data_array);
        //      }
      }

//******************************************
//manufacturers
//******************************************
      /*
            $Qmanufacturers = $mysqli->query('select *
                                              from ' . $this->PrefixTable .'manufacturers
                                            ');

            echo '<hr>';
            echo '<div>table_manufacturers et manufacturers_info</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qmanufacturers->num_rows . '</div>';
            echo '<hr>';

            while($data = $Qmanufacturers->fetch_assoc()) {
              echo $data['manufacturers_id'] . ' - ' . $data['manufacturers_name'] . '<br />';

              $sql_data_array = ['manufacturers_id' => (int)$data['manufacturers_id'],
                                 'manufacturers_name' => $data['manufacturers_name'],
                                 'manufacturers_image' => $data['manufacturers_image'],
                                 'date_added' => $data['date_added'],
                                 'last_modified' => 'now()',
                                 'suppliers_id' => 0
                                ];

              $this->db->save('manufacturers', $sql_data_array);
            }
      */
//******************************************
//manufacturers_info
//******************************************

      $QmanufacturersName = $mysqli->query('select *
                                            from ' . $this->PrefixTable . 'manufacturers_info
                                          ');

      echo '<hr>';
      echo '<div>table_manufacturers et manufacturers_info</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QmanufacturersName->num_rows . '</div>';
      echo '<hr>';
      /*
            while($data = $QmanufacturersName->fetch_assoc()) {
              foreach ($clicshopping_languages as $languages) {
                $sql_data_array = ['manufacturers_id' => (int)$data['manufacturers_id'],
                                   'languages_id' => $languages['languages_id'],
                                   'manufacturers_url' => $data['manufacturers_url'],
                                   'url_clicked' => (int)$data['url_clicked'],
                                   'date_last_click' => $data['date_last_click'],
                                   'manufacturer_description ' => null,
                                   'manufacturer_seo_title' => $data['manufacturers_seo_title'],
                                   'manufacturer_seo_description' => $data['manufacturers_seo_description'],
                                   'manufacturer_seo_keyword' => $data['manufacturers_seo_keywords'],
                                  ];

                $this->db->save('manufacturers_info', $sql_data_array);
              }
            }
      */
//******************************************
//newsletter_no_account
//******************************************
      $QnewsletterNoAccount = $mysqli->query('select distinct *
                                              from ' . $this->PrefixTable . 'newsletter_no_account
                                            ');
      echo '<hr>';
      echo '<div>newsletter_no_account</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QnewsletterNoAccount->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QnewsletterNoAccount->fetch_assoc()) {
        $sql_data_array = [
          'customers_firstname' => (int)HTML::sanitize($data['customers_group_id']),
          'customers_lastname' => $data['customers_lastname'],
          'customers_email_address' => $data['customers_email_address'],
          'customers_date_added' => $data['customers_date_added '],
          'languages_id' => (int)HTML::sanitize($data['languages_id']),
        ];

        $this->db->save('newsletters_no_account', $sql_data_array);
      }

//******************************************
//newsletter
//******************************************
      $Qnewsletters = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'newsletters
                                     ');

      echo '<hr>';
      echo '<div>table_newletters</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qnewsletters->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qnewsletters->fetch_assoc()) {
        $sql_data_array = ['newsletters_id' => (int)HTML::sanitize($data['newsletters_id']),
          'title' => $data['title'],
          'content' => $data['content'],
          'module' => $data['module'],
          'date_added' => $data['date_added'],
          'date_sent' => $data['date_sent'],
          'status' => (int)HTML::sanitize($data['status']),
          'locked' => (int)HTML::sanitize($data['locked']),
        ];

        $this->db->save('newsletters', $sql_data_array);
      }

//******************************************
// pages_manager
//******************************************
      $QordersPageManager = $mysqli->query('select distinct *
                                            from ' . $this->PrefixTable . 'pages_manager
                                           ');

      echo '<hr>';
      echo '<div>table_pages_manager</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersPageManager->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersPageManager->fetch_assoc()) {
        $sql_data_array = ['pages_id' => (int)HTML::sanitize($data['pages_id']),
          'links_target' => $data['links_target'],
          'sort_order' => (int)HTML::sanitize($data['status']),
          'status' => (int)HTML::sanitize($data['status']),
          'page_type' => (int)HTML::sanitize($data['page_type']),
          'page_box' => (int)HTML::sanitize($data['page_box']),
          'page_time' => $data['page_time'],
          'page_date_start' => $data['page_date_start'],
          'page_date_closed' => $data['page_date_closed'],
          'date_added' => 'now()',
          'last_modified' => 'now()',
          'customers_group_id' => (int)HTML::sanitize($data['customers_group_id']),
          'page_general_condition' => (int)HTML::sanitize($data['page_general_condition']),
        ];

        $this->db->save('pages_manager', $sql_data_array);
      }

//******************************************
// pages_manager_description
//******************************************
      $QordersPageManager = $mysqli->query('select distinct *
                                            from ' . $this->PrefixTable . 'pages_manager_description
                                           ');

      echo '<hr>';
      echo '<div>table_pages_manager_description</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersPageManager->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersPageManager->fetch_assoc()) {

        $sql_data_array = ['pages_id' => (int)HTML::sanitize($data['pages_id']),
          'pages_title' => $data['pages_title'],
          'pages_html_text' => $data['pages_html_text'],
          'externallink' => (int)HTML::sanitize($data['externallink']),
          'language_id' => (int)HTML::sanitize($data['language_id']),
          'page_manager_head_title_tag' => $data['page_manager_head_title_tag'],
          'page_manager_head_desc_tag' => $data['page_manager_head_desc_tag'],
          'page_manager_head_keywords_tag' => $data['page_manager_head_keywords_tag'],
        ];

        $this->db->save('pages_manager_description', $sql_data_array);
      }

//******************************************
//orders_pages_manager
//******************************************
      $QordersPageManager = $mysqli->query('select distinct *
                                            from ' . $this->PrefixTable . 'orders_pages_manager
                                           ');

      echo '<hr>';
      echo '<div>table_orders_pages_manager</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersPageManager->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersPageManager->fetch_assoc()) {
        $sql_data_array = ['orders_page_manager_id' => (int)HTML::sanitize($data['orders_page_manager_id']),
          'orders_id' => (int)HTML::sanitize($data['orders_id']),
          'customers_id' => (int)HTML::sanitize($data['customers_id']),
          'page_manager_general_condition' => $data['page_manager_general_condition'],
        ];

        $this->db->save('orders_pages_manager', $sql_data_array);
      }

//******************************************
//orders_pages_manager
//******************************************
      $QordersProducts = $mysqli->query('select distinct *
                                         from ' . $this->PrefixTable . 'orders_products
                                        ');

      echo '<hr>';
      echo '<div>table_orders_pages_manager</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersProducts->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersProducts->fetch_assoc()) {
        $sql_data_array = ['orders_products_id' => (int)HTML::sanitize($data['orders_products_id']),
          'orders_id' => (int)HTML::sanitize($data['orders_id']),
          'products_id' => (int)HTML::sanitize($data['products_id']),
          'products_model' => $data['products_model'],
          'products_name' => $data['products_name'],
          'products_price' => (float)$data['products_price'],
          'final_price' => (float)$data['final_price'],
          'products_tax' => (float)$data['products_tax'],
          'products_quantity' => (int)HTML::sanitize($data['products_quantity']),
        ];

        $this->db->save('orders_products', $sql_data_array);
      }

//******************************************
//orders
//******************************************
      $Qorders = $mysqli->query('select *
                                   from ' . $this->PrefixTable . 'orders
                                 ');

      echo '<hr>';
      echo '<div>table_orders</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qorders->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qorders->fetch_assoc()) {
        $sql_data_array = ['orders_id' => (int)$data['orders_id'],
          'customers_id' => (int)$data['customers_id'],
          'customers_name' => $data['customers_name'],
          'customers_company' => $data['customers_company'],
          'customers_siret' => $data['customers_siret'],
          'customers_ape' => $data['customers_ape'],
          'customers_tva_intracom' => $data['customers_tva_intracom'],
          'customers_street_address' => $data['customers_street_address'],
          'customers_suburb' => $data['customers_suburb'],
          'customers_city' => $data['customers_city'],
          'customers_postcode' => $data['customers_postcode'],
          'customers_state' => $data['customers_state'],
          'customers_country' => $data['customers_country'],
          'customers_telephone' => $data['customers_telephone'],
          'customers_email_address' => $data['customers_email_address'],
          'customers_address_format_id' => (int)$data['customers_address_format_id'],
          'delivery_name' => $data['delivery_name'],
          'delivery_company' => $data['delivery_company'],
          'delivery_street_address' => $data['delivery_street_address'],
          'delivery_suburb' => $data['delivery_suburb'],
          'delivery_city' => $data['delivery_city'],
          'delivery_postcode' => $data['delivery_postcode'],
          'delivery_state' => $data['delivery_state'],
          'delivery_country' => $data['delivery_country'],
          'delivery_address_format_id' => (int)$data['delivery_address_format_id'],
          'billing_name' => $data['billing_name'],
          'billing_company' => $data['billing_company'],
          'billing_cf' => null,
          'billing_piva' => null,
          'billing_street_address' => $data['billing_street_address'],
          'billing_suburb' => $data['billing_suburb'],
          'billing_city' => $data['billing_city'],
          'billing_postcode' => $data['billing_postcode'],
          'billing_state' => $data['billing_state'],
          'billing_country' => $data['billing_country'],
          'billing_address_format_id' => (int)$data['billing_address_format_id'],
          'payment_method' => $data['payment_method'],
          'cc_type' => $data['cc_type'],
          'cc_owner' => $data['cc_owner'],
          'cc_number' => $data['cc_number'],
          'cc_expires' => $data['cc_expires'],
          'last_modified' => $data['last_modified'],
          'date_purchased' => $data['date_purchased'],
          'orders_status' => (int)$data['orders_status'],
          'orders_status_invoice' => (int)$data['orders_status_invoice'],
          'orders_date_finished' => $data['orders_date_finished'],
          'currency' => $data['currency'],
          'currency_value' => (float)$data['currency_value'],
          'customers_group_id' => (int)$data['customers_group_id'],
          'client_computer_ip' => $data['client_computer_ip'],
          'provider_name_client' => $data['provider_name_client'],
          'customers_cellular_phone' => $data['customers_cellular_phone'],
          'orders_archive' => (int)$data['orders_archive'],
          'erp_invoice' => (int)$data['erp_invoice'],
        ];

        $this->db->save('orders', $sql_data_array);
      }

//******************************************
//orders_products
//******************************************
      /*
            $QordersProducts = $mysqli->query('select *
                                               from ' . $this->PrefixTable .'orders_products
                                             ');

            echo '<hr>';
            echo '<div>table_orders_products</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersProducts->num_rows . '</div>';
            echo '<hr>';

            while($data = $QordersProducts->fetch_assoc()) {
              $sql_data_array = ['orders_products_id' => (int)$data['orders_products_id'],
                                 'orders_id' => (int)$data['orders_id'],
                                 'products_id' => (int)$data['products_id'],
                                 'products_model' => $data['products_model'],
                                 'products_name' => $data['products_name'],
                                 'products_price' => (float)$data['products_price'],
                                 'final_price' => (float)$data['final_price'],
                                 'products_tax' => (float)$data['products_tax'],
                                 'products_quantity' => (int)$data['products_quantity']
                                ];

              $this->db->save('orders_products', $sql_data_array);
            }
      */

//******************************************
// orders_status
//******************************************
      $QordersStatus = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'orders_status
                                      ');
      echo '<hr>';
      echo '<div>table_orders_status <br /> Status update > 4</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersStatus->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersStatus->fetch_assoc()) {
        if ($data['orders_status_id'] > 4) {
          $sql_data_array = ['orders_status_id' => (int)$data['orders_status_id'],
            'language_id' => (int)$data['language_id'],
            'orders_status_name' => $data['orders_status_name'],
            'public_flag' => (int)$data['public_flag'],
            'downloads_flag' => (int)$data['downloads_flag'],
            'support_orders_flag' => $data['support_orders_flag'],
          ];

          $this->db->save('orders_status', $sql_data_array);
        }
      }

//******************************************
//orders_status_history
//******************************************
      $QordersHistory = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'orders_status_history
                                     ');
      echo '<hr>';
      echo '<div>table_orders_status_history</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersHistory->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersHistory->fetch_assoc()) {
        $sql_data_array = ['orders_status_history_id' => (int)$data['orders_status_history_id'],
          'orders_id' => (int)$data['orders_id'],
          'orders_status_id' => (int)$data['order_status_id'],
          'orders_status_invoice_id' => 1,
          'date_added' => $data['date_added'],
          'customer_notified' => (int)$data['customer_notified'],
          'comments' => $data['comments'],
          'date_added' => $data['date_added'],
        ];
        $this->db->save('orders_status_history', $sql_data_array);
      }

//******************************************
//orders_total
//******************************************
      $QordersTotal = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'orders_total
                                     ');

      echo '<hr>';
      echo '<div>table_orders_total</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersTotal->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersTotal->fetch_assoc()) {
        $sql_data_array = ['orders_total_id' => (int)$data['orders_total_id'],
          'orders_id' => (int)$data['orders_id'],
          'title' => $data['title'],
          'text' => $data['text'],
          'value' => (float)$data['value'],
          'class' => $data['class'],
          'sort_order' => (int)$data['sort_order'],
        ];

        $this->db->save('orders_total', $sql_data_array);
      }


//******************************************
//  orders_products_attributes
//******************************************
      /*
              $QordersProductsAttributes = $mysqli->query('select *
                                                         from ' . $this->PrefixTable .'orders_products_attributes
                                                       ');
            echo '<hr>';
            echo '<div>table_orders_products_attributes </div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersProductsAttributes->num_rows . '</div>';
            echo '<hr>';

            while($data = $QordersProductsAttributes->fetch_assoc()) {
              $sql_data_array = ['orders_products_attributes_id' => (int)$data['orders_products_attributes_id'],
                                 'orders_id' => (int)$data['orders_id'],
                                 'orders_products_id' => (int)$data['orders_products_id'],
                                 'products_options' => $data['products_options'],
                                 'products_options_values' => (float)$data['products_options_values'],
                                 'options_values_price' => (float)$data['options_values_price'],
                                 'price_prefix' => $data['price_prefix'],
                                 'products_attributes_reference ' => '',
                                ];

              $this->db->save('orders_products_attributes', $sql_data_array);
            }
      */
//******************************************
//  orders_products_download
//******************************************
      $QordersProductsDownload = $mysqli->query('select *
                                                           from ' . $this->PrefixTable . 'orders_products_download
                                                         ');
      echo '<hr>';
      echo '<div>table_orders_products_download </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersProductsDownload->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersProductsDownload->fetch_assoc()) {
        $sql_data_array = ['orders_products_download_id' => (int)$data['orders_products_download_id'],
          'orders_id' => (int)$data['orders_id'],
          'orders_products_id' => (int)$data['orders_products_id'],
          'orders_products_filename' => $data['orders_products_filename'],
          'download_maxdays ' => (int)$data['download_maxdays '],
          'download_count' => (int)$data['download_count']
        ];

        $this->db->save('orders_products_download', $sql_data_array);
      }


//******************************************
//  products_attributes
//******************************************
      $QordersProductsAttributes = $mysqli->query('select *
                                                   from ' . $this->PrefixTable . 'products_attributes
                                                 ');
      echo '<hr>';
      echo '<div>table_products_attributes </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersProductsAttributes->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersProductsAttributes->fetch_assoc()) {
        $sql_data_array = ['products_attributes_id' => (int)$data['products_attributes_id'],
          'products_id' => (int)$data['products_id'],
          'options_id' => (int)$data['options_id'],
          'options_values_id' => (int)$data['options_values_id'],
          'options_values_price' => (float)$data['options_values_price'],
          'price_prefix' => $data['price_prefix'],
          'products_options_sort_order' => 0,
          'products_attributes_reference' => null,
          'products_attributes_image' => null,
          'status' => null
        ];

        $this->db->save('products_attributes', $sql_data_array);
      }

//******************************************
//  products_attributes_download
//******************************************
      $QProductsAttributesDownload = $mysqli->query('select *
                                                     from ' . $this->PrefixTable . 'products_attributes_download
                                                   ');
      echo '<hr>';
      echo '<div>table_products_attributes_download </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QProductsAttributesDownload->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QProductsAttributesDownload->fetch_assoc()) {
        $sql_data_array = ['products_attributes_id' => (int)$data['products_attributes_id'],
          'products_attributes_filename' => $data['products_attributes_filename'],
          'products_attributes_maxdays' => (int)$data['products_attributes_maxdays'],
          'products_attributes_maxcount' => (int)$data['products_attributes_maxcount']
        ];

        $this->db->save('products_attributes_download', $sql_data_array);
      }

//******************************************
// products_images
//******************************************
      $Qimages = $mysqli->query('select *
                                 from ' . $this->PrefixTable . 'products_images
                               ');
      echo '<hr>';
      echo '<div>table_products_images </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qimages->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qimages->fetch_assoc()) {
        $sql_data_array = ['products_id' => (int)HTML::sanitize($data['products_id']),
          'image' => $data['image'],
          'htmlcontent' => $data['htmlcontent'],
          'sort_order' => (int)HTML::sanitize($data['sort_order']),
        ];

        $this->db->save('products_images', $sql_data_array);
      }

//******************************************
// products_notifications
//******************************************
      $Qnotifications = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'products_notifications
                                     ');
      echo '<hr>';
      echo '<div>table_products_notifications</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qnotifications->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qnotifications->fetch_assoc()) {
        $sql_data_array = ['products_id' => (int)HTML::sanitize($data['products_id']),
          'customers_id' => (int)HTML::sanitize($data['customers_id']),
          'date_added' => $data['date_added'],
        ];

        $this->db->save('products_notifications', $sql_data_array);
      }


//******************************************
// products_option
//******************************************
      $QproductsOption = $mysqli->query('select *
                                         from ' . $this->PrefixTable . 'products_options
                                       ');
      echo '<hr>';
      echo '<div>table_products_options</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QproductsOption->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QproductsOption->fetch_assoc()) {
        $sql_data_array = ['products_options_id' => (int)$data['products_options_id'],
          'language_id' => (int)$data['language_id'],
          'products_options_name' => $data['products_options_name'],
          'products_options_sort_order' => 0,
        ];

        $this->db->save('products_options', $sql_data_array);
      }

//******************************************
// products_options_values
//******************************************
      $QproductsOption = $mysqli->query('select *
                                         from ' . $this->PrefixTable . 'products_options_values
                                       ');
      echo '<hr>';
      echo '<div>products_options_values</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QproductsOption->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QproductsOption->fetch_assoc()) {
        $sql_data_array = ['products_options_values_id' => (int)$data['products_options_values_id'],
          'language_id' => (int)$data['language_id'],
          'products_options_values_name' => $data['products_options_values_name']
        ];

        $this->db->save('products_options_values', $sql_data_array);
      }

//******************************************
// products_options_values_to_products_options
//******************************************
      $QproductsOption = $mysqli->query('select *
                                         from ' . $this->PrefixTable . 'products_options_values_to_products_options
                                       ');
      echo '<hr>';
      echo '<div>products_options_values</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QproductsOption->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QproductsOption->fetch_assoc()) {
        $sql_data_array = ['products_options_values_to_products_options_id' => (int)$data['products_options_values_to_products_options_id'],
          'products_options_id' => (int)$data[' 	products_options_id'],
          'products_options_values_id' => (int)$data['products_options_values_id']
        ];

        $this->db->save('products_options_values_to_products_options', $sql_data_array);
      }


//******************************************
// products_to categories
//******************************************
      $QproductsCategories = $mysqli->query('select *
                                             from ' . $this->PrefixTable . 'products_to_categories
                                            ');
      echo '<hr>';
      echo '<div>table_products_to_categories</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QproductsCategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QproductsCategories->fetch_assoc()) {
        $sql_data_array = ['products_id' => (int)HTML::sanitize($data['products_id']),
          'categories_id' => (int)HTML::sanitize($data['categories_id'])
        ];

        $this->db->save('products_to_categories', $sql_data_array);
      }

//******************************************
// reviews
//******************************************
      $Qreviews = $mysqli->query('select *
                                  from ' . $this->PrefixTable . 'reviews r,
                                       ' . $this->PrefixTable . 'reviews_description rd
                                  where r.reviews_id = rd.reviews_id
                                 ');
      echo '<hr>';
      echo '<div>table_reviews and reviews description </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qreviews->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qreviews->fetch_assoc()) {

        $sql_data_array = ['reviews_id' => (int)HTML::sanitize($data['reviews_id']),
          'products_id' => (int)HTML::sanitize($data['products_id']),
          'customers_id' => (int)HTML::sanitize($data['customers_id']),
          'customers_name' => $data['customers_name'],
          'reviews_rating' => (int)HTML::sanitize($data['reviews_rating']),
          'date_added' => $data['date_added'],
          'last_modified' => $data['last_modified'],
          'status' => (int)HTML::sanitize($data['reviews_status']),
          'reviews_read' => (int)HTML::sanitize($data['reviews_read']),
          'customers_group_id' => 0
        ];

        $this->db->save('reviews', $sql_data_array);

        $i = 0;
        /*
                foreach ($clicshopping_languages as $languages) {
                  $cl[$i] = $languages['languages_id'];

                  $sql_data_array = ['reviews_id' => (int)HTML::sanitize($data['reviews_id']),
                                     'languages_id' => (int)HTML::sanitize($cl[$i]),
                                     'reviews_text' => $data['reviews_text'],
                                     ];
                  $this->db->save('reviews_description', $sql_data_array);
                  $i++;
                }
        */
      }

//***********************************
//Specials
//***********************************

      $Qspecials = $mysqli->query('select *
                                  from ' . $this->PrefixTable . 'specials
                                 ');

      echo '<hr>';
      echo '<div>table_specials</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qspecials->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qspecials->fetch_assoc()) {

        $sql_data_array = ['specials_id' => (int)HTML::sanitize($data['specials_id']),
          'products_id' => (int)HTML::sanitize($data['products_id']),
          'specials_new_products_price' => (float)$data['specials_new_products_price'],
          'specials_date_added' => $data['specials_date_added'],
          'specials_last_modified' => $data['specials_last_modified'],
          'expires_date' => $data['expires_date'],
          'date_status_change' => $data['date_status_change'],
          'status' => (int)HTML::sanitize($data['status']),
          'customers_group_id ' => 0,
          'flash_discount' => 0
        ];

        $this->db->save('specials', $sql_data_array);
      }


//**********************************
//products table
//**********************************
      /*
            $QproductDescriptions = $mysqli->query('select *
                                                    from  ' . $this->PrefixTable .'products_description
                                                  ');
            echo '<hr>';
            echo '<div>table_products_description </div>';
            echo '<div>' . CLICSHOPPING::getDef('number_of_products') . ' : ' . $QproductDescriptions->num_rows . '</div>';
            echo '<hr>';

            while($data = $QproductDescriptions->fetch_assoc()) {
              foreach ($clicshopping_languages as $languages) {
                echo (int)HTML::sanitize($data['products_id']) . ' - ' . $data['products_name'] . '<br />';

                $sql_data_array_description = ['products_id' => (int)$data['products_id'],
                                               'language_id' => (int)$languages['languages_id'],
                                               'products_name' => $data['products_name'],
                                               'products_description' => $data['products_description'],
                                               'products_url' => $data['products_url'],
                                               'products_viewed ' => (int)$data['products_viewed'],
                                               'products_head_title_tag' => $data['products_seo_title'],
                                               'products_head_desc_tag' => $data['products_seo_description'],
                                               'products_head_keywords_tag' => $data['products_seo_keywords'],
                                               'products_head_tag' => null,
                                               'products_shipping_delay' => null,
                                               'products_description_summary' => null,
                                               ];

                $this->db->save('products_description', $sql_data_array_description);
              }
            }
      */

//products
      $Qproducts = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'products
                                 ');
      echo '<hr>';
      echo '<div>table_products</div>';
      echo '<div>' . CLICSHOPPING::getDef('number_of_products') . ' : ' . $Qproducts->num_rows . '</div>';
      echo '<hr>';


      while ($data = $Qproducts->fetch_assoc()) {
        $sql_data_array_products = ['products_id' => (int)HTML::sanitize($data['products_id']),
          'products_quantity' => (int)HTML::sanitize($data['products_quantity']),
          'products_ean' => HTML::sanitize($data['products_gtin']),
          'products_model' => $data['products_model'],
          'products_sku' => HTML::sanitize($data['products_gtin']),
          'products_price' => (float)$data['products_price'],
          'products_date_available' => $data['products_date_available'],
          'products_weight' => (float)$data['products_weight'],
          'products_price_kilo' => $data['products_price_kilo'],
          'products_status' => (int)HTML::sanitize($data['products_status']),
          'products_percentage' => (int)HTML::sanitize($data['products_percentage']),
          'products_view' => (int)HTML::sanitize($data['products_view']),
          'orders_view' => (int)HTML::sanitize($data['orders_view']),
          'products_cost' => 0,
          'products_tax_class_id' => (int)HTML::sanitize($data['products_tax_class_id']),
          'manufacturers_id' => (int)HTML::sanitize($data['manufacturers_id']),
          'suppliers_id' => (int)HTML::sanitize($data['suppliers_id']),
          'products_min_qty_order' => (int)HTML::sanitize($data['products_min_qty_order']),
          'products_price_comparison' => (int)HTML::sanitize($data['products_price_comparison']),
          'products_dimension_width' => (float)HTML::sanitize($data['products_dimension_width']),
          'products_dimension_height' => (float)HTML::sanitize($data['products_dimension_height']),
          'products_dimension_depth' => (float)HTML::sanitize($data['products_dimension_depth']),
          'products_dimension_type' => HTML::sanitize($data['products_dimension_type']),
          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
          'products_volume' => HTML::sanitize($data['products_volume']),
          'products_quantity_unit_id' => (int)HTML::sanitize($data['products_quantity_unit_id']),
          'products_only_online' => (int)HTML::sanitize($data['products_only_online']),
          'products_weight_class_id' => 2,
          'products_cost' => (float)HTML::sanitize($data['products_cost']),
          'products_handling' => (float)HTML::sanitize($data['products_handling']),
          'products_warehouse_time_replenishment' => HTML::sanitize($data['products_warehouse_time_replenishment']),
          'products_warehouse' => HTML::sanitize($data['products_warehouse']),
          'products_warehouse_row' => HTML::sanitize($data['products_warehouse_row']),
          'products_warehouse_level_location' => HTML::sanitize($data['products_warehouse_level_location']),
          'products_packaging' => HTML::sanitize($data['products_packaging']),
          'products_sort_order' => (int)HTML::sanitize($data['products_sort_order']),
          'products_quantity_alert' => (int)HTML::sanitize($data['products_quantity_alert']),
          'products_only_shop' => (int)HTML::sanitize($data['products_only_shop']),
          'products_download_public' => (int)HTML::sanitize($data['products_download_public']),
          'products_type' => HTML::sanitize($data['products_type']),
          'products_barcode' => HTML::sanitize($data['products_barcode']),
          'products_sort_order' => (int)HTML::sanitize($data['products_ordered']),
          'products_date_added' => 'now()',
          'products_last_modified' => 'now()',
          'products_date_available' => 'now()',
          'products_image' => $data['products_image'],
          'products_weight_class_id' => 2,
        ];
        $this->db->save('products', $sql_data_array_products);
      }

//***************************************
// B2B
//***************************************
      $QcustomersGroup = $this->db->prepare('select distinct customers_group_id,
                                                                 customers_group_name,
                                                                 customers_group_discount
                                                  from :table_customers_groups
                                                  where customers_group_id != :customers_group_id
                                                  order by customers_group_id
                                                ');

      $QcustomersGroup->bindInt(':customers_group_id', 0);
      $QcustomersGroup->execute();


      while ($QcustomersGroup->fetch()) {

//build the data for b2b
        if ($QcustomersGroup->rowCount() > 0) {

          $Qattributes = $this->db->prepare('select customers_group_id,
                                                         customers_group_price,
                                                         products_price
                                                   from :table_products_groups
                                                   where products_id = :products_id
                                                   and customers_group_id = :customers_group_id
                                                   order by customers_group_id
                                                  ');
          $Qattributes->bindInt(':products_id', (int)$data['products_id']);
          $Qattributes->bindInt(':customers_group_id', $QcustomersGroup->valueInt('customers_group_id'));
          $Qattributes->execute();

          $ricarico = $QcustomersGroup->value('customers_group_discount');
        } // end num_rows

// if check is OFF the b2bsuite percentage is not apply
        if (($sql_data_array_products['products_percentage']) || (MODE_B2B_B2C == 'false')) {
          $pricek = $sql_data_array_products['products_price'];

// apply b2b
          if ($pricek > 0) {
            if (B2B == 'true') {
              if ($ricarico > 0) $newprice = $pricek + ($pricek / 100) * $ricarico;
              if ($ricarico == 0) $newprice = $pricek;
            }

            if (B2B == 'false') {
              if ($ricarico > 0) $newprice = $pricek - ($pricek / 100) * $ricarico;
              if ($ricarico == 0) $newprice = $pricek;
            }
// Prix TTC
            $sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] = $newprice;
          } else {
            $newprice;
          } // end $pricek

        } else if (!is_null($_POST)) {
// Prix TTC B2B
          $newprice = $sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')];
        } else {
          $newprice = $Qattributes->valueDecimal('customers_group_price');
        }
      } // end while


      $this->db->save('products', $sql_data_array_products);


      $QcustomersGroup = $this->db->prepare('select distinct customers_group_id,
                                                                  customers_group_name,
                                                                  customers_group_discount
                                                  from :table_customers_groups
                                                  where customers_group_id != :customers_group_id
                                                  order by customers_group_id
                                                ');

      $QcustomersGroup->bindInt(':customers_group_id', 0);
      $QcustomersGroup->execute();

// Gets all of the customers groups
      while ($QcustomersGroup->fetch()) {

        $Qattributes = $this->db->prepare('select g.customers_group_id,
                                                       g.customers_group_price,
                                                       p.products_price
                                                from :table_products_groups g,
                                                     :table_products p
                                                where p.products_id = :products_id
                                                and p.products_id = g.products_id
                                                and g.customers_group_id = :customers_group_id
                                                order by g.customers_group_id
                                              ');
        $Qattributes->bindInt(':products_id', (int)$data['products_id']);
        $Qattributes->bindInt(':customers_group_id', $QcustomersGroup->valueInt('customers_group_id'));
        $Qattributes->execute();

        if ($Qattributes->rowCount() > 0) {
// Definir la position 0 ou 1 pour --> Affichage Prix Public + Affichage Produit + Autorisation Commande
// L'Affichage des produits, autorisation de commander et affichage des prix mis par defaut en valeur 1 dans la cas de la B2B desactive.
          if (MODE_B2B_B2C == 'true') {
            if (HTML::sanitize($sql_data_array['price_group_view' . $QcustomersGroup->valueInt('customers_group_id')]) == 1) {
              $price_group_view = 1;
            } else {
              $price_group_view = 0;
            }


            if (HTML::sanitize($sql_data_array['products_group_view' . $QcustomersGroup->valueInt('customers_group_id')]) == 1) {
              $products_group_view = 1;
            } else {
              $products_group_view = 0;
            }

            if (HTML::sanitize($sql_data_array['orders_group_view' . $QcustomersGroup->valueInt('customers_group_id')]) == 1) {
              $orders_group_view = 1;
            } else {
              $orders_group_view = 0;
            }

            $products_quantity_unit_id_group = $sql_data_array['products_quantity_unit_id_group' . $QcustomersGroup->valueInt('customers_group_id')];
            $products_model_group = $sql_data_array['products_model_group' . $QcustomersGroup->valueInt('customers_group_id')];
            $products_quantity_fixed_group = $sql_data_array['products_quantity_fixed_group' . $QcustomersGroup->valueInt('customers_group_id')];

          } else {
            $price_group_view = 1;
            $products_group_view = 1;
            $orders_group_view = 1;
            $products_quantity_unit_id_group = 0;
            $products_model_group = '';
            $products_quantity_fixed_group = 1;

          } //end MODE_B2B_B2C

          $Qupdate = $this->db->prepare('update products_groups
                                              set price_group_view = 1,
                                                  products_group_view = 1,
                                                  orders_group_view = 1,
                                                  products_quantity_unit_id_group = :products_quantity_unit_id_group,
                                                  products_model_group= :products_model_group,
                                                  products_quantity_fixed_group= :products_quantity_fixed_group
                                              where customers_group_id = :customers_group_id
                                              and products_id = :products_id
                                            ');

          $Qupdate->bindInt(':products_quantity_unit_id_group', $products_quantity_unit_id_group);
          $Qupdate->bindValue(':products_model_group', $products_model_group);
          $Qupdate->bindInt(':products_quantity_fixed_group', $products_quantity_fixed_group);
          $Qupdate->bindInt(':customers_group_id', $Qattributes->valueInt('customers_group_id'));
          $Qupdate->bindInt(':products_id', (int)$data['products_id']);
          $Qupdate->execute();


// Prix TTC B2B ----------
          if (($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] != $Qattributes->value('customers_group_price')) && ($Qattributes->valueInt('customers_group_id') == $QcustomersGroup->valueInt('customers_group_id'))) {

            $this->db->save('products_groups', ['customers_group_price' => $sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')],
              'products_price' => (float)HTML::sanitize($_POST['products_price']),
            ],
              ['products_id' => (int)HTML::sanitize($data['products_id']),
                'customers_group_id' => $Qattributes->valueInt('customers_group_id')
              ]
            );

          } elseif (($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] == $Qattributes->valueInt('customers_group_price'))) {
            $attributes = $Qattributes->fetch();
          }


// Prix + Afficher Prix Public + Afficher Produit + Autoriser Commande
        }
/*
        elseif ($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] != '') {

          $sql_data_array1 = ['products_id' => (int)HTML::sanitize($data['products_id']),
            'products_price' => (float)HTML::sanitize($sql_data_array['products_price']),
            'customers_group_id' => $QcustomersGroup->valueInt('customers_group_id'),
            'customers_group_price' => (float)$sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')],
            'price_group_view' => (int)$sql_data_array['price_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
            'products_group_view' => (int)$sql_data_array['products_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
            'orders_group_view' => (int)$sql_data_array['orders_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
            'products_quantity_unit_id_group' => (int)$sql_data_array['products_quantity_unit_id_group' . $QcustomersGroup->valueInt('customers_group_id')],
            'products_model_group' => $sql_data_array['products_model_group' . $QcustomersGroup->valueInt('customers_group_id')],
            'products_quantity_fixed_group' => (int)$sql_data_array['products_quantity_fixed_group' . $QcustomersGroup->valueInt('customers_group_id')]
          ];

                    $this->db->save('products_groups', $sql_data_array1);
                  }
      }
 */
    }

  $mysqli->close();
  unset($data);

  Cache::clear('categories');
  Cache::clear('products-also_purchased');
  Cache::clear('products_related');
  Cache::clear('products_cross_sell');
  Cache::clear('upcoming');

      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_success_import'), 'success');

      echo '<div class="text-md-center">' . HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(), 'success') . '</div>';
    }
  }
