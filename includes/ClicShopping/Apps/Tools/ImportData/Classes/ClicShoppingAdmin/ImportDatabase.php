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

    /**
     * @param string $name
     * @return string
     */
    public static function ImportSolution(string $name) :string
    {
      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/ClicShoppingAdmin/ImportDatabase/';

      if ($contents = @scandir($template_directory, SCANDIR_SORT_NONE)) {
        if ($contents = @scandir($template_directory, SCANDIR_SORT_NONE)) {
          $fileTypes = ['php']; // Create an array of file types7
          $found = []; // Traverse the folder, and add filename to $found array if type matches

          foreach ($contents as $item) {
            $fileInfo = pathinfo($item);
            if (array_key_exists('extension', $fileInfo) && \in_array($fileInfo['extension'], $fileTypes)) {
              $found[] = $item;
            }
          }

          if ($found) { // Check the $found array is not empty
            natcasesort($found); // Sort in natural, case-insensitive order, and populate menu
            $filename_array = [];

            foreach ($found as $filename) {
              $filename = basename($filename, '.php');
              $filename_array[] = [
                'id' => $filename,
                'text' => $filename
              ];

              if ($filename == 'Oscommerce') {
                $filename_array[] = ['id' => 'Oscommerce', 'text' => 'Creload'];
                $filename_array[] = ['id' => 'Oscommerce', 'text' => 'Zencart'];
              }
            }
          }
        }

        return HTML::selectMenu($name, $filename_array);
      }
    }

//********************************************
//  Hooks function
//********************************************
    /**
     * remove sql data
     */
    public function cleanTableClicShopping(array $array_db)
    {
      if (\is_array($array_db)) {
        foreach ($array_db as $value) {
          $this->app->db->delete($value);
        }
      }
    }

    /**
     * @return mixed
     */
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