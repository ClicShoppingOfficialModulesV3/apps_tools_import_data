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

  class Oscommerce
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

      $CLICSHOPPING_ImportDatabase->cleanTableClicShopping();

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
      $QAddressBook = $mysqli->query('select *
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
          'entry_firstname' => $data['entry_firstname'],
          'entry_lastname' => $data['entry_lastname'],
          'entry_street_address' => $data['entry_street_address'],
          'entry_suburb' => $data['entry_suburb'],
          'entry_postcode' => $data['entry_postcode'],
          'entry_city' => $data['entry_city'],
          'entry_state' => $data['entry_state'],
          'entry_country_id' => (int)HTML::sanitize($data['entry_country_id']),
          'entry_zone_id' => HTML::sanitize($data['entry_zone_id']),
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
          'banners_target' => '_self',
          'banners_html_text' => $data['banners_html_text'],
          'expires_impressions' => (int)$data['expires_impressions'],
          'expires_date' => $data['expires_date'],
          'date_scheduled' => $data['date_scheduled'],
          'date_added' => $data['date_added'],
          'date_status_change' => $data['date_status_change'],
          'status' => (int)$data['status'],
          'languages_id' => 1,
          'banners_title_admin' => $data['banners_title']
        ];

        $this->db->save('banners', $sql_data_array);
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
          'customers_id' => (int)$data['customers_id'],
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
          'languages_id' => 1,
          'customers_group_id' => 0,
        ];

        $this->db->save('customers', $sql_data_array);
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
          'password_reset_key' => $data['password_reset_key'],
          'password_reset_date' => $data['password_reset_date']
        ];

        $this->db->save('customers_info', $sql_data_array);
      }


//******************************************
//manufacturers
//******************************************
      $Qmanufacturers = $mysqli->query('select *
                                        from ' . $this->PrefixTable . 'manufacturers
                                      ');

      echo '<hr>';
      echo '<div>table_manufacturers et manufacturers_info</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qmanufacturers->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qmanufacturers->fetch_assoc()) {
        echo $data['manufacturers_id'] . ' - ' . $data['manufacturers_name'] . '<br />';

        $sql_data_array = ['manufacturers_id' => (int)$data['manufacturers_id'],
          'manufacturers_name' => $data['manufacturers_name'],
          'manufacturers_image' => $data['manufacturers_image'],
          'date_added' => $data['date_added'],
          'last_modified' => $data['last_modified'],
          'suppliers_id' => 0
        ];

        $this->db->save('manufacturers', $sql_data_array);
      }

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

      while ($data = $QmanufacturersName->fetch_assoc()) {
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
          'content_html' => $data['content_html'],
          'module' => $data['module'],
          'date_added' => $data['date_added'],
          'date_sent' => $data['date_sent'],
          'status' => (int)HTML::sanitize($data['status']),
          'locked' => (int)HTML::sanitize($data['locked']),
        ];

        $this->db->save('newsletters', $sql_data_array);
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
        $sql_data_array = ['orders_id' => (int)HTML::sanitize($data['orders_id']),
          'customers_id' => (int)HTML::sanitize($data['customers_id']),
          'customers_name' => $data['customers_name'],
          'customers_company' => $data['customers_company'],
          'customers_street_address' => $data['customers_street_address'],
          'customers_suburb' => $data['customers_suburb'],
          'customers_city' => $data['customers_city'],
          'customers_postcode' => $data['customers_postcode'],
          'customers_state' => $data['customers_state'],
          'customers_country' => $data['customers_country'],
          'customers_telephone' => $data['customers_telephone'],
          'customers_email_address' => $data['customers_email_address'],
          'customers_address_format_id' => (int)HTML::sanitize($data['customers_address_format_id']),
          'delivery_name' => $data['delivery_name'],
          'delivery_company' => $data['delivery_company'],
          'delivery_street_address' => $data['delivery_street_address'],
          'delivery_suburb' => $data['delivery_suburb'],
          'delivery_city' => $data['delivery_city'],
          'delivery_postcode' => $data['delivery_postcode'],
          'delivery_state' => $data['delivery_state'],
          'delivery_country' => $data['delivery_country'],
          'delivery_address_format_id' => (int)HTML::sanitize($data['delivery_address_format_id']),
          'billing_name' => $data['billing_name'],
          'billing_company' => $data['billing_company'],
          'billing_street_address' => $data['billing_street_address'],
          'billing_suburb' => $data['billing_suburb'],
          'billing_city' => $data['billing_city'],
          'billing_postcode' => $data['billing_postcode'],
          'billing_state' => $data['billing_state'],
          'billing_country' => $data['billing_country'],
          'billing_address_format_id' => (int)HTML::sanitize($data['billing_address_format_id']),
          'payment_method' => $data['payment_method'],
          'cc_type' => $data['cc_type'],
          'cc_owner' => $data['cc_owner'],
          'cc_number' => $data['cc_number'],
          'cc_expires' => $data['cc_expires'],
          'last_modified' => $data['last_modified'],
          'date_purchased' => $data['date_purchased'],
          'orders_status' => $data['orders_status'],
          'orders_date_finished' => $data['orders_date_finished'],
          'currency' => $data['currency'],
          'currency_value' => $data['currency_value'],
          `customers_group_id` => 0,
        ];

        $this->db->save('orders', $sql_data_array);
      }

