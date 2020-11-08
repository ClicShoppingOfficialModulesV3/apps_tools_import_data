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

  class Prestashop16
  {
    protected $PrefixTable;

    public function __construct()
    {

      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        CLICSHOPPING::redirect();
      }

      $this->PrefixTable = HTML::outputProtected($_POST['prefix_tables']);
    }

    public function execute()
    {
      global $mysqli;

      $CLICSHOPPING_Db = Registry::get('Db');
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
        $i = $i + 1;
      }

      $Qlanguages = $mysqli->query('select *
                                     from ' . $this->PrefixTable . 'lang
                                   ');
      echo '<hr>';
      echo '<div>table_languages</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qlanguages->num_rows . '</div>';

      $i = 0;

      while ($data = $Qlanguages->fetch_assoc()) {
        if ($cl[$i] != $data['code']) {
          $sql_data_array = [
            'languages_id' => (int)HTML::sanitize($data['id_lang']),
            'name' => $data['name'],
            'code' => $data['iso_code'],
          ];

//          $CLICSHOPPING_Db->save('languages', $sql_data_array);
          echo '<p class="text-info"> new language imported : ' . $data['name'] . '</p>';
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
                                      where id_customer = 1
                                    ');
      echo '<hr>';
      echo '<div>table_address_book</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QAddressBook->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QAddressBook->fetch_assoc()) {
//address_book
        $sql_data_array = [
          'address_book_id' => (int)HTML::sanitize($data['id_address']),
          'customers_id' => (int)HTML::sanitize($data['id_customer']),
          'entry_gender' => '',
          'entry_company' => $data['company'],
          'entry_siret' => '',
          'entry_ape' => '',
          'entry_tva_intracom' => $data['vat_number'],
          'entry_cf' => '',
          'entry_piva' => '',
          'entry_firstname' => $data['firstname'],
          'entry_lastname' => $data['lastname'],
          'entry_street_address' => $data['address1'],
          'entry_suburb' => $data['address2'],
          'entry_postcode' => $data['postcode'],
          'entry_city' => $data['city'],
          /*
                                    'entry_state' => $data['id_state'],
                                    'entry_country_id' => (int)HTML::sanitize($data['id_country']),
                                    'entry_zone_id' => '',
          */
          'entry_telephone' => $data['phone'],
        ];
//        $CLICSHOPPING_Db->save('address_book', $sql_data_array);
      }

//******************************************
// table_categories
//******************************************
      $Qcategories = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'category c,
                                         ' . $this->PrefixTable . 'category_lang cd
                                    where c.id_category = cd.id_category
                                  ');
      echo '<hr>';
      echo '<div>table_categories</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qcategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qcategories->fetch_assoc()) {
        echo $data['id_category'] . ' - ' . $data['name'] . '<br />';
        //categories
        $sql_data_array = [
          'categories_id' => (int)HTML::sanitize($data['id_category']),
          'parent_id' => (int)HTML::sanitize($data['id_parent']),
          'categories_image' => null,
          'sort_order' => (int)HTML::sanitize($data['level_depth']),
          'date_added' => $data['date_add'],
          'last_modified' => $data['last_upd'],
          'virtual_categories' => 0
        ];

//        $CLICSHOPPING_Db->save('categories', $sql_data_array);
// categories_description

        $i = 0;
        foreach ($clicshopping_languages as $languages) {
          $cl[$i] = $languages['id_lang'];

          $sql_data_array = [
            'categories_id' => (int)HTML::sanitize($data['id_category']),
            'language_id' => (int)HTML::sanitize($cl[$i]),
            'categories_name' => $data['name'],
            'categories_description ' => $data['description'],
            'categories_head_title_tag' => $data['meta_title'],
            'categories_head_desc_tag' => $data['meta_description'],
            'categories_head_keywords_tag' => $data['meta_keywords']
          ];

//          $CLICSHOPPING_Db->save('categories_description', $sql_data_array);
          $i = $i + 1;
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
          'customers_id' => (int)HTML::sanitize($data['id_customer']),
          'customers_company' => $data['company'],
          'customers_siret' => $data['siret'],
          'customers_ape' => $data['ape'],
          'customers_tva_intracom' => null,
          'customers_tva_intracom_code_iso' => null,
          'customers_gender' => null,
          'customers_firstname' => $data['firstname'],
          'customers_lastname' => $data['lastname'],
          'customers_dob' => $data['birthday'],
          'customers_email_address' => $data['email'],
          'customers_default_address_id' => 1,
          'customers_telephone' => null,
          'customers_fax' => null,
          'customers_password' => $data['passwd'],
          'customers_newsletter' => (int)HTML::sanitize($data['newsletter']),
          'languages_id' => (int)HTML::sanitize($data['id_lang']),
          'customers_group_id' => 0,
          'member_level' => 1,
          'customers_options_order_taxe' => 1,
          'customers_modify_company' => 1,
          'customers_modify_address_default' => 1,
          'customers_add_address' => 1,
          'customers_cellular_phone' => null,
          'customers_email_validation' => 1,
          'customer_discount' => 0,
          'client_computer_ip' => null,
          'provider_name_client' => null,
          'customer_website_company' => null,
        ];

//        $CLICSHOPPING_Db->save('customers', $sql_data_array);
      }

//******************************************
//manufacturers
//******************************************
      $Qmanufacturers = $mysqli->query('select *
                                        from ' . $this->PrefixTable . 'manufacturer m,
                                             ' . $this->PrefixTable . 'manufacturer_lang mi
                                        where m.id_manufacturer = mi.id_manufacturer
                                      ');

      echo '<hr>';
      echo '<div>table_manufacturers et manufacturers_info</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qmanufacturers->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qmanufacturers->fetch_assoc()) {
        echo $data['id_manufacturer'] . ' - ' . $data['name'] . '<br />';

        $sql_data_array = ['manufacturers_id' => (int)HTML::sanitize($data['id_manufacturer']),
          'manufacturers_name' => $data['name'],
          'manufacturers_image' => null,
          'date_added' => $data['date_add'],
          'last_modified' => $data['date_upd'],
        ];

//        $CLICSHOPPING_Db->save('manufacturers', $sql_data_array);

//manufacturers_info
        $i = 0;
        foreach ($clicshopping_languages as $languages) {
          $cl[$i] = $languages['id_lang'];

          $sql_data_array = ['manufacturers_id' => (int)HTML::sanitize($data['id_manufacturer']),
            'languages_id' => $cl[$i],
            'manufacturers_url' => null,
            'url_clicked' => 0,
            'date_last_click' => 0,
            'manufacturer_description ' => $data['description'],
            'manufacturer_seo_title' => $data['meta_title'],
            'manufacturer_seo_description' => $data['meta_description'],
            'manufacturer_seo_keyword' => $data['meta_keywords'],
          ];

//          $CLICSHOPPING_Db->save('manufacturers_info', $sql_data_array);
          $i = $i + 1;
        }
      }

//******************************************
//suppliers
//******************************************
      $Qmanufacturers = $mysqli->query('select *
                                        from ' . $this->PrefixTable . 'supplier m,
                                             ' . $this->PrefixTable . 'supplier_lang mi
                                        where m.id_supplier = mi.id_supplier
                                      ');

      echo '<hr>';
      echo '<div>table_manufacturers et manufacturers_info</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qmanufacturers->num_rows . '</div>';
      echo '<hr>';

      while ($data = $Qmanufacturers->fetch_assoc()) {
        echo $data['id_supplier'] . ' - ' . $data['name'] . '<br />';

        $sql_data_array = ['suppliers_id' => (int)HTML::sanitize($data['id_supplier']),
          'suppliers_name' => $data['name'],
          'suppliers_image' => null,
          'date_added' => $data['date_add'],
          'last_modified' => $data['date_upd'],
        ];

//        $CLICSHOPPING_Db->save('manufacturers', $sql_data_array);

//suppliers_info
        $i = 0;
        foreach ($clicshopping_languages as $languages) {
          $cl[$i] = $languages['id_lang'];

          $sql_data_array = ['suppliers_id' => (int)HTML::sanitize($data['id_supplier']),
            'languages_id' => $cl[$i],
            'suppliers_url' => null,
            'url_clicked' => 0,
            'date_last_click' => 0,
          ];

//          $CLICSHOPPING_Db->save('manufacturers_info', $sql_data_array);
          $i = $i + 1;
        }
      }


//******************************************
// products_images
//******************************************
      /*
      $Qimages = $mysqli->query('select *
                                 from ' . $this->PrefixTable .'image i left join ' . $this->PrefixTable .'image_type on ,
                                      ' . $this->PrefixTable .'image_lang il,
                                 where i.id_image = il.id_image_id
                                 and il.id_lang = 1
                               ');
      echo '<hr>';
      echo '<div>table_products_images </div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $Qimages->num_rows . '</div>';
      echo '<hr>';

      while($data = $Qimages->fetch_assoc()) {
        $sql_data_array = ['products_id' => (int)HTML::sanitize($data['id_product']),
                           'image' => 'products/' . $data['image'],
                           'htmlcontent' => null,
                           'sort_order' => 0,
                           ];

//        $CLICSHOPPING_Db->save('products_images', $sql_data_array);
      }
*/
// ****************************************
// products_to categories
//******************************************
      $QproductsCategories = $mysqli->query('select *
                                             from ' . $this->PrefixTable . 'category_product
                                            ');
      echo '<hr>';
      echo '<div>table_products_to_categories</div>';
      echo '<div>' . CLICSHOPPING::getDef('text_number_of_item') . ' : ' . $QproductsCategories->num_rows . '</div>';
      echo '<hr>';

      while ($data = $QproductsCategories->fetch_assoc()) {
        $sql_data_array = ['products_id' => (int)HTML::sanitize($data['id_product']),
          'categories_id' => (int)HTML::sanitize($data['id_category'])
        ];

//        $CLICSHOPPING_Db->save('products_to_categories', $sql_data_array);

      }


//**********************************
//products table
//**********************************
      $Qproducts = $mysqli->query('select *
                                    from ' . $this->PrefixTable . 'product p,
                                         ' . $this->PrefixTable . 'product_lang pd
                                    where p.id_product = pd.id_product
                                 ');
      echo '<hr>';
      echo '<div>table_products & table_products_description </div>';
      echo '<div>' . CLICSHOPPING::getDef('number_of_products') . ' : ' . $Qproducts->num_rows . '</div>';
      echo '<hr>';

// products description
      $i = 0;
      while ($data = $Qproducts->fetch_assoc()) {
        foreach ($clicshopping_languages as $languages) {
          $pl[$i] = $languages['id_lang'];

          echo (int)HTML::sanitize($data['id_product']) . ' - ' . $data['name'] . '<br />';

          $sql_data_array_description = ['products_id' => (int)HTML::sanitize($data['id_product']),
            'language_id' => (int)HTML::sanitize($pl[$i]),
            'products_name' => $data['name'],
            'products_description' => $data['description'],
            'products_url' => null,
            'products_viewed ' => 0,
            'products_head_title_tag' => $data['meta_title'],
            'products_head_desc_tag' => $data['meta_description'],
            'products_head_keywords_tag' => $data['meta_keywords'],
            'products_head_tag' => null,
            'products_shipping_delay' => null,
            'products_description_summary' => $data['description_short'],
          ];

//          $CLICSHOPPING_Db->save('products_description', $sql_data_array_description);
          $i = $i + 1;
        }


// products
        $sql_data_array_products = ['products_id' => (int)HTML::sanitize($data['id_product']),
          'products_quantity' => (int)HTML::sanitize($data['quantity']),
          'products_ean' => HTML::sanitize($data['ean13']),
          'products_model' => $data['reference'],
          'products_sku' => HTML::sanitize($data['upc']),
          'products_price' => (float)$data['price'],
          'products_date_available' => $data['available_date'],
          'products_weight' => (float)$data['weight'],
          'products_price_kilo' => null,
          'products_status' => 1,
          'products_percentage' => 0,
          'products_view' => 1,
          'orders_view' => 1,
          'products_tax_class_id' => 1,
          'manufacturers_id' => (int)HTML::sanitize($data['id_manufacturer']),
          'suppliers_id' => (int)HTML::sanitize($data['id_supplier']),
          'products_min_qty_order' => 0,
          'products_price_comparison' => 0,
          'products_dimension_width' => (float)HTML::sanitize($data['width']),
          'products_dimension_height' => (float)HTML::sanitize($data['height']),
          'products_dimension_depth' => (float)HTML::sanitize($data['depth']),
          'products_dimension_type' => null,
          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
          'products_volume' => null,
          'products_quantity_unit_id' => 0,
          'products_only_online' => 0,
          'products_weight_class_id' => 2,
          'products_cost' => null,
          'products_handling' => null,
          'products_warehouse_time_replenishment' => null,
          'products_warehouse' => null,
          'products_warehouse_row' => null,
          'products_warehouse_level_location' => null,
          'products_packaging' => null,
          'products_sort_order' => (int)HTML::sanitize($data['products_sort_order']),
          'products_quantity_alert' => (int)HTML::sanitize($data['minimal_quantity']),
          'products_only_shop' => (int)HTML::sanitize($data['products_only_shop']),
          'products_download_public' => 0,
          'products_type' => null,
          'products_sort_order' => 0,
          'products_date_added' => $data['date_add'],
          'products_last_modified' => $data['date_upd'],
          'products_image' => null,
        ];

//***************************************
// B2B
//***************************************
        $QcustomersGroup = $CLICSHOPPING_Db->prepare('select distinct customers_group_id,
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
            $Qattributes = $CLICSHOPPING_Db->prepare('select customers_group_id,
                                                         customers_group_price,
                                                         products_price
                                                   from :table_products_groups
                                                   where products_id = :products_id
                                                   and customers_group_id = :customers_group_id
                                                   order by customers_group_id
                                                  ');
            $Qattributes->bindInt(':products_id', $data['id_product']);
            $Qattributes->bindInt(':customers_group_id', $QcustomersGroup->valueInt('customers_group_id'));
            $Qattributes->execute();

            $attributes = $Qattributes->fetch();

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


//        $CLICSHOPPING_Db->save('products', $sql_data_array_products);


        $QcustomersGroup = $CLICSHOPPING_Db->prepare('select distinct customers_group_id,
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
          $Qattributes = $CLICSHOPPING_Db->prepare('select g.customers_group_id,
                                                       g.customers_group_price,
                                                       p.products_price
                                                from :table_products_groups g,
                                                     :table_products p
                                                where p.products_id = :products_id
                                                and p.products_id = g.products_id
                                                and g.customers_group_id = :customers_group_id
                                                order by g.customers_group_id
                                              ');
          $Qattributes->bindInt(':products_id', (int)$data['id_product']);
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

            $Qupdate = $CLICSHOPPING_Db->prepare('update products_groups
                                              set price_group_view = :price_group_view,
                                                  products_group_view = :products_group_view,
                                                  orders_group_view = :orders_group_view,
                                                  products_quantity_unit_id_group = :products_quantity_unit_id_group,
                                                  products_model_group= :products_model_group,
                                                  products_quantity_fixed_group= :products_quantity_fixed_group
                                              where customers_group_id = :customers_group_id
                                              and products_id = :products_id
                                            ');
            $Qupdate->bindInt(':price_group_view', $price_group_view);
            $Qupdate->bindInt(':products_group_view', $products_group_view);
            $Qupdate->bindInt(':orders_group_view', $orders_group_view);
            $Qupdate->bindInt(':products_quantity_unit_id_group', $products_quantity_unit_id_group);
            $Qupdate->bindValue(':products_model_group', $products_model_group);
            $Qupdate->bindInt(':products_quantity_fixed_group', $products_quantity_fixed_group);
            $Qupdate->bindInt(':customers_group_id', $Qattributes->valueInt('customers_group_id'));
            $Qupdate->bindInt(':products_id', (int)$data['id_product']);
            $Qupdate->execute();


// Prix TTC B2B ----------
            if (($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] != $Qattributes->value('customers_group_price')) && ($Qattributes->valueInt('customers_group_id') == $QcustomersGroup->valueInt('customers_group_id'))) {
              /*
                              $CLICSHOPPING_Db->save('products_groups',  ['customers_group_price' => $sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')],
                                                                   'products_price' => (float)HTML::sanitize($_POST['products_price']),
                                                                  ],
                                                                  ['products_id' => (int)HTML::sanitize($data['products_id']),
                                                                   'customers_group_id' => $Qattributes->valueInt('customers_group_id')
                                                                  ]
                                            );
              */
            } elseif (($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] == $Qattributes->valueInt('customers_group_price'))) {
              $attributes = $Qattributes->fetch();
            }


// Prix + Afficher Prix Public + Afficher Produit + Autoriser Commande
          }
/*
          elseif ($sql_data_array_products_group_price['price' . $QcustomersGroup->valueInt('customers_group_id')] != '') {
            $sql_data_array1 = [
              'products_id' => (int)HTML::sanitize($data['products_id']),
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
*/
        }
      }

      $mysqli->close();
      unset($data);

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');

      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_success_import'), 'success');
      echo '<div class="alert alert-warning text-md-center">Please update your customers group (Customer menu)</div>';
      echo '<div class="text-md-center">' . HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(), 'success') . '</div>';
    }
  }
