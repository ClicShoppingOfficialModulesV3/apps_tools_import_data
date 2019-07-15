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

  namespace ClicShopping\Apps\Tools\ImportData\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class ImportDatabase
  {

    public function __construct()
    {
      $CLICSHOPPING_ImportData = Registry::get('ImportData');
      $this->app = $CLICSHOPPING_ImportData;
    }

    public static function ImportSolution(string $name)
    {

      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/ClicShoppingAdmin/ImportDatabase/';

      if ($contents = @scandir($template_directory)) {
        if ($contents = @scandir($template_directory)) {
          $fileTypes = ['php']; // Create an array of file types7
          $found = []; // Traverse the folder, and add filename to $found array if type matches

          foreach ($contents as $item) {
            $fileInfo = pathinfo($item);
            if (array_key_exists('extension', $fileInfo) && in_array($fileInfo['extension'], $fileTypes)) {
              $found[] = $item;
            }
          }

          if ($found) { // Check the $found array is not empty
            natcasesort($found); // Sort in natural, case-insensitive order, and populate menu
            $filename_array = [];

            foreach ($found as $filename) {
              $filename = basename($filename, '.php');
              $filename_array[] = ['id' => $filename,
                'text' => $filename
              ];

              if ($filename == 'Oscommerce') {
                $filename_array[] = array('id' => 'Oscommerce', 'text' => 'Creload');
                $filename_array[] = array('id' => 'Oscommerce', 'text' => 'OscMax');
                $filename_array[] = array('id' => 'Oscommerce', 'text' => 'Zencart');
              }
            }
          }
        }
      }

      return HTML::selectMenu($name, $filename_array);
    }

//********************************************
//  Hooks function
//********************************************

    public function cleanTableClicShopping()
    {
      $this->app->db->delete('banners');
      $this->app->db->delete('categories');
      $this->app->db->delete('categories_description');
      $this->app->db->delete('manufacturers');
      $this->app->db->delete('manufacturers_info');
      $this->app->db->delete('products');
      $this->app->db->delete('products_description');
      $this->app->db->delete('products_groups');
      $this->app->db->delete('products_images');
      $this->app->db->delete('products_notifications');
      $this->app->db->delete('products_to_categories');
      $this->app->db->delete('reviews');
      $this->app->db->delete('reviews_description');
      $this->app->db->delete('specials');
      $this->app->db->delete('orders_products_attributes');
      $this->app->db->delete('orders_products_download');
      $this->app->db->delete('products_attributes');
      $this->app->db->delete('products_attributes_download');
      $this->app->db->delete('products_options');
      $this->app->db->delete('products_options_values');
      $this->app->db->delete('products_options_values_to_products_options');
    }


    public function readLanguage()
    {
      $Languages = $this->app->db->prepare('select languages_id,
                                                    code
                                           from :table_languages
                                          ');

      $Languages->execute();
      $languages = $Languages->fetchAll();

      return $languages;
    }
  }