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

  class OpenCart
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
      //en-gb -oc_language
//******************************************
      $clicshopping_languages = $CLICSHOPPING_ImportDatabase->readLanguage();

      $i = 0;
      $cl = [];

      foreach ($clicshopping_languages as $languages) {
        $cl[$i] = $languages['code'];

        $i++;
      }

      $Qlanguages = $mysqli->query('select *
                                     from ' . $this->PrefixTable . 'language
                                   ');
      echo '<hr>';
      echo '<div>table_languages</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qlanguages->num_rows . '</div>';

      $i = 0;

      while ($data = $Qlanguages->fetch_assoc()) {


        $code = substr($data['code'], -5, 2);

        if ($cl[$i] != $code && $code != 'fr') {
          $sql_data_array = ['name' => $data['name'],
            'code' => $code,
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
                                      from ' . $this->PrefixTable . 'address
                                    ');
      echo '<hr>';
      echo '<div>table_address_book</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QAddressBook->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QAddressBook->fetch_assoc()) {
//address_book
        $sql_data_array = ['address_book_id' => (int)$data['address_id'],
          'customers_id' => (int)$data['customer_id'],
          'entry_firstname' => $data['firstname'],
          'entry_lastname' => $data['lastname'],
          'entry_company' => $data['company'],
          'entry_street_address' => $data['address_1'],
          'entry_suburb' => $data['address_2'],
          'entry_city' => $data['city'],
          'entry_postcode' => $data['postcode'],
          'entry_country_id' => HTML::sanitize($data['country_id']),
          'entry_zone_id' => HTML::sanitize($data['zone_id']),
        ];
        $this->db->save('address_book', $sql_data_array);
      }


//******************************************
// table_categories
//******************************************
      $Qcategories = $mysqli->query('select *
                                     from ' . $this->PrefixTable . 'category c
                                    ');
      echo '<hr>';
      echo '<div>table_categories</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qcategories->fetch_assoc()) {
//categories
        $sql_data_array = ['categories_id' => HTML::sanitize($data['category_id']),
          'categories_image' => $data['category_image'],
          'parent_id' => HTML::sanitize($data['parent_id']),
          'sort_order' => HTML::sanitize($data['sort_order']),
          'date_added' => $data['date_added'],
          'last_modified' => $data['date_modified'],
          'virtual_categories' => 0
        ];

        $this->db->save('categories', $sql_data_array);
      }

//description
      $Qcategories = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'category_description
                                  ');
      echo '<hr>';
      echo '<div>table_categories_description</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qcategories->fetch_assoc()) {
        echo $data['category_id'] . ' - ' . $data['name'] . '<br />';

// categories_description
        foreach ($clicshopping_languages as $languages) {
          $sql_data_array = ['categories_id' => (int)$data['category_id'],
            'language_id' => (int)$languages['languages_id'],
            'categories_name' => $data['name'],
            'categories_description' => $data['description'],
            'categories_head_title_tag' => $data['meta_title'],
            'categories_head_desc_tag' => $data['meta_description'],
            'categories_head_keywords_tag' => $data['meta_keyword']
          ];
          $this->db->save('categories_description', $sql_data_array);
        }
      }

//******************************************
//Customers
//******************************************
      $Qcustomers = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'customer
                                   ');
      echo '<hr>';
      echo '<div>table_customers</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcustomers->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qcustomers->fetch_assoc()) {
        $sql_data_array = [
          'customers_id' => (int)HTML::sanitize($data['customer_id']),
          'customers_firstname' => $data['firstname'],
          'customers_lastname' => $data['lastname'],
          'customers_email_address' => $data['email'],
          'customers_default_address_id' => (int)$data['address_id'],
          'customers_telephone' => $data['telephone'],
          'customers_fax' => $data['fax'],
          'customers_password' => $data['password'],
          'customers_newsletter' => $data['newsletter'],
          'languages_id' => $data['language_id'],
          'customers_group_id' => $data['customer_group_id'],
        ];

        $this->db->save('customers', $sql_data_array);
      }

//******************************************
//Customers Info
//******************************************
      /*
            $QcustomersInfo = $mysqli->query('select *
                                               from ' . $this->PrefixTable .'customers_info
                                             ');
            echo '<hr>';
            echo '<div>table_customers_info</div>';
            echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QcustomersInfo->num_rows . '</div>';
            echo '<hr>';
      
            while($data = $QcustomersInfo->fetch_assoc()) {
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
      */


//******************************************
//Customers group
//******************************************

      $QcustomersGroups = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'customer_group cg,
                                            ' . $this->PrefixTable . 'customer_group_description cgd
                                       where cg.customer_group_id = cgd.customer_group_id
                                     ');

      echo '<hr>';
      echo '<div>table_customers_groups</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QcustomersGroups->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QcustomersGroups->fetch_assoc()) {
        $customers_groups_id = $data['customer_group_id'] + 1;
        echo $customers_groups_id . ' - ' . $data['name'] . '<br />';

        $sql_data_array = ['customers_group_id' => (int)$customers_groups_id,
          'customers_group_name' => $data['name'],
          'customers_group_discount' => 0,
          'color_bar' => '#fff',
          'group_order_taxe' => 0,
          'group_payment_unallowed' => 'CO',
          'group_shipping_unallowed' => 'IT',
          'group_tax' => false,
          'customers_group_quantity_default' => 0
        ];

        $this->db->save('customers_groups', $sql_data_array);
      }

