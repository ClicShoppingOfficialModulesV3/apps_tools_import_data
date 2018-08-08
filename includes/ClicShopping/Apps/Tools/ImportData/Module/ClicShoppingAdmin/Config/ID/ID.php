<?php
/*
 * BS.php
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 License & MIT Licencse

   * http://clicshopping.no-ip.biz/clicshopping_test/boutique/ClicShoppingAdmin/index.php?A&Tools\ImportData&ActionConfigure&module=ImportData
*/

  namespace ClicShopping\Apps\Tools\ImportData\Module\ClicShoppingAdmin\Config\ID;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class ID extends \ClicShopping\Apps\Tools\ImportData\Module\ClicShoppingAdmin\Config\ConfigAbstract {

    protected $pm_code = 'import_data';

    public $is_uninstallable = true;
    public $sort_order = 400;

    protected function init() {
        $this->title = $this->app->getDef('module_id_title');
        $this->short_title = $this->app->getDef('module_id_short_title');
        $this->introduction = $this->app->getDef('module_id_introduction');
        $this->is_installed = defined('CLICSHOPPING_APP_IMPORT_DATA_ID_STATUS') && (trim(CLICSHOPPING_APP_IMPORT_DATA_ID_STATUS) != '');
    }

    public function install() {
      parent::install();

      if (defined('MODULE_MODULES_IMPORT_DATA_INSTALLED')) {
        $installed = explode(';', MODULE_MODULES_IMPORT_DATA_INSTALLED);
      }

      $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

      $this->app->saveCfgParam('MODULE_MODULES_IMPORT_DATA_INSTALLED', implode(';', $installed));
    }

    public function uninstall() {
      parent::uninstall();

      $installed = explode(';', MODULE_MODULES_IMPORT_DATA_INSTALLED);
      $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

      if ($installed_pos !== false) {
        unset($installed[$installed_pos]);

        $this->app->saveCfgParam('MODULE_MODULES_IMPORT_DATA_INSTALLED', implode(';', $installed));
      }
    }
  }