//******************************************
//orders_products
//******************************************
      $QordersProducts = $mysqli->query('select *
                                         from ' . $this->PrefixTable . 'orders_products
                                       ');

      echo '<hr>';
      echo '<div>table_orders_products</div>';
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
          $sql_data_array = ['orders_status_id' => (int)HTML::sanitize($data['orders_status_id']),
            'language_id' => (int)HTML::sanitize($data['language_id']),
            'orders_status_name' => $data['orders_status_name'],
            'public_flag' => (int)HTML::sanitize($data['public_flag']),
            'downloads_flag' => (int)HTML::sanitize($data['downloads_flag']),
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
        $sql_data_array = ['orders_status_history_id' => (int)HTML::sanitize($data['orders_status_history_id']),
          'orders_id' => (int)HTML::sanitize($data['orders_id']),
          'orders_status_id' => (int)HTML::sanitize($data['order_status_id']),
          'orders_status_invoice_id' => 1,
          'comment' => $data['comment'],
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
        $sql_data_array = ['orders_total_id' => (int)HTML::sanitize($data['orders_total_id']),
          'orders_id' => (int)HTML::sanitize($data['orders_id']),
          'title' => $data['title'],
          'text' => $data['text'],
          'value' => (float)$data['value'],
          'class' => $data['class'],
          'sort_order' => (int)HTML::sanitize($data['sort_order']),
        ];

        $this->db->save('orders_products', $sql_data_array);
      }

//******************************************
//  orders_products_attributes
//******************************************
      $QordersProductsAttributes = $mysqli->query('select *
                                                   from ' . $this->PrefixTable . 'orders_products_attributes
                                                 ');
      echo '<hr>';
      echo '<div>table_orders_products_attributes </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersProductsAttributes->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersProductsAttributes->fetch_assoc()) {
        $sql_data_array = ['orders_products_attributes_id' => (int)$data['orders_products_attributes_id'],
          'orders_id' => (int)$data['orders_id'],
          'orders_products_id' => (int)$data['orders_products_id'],
          'products_options' => $data['products_options'],
          'products_options_values' => $data['products_options_values'],
          'options_values_price' => (float)$data['options_values_price'],
          'price_prefix' => $data['price_prefix'],
          'products_attributes_reference ' => '',
        ];

        $this->db->save('orders_products_attributes', $sql_data_array);
      }

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
          'options_values_id' => $data['options_values_id'],
          'options_values_price' => (float)$data['options_values_price'],
          'price_prefix' => $data['price_prefix'],
          'products_options_sort_order' => 0,
          'products_attributes_reference' => '',
          'customers_group_id' => 0,
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
          'products_options_type' => 'select'
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

        foreach ($clicshopping_languages as $languages) {
          $cl[$i] = $languages['languages_id'];

          $sql_data_array = ['reviews_id' => (int)HTML::sanitize($data['reviews_id']),
            'languages_id' => (int)HTML::sanitize($cl[$i]),
            'reviews_text' => $data['reviews_text'],
          ];
          $this->db->save('reviews_description', $sql_data_array);
          $i++;
        }
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
      $QproductDescriptions = $mysqli->query('select *
                                              from  ' . $this->PrefixTable . 'products_description
                                            ');
      echo '<hr>';
      echo '<div>table_products_description </div>';
      echo '<div>' . CLICSHOPPING::getDef('number_of_products') . ' : ' . $QproductDescriptions->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QproductDescriptions->fetch_assoc()) {
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


//products
      $Qproducts = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'products
                                 ');
      echo '<hr>';
      echo '<div>table_products</div>';
      echo '<div>' . CLICSHOPPING::getDef('number_of_products') . ' : ' . $Qproducts->num_rows . '</div>';
      echo '<hr>';

// products
      while ($data = $Qproducts->fetch_assoc()) {
        $sql_data_array_products = ['products_id' => (int)HTML::sanitize($data['products_id']),
          'products_quantity' => (int)HTML::sanitize($data['products_quantity']),
          'products_ean' => HTML::sanitize($data['products_gtin']),
          'products_model' => $data['products_model'],
          'products_sku' => HTML::sanitize($data['products_gtin']),
          'products_price' => (float)$data['products_price'],
          'products_date_available' => $data['products_date_available'],
          'products_weight' => (float)$data['products_weight'],
          'products_status' => (int)HTML::sanitize($data['products_status']),
          'products_percentage' => 1,
          'products_view' => 1,
          'orders_view' => 1,
          'products_cost' => 0,
          'products_tax_class_id' => (int)HTML::sanitize($data['products_tax_class_id']),
          'manufacturers_id' => (int)HTML::sanitize($data['manufacturers_id']),
          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
          'products_sort_order' => (int)HTML::sanitize($data['products_ordered']),
          'products_date_added' => 'now()',
          'products_last_modified' => 'now()',
          'products_date_available' => 'now()',
          'products_image' => $data['products_image'],
          'products_weight_class_id' => 2,
        ];


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
          } elseif ($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] != '') {

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
      }

      $mysqli->close();
      unset($data);
      Cache::clear('categories');

      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_success_import'), 'success');

      echo '<div class="text-md-center">' . HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(), 'success') . '</div>';
    }
  }