//******************************************
//manufacturers
//******************************************

      $Qmanufacturers = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'manufacturer
                                     ');

      echo '<hr>';
      echo '<div>table_manufacturers et manufacturers_info</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qmanufacturers->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qmanufacturers->fetch_assoc()) {
        echo $data['manufacturer_id'] . ' - ' . $data['name'] . '<br />';

        $sql_data_array = ['manufacturers_id' => (int)$data['manufacturer_id'],
          'manufacturers_name' => $data['name'],
          'manufacturers_image' => $data['image'],
          'date_added' => 'now()',
          'last_modified' => 'now()',
          'suppliers_id' => 0
        ];

        $this->db->save('manufacturers', $sql_data_array);

        /*
        //******************************************
        //manufacturers_info
        //******************************************
                $i = 0;
                foreach ($clicshopping_languages as $languages) {
                  $cl[$i] = $languages['languages_id'];
        
                    $sql_data_array = ['manufacturers_id' => (int)HTML::sanitize($data['manufacturer_id']),
                                       'languages_id' => $cl[$i],
                                      ];
        //          $CLICSHOPPING_Db->save('manufacturers_info', $sql_data_array);
                  $i = $i+1;
        */
      }

//******************************************
//orders
//******************************************
      $Qorders = $mysqli->query('select *
                                 from ' . $this->PrefixTable . 'order
                                ');

      echo '<hr>';
      echo '<div>table_orders</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qorders->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qorders->fetch_assoc()) {
        $sql_data_array = ['orders_id' => (int)HTML::sanitize($data['order_id']),


          'customers_id' => (int)HTML::sanitize($data['customer_id']),
          'customers_name' => $data['lastname'],
//                           'customers_company' => $data['customers_company'],
//                           'customers_street_address' => $data['customers_street_address'],
//                           'customers_suburb' => $data['customers_suburb'],
//                           'customers_city' => $data['customers_city'],
//                           'customers_postcode' => $data['customers_postcode'],
//                           'customers_state' => $data['customers_state'],
//                           'customers_country' => $data['customers_country'],
          'customers_telephone' => $data['telephone'],
          'customers_email_address' => $data['email'],
          'customers_address_format_id' => (int)$data['customers_address_format_id'],

          'delivery_name' => $data['shipping_lastname'],
          'delivery_company' => $data['shipping_company'],
          'delivery_street_address' => $data['shipping_address_1'],
          'delivery_suburb' => $data['shipping_address_2'],
          'delivery_city' => $data['shipping_city'],
          'delivery_postcode' => $data['shipping_postcode'],
          'delivery_state' => $data['delivery_state'],
          'delivery_country' => $data['shipping_country'],
          'delivery_address_format_id' => (int)$data['shipping_address_format'],


          'billing_name' => $data['payment_lastname'],
          'billing_company' => $data['payment_company'],
          'billing_street_address' => $data['payment_address_1'],
          'billing_suburb' => $data['payment_address_2'],
          'billing_city' => $data['payment_city'],
          'billing_postcode' => $data['payment_postcode'],
          'billing_country' => $data['payment_country'],
          'billing_address_format_id' => (int)$data['payment_address_format'],
          'payment_method' => $data['payment_method'],

//                           'cc_type' => $data['cc_type'],
//                           'cc_owner' => $data['cc_owner'],
//                           'cc_number' => $data['cc_number'],
//                           'cc_expires' => $data['cc_expires'],
//                           'last_modified' => $data['last_modified'],
//                           'date_purchased' => $data['date_purchased'],
//                           'orders_status' => $data['orders_status'],
          'orders_date_finished' => $data['date_added'],

          'currency' => $data['currency_code'],
          'currency_value' => $data['currency_value'],
          `customers_group_id` => $data['customer_group_id'],
          'client_computer_ip' => $data['ip'],
        ];

        $this->db->save('orders', $sql_data_array);
      }

//******************************************
//orders_products
//******************************************
      $QordersProducts = $mysqli->query('select *
                                         from ' . $this->PrefixTable . 'order_product
                                       ');

      echo '<hr>';
      echo '<div>table_orders_products</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersProducts->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersProducts->fetch_assoc()) {
        $sql_data_array = ['orders_products_id' => (int)$data['order_product_id'],
          'orders_id' => (int)$data['order_id'],
          'products_id' => (int)$data['product_id'],
          'products_model' => $data['model'],
          'products_name' => $data['name'],
          'products_price' => (float)$data['price'],
          'final_price' => (float)$data['total'],
          'products_tax' => (float)$data['tax'],
          'products_quantity' => (int)$data['quantity']
        ];

        $this->db->save('orders_products', $sql_data_array);
      }


//******************************************
//orders_status_history
//******************************************
      $QordersHistory = $mysqli->query('select *
                                       from ' . $this->PrefixTable . 'order_history
                                     ');
      echo '<hr>';
      echo '<div>table_orders_status_history</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersHistory->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersHistory->fetch_assoc()) {
        $sql_data_array = ['orders_status_history_id' => (int)HTML::sanitize($data['order_history_id']),
          'orders_id' => (int)HTML::sanitize($data['order_id ']),
          'order_status_id' => (int)HTML::sanitize($data['order_status_id ']),
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
                                       from ' . $this->PrefixTable . 'order_total
                                     ');

      echo '<hr>';
      echo '<div>table_orders_total</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QordersTotal->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QordersTotal->fetch_assoc()) {
        $sql_data_array = ['orders_total_id' => (int)HTML::sanitize($data['order_total_id']),
          'orders_id' => (int)HTML::sanitize($data['order_id']),
          'title' => $data['title'],
          'text' => $data['code'],
          'value' => (float)$data['value'],
//                           'class' => $data['class'],
          'sort_order' => (int)HTML::sanitize($data['sort_order']),
        ];

        $this->db->save('orders_products', $sql_data_array);
      }

//******************************************
// products attributes
//******************************************


//******************************************
// products attributes download
//******************************************


//******************************************
// products_images
//******************************************
      $Qimages = $mysqli->query('select *
                                 from ' . $this->PrefixTable . 'product_image
                               ');
      echo '<hr>';
      echo '<div>table_products_images </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qimages->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qimages->fetch_assoc()) {
        $sql_data_array = ['products_id' => (int)$data['product_id'],
          'image' => $data['image'],
          'htmlcontent' => '',
          'sort_order' => (int)$data['sort_order'],
        ];

        $this->db->save('products_images', $sql_data_array);
      }


//******************************************
// products_to categories
//******************************************
      $QproductsCategories = $mysqli->query('select *
                                             from ' . $this->PrefixTable . 'product_to_category
                                            ');
      echo '<hr>';
      echo '<div>table_products_to_categories</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QproductsCategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QproductsCategories->fetch_assoc()) {
        $sql_data_array = ['products_id' => (int)$data['product_id'],
          'categories_id' => (int)$data['category_id']
        ];

        $this->db->save('products_to_categories', $sql_data_array);
      }


//**********************************
//products table
//**********************************
      $QproductDescriptions = $mysqli->query('select *
                                              from ' . $this->PrefixTable . 'product_description
                                             ');
      echo '<hr>';
      echo '<div>table_products_description </div>';
      echo '<div>' . CLICSHOPPING::getDef('number_of_products') . ' : ' . $QproductDescriptions->num_rows . '</div>';
      echo '<hr>';

// products description

      while ($data = $QproductDescriptions->fetch_assoc()) {
        foreach ($clicshopping_languages as $languages) {

          echo (int)$data['product_id'] . ' - ' . $data['name'] . '<br />';

          $sql_data_array_description = ['products_id' => (int)$data['product_id'],
            'language_id' => (int)$languages['languages_id'],
            'products_name' => $data['name'],
            'products_description' => $data['description'],
            'products_url' => '',
            'products_viewed ' => 0,
            'products_head_title_tag' => $data['meta_title'],
            'products_head_desc_tag' => $data['meta_description'],
            'products_head_keywords_tag' => $data['meta_keyword'],
            'products_head_tag' => null,
            'products_shipping_delay' => null,
            'products_description_summary' => null,
          ];

          $this->db->save('products_description', $sql_data_array_description);
        }
      }


//products
      $Qproducts = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'product
                                 ');
      echo '<hr>';
      echo '<div>table_products</div>';
      echo '<div>' . CLICSHOPPING::getDef('number_of_products') . ' : ' . $Qproducts->num_rows . '</div>';
      echo '<hr>';

// products
      while ($data = $Qproducts->fetch_assoc()) {
        $sql_data_array_products = ['products_id' => (int)$data['product_id'],
          'products_quantity' => (int)$data['quantity'],
          'products_ean' => $data['ean'],
          'products_model' => $data['model'],
          'products_sku' => $data['sku'],
          'products_price' => (float)$data['price'],
          'products_date_available' => $data['date_available'],
          'products_weight' => (float)$data['weight'],
          'products_status' => $data['status'],
          'products_percentage' => 1,
          'products_view' => 1,
          'orders_view' => 1,
          'products_cost' => 0,
          'products_tax_class_id' => (int)$data['tax_class_id'],
          'manufacturers_id' => (int)$data['manufacturer_id'],
          'products_dimension_width' => $data['width'],
          'products_dimension_height' => $data['height'],
          'products_dimension_depth' => $data['length'],
          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
          'products_sort_order' => (int)$data['sort_order'],
          'products_date_added' => $data['date_added'],
          'products_last_modified' => $data['date_modified '],
          'products_date_available' => 'now()',
          'products_image' => $data['image'],
          'products_weight_class_id' => $data['weight_class_id'],
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
            $Qattributes->bindInt(':products_id', (int)$data['product_id']);
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
          $Qattributes->bindInt(':products_id', (int)$data['product_id']);
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
            $Qupdate->bindInt(':products_id', (int)$data['product_id']);
            $Qupdate->execute();


// Prix TTC B2B ----------
            if (($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] != $Qattributes->value('customers_group_price')) && ($Qattributes->valueInt('customers_group_id') == $QcustomersGroup->valueInt('customers_group_id'))) {

              $this->db->save('products_groups', ['customers_group_price' => $sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')],
                'products_price' => (float)HTML::sanitize($_POST['products_price']),
              ],
                ['products_id' => (int)HTML::sanitize($data['product_id']),
                  'customers_group_id' => $Qattributes->valueInt('customers_group_id')
                ]
              );

            } elseif (($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] == $Qattributes->valueInt('customers_group_price'))) {
              $attributes = $Qattributes->fetch();
            }


// Prix + Afficher Prix Public + Afficher Produit + Autoriser Commande
          } elseif ($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] != '') {

            $sql_data_array1 = ['products_id' => (int)HTML::sanitize($data['product_id']),
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